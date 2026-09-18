<?php
/**
 * Gutenberg content builder.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Populates stable metadata.name targets in a resolved core-block Pattern.
 */
final class ContentBuilder {

	/**
	 * Creates the builder.
	 *
	 * @param PatternRegistry $patterns Pattern registry.
	 */
	public function __construct( private PatternRegistry $patterns ) {}

	/**
	 * Builds serialized block content.
	 *
	 * @param array $manifest Validated resolved manifest.
	 * @param array $content  Sanitized content.
	 * @return string|WP_Error
	 */
	public function build( array $manifest, array $content ) {
		$markup = $this->patterns->read_pattern( $manifest['_pattern_path'] );
		if ( is_wp_error( $markup ) ) {
			return $markup;
		}

		$counts = array_fill_keys( array_keys( $manifest['fields'] ), 0 );
		$blocks = $this->populate_blocks( parse_blocks( $markup ), $manifest['fields'], $content, $counts );
		if ( is_wp_error( $blocks ) ) {
			return $blocks;
		}

		foreach ( $counts as $count ) {
			if ( 1 !== $count ) {
				return new WP_Error( 'beanstalk_pattern_target_mismatch', __( 'The pattern template does not contain exactly one target for every manifest field.', 'beanstalk-content-engine' ) );
			}
		}

		return serialize_blocks( $blocks );
	}

	/**
	 * Recursively populates blocks.
	 *
	 * @param array $blocks  Parsed blocks.
	 * @param array $fields  Manifest fields.
	 * @param array $content Sanitized content.
	 * @param array $counts  Target counts.
	 * @return array|WP_Error
	 */
	private function populate_blocks( array $blocks, array $fields, array $content, array &$counts ) {
		foreach ( $blocks as &$block ) {
			$name = $block['attrs']['metadata']['name'] ?? '';
			foreach ( $fields as $field_id => $field ) {
				$target = $field['target'];
				if ( $name !== $target['name'] ) {
					continue;
				}
				if ( $block['blockName'] !== $target['block'] ) {
					return new WP_Error( 'beanstalk_pattern_target_mismatch', __( 'A pattern field target uses an unexpected block type.', 'beanstalk-content-engine' ) );
				}
				++$counts[ $field_id ];
				if ( 'image' === $field['type'] && null === $content[ $field_id ] ) {
					continue;
				}
				$block = $this->populate_block( $block, $target, $content[ $field_id ] );
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$inner_blocks = $this->populate_blocks( $block['innerBlocks'], $fields, $content, $counts );
				if ( is_wp_error( $inner_blocks ) ) {
					return $inner_blocks;
				}
				$block['innerBlocks'] = $inner_blocks;
			}
		}
		unset( $block );

		return $blocks;
	}

	/**
	 * Populates one supported block.
	 *
	 * @param array $block  Parsed block.
	 * @param array $target Field target.
	 * @param mixed $value  Sanitized value.
	 * @return array
	 */
	private function populate_block( array $block, array $target, $value ): array {
		if ( 'core/details' === $target['block'] && 'summary' === $target['attribute'] ) {
			$summary            = '<summary>' . esc_html( $value ) . '</summary>';
			$replace            = static fn( string $html ): string => (string) preg_replace( '/<summary>.*?<\/summary>/s', $summary, $html, 1 );
			$block['innerHTML'] = $replace( $block['innerHTML'] );
			foreach ( $block['innerContent'] as &$fragment ) {
				if ( is_string( $fragment ) && false !== strpos( $fragment, '<summary>' ) ) {
					$fragment = $replace( $fragment );
					break;
				}
			}
			unset( $fragment );
			return $block;
		}

		if ( 'core/image' === $target['block'] && is_array( $value ) ) {
			$image_id                   = (int) $value['id'];
			$alt                        = (string) $value['alt'];
			$image                      = wp_get_attachment_image(
				$image_id,
				'large',
				false,
				array(
					'alt'     => $alt,
					'loading' => 'lazy',
				)
			);
			$block['attrs']['id']       = $image_id;
			$block['attrs']['alt']      = $alt;
			$block['attrs']['sizeSlug'] = 'large';
			$html                       = '<figure class="wp-block-image size-large">' . $image . '</figure>';
		} elseif ( 'core/heading' === $target['block'] ) {
			$level = isset( $block['attrs']['level'] ) ? (int) $block['attrs']['level'] : 2;
			$html  = sprintf( '<h%1$d class="wp-block-heading">%2$s</h%1$d>', $level, esc_html( $value ) );
		} else {
			$html = '<p>' . $value . '</p>';
		}

		$block['innerHTML']    = $html;
		$block['innerContent'] = array( $html );
		return $block;
	}
}
