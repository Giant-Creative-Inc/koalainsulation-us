<?php
/**
 * Editable financing banner with live location metadata.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return whether this location offers financing.
 *
 * @param int $post_id Location ID.
 * @return bool
 */
function koala_beanstalk_location_financing_enabled( int $post_id ): bool {
	return '1' === (string) get_post_meta( $post_id, 'show_financing', true );
}

/** Return this location's application URL.
 *
 * @param int $post_id Location ID.
 * @return string
 */
function koala_beanstalk_location_financing_link( int $post_id ): string {
	return (string) get_post_meta( $post_id, 'financing_link', true );
}

/** Return the native editable pattern. */
function koala_beanstalk_location_financing_content(): string {
	ob_start();
	include __DIR__ . '/patterns/location-financing.php';
	return (string) ob_get_clean();
}

/** Register the reusable financing pattern. */
function koala_beanstalk_register_location_financing(): void {
	register_block_pattern(
		'koala/location-financing',
		array(
			'title'       => 'Koala Location — Financing',
			'description' => 'Editable financing banner controlled by the location financing fields.',
			'categories'  => array( 'featured', 'call-to-action' ),
			'postTypes'   => array( 'location' ),
			'content'     => koala_beanstalk_location_financing_content(),
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_financing' );

/** Apply visibility and the live application URL; preserve editable banner text.
 *
 * @param string $html Rendered group.
 * @param array  $block Parsed group.
 * @return string
 */
function koala_beanstalk_render_location_financing( string $html, array $block ): string {
	if ( 'koala-location-financing' !== ( $block['attrs']['metadata']['name'] ?? '' ) || is_admin() ) {
		return $html;
	}
	$post_id = get_the_ID();
	if ( ! koala_beanstalk_location_financing_enabled( $post_id ) ) {
		return '';
	}
	$url  = esc_url( koala_beanstalk_location_financing_link( $post_id ) );
	$tags = new WP_HTML_Tag_Processor( $html );
	while ( $tags->next_tag( 'A' ) ) {
		if ( $tags->has_class( 'wp-block-button__link' ) ) {
			$tags->set_attribute( 'href', $url );
			$tags->set_attribute( 'target', '_blank' );
			$tags->set_attribute( 'rel', 'noopener noreferrer' );
		}
	}
	return $tags->get_updated_html();
}
add_filter( 'render_block_core/group', 'koala_beanstalk_render_location_financing', 100, 2 );

/** Append the financing pattern for incremental Local previews.
 *
 * @param string $html Rendered group.
 * @param array  $block Parsed group.
 * @return string
 */
function koala_beanstalk_location_financing_extra( string $html, array $block ): string {
	if ( koala_beanstalk_sidebar_preview() && 'koala-location-expect' === ( $block['attrs']['metadata']['name'] ?? '' ) && strpos( (string) get_post_field( 'post_content', get_the_ID() ), '"name":"koala-location-financing"' ) === false ) {
		$html .= do_blocks( koala_beanstalk_location_financing_content() );
	}
	return $html;
}
add_filter( 'render_block_core/group', 'koala_beanstalk_location_financing_extra', 1000, 2 );

/** Hide the application button when no application URL is configured.
 *
 * @param string $html Rendered buttons.
 * @param array  $block Parsed buttons.
 * @return string
 */
function koala_beanstalk_location_financing_button( string $html, array $block ): string {
	if ( ! is_admin() && 'koala-location-financing__apply' === ( $block['attrs']['className'] ?? '' ) && ! esc_url( koala_beanstalk_location_financing_link( get_the_ID() ) ) ) {
		return '';
	}
	return $html;
}
add_filter( 'render_block_core/buttons', 'koala_beanstalk_location_financing_button', 20, 2 );
