<?php
/**
 * Near-viewport loading for native location review and map widgets.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Delay only known widget scripts, preserving all form dependencies.
 *
 * @param string $tag Script markup.
 * @param string $handle Script handle.
 * @return string
 */
function koala_beanstalk_location_lazy_widget_script( string $tag, string $handle ): string {
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return $tag;
	}
	$groups = array(
		'grw-public-main-js'   => 'reviews',
		'zip-shape-map-loader' => 'map',
	);
	if ( ! isset( $groups[ $handle ] ) ) {
		return $tag;
	}
	return preg_replace_callback(
		'/<script\b[^>]*>/i',
		static function ( array $matches ) use ( $groups, $handle ): string {
			$script = $matches[0];
			if ( ! preg_match( '/\ssrc\s*=/i', $script ) ) {
				return $script;
			}
			$script = preg_replace( '/\stype=([\'"])[^\'"]*\1/i', '', $script );
			$script = preg_replace( '/\ssrc\s*=/i', ' data-widget-src=', $script, 1 );
			return preg_replace( '/^<script/i', '<script type="text/plain" data-location-widget="' . $groups[ $handle ] . '"', $script );
		},
		$tag
	);
}
add_filter( 'script_loader_tag', 'koala_beanstalk_location_lazy_widget_script', 100, 2 );

/** Keep review stylesheet downloads with the review widget.
 *
 * @param string $html Style markup.
 * @param string $handle Style handle.
 * @return string
 */
function koala_beanstalk_location_lazy_widget_style( string $html, string $handle ): string {
	if ( ! koala_beanstalk_sidebar_preview() || 'grw-public-main-css' !== $handle ) {
		return $html;
	}
	$tag = preg_replace( '/\sonload=([\'"]).*?\1/i', '', $html );
	$tag = preg_replace( '/\shref=/i', ' data-widget-href=', $tag, 1 );
	return str_replace( '<link', '<link data-location-widget="reviews"', $tag ) . '<noscript>' . $html . '</noscript>';
}
add_filter( 'style_loader_tag', 'koala_beanstalk_location_lazy_widget_style', 120, 2 );

/** Enqueue the small observer and remove the unused legacy Swiper library. */
function koala_beanstalk_location_lazy_widgets_assets(): void {
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return;
	}
	wp_dequeue_script( 'swiper-bundle' );
	wp_enqueue_script( 'koala-location-lazy-widgets', get_template_directory_uri() . '/beanstalk/assets/location-lazy-widgets.js', array(), (string) filemtime( __DIR__ . '/assets/location-lazy-widgets.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_location_lazy_widgets_assets', 1010 );
