<?php
/**
 * Fixed Koala City Page draft context.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/** Validates and applies the only non-editorial City Page draft inputs. */
final class CityPageContext {

	private const PATTERN_ID       = 'koala/city-page';
	private const POST_TYPE        = 'resources-landing-pa';
	private const LOCATION_TYPE    = 'location';
	private const TAXONOMY         = 'resources-page-type';
	private const TERM_SLUG        = 'areas-served';
	private const RELATIONSHIP_KEY = 'rl_related_location';
	private const ACF_FIELD_KEY    = 'field_670cb342e69f1';

	/**
	 * Whether the exact pattern and destination require this context.
	 *
	 * @param string $pattern_id Resolved pattern ID.
	 * @param string $post_type  Validated destination post type.
	 */
	public function is_required( string $pattern_id, string $post_type ): bool {
		return self::PATTERN_ID === $pattern_id && self::POST_TYPE === $post_type;
	}

	/**
	 * Resolve context routing without allowing unrelated patterns to consume it.
	 *
	 * @param string $pattern_id      Resolved pattern ID.
	 * @param string $post_type       Validated destination post type.
	 * @param mixed  $context         Submitted context value.
	 * @param bool   $context_present Whether the input contained a context key.
	 * @return array|null|WP_Error
	 */
	public function prepare( string $pattern_id, string $post_type, $context, bool $context_present ) {
		if ( ! $this->is_required( $pattern_id, $post_type ) ) {
			return $context_present
				? new WP_Error( 'beanstalk_unexpected_draft_context', __( 'Draft context is not supported by the requested pattern and post type.', 'beanstalk-content-engine' ) )
				: null;
		}

		if ( ! $context_present ) {
			return new WP_Error( 'beanstalk_missing_draft_context', __( 'The Koala City Page pattern requires a related location.', 'beanstalk-content-engine' ) );
		}

		return $this->validate( $context );
	}

	/**
	 * Validate and normalize the exact City Page context before post insertion.
	 *
	 * @param mixed $context Submitted context.
	 * @return array|WP_Error
	 */
	public function validate( $context ) {
		if ( ! is_array( $context )
			|| array( 'related_location_id' ) !== array_keys( $context )
			|| ! is_int( $context['related_location_id'] )
			|| $context['related_location_id'] < 1 ) {
			return new WP_Error( 'beanstalk_invalid_city_page_context', __( 'City Page context must contain only a positive integer related_location_id.', 'beanstalk-content-engine' ) );
		}

		$location_id = $context['related_location_id'];
		$location    = get_post( $location_id );
		if ( ! $location ) {
			return new WP_Error( 'beanstalk_invalid_related_location', __( 'The related location does not exist.', 'beanstalk-content-engine' ) );
		}
		if ( self::LOCATION_TYPE !== $location->post_type ) {
			return new WP_Error( 'beanstalk_invalid_related_location_type', __( 'The related location must reference a location post.', 'beanstalk-content-engine' ) );
		}
		if ( 'publish' !== $location->post_status ) {
			return new WP_Error( 'beanstalk_unavailable_related_location', __( 'The related location must be published.', 'beanstalk-content-engine' ) );
		}

		$taxonomy = get_taxonomy( self::TAXONOMY );
		if ( ! $taxonomy || ! in_array( self::POST_TYPE, $taxonomy->object_type, true ) ) {
			return new WP_Error( 'beanstalk_city_page_taxonomy_unavailable', __( 'The City Page taxonomy is unavailable for this destination.', 'beanstalk-content-engine' ) );
		}

		$term = term_exists( self::TERM_SLUG, self::TAXONOMY );
		if ( ! $term || is_wp_error( $term ) ) {
			return new WP_Error( 'beanstalk_city_page_term_unavailable', __( 'The required areas-served term is unavailable.', 'beanstalk-content-engine' ) );
		}

		if ( ! current_user_can( $taxonomy->cap->assign_terms ) ) {
			return new WP_Error( 'beanstalk_city_page_context_forbidden', __( 'The current user cannot assign the required City Page term.', 'beanstalk-content-engine' ) );
		}

		$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
		return array(
			'related_location_id'  => $location_id,
			'areas_served_term_id' => $term_id,
		);
	}

	/**
	 * Assign the fixed taxonomy and ACF relationship to a newly inserted draft.
	 *
	 * Any failure removes that new draft so callers never receive a partial City Page.
	 *
	 * @param int   $post_id Newly inserted draft ID.
	 * @param array $context Validated context.
	 * @return true|WP_Error
	 */
	public function apply( int $post_id, array $context ) {
		$term_result = wp_set_object_terms( $post_id, array( $context['areas_served_term_id'] ), self::TAXONOMY, false );
		if ( is_wp_error( $term_result ) || empty( $term_result ) ) {
			return $this->rollback( $post_id );
		}
		$assigned_terms = wp_get_object_terms( $post_id, self::TAXONOMY, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $assigned_terms ) || array( $context['areas_served_term_id'] ) !== array_map( 'intval', $assigned_terms ) ) {
			return $this->rollback( $post_id );
		}

		$relationship = array( (string) $context['related_location_id'] );
		if ( ! function_exists( 'update_field' ) ) {
			return $this->rollback( $post_id );
		}
		update_field( self::ACF_FIELD_KEY, $relationship, $post_id );

		$stored_relationship = get_post_meta( $post_id, self::RELATIONSHIP_KEY, true );
		if ( ! is_array( $stored_relationship )
			|| array( (int) $context['related_location_id'] ) !== array_map( 'intval', $stored_relationship )
			|| self::ACF_FIELD_KEY !== get_post_meta( $post_id, '_' . self::RELATIONSHIP_KEY, true ) ) {
			return $this->rollback( $post_id );
		}

		return true;
	}

	/**
	 * Remove a newly inserted partial draft and return a stable failure.
	 *
	 * @param int $post_id Newly inserted draft ID.
	 */
	private function rollback( int $post_id ): WP_Error {
		if ( ! wp_delete_post( $post_id, true ) ) {
			return new WP_Error(
				'beanstalk_draft_rollback_failed',
				__( 'City Page context failed and the partial draft could not be removed.', 'beanstalk-content-engine' ),
				array( 'post_id' => $post_id )
			);
		}

		return new WP_Error( 'beanstalk_city_page_context_write_failed', __( 'The required City Page context could not be assigned.', 'beanstalk-content-engine' ) );
	}
}
