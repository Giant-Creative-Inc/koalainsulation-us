<?php
/**
 * Manual "Resend to n8n" tools for the Gravity Forms admin.
 *
 * Lets an admin re-run the background job for an entry that has already been
 * processed — e.g. after fixing a field mapping — so a corrected payload is
 * re-POSTed to n8n. Exposed as a button on the entry detail screen and as a
 * bulk action on the entries list.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the resend hooks (button handler, bulk action, notices).
 *
 * @since 0.7.1
 */
function kgi_register_resend_hooks(): void {
	add_action( 'admin_post_kgi_resend_entry', 'kgi_handle_resend_entry_request' );
	add_action( 'admin_init', 'kgi_maybe_process_resend_bulk_action' );
	add_filter( 'gform_entry_list_bulk_actions', 'kgi_add_resend_bulk_action' );
	add_action( 'admin_notices', 'kgi_render_resend_admin_notice' );
}

/**
 * Returns whether the current user may resend entries.
 *
 * @since 0.7.1
 *
 * @return bool
 */
function kgi_current_user_can_resend(): bool {
	// gravityforms_edit_entries is a custom capability registered by Gravity Forms.
	return current_user_can( 'gravityforms_edit_entries' ) || current_user_can( 'manage_options' ); // phpcs:ignore WordPress.WP.Capabilities.Unknown
}

/**
 * Re-runs the background job for a single entry, forcing it past the
 * "already processed" status guard so the payload is rebuilt and re-sent.
 *
 * @since 0.7.1
 *
 * @param int $entry_id Gravity Forms entry ID.
 * @return bool True if the entry finished in the `succeeded` state.
 */
function kgi_resend_entry_to_n8n( int $entry_id ): bool {
	if ( $entry_id <= 0 ) {
		return false;
	}

	$entry = GFAPI::get_entry( $entry_id );

	if ( is_wp_error( $entry ) ) {
		return false;
	}

	// Clear the guard in kgi_process_quote_entry_job() that skips entries
	// already marked `processing`/`succeeded`, so this runs again.
	gform_update_meta( $entry_id, 'kgi_submission_status', 'queued' );

	kgi_process_quote_entry_job( $entry_id );

	return 'succeeded' === gform_get_meta( $entry_id, 'kgi_submission_status' );
}

/**
 * Renders the "Resend to n8n" button in the entry detail sidebar.
 *
 * Called from kgi_show_location_entry_details(). Posts to admin-post.php with
 * a per-entry nonce; a JS confirm warns about duplicate downstream sends.
 *
 * @since 0.7.1
 *
 * @param int $form_id  Gravity Forms form ID.
 * @param int $entry_id Gravity Forms entry ID.
 */
function kgi_render_resend_button( int $form_id, int $entry_id ): void {
	if ( ! kgi_current_user_can_resend() ) {
		return;
	}
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top: 12px;">
		<input type="hidden" name="action" value="kgi_resend_entry" />
		<input type="hidden" name="entry_id" value="<?php echo esc_attr( (string) $entry_id ); ?>" />
		<input type="hidden" name="form_id" value="<?php echo esc_attr( (string) $form_id ); ?>" />
		<?php wp_nonce_field( 'kgi_resend_entry_' . $entry_id ); ?>
		<button
			type="submit"
			class="button"
			onclick="return confirm('<?php echo esc_js( __( 'Resend this entry to n8n now? This re-runs the full workflow and may create a duplicate downstream (CRM / Google Sheet).', 'koala-gravity-integration' ) ); ?>');"
		>
			<?php esc_html_e( 'Resend to n8n', 'koala-gravity-integration' ); ?>
		</button>
	</form>
	<?php
}

/**
 * Handles the single-entry resend request from the entry detail button.
 *
 * @since 0.7.1
 */
function kgi_handle_resend_entry_request(): void {
	$entry_id = isset( $_POST['entry_id'] ) ? absint( $_POST['entry_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$form_id  = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	check_admin_referer( 'kgi_resend_entry_' . $entry_id );

	if ( ! kgi_current_user_can_resend() ) {
		wp_die( esc_html__( 'You are not allowed to resend entries.', 'koala-gravity-integration' ) );
	}

	$succeeded = kgi_resend_entry_to_n8n( $entry_id );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'       => 'gf_entries',
				'view'       => 'entry',
				'id'         => $form_id,
				'lid'        => $entry_id,
				'kgi_resent' => $succeeded ? '1' : '0',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}

/**
 * Adds the "Resend to n8n" option to the entries list bulk-action dropdown.
 *
 * @since 0.7.1
 *
 * @param array<string, string> $actions Existing bulk actions.
 * @return array<string, string> Actions with the resend option added.
 */
function kgi_add_resend_bulk_action( array $actions ): array {
	if ( kgi_current_user_can_resend() ) {
		$actions['kgi_resend'] = __( 'Resend to n8n', 'koala-gravity-integration' );
	}

	return $actions;
}

/**
 * Processes the "Resend to n8n" bulk action on the entries list.
 *
 * Reads the selected action and entry IDs defensively, since Gravity Forms'
 * field names for these have differed across versions (`bulk_action`/`action`
 * for the dropdown, `lead`/`entry` for the selected IDs), and accepts either
 * the Gravity Forms list nonce or the WP_List_Table bulk nonce.
 *
 * @since 0.7.1
 */
function kgi_maybe_process_resend_bulk_action(): void {
	if ( ! is_admin() || 'gf_entries' !== rgget( 'page' ) ) {
		return;
	}

	$action = '';

	foreach ( array( 'bulk_action', 'bulk_action2', 'action', 'action2' ) as $field ) {
		$value = rgpost( $field );

		if ( $value && '-1' !== $value ) {
			$action = $value;
			break;
		}
	}

	if ( 'kgi_resend' !== $action ) {
		return;
	}

	$nonce_ok = ( rgpost( 'gforms_entry_list' ) && wp_verify_nonce( rgpost( 'gforms_entry_list' ), 'gforms_entry_list' ) )
		|| ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'bulk-entries' ) );

	if ( ! $nonce_ok || ! kgi_current_user_can_resend() ) {
		return;
	}

	$ids = rgpost( 'entry' );

	if ( ! is_array( $ids ) ) {
		$ids = rgpost( 'lead' );
	}

	if ( ! is_array( $ids ) ) {
		return;
	}

	$count = 0;

	foreach ( $ids as $id ) {
		if ( kgi_resend_entry_to_n8n( absint( $id ) ) ) {
			++$count;
		}
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'             => 'gf_entries',
				'id'               => absint( rgget( 'id' ) ),
				'kgi_resent_count' => $count,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}

/**
 * Shows a success/failure notice after a resend.
 *
 * @since 0.7.1
 */
function kgi_render_resend_admin_notice(): void {
	if ( isset( $_GET['kgi_resent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$succeeded = '1' === $_GET['kgi_resent']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		printf(
			'<div class="notice %1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $succeeded ? 'notice-success' : 'notice-error' ),
			esc_html(
				$succeeded
					? __( 'Entry resent to n8n.', 'koala-gravity-integration' )
					: __( 'Resend did not complete — check the Koala Location Routing panel and logs.', 'koala-gravity-integration' )
			)
		);
		return;
	}

	if ( isset( $_GET['kgi_resent_count'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$count = absint( $_GET['kgi_resent_count'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %d: number of entries resent */
					_n( '%d entry resent to n8n.', '%d entries resent to n8n.', $count, 'koala-gravity-integration' ),
					$count
				)
			)
		);
	}
}
