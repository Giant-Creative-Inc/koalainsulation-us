<?php
/**
 * First-paint asset delivery for native location pages.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Inline trusted critical CSS and defer the lower section bundle.
 *
 * @param string $html Stylesheet link.
 * @param string $handle Style handle.
 * @return string
 */
function koala_beanstalk_location_performance_style( string $html, string $handle ): string {
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return $html;
	}
	if ( 'koala-beanstalk-location-deferred' === $handle ) {
		$link = preg_replace( '/\smedia\s*=\s*([\'"])[^\'"]*\1/i', '', $html );
		return preg_replace( '/<link\b/i', '<link data-no-optimize="1" media="print" onload="this.media=\'all\'"', $link, 1 ) . '<noscript>' . $html . '</noscript>';
	}
	$files = array(
		'koala-beanstalk-location-critical' => __DIR__ . '/assets/location-critical.css',
		'gravity_forms_theme_reset'         => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-theme-reset.min.css',
		'gravity_forms_theme_foundation'    => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-theme-foundation.min.css',
		'gravity_forms_theme_framework'     => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-theme-framework.min.css',
		'gravity_forms_orbital_theme'       => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-orbital-theme.min.css',
	);
	if ( ! isset( $files[ $handle ] ) || ! is_readable( $files[ $handle ] ) ) {
		return $html;
	}
	$base = 'koala-beanstalk-location-critical' === $handle ? get_template_directory_uri() . '/beanstalk/assets/' : plugins_url( 'gravityforms/assets/css/dist/' );
 // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Trusted local CSS.
	$css = file_get_contents( $files[ $handle ] );
	$css = preg_replace_callback(
		'~url\(\s*([\'"]?)([^\'"\)]+)\1\s*\)~i',
		static function ( $css_match ) use ( $base ): string {
			$url = trim( $css_match[2] );
			return 'url("' . ( preg_match( '~^(?:[a-z][a-z0-9+.-]*:|/|\#)~i', $url ) ? $url : $base . $url ) . '")';
		},
		$css
	);
	return '<style id="' . esc_attr( $handle ) . '-css" data-no-optimize="1">' . $css . '</style>';
}
add_filter( 'style_loader_tag', 'koala_beanstalk_location_performance_style', 110, 2 );

/** Preload only the fonts needed by the mobile hero text. */
function koala_beanstalk_location_preload_fonts(): void {
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return;
	}
	foreach ( array( 'location-fonts/FilsonProRegular.woff2', 'location-hero/alegreya-sans-bold.woff2' ) as $font ) {
		echo '<link rel="preload" as="font" type="font/woff2" crossorigin href="' . esc_url( get_template_directory_uri() . '/beanstalk/assets/' . $font ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'koala_beanstalk_location_preload_fonts', 1 );
