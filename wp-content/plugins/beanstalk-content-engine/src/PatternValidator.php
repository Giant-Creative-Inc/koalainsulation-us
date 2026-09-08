<?php
/**
 * Pattern manifest validation.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Validates the content engine's manifest contract.
 */
final class PatternValidator {

	/**
	 * Validates a manifest.
	 *
	 * @param array $manifest Manifest data.
	 * @return true|WP_Error
	 */
	public function validate( array $manifest ) {
		$required_keys = array( 'id', 'label', 'description', 'version', 'postTypes', 'template', 'fields' );
		$allowed_keys  = array_merge( array( '$schema' ), $required_keys );

		if ( array_diff( array_keys( $manifest ), $allowed_keys ) || array_diff( $required_keys, array_keys( $manifest ) ) ) {
			return $this->error( __( 'A pattern manifest has missing or unsupported properties.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['id'] ) || ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $manifest['id'] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid ID.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['label'] ) || '' === trim( $manifest['label'] ) || ! is_string( $manifest['description'] ) || '' === trim( $manifest['description'] ) ) {
			return $this->error( __( 'A pattern manifest must have a label and description.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['version'] ) || ! preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/', $manifest['version'] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid version.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_array( $manifest['postTypes'] ) || empty( $manifest['postTypes'] ) || count( $manifest['postTypes'] ) > 50 ) {
			return $this->error( __( 'A pattern manifest contains an unsupported post type.', 'beanstalk-content-engine' ) );
		}

		$post_types = array_filter(
			$manifest['postTypes'],
			static fn( $post_type ) => is_string( $post_type ) && (bool) preg_match( '/^[a-z0-9_-]{1,20}$/', $post_type )
		);
		if ( count( $post_types ) !== count( $manifest['postTypes'] ) || count( array_unique( $post_types ) ) !== count( $post_types ) ) {
			return $this->error( __( 'A pattern manifest contains an unsupported or duplicate post type.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['template'] ) || ! preg_match( '/^[a-z0-9-]+\.php$/', $manifest['template'] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid template.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_array( $manifest['fields'] ) || empty( $manifest['fields'] ) ) {
			return $this->error( __( 'A pattern manifest must define content fields.', 'beanstalk-content-engine' ) );
		}

		$target_names = array();
		foreach ( $manifest['fields'] as $field_id => $field ) {
			$result = $this->validate_field( $field_id, $field, $target_names );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return true;
	}

	/**
	 * Validates one field.
	 *
	 * @param mixed $field_id    Field ID.
	 * @param mixed $field       Field data.
	 * @param array $target_names Seen target names.
	 * @return true|WP_Error
	 */
	private function validate_field( $field_id, $field, array &$target_names ) {
		$allowed_keys = array( 'label', 'type', 'required', 'target' );

		if ( ! is_string( $field_id ) || ! preg_match( '/^[a-z][a-z0-9_]*$/', $field_id ) || ! is_array( $field ) ) {
			return $this->error( __( 'A pattern manifest contains an invalid field.', 'beanstalk-content-engine' ) );
		}

		if ( array_diff( array_keys( $field ), $allowed_keys ) || array_diff( $allowed_keys, array_keys( $field ) ) ) {
			return $this->error( __( 'A pattern manifest field has invalid properties.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $field['label'] ) || '' === trim( $field['label'] ) || ! in_array( $field['type'], array( 'text', 'rich-text', 'image' ), true ) || ! is_bool( $field['required'] ) || ! is_array( $field['target'] ) ) {
			return $this->error( __( 'A pattern manifest field has an invalid definition.', 'beanstalk-content-engine' ) );
		}

		$target       = $field['target'];
		$valid_target = 'image' === $field['type']
			? 'core/image' === ( $target['block'] ?? '' ) && 'image' === ( $target['attribute'] ?? '' )
			: ( in_array( $target['block'] ?? '', array( 'core/heading', 'core/paragraph' ), true ) && 'content' === ( $target['attribute'] ?? '' ) )
				|| ( 'text' === $field['type'] && 'core/details' === ( $target['block'] ?? '' ) && 'summary' === ( $target['attribute'] ?? '' ) );
		if ( array( 'block', 'name', 'attribute' ) !== array_keys( $target )
			|| ! $valid_target
			|| ! preg_match( '/^[a-z0-9-]+-field-[a-z0-9-]+$/', $target['name'] )
			|| isset( $target_names[ $target['name'] ] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid or duplicate field target.', 'beanstalk-content-engine' ) );
		}

		$target_names[ $target['name'] ] = true;
		return true;
	}

	/**
	 * Creates a consistent validation error.
	 *
	 * @param string $message Error message.
	 * @return WP_Error
	 */
	private function error( string $message ): WP_Error {
		return new WP_Error( 'beanstalk_invalid_manifest', $message );
	}
}
