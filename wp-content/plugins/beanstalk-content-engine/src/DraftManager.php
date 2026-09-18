<?php
/**
 * Draft creation service.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Creates allowlisted unpublished content and controlled metadata.
 */
final class DraftManager {

	/**
	 * Finds an existing unpublished draft by its idempotency identity.
	 *
	 * @param array $input Validated lookup input.
	 * @return array
	 */
	public function find( array $input ): array {
		$external_id = sanitize_text_field( $input['external_id'] );
		$post_type   = sanitize_key( $input['post_type'] );
		$matches     = get_posts(
			array(
				'fields'         => 'ids',
				'meta_key'       => '_beanstalk_ai_external_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Narrow idempotency lookup.
				'meta_value'     => $external_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Narrow idempotency lookup.
				'no_found_rows'  => true,
				'post_status'    => 'draft',
				'post_type'      => $post_type,
				'posts_per_page' => 1,
			)
		);

		if ( ! $matches ) {
			return array( 'found' => false );
		}

		$post_id = (int) $matches[0];
		$post    = get_post( $post_id );
		return array(
			'found'       => true,
			'post_id'     => $post_id,
			'status'      => 'draft',
			'slug'        => $post->post_name,
			'edit_url'    => get_edit_post_link( $post_id, 'raw' ),
			'preview_url' => get_preview_post_link( $post_id ),
		);
	}

	/**
	 * Creates the draft service.
	 *
	 * @param PatternRegistry  $patterns          Pattern registry.
	 * @param ContentValidator $content_validator Content validator.
	 * @param ContentBuilder   $content_builder   Content builder.
	 * @param CityPageContext  $city_context      Fixed Koala City Page context.
	 */
	public function __construct(
		private PatternRegistry $patterns,
		private ContentValidator $content_validator,
		private ContentBuilder $content_builder,
		private CityPageContext $city_context
	) {}

	/**
	 * Creates a draft from ability input.
	 *
	 * @param array $input Validated ability input.
	 * @return array|WP_Error
	 */
	public function create( array $input ) {
		$external_id = sanitize_text_field( $input['external_id'] );
		$post_type   = sanitize_key( $input['post_type'] );
		$title       = sanitize_text_field( $input['title'] );
		$slug        = empty( $input['slug'] ) ? sanitize_title( $title ) : sanitize_title( $input['slug'] );
		$manifest    = $this->patterns->manifest( sanitize_text_field( $input['pattern_id'] ) );

		if ( is_wp_error( $manifest ) ) {
			return $manifest;
		}
		if ( ! in_array( $post_type, $manifest['postTypes'], true ) ) {
			return new WP_Error( 'beanstalk_unsupported_post_type', __( 'The requested post type is not supported by this pattern.', 'beanstalk-content-engine' ) );
		}

		$allowed = apply_filters( 'beanstalk_content_engine_allowed_post_types', array( 'page', 'post', 'resources-landing-pa' ) );
		$allowed = is_array( $allowed ) ? array_filter( $allowed, 'is_string' ) : array();
		$allowed = array_unique( array_filter( array_map( 'sanitize_key', $allowed ) ) );
		$object  = get_post_type_object( $post_type );
		if ( ! in_array( $post_type, $allowed, true ) || ! $object || ! $object->public || ! $object->show_ui || ! post_type_supports( $post_type, 'editor' ) || ! current_user_can( $object->cap->create_posts ) ) {
			return new WP_Error( 'beanstalk_unavailable_destination', __( 'The requested post type is not an available Beanstalk destination.', 'beanstalk-content-engine' ) );
		}
		if ( '' === $external_id || '' === $title || '' === $slug ) {
			return new WP_Error( 'beanstalk_invalid_draft_identity', __( 'The external ID, title, and resulting slug must not be empty.', 'beanstalk-content-engine' ) );
		}

		$context = $this->city_context->prepare(
			$manifest['id'],
			$post_type,
			$input['context'] ?? null,
			array_key_exists( 'context', $input )
		);
		if ( is_wp_error( $context ) ) {
			return $context;
		}

		$existing = get_posts(
			array(
				'fields'         => 'ids',
				'meta_key'       => '_beanstalk_ai_external_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Narrow idempotency lookup.
				'meta_value'     => $external_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Narrow idempotency lookup.
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'post_type'      => $post_type,
				'posts_per_page' => 1,
			)
		);

		if ( $existing ) {
			return new WP_Error( 'beanstalk_duplicate_external_id', __( 'A post already uses this external ID.', 'beanstalk-content-engine' ) );
		}

		$content = $this->content_validator->validate( $input['content'], $manifest );
		if ( is_wp_error( $content ) ) {
			return $content;
		}

		$post_content = $this->content_builder->build( $manifest, $content );
		if ( is_wp_error( $post_content ) ) {
			return $post_content;
		}

		$post_id = wp_insert_post(
			array(
				'meta_input'   => array(
					'_beanstalk_ai_external_id'      => $external_id,
					'_beanstalk_ai_manifest_version' => $manifest['version'],
				),
				'post_content' => $post_content,
				'post_name'    => $slug,
				'post_status'  => 'draft',
				'post_title'   => $title,
				'post_type'    => $post_type,
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( is_array( $context ) ) {
			$context_result = $this->city_context->apply( $post_id, $context );
			if ( is_wp_error( $context_result ) ) {
				return $context_result;
			}
		}

		$post = get_post( $post_id );
		return array(
			'post_id'     => $post_id,
			'status'      => $post->post_status,
			'slug'        => $post->post_name,
			'edit_url'    => get_edit_post_link( $post_id, 'raw' ),
			'preview_url' => get_preview_post_link( $post_id ),
		);
	}
}
