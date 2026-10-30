<?php
/** City spreadsheet adapter. @package BeanstalkContentEngine */
namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/** Accepts the Local Cities Pages spreadsheet without requiring production IDs. */
final class CityPageCsv {
	public const REQUIRED = array( 'title', 'slug', 'related_location_id', 'state_abbreviation', 'state_name' );
	public const MAX_ROWS = 2000;

	/** Parse a bounded UTF-8 CSV. No content or media writes occur. */
	public static function read( string $path ) {
		$handle = fopen( $path, 'rb' );
		if ( ! $handle ) {
			return new WP_Error( 'csv_unreadable', 'The CSV could not be opened.' );
		}
		try {
			$headers = fgetcsv( $handle, null, ',', '"', '' );
			if ( ! is_array( $headers ) ) {
				return new WP_Error( 'csv_empty', 'The CSV needs a header row.' );
			}
			$headers[0] = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $headers[0] );
			$headers = array_map( 'trim', $headers );
			if ( count( array_unique( $headers ) ) !== count( $headers ) || in_array( '', $headers, true ) || array_diff( self::REQUIRED, $headers ) ) {
				return new WP_Error( 'csv_headers', 'Use the template headers, including title, slug, related_location_id, state_abbreviation and state_name. Headers must be unique.' );
			}
			$rows = array();
			$line = 1;
			while ( false !== ( $values = fgetcsv( $handle, null, ',', '"', '' ) ) ) {
				$line++;
				if ( ! array_filter( $values, static fn( $v ) => '' !== trim( (string) $v ) ) ) {
					continue;
				}
				if ( count( $values ) !== count( $headers ) ) {
					return new WP_Error( 'csv_columns', 'A CSV row has the wrong number of columns.' );
				}
				$row = array_combine( $headers, $values );
				if ( ! array_filter( array_intersect_key( $row, array_flip( self::REQUIRED ) ), static fn( $v ) => '' !== trim( (string) $v ) ) ) {
					continue;
				}
				$row['__csv_row'] = $line;
				$rows[] = $row;
				if ( count( $rows ) > self::MAX_ROWS ) {
					return new WP_Error( 'csv_limit', 'Upload at most 2,000 rows per file.' );
				}
			}
			return $rows ?: new WP_Error( 'csv_empty', 'The CSV contains no city rows.' );
		} finally {
			fclose( $handle );
		}
	}

	/** Resolve an exact existing city and map spreadsheet values for the updater. */
	public static function normalize( array $row ) {
		foreach ( self::REQUIRED as $key ) {
			if ( '' === trim( (string) ( $row[ $key ] ?? '' ) ) ) {
				return new WP_Error( 'csv_required', 'Missing required value: ' . $key );
			}
		}
		$slug = sanitize_title( trim( $row['slug'] ) );
		$row['slug'] = $slug;
		if ( '' === $slug ) {
			return new WP_Error( 'csv_slug', 'The city slug is empty after sanitization.' );
		}
		$location = trim( $row['related_location_id'] );
		if ( ! filter_var( $location, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) ) ) {
			$location_slug = trim( $location, '/' );
			if ( '' === $location_slug || sanitize_title( $location_slug ) !== $location_slug || preg_match( '/^[0-9]/', $location_slug ) ) {
				return new WP_Error( 'csv_location', 'related_location_id must be a positive ID or an exact franchise slug.' );
			}
			$locations = get_posts( array( 'post_type' => 'location', 'name' => $location_slug, 'post_status' => 'publish', 'posts_per_page' => 2, 'suppress_filters' => true ) );
			if ( 1 !== count( $locations ) || $locations[0]->post_name !== $location_slug ) {
				return new WP_Error( 'csv_location', 'The franchise slug must match exactly one published location on this site.' );
			}
			$row['related_location_id'] = (int) $locations[0]->ID;
		}
		$matches = get_posts( array(
			'post_type' => 'resources-landing-pa', 'name' => $slug,
			'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 2, 'suppress_filters' => true,
		) );
		// Existing cities may predate the CSV's state-suffix naming convention.
		$suffix = '-' . strtolower( trim( $row['state_abbreviation'] ) );
		if ( strlen( $suffix ) === 3 && str_ends_with( $slug, $suffix ) ) {
			$base = substr( $slug, 0, -3 );
			$legacy = get_posts( array( 'post_type' => 'resources-landing-pa', 'name' => $base, 'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ), 'posts_per_page' => 2, 'suppress_filters' => true ) );
			$legacy = array_values( array_filter( $legacy, static function ( $post ) use ( $row, $base ) {
				$location = get_post_meta( $post->ID, 'rl_related_location', true );
				$ids = array_map( 'intval', is_array( $location ) ? $location : array( $location ) );
				return $post->post_name === $base && has_term( 'areas-served', 'resources-page-type', $post->ID ) && array( (int) $row['related_location_id'] ) === $ids;
			} ) );
			if ( count( $legacy ) > 1 || ( $legacy && $matches ) ) {
				return new WP_Error( 'csv_city_conflict', 'Both state-suffixed and original city URLs exist, or city matching is ambiguous. Resolve the duplicate before importing.' );
			}
			if ( $legacy ) {
				$matches = $legacy;
				$slug = $base;
				$row['slug'] = $base;
			}
		}
		if ( count( $matches ) > 1 || ( $matches && ( $matches[0]->post_name !== $slug || ! has_term( 'areas-served', 'resources-page-type', $matches[0]->ID ) ) ) ) {
			return new WP_Error( 'csv_target', 'The slug must match exactly one existing areas-served City Page.' );
		}
		if ( $matches && ! current_user_can( 'edit_post', $matches[0]->ID ) ) {
			return new WP_Error( 'csv_forbidden', 'You cannot edit this City Page.' );
		}
		$row['post_id'] = $matches ? (int) $matches[0]->ID : 0;
		$row['_import_action'] = $matches ? 'update' : 'create';
		$row['service_area_name'] = sanitize_text_field( $row['title'] );
		$row['service_type'] = 'Insulation services';
		$row['testimonials_fallback_link_label'] = $row['testimonials_fallback_link_label'] ?? 'Read more reviews';
		foreach ( array( 'commercial_services_image_1', 'commercial_services_image_2', 'why_koala_image', 'final_cta_decorative_image' ) as $key ) {
			$value = trim( (string) ( $row[ $key ] ?? '' ) );
			if ( '' === $value || '{' === substr( $value, 0, 1 ) ) {
				continue;
			}
			$id = attachment_url_to_postid( $value );
			// A Local/staging copy may store its uploads under a different host.
			if ( ! $id && preg_match( '#^https?://[^/]+/wp-content/uploads/(.+)$#', $value, $match ) ) {
				$uploads = wp_get_upload_dir();
				$id = attachment_url_to_postid( $uploads['baseurl'] . '/' . $match[1] );
			}
			if ( ! $id || ! wp_attachment_is_image( $id ) ) {
				return new WP_Error( 'csv_image', $key . ': URL must match an existing Media Library image on this site.' );
			}
			$row[ $key ] = wp_json_encode( array( 'id' => $id, 'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
		}
		return $row;
	}
}
