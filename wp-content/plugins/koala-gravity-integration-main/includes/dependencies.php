<?php
/**
 * Dependency checks and plugin activation guard.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks whether Gravity Forms is active.
 *
 * @since 0.1.0
 *
 * @return bool True if Gravity Forms is active, false otherwise.
 */
function kgi_is_gravity_forms_active(): bool {
	return class_exists( 'GFForms' );
}

/**
 * Deactivates the plugin with an error page if Gravity Forms is not active.
 *
 * Hooked to the plugin activation hook.
 *
 * @since 0.1.0
 */
function kgi_activate_plugin(): void {
	if ( kgi_is_gravity_forms_active() ) {
		return;
	}

	deactivate_plugins( KGI_PLUGIN_BASENAME );

	wp_die(
		esc_html__( 'Koala Gravity Integration requires Gravity Forms to be installed and active first.', 'koala-gravity-integration' ),
		esc_html__( 'Gravity Forms Required', 'koala-gravity-integration' ),
		array( 'back_link' => true )
	);
}
