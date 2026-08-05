<?php
/**
 * Location resolution utilities.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Transient key for the normalized ZIP/postal-code-to-location index.
 */
const KGI_LOCATION_ZIP_INDEX_TRANSIENT = 'kgi_location_zip_index';

/**
 * Normalizes a ZIP or postal code for exact ownership comparisons.
 *
 * The submitted value is not changed. This comparison-only representation
 * makes differences in case, spaces, and hyphens insignificant.
 *
 * @since 0.5.0
 *
 * @param mixed $value Raw ZIP or postal code.
 * @return string Uppercase letters and digits only.
 */
function kgi_normalize_zip_code( $value ): string {
	$value = strtoupper( trim( (string) $value ) );

	return (string) preg_replace( '/[^A-Z0-9]/', '', $value );
}

/**
 * Returns all normalized ZIP/postal codes owned by a location.
 *
 * `additional_zipcodes` is stored by ACF as a comma-separated text value.
 * Empty values and duplicates are removed.
 *
 * @since 0.5.0
 *
 * @param int $location_id Location CPT post ID.
 * @return string[] Normalized ZIP/postal codes.
 */
function kgi_get_location_zip_codes( int $location_id ): array {
	$values     = array( get_field( 'location_zipcode', $location_id ) );
	$additional = get_field( 'additional_zipcodes', $location_id );

	if ( is_array( $additional ) ) {
		$values = array_merge( $values, $additional );
	} else {
		$values = array_merge( $values, explode( ',', (string) $additional ) );
	}

	$normalized = array_map( 'kgi_normalize_zip_code', $values );

	return array_values( array_unique( array_filter( $normalized ) ) );
}

/**
 * Builds the exact ZIP/postal-code-to-location lookup index.
 *
 * Location IDs are ordered ascending so duplicate ownership always resolves
 * deterministically. All owners are retained so the caller can prefer the
 * form's original location and log ambiguous ownership.
 *
 * @since 0.5.0
 *
 * @return array<string, int[]> Normalized ZIP/postal code => location IDs.
 */
function kgi_build_location_zip_index(): array {
	$location_ids = get_posts(
		array(
			'post_type'              => kgi_get_location_post_type(),
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		)
	);
	$index        = array();

	foreach ( $location_ids as $location_id ) {
		foreach ( kgi_get_location_zip_codes( (int) $location_id ) as $zip_code ) {
			$index[ $zip_code ][] = (int) $location_id;
		}
	}

	set_transient( KGI_LOCATION_ZIP_INDEX_TRANSIENT, $index, DAY_IN_SECONDS );

	return $index;
}

/**
 * Returns the cached ZIP/postal-code ownership index.
 *
 * @since 0.5.0
 *
 * @return array<string, int[]> Normalized ZIP/postal code => location IDs.
 */
function kgi_get_location_zip_index(): array {
	$index = get_transient( KGI_LOCATION_ZIP_INDEX_TRANSIENT );

	return is_array( $index ) ? $index : kgi_build_location_zip_index();
}

/**
 * Pre-warms the ownership index once after this plugin version is deployed.
 *
 * Runs on `init`, after the Location CPT has been registered, so the first
 * form display warms the lookup before a visitor can submit the form.
 *
 * @since 0.5.0
 */
function kgi_maybe_warm_location_zip_index(): void {
	if ( KGI_VERSION === get_option( 'kgi_zip_index_version', '' ) ) {
		return;
	}

	delete_transient( KGI_LOCATION_ZIP_INDEX_TRANSIENT );
	kgi_build_location_zip_index();
	update_option( 'kgi_zip_index_version', KGI_VERSION, false );
}

/**
 * Clears the ownership index after ACF saves a Location CPT.
 *
 * Hooked after ACF has saved its submitted field values. The next background
 * delivery rebuilds the index; form validation and confirmation never do.
 *
 * @since 0.5.0
 *
 * @param mixed $post_id ACF post identifier.
 */
function kgi_invalidate_location_zip_index( $post_id ): void {
	$location_id = absint( $post_id );

	if ( $location_id <= 0 || kgi_get_location_post_type() !== get_post_type( $location_id ) ) {
		return;
	}

	delete_transient( KGI_LOCATION_ZIP_INDEX_TRANSIENT );
}

/**
 * Rebuilds the ownership index after ACF saves a Location CPT.
 *
 * Warming the cache during the administrative save request prevents the next
 * lead's thank-you request from paying the cost of rebuilding the index.
 *
 * @since 0.5.0
 *
 * @param mixed $post_id ACF post identifier.
 */
function kgi_refresh_location_zip_index( $post_id ): void {
	$location_id = absint( $post_id );

	if ( $location_id <= 0 || kgi_get_location_post_type() !== get_post_type( $location_id ) ) {
		return;
	}

	delete_transient( KGI_LOCATION_ZIP_INDEX_TRANSIENT );
	kgi_build_location_zip_index();
}

/**
 * Resolves the location that owns an entry's submitted ZIP/postal code.
 *
 * The original location is always the fallback. It is also preferred when it
 * appears among duplicate owners. The entry's submitted values are untouched.
 *
 * @since 0.5.0
 *
 * @param mixed[] $entry             Gravity Forms entry array.
 * @param WP_Post $original_location Location originally resolved from the form.
 * @param int     $entry_id          Gravity Forms entry ID for diagnostic logging.
 * @return WP_Post Location to use for the n8n location payload.
 */
