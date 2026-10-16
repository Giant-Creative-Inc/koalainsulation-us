<?php
/**
 * Location sidebar form and bounded Local preview.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow native previews on Local or an explicitly configured staging environment.
 */
function koala_beanstalk_location_environment_enabled(): bool {
	return 'koalainsulation.local' === wp_parse_url( home_url(), PHP_URL_HOST )
	|| ( function_exists( 'wp_get_environment_type' ) && 'staging' === wp_get_environment_type() );
}

/**
 * Determine whether this Local location request opted into the sidebar preview.
 */
function koala_beanstalk_sidebar_preview(): bool {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only Local preview switch.
	$requested = isset( $_GET['koala_beanstalk_sidebar'] ) && '1' === $_GET['koala_beanstalk_sidebar'];
	return $requested
	&& koala_beanstalk_location_environment_enabled()
	&& is_singular( 'location' )
	&& ! ( function_exists( 'bricks_is_builder' ) && bricks_is_builder() )
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Never change builder output.
	&& ! isset( $_GET['bricks'] );
}

/**
 * Render the real location lead form, preserving Gravity Forms feeds and consent.
 *
 * @param  array $attributes Optional block attributes.
 * @return string
 */
function koala_beanstalk_render_location_sidebar( array $attributes = array() ): string {
	if ( ! function_exists( 'gravity_form' ) ) {
		return '';
	}
	$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : 'Talk to Our Insulation Experts Today.';
	$form    = gravity_form( 12, false, false, false, null, true, 200, false );
	return '<div class="koala-location-sidebar" data-location-sidebar>'
	. '<button type="button" class="koala-location-sidebar__trigger" data-sidebar-open aria-controls="koala-location-sidebar" aria-expanded="false">Get a FREE Quote</button>'
	. '<aside id="koala-location-sidebar" class="koala-location-sidebar__panel" aria-labelledby="koala-location-sidebar-title" tabindex="-1">'
	. '<button type="button" class="koala-location-sidebar__close" data-sidebar-close aria-label="Close quote form">&times;</button>'
	. '<div class="koala-location-sidebar__heading"><h2 id="koala-location-sidebar-title">' . esc_html( $heading ) . '</h2></div>'
	. '<div class="koala-location-sidebar__form" id="sidebar-form">' . $form . '</div></aside></div>';
}

/**
 * Register the reusable sidebar block.
 */
function koala_beanstalk_register_location_sidebar(): void {
	register_block_type(
		'koala/location-sidebar-form',
		array(
			'api_version'     => 3,
			'attributes'      => array(
				'heading' => array(
					'type'    => 'string',
					'default' => 'Talk to Our Insulation Experts Today.',
				),
			),
			'render_callback' => 'koala_beanstalk_render_location_sidebar',
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_sidebar' );

/**
 * Enqueue the form early enough for Gravity Forms AJAX dependencies.
 */
function koala_beanstalk_sidebar_assets(): void {
	if ( ! koala_beanstalk_sidebar_preview() || ! function_exists( 'gravity_form_enqueue_scripts' ) ) {
		return;
	}
	gravity_form_enqueue_scripts( 12, true );
	foreach ( array( 'css', 'js' ) as $extension ) {
		$file = '/beanstalk/assets/location-sidebar.' . $extension;
		$url  = get_template_directory_uri() . $file;
		$ver  = (string) filemtime( __DIR__ . '/assets/location-sidebar.' . $extension );
		if ( 'css' === $extension ) {
			wp_enqueue_style( 'koala-location-sidebar', $url, array(), $ver );
		} else {
			wp_enqueue_script( 'koala-location-sidebar', $url, array(), $ver, true );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_sidebar_assets', 1001 );

/**
 * Replace only the supplied Bricks sidebar root in Local preview.
 *
 * @param  bool   $render  Whether Bricks should render the element.
 * @param  object $element Bricks element.
 * @return bool
 */
function koala_beanstalk_sidebar_legacy_element( bool $render, $element ): bool {
	return 'ptegrr' === ( $element->id ?? '' ) && koala_beanstalk_sidebar_preview() && function_exists( 'gravity_form' ) ? false : $render;
}
add_filter( 'bricks/element/render', 'koala_beanstalk_sidebar_legacy_element', 20, 2 );

/**
 * Output the replacement once, outside Bricks layout containers.
 */
function koala_beanstalk_sidebar_preview_output(): void {
	if ( koala_beanstalk_sidebar_preview() ) {
     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped wrapper and trusted Gravity Forms output.
		echo koala_beanstalk_render_location_sidebar();
	}
}
add_action( 'wp_footer', 'koala_beanstalk_sidebar_preview_output', 5 );
