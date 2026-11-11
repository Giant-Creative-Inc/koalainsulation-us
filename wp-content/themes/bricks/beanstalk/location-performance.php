<?php
/**
 * First-paint asset delivery for native location pages.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow a lower section stylesheet to download without blocking first paint.
 *
 * @param string $html Stylesheet link.
 */
function koala_beanstalk_defer_stylesheet( string $html ): string {
	$link = preg_replace( '/\smedia\s*=\s*([\'"])[^\'"]*\1/i', '', $html );
	return preg_replace( '/<link\b/i', '<link data-no-optimize="1" media="print" onload="this.media=\'all\'"', $link, 1 ) . '<noscript>' . $html . '</noscript>';
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
		return koala_beanstalk_defer_stylesheet( $html );
	}
	$files = array(
		'koala-beanstalk-location-critical' => __DIR__ . '/assets/location-critical.css',
		'gravity_forms_theme_reset'         => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-theme-reset.min.css',
		'gravity_forms_theme_foundation'    => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-theme-foundation.min.css',
		'gravity_forms_theme_framework'     => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-theme-framework.min.css',
		'gravity_forms_orbital_theme'       => WP_PLUGIN_DIR . '/gravityforms/assets/css/dist/gravity-forms-orbital-theme.min.css',
	);
	if ( ! isset( $files[ $handle ] ) ) {
		return $html;
	}
	$base = 'koala-beanstalk-location-critical' === $handle ? get_template_directory_uri() . '/beanstalk/assets/' : plugins_url( 'gravityforms/assets/css/dist/' );
	return koala_beanstalk_inline_asset_stylesheet( $html, $handle, $files[ $handle ], $base );
}
add_filter( 'style_loader_tag', 'koala_beanstalk_location_performance_style', 110, 2 );

/**
 * Inline a trusted stylesheet and resolve relative URLs against its asset directory.
 *
 * @param string $html Original link.
 * @param string $handle WordPress style handle.
 * @param string $file Local stylesheet path.
 * @param string $base Asset base URL.
 */
function koala_beanstalk_inline_asset_stylesheet( string $html, string $handle, string $file, string $base ): string {
	if ( ! is_readable( $file ) ) {
		return $html;
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Trusted local CSS.
	$css = file_get_contents( $file );
	if ( false === $css ) {
		return $html;
	}
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
