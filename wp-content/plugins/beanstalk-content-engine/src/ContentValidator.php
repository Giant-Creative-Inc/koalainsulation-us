<?php
/**
 * Structured content validation.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Sanitizes structured content against a manifest.
 */
final class ContentValidator {

	/**
	 * Validates content.
	 *
	 * @param mixed $content  Submitted content.
	 * @param array $manifest Validated manifest.
	 * @return array|WP_Error
	 */
	public function validate( $content, array $manifest ) {
		if ( ! is_array( $content ) || isset( $content[0] ) ) {
			return new WP_Error( 'beanstalk_invalid_content', __( 'Pattern content must be an object of named fields.', 'beanstalk-content-engine' ) );
		}

		if ( array_diff( array_keys( $content ), array_keys( $manifest['fields'] ) ) ) {
			return new WP_Error( 'beanstalk_unknown_fields', __( 'Pattern content contains unsupported fields.', 'beanstalk-content-engine' ) );
		}

		$sanitized = array();
		foreach ( $manifest['fields'] as $field_id => $field ) {
			if ( 'image' === $field['type'] ) {
				$image = $content[ $field_id ] ?? null;
				if ( ! $field['required'] && ( null === $image || '' === $image ) ) {
					$sanitized[ $field_id ] = null;
					continue;
				}
				if ( ! is_array( $image ) || array( 'id', 'alt' ) !== array_keys( $image ) || ! is_string( $image['alt'] ) ) {
					return new WP_Error( 'beanstalk_invalid_image', __( 'An image field must contain only an attachment ID and alt text.', 'beanstalk-content-engine' ) );
				}
				$image_id = is_int( $image['id'] ) ? $image['id'] : ( is_string( $image['id'] ) && ctype_digit( $image['id'] ) ? (int) $image['id'] : 0 );
				if ( $image_id <= 0 || ! wp_attachment_is_image( $image_id ) ) {
					return new WP_Error( 'beanstalk_invalid_image', __( 'An image field must reference a valid image attachment.', 'beanstalk-content-engine' ) );
				}
				$sanitized[ $field_id ] = array(
					'id'  => $image_id,
					'alt' => sanitize_text_field( $image['alt'] ),
				);
				continue;
			}

			$value = isset( $content[ $field_id ] ) && is_string( $content[ $field_id ] ) ? trim( $content[ $field_id ] ) : '';

			if ( $field['required'] && '' === $value ) {
				return new WP_Error( 'beanstalk_missing_field', __( 'Pattern content is missing a required field.', 'beanstalk-content-engine' ) );
			}

			if ( false !== stripos( $value, '<!-- wp:' ) || false !== stripos( $value, '<!-- /wp:' ) ) {
				return new WP_Error( 'beanstalk_invalid_content', __( 'Block markup is not allowed inside a pattern content field.', 'beanstalk-content-engine' ) );
			}

			$sanitized_value = 'text' === $field['type'] ? sanitize_text_field( $value ) : wp_kses(
				$value,
				array(
					'a'      => array(
						'href'   => true,
						'rel'    => true,
						'target' => true,
						'title'  => true,
					),
					'br'     => array(),
					'code'   => array(),
					'em'     => array(),
					's'      => array(),
					'strong' => array(),
				)
			);

			if ( $field['required'] && '' === trim( wp_strip_all_tags( $sanitized_value ) ) ) {
				return new WP_Error( 'beanstalk_empty_field', __( 'A required pattern content field is empty after sanitization.', 'beanstalk-content-engine' ) );
			}

			$sanitized[ $field_id ] = $sanitized_value;
		}

		return $sanitized;
	}
}
