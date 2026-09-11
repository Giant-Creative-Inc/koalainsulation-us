<?php
/**
 * Plugin Name: Beanstalk Content Engine
 * Plugin URI: https://github.com/Giant-Creative-Inc/beanstalk-content-engine
 * Description: Structured Gutenberg content definitions, WordPress abilities, and draft creation for Beanstalk and other block themes.
 * Version: 0.1.1
 * Requires at least: 6.9
 * Requires PHP: 8.0
 * Author: Giant Creative Inc.
 * Author URI: https://giantcreative.ca/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: beanstalk-content-engine
 * Update URI: https://github.com/Giant-Creative-Inc/beanstalk-content-engine
 *
 * @package BeanstalkContentEngine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BEANSTALK_CONTENT_ENGINE_VERSION', '0.1.1' );
define( 'BEANSTALK_CONTENT_ENGINE_FILE', __FILE__ );
define( 'BEANSTALK_CONTENT_ENGINE_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Grants the connector capability to administrators.
 *
 * This runs on activation and once after each plugin version change so an
 * already-active installation receives newly introduced capabilities.
 */
function beanstalk_content_engine_install_capabilities(): void {
	$role = get_role( 'administrator' );
	if ( $role && ! $role->has_cap( 'beanstalk_create_ai_content' ) ) {
		$role->add_cap( 'beanstalk_create_ai_content' );
	}
	update_option( 'beanstalk_content_engine_version', BEANSTALK_CONTENT_ENGINE_VERSION, false );
}

register_activation_hook( __FILE__, 'beanstalk_content_engine_install_capabilities' );

/** Applies capability upgrades to installations where the plugin is active. */
function beanstalk_content_engine_maybe_upgrade(): void {
	if ( get_option( 'beanstalk_content_engine_version' ) !== BEANSTALK_CONTENT_ENGINE_VERSION ) {
		beanstalk_content_engine_install_capabilities();
	}
}

add_action( 'init', 'beanstalk_content_engine_maybe_upgrade', 1 );

require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/ProviderRegistry.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/PatternValidator.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/ContentValidator.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/PatternRegistry.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/ContentBuilder.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/CityPageContext.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/DraftManager.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/AbilityRegistrar.php';
require_once BEANSTALK_CONTENT_ENGINE_PATH . 'src/Bootstrap.php';

/**
 * Registers a content-definition provider.
 *
 * Providers should register on the `beanstalk_content_engine_register_providers`
 * action. Registration does not grant MCP access by itself; every definition is
 * validated before it becomes available to an ability.
 *
 * @param array $provider Provider configuration.
 * @return true|WP_Error
 */
function beanstalk_content_engine_register_provider( array $provider ) {
	return GiantCreative\BeanstalkContentEngine\Bootstrap::instance()
		->providers()
		->register( $provider );
}

GiantCreative\BeanstalkContentEngine\Bootstrap::instance()->boot();
