<?php
/**
 * Entry detail sidebar panel for the Gravity Forms admin.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Gravity Forms entry detail sidebar hook.
 *
 * @since 0.1.0
 */
function kgi_register_admin_entry_hooks(): void {
	add_action(
		'gform_entry_detail_sidebar_after',
		'kgi_show_location_entry_details',
		10,
		2
	);
}

/**
 * Renders the location routing panel in the Gravity Forms entry detail sidebar.
 *
 * Displays the resolved location name, ID, and submission status. Shows
 * a warning if the entry has been stuck in `queued` for over 5 minutes
 * (indicating WP-Cron may not be running), and an error panel if processing
 * failed with a stored error message.
 *
 * @since 0.1.0
 *
 * @param mixed[] $form  Gravity Forms form array.
 * @param mixed[] $entry Gravity Forms entry array.
 */
function kgi_show_location_entry_details( array $form, array $entry ): void {
	$form_id       = (int) $form['id'];
	$is_known_form = in_array( $form_id, kgi_get_all_quote_form_ids(), true ) || null !== kgi_get_fixed_location_form_config( $form_id );

	if ( ! $is_known_form ) {
		return;
	}

	$entry_id             = (int) $entry['id'];
	$location_status      = gform_get_meta( $entry_id, 'kgi_location_status' );
	$location_id          = gform_get_meta( $entry_id, 'kgi_location_id' );
	$location_name        = gform_get_meta( $entry_id, 'kgi_location_name' );
	$submission_status    = gform_get_meta( $entry_id, 'kgi_submission_status' );
	$error_message        = gform_get_meta( $entry_id, 'kgi_error_message' );
	$queued_at            = gform_get_meta( $entry_id, 'kgi_queued_at' );
	$retry_count          = (int) gform_get_meta( $entry_id, 'kgi_retry_count' );
	$sent_at              = gform_get_meta( $entry_id, 'kgi_sent_at' );
	$send_delay           = gform_get_meta( $entry_id, 'kgi_send_delay_seconds' );
	$sheet_status         = gform_get_meta( $entry_id, 'kgi_google_sheet_status' );
	$sheet_error          = gform_get_meta( $entry_id, 'kgi_google_sheet_error' );
	$sheet_sent_at        = gform_get_meta( $entry_id, 'kgi_google_sheet_sent_at' );
	$routed_location_id   = gform_get_meta( $entry_id, 'kgi_routed_location_id' );
	$routed_location_name = gform_get_meta( $entry_id, 'kgi_routed_location_name' );
	$zip_routing_status   = gform_get_meta( $entry_id, 'kgi_zip_routing_status' );

	$has_failed  = in_array( $submission_status, array( 'failed', 'schedule_failed' ), true );
	$is_retrying = 'retrying' === $submission_status;
	$is_stuck    = 'queued' === $submission_status && $queued_at && ( time() - (int) $queued_at ) > 300;

	?>
	<div class="postbox">
		<h3 class="hndle" style="padding: 10px 12px; margin: 0;">
			<?php esc_html_e( 'Koala Location Routing', 'koala-gravity-integration' ); ?>
		</h3>

		<div class="inside">

			<?php if ( $has_failed ) : ?>
				<div style="background: #fbeaea; border-left: 4px solid #dc3232; padding: 8px 10px; margin-bottom: 10px;">
					<strong><?php esc_html_e( 'Processing failed.', 'koala-gravity-integration' ); ?></strong>
					<?php if ( $error_message ) : ?>
						<br><code><?php echo esc_html( $error_message ); ?></code>
					<?php endif; ?>
				</div>
			<?php elseif ( $is_retrying ) : ?>
				<div style="background: #fff8e5; border-left: 4px solid #ffb900; padding: 8px 10px; margin-bottom: 10px;">
					<?php
					printf(
						/* translators: 1: current retry attempt number, 2: maximum number of retries */
						esc_html__( 'n8n delivery failed. Retry %1$d of %2$d scheduled.', 'koala-gravity-integration' ),
						absint( $retry_count ),
						absint( KGI_MAX_RETRIES )
					);
					?>
					<?php if ( $error_message ) : ?>
						<br><code><?php echo esc_html( $error_message ); ?></code>
					<?php endif; ?>
				</div>
			<?php elseif ( $is_stuck ) : ?>
				<div style="background: #fff8e5; border-left: 4px solid #ffb900; padding: 8px 10px; margin-bottom: 10px;">
					<?php esc_html_e( 'This entry has been queued for more than 5 minutes. WP-Cron may not be running.', 'koala-gravity-integration' ); ?>
				</div>
			<?php endif; ?>

			<p>
				<strong><?php esc_html_e( 'Status:', 'koala-gravity-integration' ); ?></strong>
				<?php echo esc_html( $submission_status ? $submission_status : __( 'Not set', 'koala-gravity-integration' ) ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Location Status:', 'koala-gravity-integration' ); ?></strong>
				<?php echo esc_html( $location_status ? $location_status : __( 'Not set', 'koala-gravity-integration' ) ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Location Name:', 'koala-gravity-integration' ); ?></strong>
				<?php echo esc_html( $location_name ? $location_name : __( 'Not resolved', 'koala-gravity-integration' ) ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Location ID:', 'koala-gravity-integration' ); ?></strong>
				<?php echo esc_html( $location_id ? $location_id : __( 'Not resolved', 'koala-gravity-integration' ) ); ?>
			</p>

			<?php if ( $routed_location_id ) : ?>
				<p>
					<strong><?php esc_html_e( 'n8n Location:', 'koala-gravity-integration' ); ?></strong>
					<?php echo esc_html( $routed_location_name ? $routed_location_name : __( 'Not resolved', 'koala-gravity-integration' ) ); ?>
					<?php echo esc_html( ' (#' . absint( $routed_location_id ) . ')' ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'ZIP Routing:', 'koala-gravity-integration' ); ?></strong>
					<?php echo esc_html( $zip_routing_status ? $zip_routing_status : __( 'Not set', 'koala-gravity-integration' ) ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $sent_at ) : ?>
				<p>
					<strong><?php esc_html_e( 'Sent to n8n at:', 'koala-gravity-integration' ); ?></strong>
					<?php echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d H:i:s', (int) $sent_at ), 'Y-m-d H:i:s' ) ); ?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Delay after submission:', 'koala-gravity-integration' ); ?></strong>
					<?php
					printf(
						/* translators: %s: number of seconds between form submission and the n8n push */
						esc_html__( '%s seconds', 'koala-gravity-integration' ),
						esc_html( (string) absint( $send_delay ) )
					);
					?>
				</p>
			<?php endif; ?>

			<?php if ( $sheet_status ) : ?>
				<p>
					<strong><?php esc_html_e( 'Google Sheet webhook:', 'koala-gravity-integration' ); ?></strong>
					<?php echo esc_html( $sheet_status ); ?>
				</p>

				<?php if ( $sheet_error ) : ?>
					<p><code><?php echo esc_html( $sheet_error ); ?></code></p>
				<?php endif; ?>

				<?php if ( $sheet_sent_at ) : ?>
					<p>
						<strong><?php esc_html_e( 'Dispatched at:', 'koala-gravity-integration' ); ?></strong>
						<?php echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d H:i:s', (int) $sheet_sent_at ), 'Y-m-d H:i:s' ) ); ?>
						<br>
						<em><?php esc_html_e( '"Dispatched" only confirms the request was sent from this server, not that Zapier received or processed it.', 'koala-gravity-integration' ); ?></em>
					</p>
				<?php endif; ?>
			<?php endif; ?>

		</div>
	</div>
	<?php
}
