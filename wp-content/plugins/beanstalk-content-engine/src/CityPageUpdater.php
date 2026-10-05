<?php
/**
 * Existing Koala City Page migration service.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/** Validates and atomically migrates an existing City Page to Beanstalk. */
final class CityPageUpdater {
	public const TEMPLATE_META  = '_beanstalk_template';
	public const TEMPLATE_VALUE = 'koala/city-page';
	private const POST_TYPE     = 'resources-landing-pa';

	/** Protected metadata written by this migration. */
	private const META_KEYS = array(
		'_beanstalk_ai_manifest_version',
		'_beanstalk_structured_data_profile',
		'_beanstalk_structured_data_contract_version',
		'_beanstalk_structured_data_values',
		'_beanstalk_structured_data_contract_sha256',
		self::TEMPLATE_META,
		'rl_related_location',
		'_rl_related_location',
	);

	/**
	 * Create the existing-page migration service.
	 *
	 * @param PatternRegistry  $patterns          Pattern registry.
	 * @param ContentValidator $content_validator Content validator.
	 * @param ContentBuilder   $content_builder   Gutenberg content builder.
	 * @param CityPageContext  $city_context      City Page context service.
	 */
	public function __construct(
		private PatternRegistry $patterns,
		private ContentValidator $content_validator,
		private ContentBuilder $content_builder,
		private CityPageContext $city_context
	) {}

