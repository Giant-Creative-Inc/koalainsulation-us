<?php
/**
 * Plugin bootstrap loader.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads plugin modules and registers all hooks.
 *
 * Fires on `plugins_loaded` after confirming Gravity Forms is active.
 *
 * @since 0.1.0
 */
function kgi_bootstrap(): void {
	if ( ! kgi_is_gravity_forms_active() ) {
		return;
	}

	require_once KGI_PLUGIN_DIR . 'includes/logger.php';
	require_once KGI_PLUGIN_DIR . 'includes/location-resolver.php';
	require_once KGI_PLUGIN_DIR . 'includes/admin/entry-details.php';
	require_once KGI_PLUGIN_DIR . 'includes/admin/resend.php';
	require_once KGI_PLUGIN_DIR . 'includes/admin/settings.php';
	require_once KGI_PLUGIN_DIR . 'includes/forms/assets.php';
	require_once KGI_PLUGIN_DIR . 'includes/forms/field-population.php';
	require_once KGI_PLUGIN_DIR . 'includes/forms/form-handler.php';
	require_once KGI_PLUGIN_DIR . 'includes/forms/payload-builder.php';
	require_once KGI_PLUGIN_DIR . 'includes/jobs/background-jobs.php';

	kgi_register_form_asset_hooks();
	kgi_register_field_population_hooks();
	kgi_register_background_job_hooks();
	kgi_register_admin_entry_hooks();
	kgi_register_resend_hooks();
	kgi_register_settings_hooks();
	kgi_register_form_hooks();

	add_action( 'init', 'kgi_maybe_warm_location_zip_index', 20 );
	add_action( 'acf/save_post', 'kgi_refresh_location_zip_index', 20 );
	add_action( 'save_post_' . kgi_get_location_post_type(), 'kgi_invalidate_location_zip_index', 20, 1 );
	add_action( 'before_delete_post', 'kgi_invalidate_location_zip_index', 10, 1 );
}
