<?php
/**
 * Plugin Name:       Koala Gravity Integration
 * Description:       Custom Gravity Forms integration for Koala Insulation quote forms.
 * Version:           0.7.3
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            GIANT Creative
 * Text Domain:       koala-gravity-integration
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/constants.php';
require_once KGI_PLUGIN_DIR . 'includes/dependencies.php';
require_once KGI_PLUGIN_DIR . 'includes/admin/notices.php';
require_once KGI_PLUGIN_DIR . 'includes/bootstrap.php';

register_activation_hook( __FILE__, 'kgi_activate_plugin' );

add_action( 'admin_notices', 'kgi_gravity_forms_missing_notice' );
add_action( 'plugins_loaded', 'kgi_bootstrap' );