	/**
	 * Validate or apply one normalized CSV row.
	 *
	 * @param array $row       Associative CSV row.
	 * @param bool  $apply     Whether to write the migration.
	 * @return array|WP_Error
	 */
	public function update( array $row, bool $apply = false ) {
		$post_id = filter_var( $row['post_id'] ?? null, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( ! $post_id ) {
			return new WP_Error( 'beanstalk_invalid_city_page_id', __( 'Each row requires a positive integer post_id.', 'beanstalk-content-engine' ) );
		}

		$post = get_post( (int) $post_id );
		if ( ! $post || self::POST_TYPE !== $post->post_type || in_array( $post->post_status, array( 'trash', 'auto-draft' ), true ) ) {
			return new WP_Error( 'beanstalk_invalid_city_page_target', __( 'The target must be an existing City Page.', 'beanstalk-content-engine' ) );
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return new WP_Error( 'beanstalk_city_page_update_forbidden', __( 'The current user cannot edit the target City Page.', 'beanstalk-content-engine' ) );
		}
		$expected_slug = sanitize_title( (string) ( $row['slug'] ?? '' ) );
		if ( '' === $expected_slug || $expected_slug !== $post->post_name ) {
			return new WP_Error( 'beanstalk_city_page_slug_mismatch', __( 'The CSV slug does not match the existing page.', 'beanstalk-content-engine' ) );
		}

		$manifest = $this->patterns->manifest( self::TEMPLATE_VALUE );
		if ( is_wp_error( $manifest ) ) {
			return $manifest;
		}
		$content = array();
		foreach ( $manifest['fields'] as $field_id => $definition ) {
			if ( ! array_key_exists( $field_id, $row ) ) {
				if ( ! empty( $definition['required'] ) ) {
					/* translators: %s is a manifest field ID. */
					return new WP_Error( 'beanstalk_missing_import_column', sprintf( __( 'The import is missing required column %s.', 'beanstalk-content-engine' ), $field_id ) );
				}
				$content[ $field_id ] = null;
				continue;
			}
			$content[ $field_id ] = $this->normalize_field( $row[ $field_id ], $definition );
			if ( is_wp_error( $content[ $field_id ] ) ) {
				return $content[ $field_id ];
			}
		}
		$content = $this->content_validator->validate( $content, $manifest );
		if ( is_wp_error( $content ) ) {
			return $content;
		}
		$post_content = $this->content_builder->build( $manifest, $content );
		if ( is_wp_error( $post_content ) ) {
			return $post_content;
		}

		$context = $this->city_context->prepare(
			self::TEMPLATE_VALUE,
			self::POST_TYPE,
			array( 'related_location_id' => (int) ( $row['related_location_id'] ?? 0 ) ),
			true
		);
		if ( is_wp_error( $context ) ) {
			return $context;
		}
		$structured = $this->structured_values( $row, $manifest );
		if ( is_wp_error( $structured ) ) {
			return $structured;
		}

		$result = array(
			'post_id'          => $post->ID,
			'slug'             => $post->post_name,
			'status'           => $post->post_status,
			'manifest_version' => $manifest['version'],
			'action'           => $apply ? 'updated' : 'validated',
		);
		if ( ! $apply ) {
			return $result;
		}

		$snapshot = $this->snapshot( $post );
		$revision = function_exists( 'wp_save_post_revision' ) ? wp_save_post_revision( $post->ID ) : 0;
		$updated  = wp_update_post(
			array(
				'ID'           => $post->ID,
				'post_content' => $post_content,
			),
			true
		);
		if ( is_wp_error( $updated ) ) {
			return $updated;
		}

		$context_result = $this->city_context->apply_existing( $post->ID, $context );
		if ( is_wp_error( $context_result ) ) {
			$this->restore( $post->ID, $snapshot );
			return $context_result;
		}

		$metadata = array(
			'_beanstalk_ai_manifest_version'              => $manifest['version'],
			'_beanstalk_structured_data_profile'          => $manifest['structuredData']['profile'],
			'_beanstalk_structured_data_contract_version' => 1,
			'_beanstalk_structured_data_values'           => wp_json_encode( $structured ),
			'_beanstalk_structured_data_contract_sha256'  => hash( 'sha256', wp_json_encode( $manifest['structuredData'] ) ),
		);
		foreach ( $metadata as $key => $value ) {
			if ( ! $this->write_meta( $post->ID, $key, $value ) ) {
				$this->restore( $post->ID, $snapshot );
				return new WP_Error( 'beanstalk_city_page_metadata_write_failed', sprintf( __( 'Protected City Page metadata could not be written: %s.', 'beanstalk-content-engine' ), $key ) );
			}
		}

		// Activation is deliberately last so partial migrations retain legacy rendering.
		if ( ! $this->write_meta( $post->ID, self::TEMPLATE_META, self::TEMPLATE_VALUE ) ) {
			$this->restore( $post->ID, $snapshot );
			return new WP_Error( 'beanstalk_city_page_activation_failed', __( 'The Beanstalk template could not be activated.', 'beanstalk-content-engine' ) );
		}

		$result['revision_id'] = is_int( $revision ) ? $revision : 0;
		return $result;
	}

	/**
	 * Normalize a CSV cell for the manifest field type.
	 *
	 * @param mixed $value      Raw CSV cell.
	 * @param array $definition Manifest field definition.
	 */
	private function normalize_field( $value, array $definition ) {
		if ( 'image' !== $definition['type'] ) {
			return is_string( $value ) ? $value : '';
		}
		if ( null === $value || '' === trim( (string) $value ) ) {
			return null;
		}
		$decoded = json_decode( (string) $value, true );
		if ( ! is_array( $decoded ) ) {
			return new WP_Error( 'beanstalk_invalid_import_image', __( 'Image cells must contain JSON with id and alt values.', 'beanstalk-content-engine' ) );
		}
		return $decoded;
	}

	/**
	 * Validate importer-owned structured-data values against the manifest contract.
	 *
	 * @param array $row      Associative CSV row.
	 * @param array $manifest Resolved pattern manifest.
	 */
	private function structured_values( array $row, array $manifest ) {
		$values = array();
		foreach ( $manifest['structuredData']['fields'] as $id => $field ) {
			if ( 'content-center' !== $field['source'] ) {
				continue;
			}
			$value = trim( (string) ( $row[ $id ] ?? '' ) );
			if ( '' === $value || strlen( $value ) > 500 ) {
				/* translators: %s is a structured-data field ID. */
				return new WP_Error( 'beanstalk_invalid_structured_data_value', sprintf( __( 'Structured-data column %s is missing or invalid.', 'beanstalk-content-engine' ), $id ) );
			}
			$values[ $id ] = sanitize_text_field( $value );
		}
		return $values;
	}

	/**
	 * Capture every value that an existing-page migration can replace.
	 *
	 * @param object $post Existing WordPress post.
	 */
	private function snapshot( $post ): array {
		$metadata = array();
		foreach ( self::META_KEYS as $key ) {
			$metadata[ $key ] = array(
				'exists' => metadata_exists( 'post', $post->ID, $key ),
				'value'  => get_post_meta( $post->ID, $key, true ),
			);
		}
		return array(
			'post_content' => $post->post_content,
			'terms'        => wp_get_object_terms( $post->ID, 'resources-page-type', array( 'fields' => 'ids' ) ),
			'metadata'     => $metadata,
		);
	}

	/**
	 * Restore the pre-migration page when any write or verification fails.
	 *
	 * @param int   $post_id  WordPress post ID.
	 * @param array $snapshot Pre-migration values.
	 */
	private function restore( int $post_id, array $snapshot ): void {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $snapshot['post_content'],
			)
		);
		if ( is_array( $snapshot['terms'] ) ) {
			wp_set_object_terms( $post_id, array_map( 'intval', $snapshot['terms'] ), 'resources-page-type', false );
		}
		foreach ( $snapshot['metadata'] as $key => $stored ) {
			if ( $stored['exists'] ) {
				update_post_meta( $post_id, $key, wp_slash( $stored['value'] ) );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	/**
	 * Write metadata and distinguish an unchanged value from a failed write.
	 *
	 * @param int    $post_id WordPress post ID.
	 * @param string $key     Metadata key.
	 * @param mixed  $value   Metadata value.
	 */
	private function write_meta( int $post_id, string $key, $value ): bool {
		// WordPress unslashes metadata before storing it, including JSON escapes.
		update_post_meta( $post_id, $key, wp_slash( $value ) );
		return (string) get_post_meta( $post_id, $key, true ) === (string) $value;
	}
}
