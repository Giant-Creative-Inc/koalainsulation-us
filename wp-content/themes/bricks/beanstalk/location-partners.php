<?php
/**
 * ACF-controlled location partners section and reusable native pattern.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render only explicitly enabled partners with usable attachment images. */
function koala_beanstalk_render_location_partners(): string {
	$id = (int) get_the_ID();
	if ( '1' !== (string) get_post_meta( $id, 'show_partners', true ) ) {
		return '';
	}
	$rows  = get_field( 'partner_logos', $id );
	$items = array();
	foreach ( is_array( $rows ) ? $rows : array() as $row ) {
		$image = $row['partner_logo'] ?? null;
		$image = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;
		if ( ! $image ) {
			continue;
		}
		$html = wp_get_attachment_image( $image, 'medium_large', false, array( 'loading' => 'lazy' ) );
		if ( ! $html ) {
			continue;
		}
		$link = esc_url( $row['partner_link'] ?? '' );
		if ( $link ) {
			$html = '<a href="' . $link . '" target="_blank" rel="noopener noreferrer">' . $html . '</a>';
		}
		$items[] = '<li class="koala-location-partners__item">' . $html . '</li>';
	}
	if ( ! $items ) {
		return '';
	}
	$title = trim( (string) get_post_meta( $id, 'partners_title', true ) );
	$title = $title && '--' !== $title ? $title : 'Your Trusted Insulation Contractor:';
	$asset = get_template_directory_uri() . '/beanstalk/assets/location-partners/';
	$prev  = count( $items ) > 1 ? '<button class="koala-location-partners__arrow" type="button" data-partners-prev aria-label="Previous partner"><img src="' . esc_url( $asset . 'chevron-left.svg' ) . '" width="24" height="24" alt=""></button>' : '';
	$next  = count( $items ) > 1 ? '<button class="koala-location-partners__arrow" type="button" data-partners-next aria-label="Next partner"><img src="' . esc_url( $asset . 'chevron-right.svg' ) . '" width="24" height="24" alt=""></button>' : '';
	return '<section class="koala-location-partners" data-location-partners aria-label="' . esc_html( $title ) . '"><h2>' . esc_html( $title ) . '</h2><div class="koala-location-partners__carousel">' . $prev . '<ul class="koala-location-partners__track" aria-label="Partner logos">' . implode( '', $items ) . '</ul>' . $next . '</div></section>';
}

/** Register the ACF-backed dynamic block and reusable section pattern. */
function koala_beanstalk_register_location_partners(): void {
	wp_register_script( 'koala-location-partners-editor', get_template_directory_uri() . '/beanstalk/assets/location-partners-editor.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-server-side-render' ), (string) filemtime( __DIR__ . '/assets/location-partners-editor.js' ), true );
	register_block_type(
		'koala/location-partners',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-location-partners-editor',
			'render_callback' => 'koala_beanstalk_render_location_partners',
			'supports'        => array( 'html' => false ),
		)
	);
	register_block_pattern(
		'koala/location-partners',
		array(
			'title'      => 'Koala Location Partners',
			'categories' => array( 'featured' ),
			'postTypes'  => array( 'location' ),
			'content'    => '<!-- wp:koala/location-partners /-->',
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_partners' );
