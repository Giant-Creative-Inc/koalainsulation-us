<?php
/**
 * Admin notice for missing Gravity Forms dependency.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders an admin notice when Gravity Forms is not active.
 *
 * Hooked to `admin_notices`.
 *
 * @since 0.1.0
 */
function kgi_gravity_forms_missing_notice(): void {
	if ( kgi_is_gravity_forms_active() ) {
		return;
	}

	?>
	<div class="notice notice-error">
		<p>
			<strong><?php esc_html_e( 'Koala Gravity Integration is inactive.', 'koala-gravity-integration' ); ?></strong>
			<?php esc_html_e( 'Gravity Forms must be installed and active for this plugin to work.', 'koala-gravity-integration' ); ?>
		</p>
	</div>
	<?php
}
