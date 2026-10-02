<?php
/**
 * Settings > Koala ZIP Lookup Log admin screen.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the ZIP lookup log screen and its CSV export.
 *
 * @since 0.8.0
 */
function kgi_register_zip_lookup_log_page_hooks(): void {
	add_action( 'admin_menu', 'kgi_register_zip_lookup_log_page' );
	add_action( 'admin_post_kgi_export_zip_lookup_log', 'kgi_export_zip_lookup_log_csv' );
}

/**
 * Adds the ZIP lookup log screen under the WordPress Settings menu.
 *
 * @since 0.8.0
 */
function kgi_register_zip_lookup_log_page(): void {
	add_options_page(
		__( 'Koala ZIP Lookup Log', 'koala-gravity-integration' ),
		__( 'Koala ZIP Lookup Log', 'koala-gravity-integration' ),
		'manage_options',
		'kgi-zip-lookup-log',
		'kgi_render_zip_lookup_log_page'
	);
}

/**
 * Returns the log, optionally limited to one error type.
 *
 * @since 0.8.0
 *
 * @param string $error_type Error type to keep, or '' for all.
 * @return array<int, array<string, mixed>>
 */
function kgi_get_filtered_zip_lookup_log( string $error_type ): array {
	$log = kgi_get_zip_lookup_log();

	if ( '' === $error_type ) {
		return $log;
	}

	return array_values(
		array_filter(
			$log,
			static function ( array $row ) use ( $error_type ): bool {
				return ( $row['error_type'] ?? '' ) === $error_type;
			}
		)
	);
}

/**
 * Renders the ZIP lookup log screen.
 *
 * @since 0.8.0
 */
function kgi_render_zip_lookup_log_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$selected_type = isset( $_GET['error_type'] ) ? sanitize_key( wp_unslash( $_GET['error_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter.
	$all           = kgi_get_zip_lookup_log();
	$rows          = kgi_get_filtered_zip_lookup_log( $selected_type );
	$counts        = array_count_values( array_column( $all, 'error_type' ) );

	ksort( $counts );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Koala ZIP Lookup Log', 'koala-gravity-integration' ); ?></h1>

		<p>
			<?php esc_html_e( 'Every failed zipcodeapi.com lookup from the location search and the quote forms over the last 30 days, newest first. Search visitors saw a "please try again later" message; quote form leads went to unmatched lead routing.', 'koala-gravity-integration' ); ?>
		</p>

		<form method="get" style="margin: 12px 0;">
			<input type="hidden" name="page" value="kgi-zip-lookup-log" />
			<label for="kgi-error-type"><?php esc_html_e( 'Error type:', 'koala-gravity-integration' ); ?></label>
			<select name="error_type" id="kgi-error-type">
				<option value=""><?php echo esc_html( sprintf( /* translators: %d: number of log entries */ __( 'All (%d)', 'koala-gravity-integration' ), count( $all ) ) ); ?></option>
				<?php foreach ( $counts as $type => $count ) : ?>
					<option value="<?php echo esc_attr( (string) $type ); ?>" <?php selected( $selected_type, (string) $type ); ?>>
						<?php echo esc_html( kgi_get_zip_lookup_error_label( (string) $type ) . ' (' . $count . ')' ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php submit_button( __( 'Filter', 'koala-gravity-integration' ), 'secondary', '', false ); ?>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=kgi_export_zip_lookup_log&error_type=' . rawurlencode( $selected_type ) ), 'kgi_export_zip_lookup_log' ) ); ?>">
				<?php esc_html_e( 'Export CSV', 'koala-gravity-integration' ); ?>
			</a>
		</form>

		<?php if ( empty( $rows ) ) : ?>
			<p><em><?php esc_html_e( 'No failed lookups recorded.', 'koala-gravity-integration' ); ?></em></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Where', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'ZIP / postal code', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Error', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'zipcodeapi.com message', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Entry', 'koala-gravity-integration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( wp_date( 'Y-m-d H:i:s', (int) $row['time'] ) ); ?></td>
							<td><?php echo esc_html( 'form' === $row['source'] ? __( 'Quote form', 'koala-gravity-integration' ) : __( 'Location search', 'koala-gravity-integration' ) ); ?></td>
							<td><?php echo esc_html( (string) $row['code'] ); ?></td>
							<td><?php echo esc_html( kgi_get_zip_lookup_error_label( (string) $row['error_type'] ) ); ?></td>
							<td><?php echo esc_html( (string) $row['message'] ); ?></td>
							<td>
								<?php if ( ! empty( $row['entry_id'] ) ) : ?>
									<?php
									$entry   = GFAPI::get_entry( (int) $row['entry_id'] );
									$form_id = is_array( $entry ) ? (int) $entry['form_id'] : 0;
									?>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=gf_entries&view=entry&id=' . $form_id . '&lid=' . (int) $row['entry_id'] ) ); ?>">
										<?php echo esc_html( '#' . (int) $row['entry_id'] ); ?>
									</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Streams the (optionally filtered) ZIP lookup log as a CSV download.
 *
 * @since 0.8.0
 */
function kgi_export_zip_lookup_log_csv(): void {
	check_admin_referer( 'kgi_export_zip_lookup_log' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to export the ZIP lookup log.', 'koala-gravity-integration' ) );
	}

	$error_type = isset( $_GET['error_type'] ) ? sanitize_key( wp_unslash( $_GET['error_type'] ) ) : '';
	$rows       = kgi_get_filtered_zip_lookup_log( $error_type );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=koala-zip-lookup-log-' . gmdate( 'Y-m-d' ) . '.csv' );

	$output = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

	fputcsv( $output, array( 'time_utc', 'source', 'code', 'error_type', 'http_status', 'message', 'entry_id' ) );

	foreach ( $rows as $row ) {
		fputcsv(
			$output,
			array(
				gmdate( 'Y-m-d H:i:s', (int) $row['time'] ),
				$row['source'],
				$row['code'],
				$row['error_type'],
				$row['http_status'],
				$row['message'],
				$row['entry_id'],
			)
		);
	}

	fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
