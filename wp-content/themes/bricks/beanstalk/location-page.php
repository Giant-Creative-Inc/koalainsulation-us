<?php
/**
 * Native location-page preview foundation.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the native template without rendering any Bricks template data.
 *
 * @param string $template Original template.
 * @return string
 */
function koala_beanstalk_location_template( string $template ): string {
	return koala_beanstalk_sidebar_preview() ? __DIR__ . '/location-template.php' : $template;
}
add_filter( 'template_include', 'koala_beanstalk_location_template', 1000 );

/**
 * Identify legacy frontend assets while keeping plugins and native components.
 *
 * @param string $handle Asset handle.
 * @param string $source Asset URL.
 * @param string $theme_url Bricks theme URL with trailing slash.
 * @return bool
 */
function koala_beanstalk_location_legacy_asset( string $handle, string $source, string $theme_url ): bool {
	if ( strpos( $source, $theme_url . 'beanstalk/' ) === 0 ) {
		return false;
	}
	return strpos( $handle, 'bricks-' ) === 0
		|| strpos( $source, $theme_url ) === 0
		|| strpos( $source, '/uploads/bricks/' ) !== false;
}

/** Remove Bricks-owned output callbacks before the native document renders. */
function koala_beanstalk_location_prepare(): void {
	global $wp_filter;
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return;
	}
	remove_action( 'wp_footer', 'custom_body_code' );
	foreach ( array( 'wp_head', 'wp_footer', 'wp_enqueue_scripts', 'wp_get_attachment_image_attributes' ) as $tag ) {
		if ( ! isset( $wp_filter[ $tag ] ) ) {
			continue;
		}
		foreach ( $wp_filter[ $tag ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $callback ) {
				$function = $callback['function'];
				if ( is_array( $function ) && is_object( $function[0] ) && strpos( get_class( $function[0] ), 'Bricks\\' ) === 0 ) {
					remove_filter( $tag, $function, $priority );
				}
			}
		}
	}
}
add_action( 'template_redirect', 'koala_beanstalk_location_prepare', 101 );

/** Keep only native theme components and plugin dependencies on location previews. */
function koala_beanstalk_location_assets(): void {
	global $wp_scripts, $wp_styles;
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return;
	}
	$theme_url = trailingslashit( get_template_directory_uri() );
	foreach ( array(
		'style'  => $wp_styles,
		'script' => $wp_scripts,
	) as $type => $registry ) {
		if ( ! is_object( $registry ) ) {
			continue;
		}
		foreach ( $registry->queue as $handle ) {
			$source = $registry->registered[ $handle ]->src ?? '';
			if ( koala_beanstalk_location_legacy_asset( $handle, is_string( $source ) ? $source : '', $theme_url ) ) {
				if ( 'style' === $type ) {
					wp_dequeue_style( $handle );
				} else {
					wp_dequeue_script( $handle );
				}
			}
		}
	}
	wp_enqueue_style( 'wp-block-library' );
	wp_enqueue_style( 'global-styles' );
	// Section sources are bundled; first-paint rules are inlined by location-performance.php.
	wp_dequeue_style( 'koala-location-sidebar' );
	wp_dequeue_style( 'koala-location-estimate' );
	foreach ( array( 'location-critical', 'location-deferred' ) as $asset ) {
		wp_enqueue_style( 'koala-beanstalk-' . $asset, $theme_url . 'beanstalk/assets/' . $asset . '.css', array(), (string) filemtime( __DIR__ . '/assets/' . $asset . '.css' ) );
	}
	wp_enqueue_script(
		'koala-location-gtm',
		$theme_url . 'beanstalk/assets/location-gtm.js',
		array(),
		(string) filemtime( __DIR__ . '/assets/location-gtm.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
	wp_enqueue_script( 'koala-location-community', $theme_url . 'beanstalk/assets/location-community.js', array(), (string) filemtime( __DIR__ . '/assets/location-community.js' ), true );
	wp_enqueue_script( 'koala-location-features', $theme_url . 'beanstalk/assets/location-features.js', array(), (string) filemtime( __DIR__ . '/assets/location-features.js' ), true );
	wp_enqueue_script( 'koala-location-overview', $theme_url . 'beanstalk/assets/location-overview.js', array(), (string) filemtime( __DIR__ . '/assets/location-overview.js' ), true );
	wp_enqueue_script( 'koala-location-services', $theme_url . 'beanstalk/assets/location-services.js', array(), (string) filemtime( __DIR__ . '/assets/location-services.js' ), true );
	wp_enqueue_script( 'koala-location-partners', $theme_url . 'beanstalk/assets/location-partners.js', array(), (string) filemtime( __DIR__ . '/assets/location-partners.js' ), true );
	wp_enqueue_script( 'koala-location-hero', $theme_url . 'beanstalk/assets/location-hero.js', array(), (string) filemtime( __DIR__ . '/assets/location-hero.js' ), true );
	wp_enqueue_script( 'koala-beanstalk-header', $theme_url . 'beanstalk/assets/header.js', array(), (string) filemtime( __DIR__ . '/assets/header.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_location_assets', 1005 );