function kgi_resolve_location_for_entry_zip( array $entry, WP_Post $original_location, int $entry_id = 0 ): WP_Post {
	$form_id      = (int) ( $entry['form_id'] ?? 0 );
	$field_map    = kgi_get_field_map_for_form( $form_id );
	$zip_field_id = $field_map['zip'] ?? '';
	$zip_code     = '' !== $zip_field_id ? kgi_normalize_zip_code( rgar( $entry, $zip_field_id ) ) : '';

	if ( '' === $zip_code ) {
		return $original_location;
	}

	$owners = kgi_get_location_zip_index()[ $zip_code ] ?? array();

	if ( count( $owners ) > 1 ) {
		kgi_log(
			'Duplicate ZIP/postal-code ownership found. Original location is preferred when applicable.',
			array(
				'entry_id'           => $entry_id,
				'original_location'  => $original_location->ID,
				'owner_location_ids' => $owners,
			)
		);
	}

	if ( in_array( $original_location->ID, $owners, true ) || empty( $owners ) ) {
		return $original_location;
	}

	$matched_location = get_post( (int) $owners[0] );

	if ( ! $matched_location instanceof WP_Post || kgi_get_location_post_type() !== $matched_location->post_type ) {
		return $original_location;
	}

	return $matched_location;
}

/**
 * Resolves the current franchise location CPT post from the request URI.
 *
 * Result is cached statically for the lifetime of the request to avoid
 * redundant database queries when multiple fields are populated.
 *
 * The expected URL structure is driven by the Country setting (Settings →
 * Koala Gravity Integration) via kgi_get_country_url_prefix(): no prefix
 * for US (/{location-slug}), or a country prefix for everything else
 * (/ca/{location-slug} for Canada, /{country-slug}/{location-slug} for Other).
 *
 * @since 0.1.0
 *
 * @return WP_Post|null The matched location post, or null if not resolved.
 */
function kgi_get_current_location_from_request(): ?WP_Post {
	static $resolved = false;
	static $location = null;

	if ( $resolved ) {
		return $location;
	}

	$resolved = true;

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path        = trim( wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
	$parts       = '' === $path ? array() : explode( '/', $path );
	$prefix      = kgi_get_country_url_prefix();

	$location_slug = null;

	if ( '' !== $prefix ) {
		if ( ! empty( $parts[0] ) && $prefix === $parts[0] && ! empty( $parts[1] ) ) {
			$location_slug = sanitize_title( $parts[1] );
		}
	} elseif ( ! empty( $parts[0] ) ) {
		$location_slug = sanitize_title( $parts[0] );
	}

	if ( ! $location_slug ) {
		return null;
	}

	$post     = get_page_by_path( $location_slug, OBJECT, kgi_get_location_post_type() );
	$location = $post instanceof WP_Post ? $post : null;

	return $location;
}

/**
 * Resolves a location CPT post by post ID, falling back to the request URI.
 *
 * Shared by both the form validator and the submission handler to avoid
 * duplicating the ID-check → CPT-verify → URL-fallback pattern.
 *
 * @since 0.1.0
 *
 * @param int $location_id WordPress post ID to look up.
 * @return WP_Post|null The matched location post, or null if not resolved.
 */
function kgi_resolve_location( int $location_id ): ?WP_Post {
	if ( $location_id > 0 ) {
		$post = get_post( $location_id );

		if ( $post instanceof WP_Post && kgi_get_location_post_type() === $post->post_type ) {
			return $post;
		}
	}

	return kgi_get_current_location_from_request();
}

/**
 * Resolves a location CPT post strictly by post ID, with no URL fallback.
 *
 * Used for fixed-location forms (see `kgi_get_fixed_location_forms()`), where
 * the location is a fixed piece of admin configuration rather than something
 * derived per-request. Falling back to the request URI here (like
 * `kgi_resolve_location()` does) would be wrong: a fixed-location form's
 * configured location has no relationship to whatever page it happens to be
 * embedded on.
 *
 * @since 0.2.0
 *
 * @param int $location_id WordPress post ID to look up.
 * @return WP_Post|null The matched location post, or null if not resolved.
 */
function kgi_resolve_fixed_location( int $location_id ): ?WP_Post {
	if ( $location_id <= 0 ) {
		return null;
	}

	$post = get_post( $location_id );

	return ( $post instanceof WP_Post && kgi_get_location_post_type() === $post->post_type ) ? $post : null;
}

/**
 * Resolves the location from a Gravity Forms entry array.
 *
 * Reads the location routing field mapping for whichever dynamic quote form
 * this entry belongs to (the main Quote Form or an additional quote form —
 * see `kgi_get_location_field_id_for_form()`), since each may map
 * `location_id` to a different field ID.
 *
 * @since 0.1.0
 *
 * @param mixed[] $entry Gravity Forms entry array.
 * @return WP_Post|null The matched location post, or null if not resolved.
 */
function kgi_get_location_from_entry( array $entry ): ?WP_Post {
	$form_id = (int) ( $entry['form_id'] ?? 0 );

	return kgi_resolve_location(
		absint( rgar( $entry, (string) kgi_get_location_field_id_for_form( $form_id, 'location_id' ) ) )
	);
}

/**
 * Resolves the location from stored Gravity Forms entry meta.
 *
 * Use this in background jobs. The request URI in a WP-Cron context is the
 * cron endpoint, not the original form URL, so URL-based fallback is unsafe.
 *
 * @since 0.1.0
 *
 * @param int $entry_id Gravity Forms entry ID.
 * @return WP_Post|null The matched location post, or null if not resolved.
 */
function kgi_get_location_from_entry_meta( int $entry_id ): ?WP_Post {
	$location_id = absint( gform_get_meta( $entry_id, 'kgi_location_id' ) );

	if ( $location_id <= 0 ) {
		return null;
	}

	$post = get_post( $location_id );

	return ( $post instanceof WP_Post && kgi_get_location_post_type() === $post->post_type ) ? $post : null;
}
