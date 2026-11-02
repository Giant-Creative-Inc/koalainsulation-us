<?php
/**
 * Resolve migrated location images through WordPress and its CDN integration.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Replace only known bundled image URLs; retain fallbacks for missing imports.
 *
 * @param string $html Rendered markup.
 * @return string
 */
function koala_beanstalk_location_media_urls( string $html ): string {
	$map = get_option( 'koala_beanstalk_location_media', array() );
	if ( ! is_array( $map ) || ! $map ) {
		return $html;
	}
	return preg_replace_callback(
		'~(?:https?://[^\s"\'<>]+)?/wp-content/themes/[^/\s"\'<>]+/beanstalk/assets/(location-[a-z-]+/[^\s"\'<>(),]+\.(?:webp|png))~i',
		static function ( $image_match ) use ( $map ): string {
			$id  = (int) ( $map[ $image_match[1] ] ?? 0 );
			$url = $id ? wp_get_attachment_url( $id ) : false;
			return $url ? esc_url( $url ) : $image_match[0];
		},
		$html
	);
}
/**
 * Resolve image URLs after native location blocks are rendered.
 *
 * @param string $html Rendered block.
 * @return string
 */
function koala_beanstalk_location_media_block( string $html ): string {
	return koala_beanstalk_sidebar_preview() ? koala_beanstalk_location_media_urls( $html ) : $html;
}
add_filter( 'render_block', 'koala_beanstalk_location_media_block', 2000 );
