<?php
/**
 * Editable benefits, energy efficiency and five-step location patterns.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return one portable feature pattern with theme-owned WebP assets.
 *
 * @param string $name Pattern suffix.
 * @return string
 */
function koala_beanstalk_location_feature_content( string $name ): string {
	if ( ! in_array( $name, array( 'benefits', 'energy', 'expect' ), true ) ) {
		return '';
	}
	ob_start();
	include __DIR__ . '/patterns/location-' . $name . '.php';
	return (string) ob_get_clean();
}

/** Register the three reusable patterns in the Gutenberg inserter. */
function koala_beanstalk_register_location_features(): void {
	foreach ( array(
		'benefits' => 'Comfort and Benefits',
		'energy'   => 'Energy Efficiency',
		'expect'   => 'What to Expect',
	) as $name => $title ) {
		register_block_pattern(
			'koala/location-' . $name,
			array(
				'title'      => 'Koala Location — ' . $title,
				'categories' => array( 'featured' ),
				'postTypes'  => array( 'location' ),
				'content'    => koala_beanstalk_location_feature_content( $name ),
			)
		);
	}
}
add_action( 'init', 'koala_beanstalk_register_location_features' );

/**
 * Add default feature sections after the quote banner during incremental migration.
 *
 * @param string $html Rendered group markup.
 * @param array  $block Parsed group.
 * @return string
 */
function koala_beanstalk_location_feature_extras( string $html, array $block ): string {
	if ( ! koala_beanstalk_sidebar_preview() || 'koala-location-quote-banner' !== ( $block['attrs']['metadata']['name'] ?? '' ) ) {
		return $html;
	}
	$content = (string) get_post_field( 'post_content', get_the_ID() );
	foreach ( array( 'benefits', 'energy', 'expect' ) as $name ) {
		if ( strpos( $content, '"name":"koala-location-' . $name . '"' ) === false ) {
			$html .= do_blocks( koala_beanstalk_location_feature_content( $name ) );
		}
	}
	return $html;
}
add_filter( 'render_block_core/group', 'koala_beanstalk_location_feature_extras', 1000, 2 );

/**
 * Lazy-load pattern images without adding non-core attributes to saved image blocks.
 *
 * @param string $html Image markup.
 * @return string
 */
function koala_beanstalk_location_feature_image_loading( string $html ): string {
	if ( strpos( $html, '/beanstalk/assets/location-features/' ) === false ) {
		return $html;
	}
	$tags = new WP_HTML_Tag_Processor( $html );
	if ( $tags->next_tag( 'IMG' ) ) {
		$tags->set_attribute( 'loading', 'lazy' );
		$tags->set_attribute( 'decoding', 'async' );
	}
	return $tags->get_updated_html();
}
add_filter( 'render_block_core/image', 'koala_beanstalk_location_feature_image_loading', 20 );
