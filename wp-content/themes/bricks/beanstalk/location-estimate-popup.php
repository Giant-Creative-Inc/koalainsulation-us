<?php
/**
 * Native location estimate modal preserving the original Gravity Form.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render the modal once with this location's phone and attribution. */
function koala_beanstalk_render_location_estimate_popup(): string {
	if ( ! function_exists( 'gravity_form' ) ) {
		return '';
	}
	$id    = get_the_ID();
	$phone = (string) get_post_meta( $id, 'location_phone_number', true );
	$form  = koala_beanstalk_estimate_is_submission() ? gravity_form( 13, false, false, false, null, true, 0, false ) : '<p role="status">Loading quote form…</p>';
	return '<dialog id="koala-location-estimate" class="koala-location-estimate" aria-labelledby="koala-location-estimate-title" data-form-endpoint="' . esc_url( rest_url( 'koala-beanstalk/v1/estimate/' . $id ) ) . '" data-location-id="' . esc_attr( (string) $id ) . '" data-location-slug="' . esc_attr( get_post_field( 'post_name', $id ) ) . '" data-page-url="' . esc_url( get_permalink( $id ) ) . '">'
	. '<div class="koala-location-estimate__header"><img data-popup-image-src="' . esc_url( get_template_directory_uri() . '/beanstalk/assets/location-estimate-popup/koala.webp' ) . '" alt="" width="100" height="100"></div>'
	. '<h2 id="koala-location-estimate-title" class="screen-reader-text">Get a FREE Quote</h2>'
	. '<button type="button" class="koala-location-estimate__close" aria-label="Close quote form" data-estimate-close>&times;</button>'
	. ( $phone ? '<a class="koala-location-estimate__phone" href="tel:' . esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ) . '"><span aria-hidden="true">&#9742;</span> ' . esc_html( $phone ) . '</a>' : '' )
	. '<div class="koala-location-estimate__form">' . $form . '</div></dialog>';
}

/** Load Gravity Forms dependencies before the document head. */
function koala_beanstalk_location_estimate_assets(): void {
	if ( ! koala_beanstalk_sidebar_preview() ) {
		return;
	}
	if ( koala_beanstalk_estimate_is_submission() && function_exists( 'gravity_form_enqueue_scripts' ) ) {
		gravity_form_enqueue_scripts( 13, true );
		wp_enqueue_style( 'koala-location-estimate', get_template_directory_uri() . '/beanstalk/assets/location-estimate-popup.css', array(), (string) filemtime( __DIR__ . '/assets/location-estimate-popup.css' ) );
	}
	foreach ( array( 'js' ) as $extension ) {
		$file    = '/beanstalk/assets/location-estimate-popup.' . $extension;
		$url     = get_template_directory_uri() . $file;
		$version = (string) filemtime( __DIR__ . '/assets/location-estimate-popup.' . $extension );
		if ( 'css' === $extension ) {
			wp_enqueue_style( 'koala-location-estimate', $url, array(), $version );
		} else {
			wp_enqueue_script( 'koala-location-estimate', $url, array(), $version, true );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_location_estimate_assets', 1002 );

/** Output the accessible modal outside the location layout. */
function koala_beanstalk_location_estimate_output(): void {
	if ( koala_beanstalk_sidebar_preview() ) {
		echo koala_beanstalk_render_location_estimate_popup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped wrapper and trusted Gravity Forms markup.
	}
}
add_action( 'wp_footer', 'koala_beanstalk_location_estimate_output', 6 );

/** Preserve Gravity Forms AJAX validation/confirmation responses on their original page. */
function koala_beanstalk_estimate_is_submission(): bool {
 // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Gravity Forms validates its own submission.
	return 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && '13' === (string) ( $_POST['gform_submit'] ?? '' );
}
