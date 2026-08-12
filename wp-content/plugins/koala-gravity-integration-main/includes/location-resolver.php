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
 * Transient key prefix for cached zipcodeapi.com radius distance lookups.
 *
 * The full key appends a hash of the queried ZIP and radius. Distances between
 * ZIP centroids never change, so these are cached for a long time and reused
 * across entries.
 */
const KGI_ZIP_RADIUS_CACHE_PREFIX = 'kgi_zipdist_';

/**
 * Returns the zipcodeapi.com API key used for the nearest-location fallback.
 *
 * Configured on the plugin settings page. An empty value disables the
 * fallback entirely (the resolver keeps its original-location behavior).
 *
 * @since 0.6.0
 *
 * @return string
 */
function kgi_get_zipcodeapi_key(): string {
	return (string) get_option( 'kgi_zipcodeapi_key', '' );
}

/**
 * Returns the search radius (miles) for the nearest-location fallback.
 *
 * Defaults to 60 to match the theme's on-page ZIP lookup popup. Filterable
 * so the value can be tuned without a settings field.
 *
 * @since 0.6.0
 *
 * @return int Radius in miles.
 */
function kgi_get_zip_fallback_radius(): int {
	/**
	 * Filters the nearest-location fallback search radius, in miles.
	 *
	 * @since 0.6.0
	 *
	 * @param int $radius Radius in miles.
	 */
	return max( 1, (int) apply_filters( 'kgi_zip_fallback_radius', 60 ) );
}

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
 * Resolution order:
 *   1. Exact owner of the submitted ZIP (the form's original location is
 *      preferred when it is among duplicate owners).
 *   2. When no location owns the ZIP, the nearest owning location within the
 *      configured radius, looked up via zipcodeapi.com (US ZIP codes and
 *      Canadian postal codes; requires an API key). See
 *      kgi_find_nearest_location_by_zip().
 *   3. No location when neither lookup finds an owner.
 *
 * The entry's submitted values are untouched.
 *
 * @since 0.5.0
 *
 * @param mixed[] $entry             Gravity Forms entry array.
 * @param WP_Post $original_location Location originally resolved from the form.
 * @param int     $entry_id          Gravity Forms entry ID for diagnostic logging.
 * @return WP_Post|null Location to use for the n8n location payload, or null.
 */
function kgi_resolve_location_for_entry_zip( array $entry, WP_Post $original_location, int $entry_id = 0 ): ?WP_Post {
	$form_id      = (int) ( $entry['form_id'] ?? 0 );
	$field_map    = kgi_get_field_map_for_form( $form_id );
	$zip_field_id = $field_map['zip'] ?? '';
	$zip_code     = '' !== $zip_field_id ? kgi_normalize_zip_code( rgar( $entry, $zip_field_id ) ) : '';

	if ( '' === $zip_code ) {
		return $original_location;
	}

	$owners = kgi_get_location_zip_index()[ $zip_code ] ?? array();

	if ( empty( $owners ) ) {
		// No location owns this ZIP. Try the nearest owning location; if that
		// also fails, leave the submission unassigned for manual review.
		$nearest = kgi_find_nearest_location_by_zip( $zip_code, $entry_id );

		return $nearest instanceof WP_Post ? $nearest : null;
	}

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

	if ( in_array( $original_location->ID, $owners, true ) ) {
		return $original_location;
	}

	$matched_location = get_post( (int) $owners[0] );

	if ( ! $matched_location instanceof WP_Post || kgi_get_location_post_type() !== $matched_location->post_type ) {
		return null;
	}

	return $matched_location;
}

/**
 * Finds the nearest owning location to a ZIP with no exact owner.
 *
 * Queries zipcodeapi.com for every ZIP within the configured radius of the
 * submitted ZIP, intersects those with the ownership index, and returns the
 * owner whose nearest ZIP is closest. Returns null (so the caller keeps the
 * original location) when the fallback is disabled, unavailable, or finds no
 * owner within range.
 *
 * Handles both US ZIP codes and Canadian postal codes (each uses a different
 * zipcodeapi.com endpoint); any other format returns null.
 *
 * @since 0.6.0
 *
 * @param string $normalized_zip Normalized submitted ZIP/postal code (see kgi_normalize_zip_code()).
 * @param int    $entry_id       Gravity Forms entry ID for diagnostic logging.
 * @return WP_Post|null Nearest owning location, or null.
 */
function kgi_find_nearest_location_by_zip( string $normalized_zip, int $entry_id = 0 ): ?WP_Post {
	$api_key = kgi_get_zipcodeapi_key();

	if ( '' === $api_key ) {
		return null;
	}

	$request = kgi_zip_api_request_parts( $normalized_zip );

	if ( null === $request ) {
		// Not a recognizable US ZIP or Canadian postal code.
		return null;
	}

	// Step 1: every code within the radius of the submitted code (the filter).
	$nearby_codes = kgi_get_zip_radius_codes( $request['code'], $request['country'], $api_key, $entry_id );

	if ( empty( $nearby_codes ) ) {
		return null;
	}

	// Step 2: keep only codes an actual location owns, then rank them by the
	// precise driving/great-circle distance from the submitted code and pick
	// the closest owner. This two-step (radius filter, then distance rank)
	// mirrors the Canadian theme's zipcodeapi.com usage exactly.
	$index            = kgi_get_location_zip_index();
	$best_location_id = 0;
	$best_distance    = null;

	foreach ( $nearby_codes as $nearby_code ) {
		$owners = $index[ $nearby_code ] ?? array();

		if ( empty( $owners ) ) {
			continue;
		}

		$distance = kgi_get_zip_pair_distance( $request['code'], $nearby_code, $request['country'], $api_key, $entry_id );

		if ( null === $distance ) {
			continue;
		}

		// Owners are stored ascending by ID, so ties resolve deterministically.
		if ( null === $best_distance || $distance < $best_distance ) {
			$best_distance    = $distance;
			$best_location_id = (int) $owners[0];
		}
	}

	if ( $best_location_id <= 0 ) {
		return null;
	}

	$location = get_post( $best_location_id );

	if ( ! $location instanceof WP_Post || kgi_get_location_post_type() !== $location->post_type ) {
		return null;
	}

	kgi_log(
		'Nearest location resolved via zipcodeapi.com radius fallback.',
		array(
			'entry_id'            => $entry_id,
			'submitted_zip'       => $request['code'],
			'country'             => $request['country'],
			'nearest_location_id' => $best_location_id,
			'distance_miles'      => $best_distance,
		)
	);

	return $location;
}

/**
 * Classifies a normalized ZIP/postal code for a zipcodeapi.com radius lookup.
 *
 * The plugin's Country setting (kgi_country) is authoritative for whether an
 * install deals in US ZIP codes or Canadian postal codes, so it decides which
 * zipcodeapi.com endpoint and response shape to use. The submitted value is
 * still shape-checked for the configured country: a US ZIP+4 is truncated to
 * its leading 5 digits, and anything that isn't a plausible code for that
 * country (or a country zipcodeapi.com doesn't cover, i.e. "other") returns
 * null so the caller can leave the submission unassigned for review.
 * Normalization has already stripped spaces and hyphens and uppercased
 * letters, so a Canadian code arrives as `A1A1A1`.
 *
 * @since 0.6.0
 *
 * @param string $normalized_zip Normalized code (uppercase alphanumerics only).
 * @return array{code: string, country: string}|null Request parts, or null when unusable.
 */
function kgi_zip_api_request_parts( string $normalized_zip ): ?array {
	$country = get_option( 'kgi_country', 'us' );

	if ( 'ca' === $country ) {
		// Canadian postal code: letter-digit-letter-digit-letter-digit (space removed).
		if ( preg_match( '/^[A-Z]\d[A-Z]\d[A-Z]\d$/', $normalized_zip ) ) {
			return array(
				'code'    => $normalized_zip,
				'country' => 'ca',
			);
		}

		return null;
	}

	if ( 'us' === $country ) {
		// US ZIP: digits only. ZIP+4 is truncated to its leading 5 digits.
		if ( strlen( $normalized_zip ) >= 5 && ctype_digit( $normalized_zip ) ) {
			return array(
				'code'    => substr( $normalized_zip, 0, 5 ),
				'country' => 'us',
			);
		}

		return null;
	}

	// "other" — zipcodeapi.com does not cover it.
	return null;
}

/**
 * Formats a normalized code for a zipcodeapi.com radius request.
 *
 * The Canadian radius endpoint expects the spaced postal-code form
 * (`A1A 1A1`), matching the Canadian theme, while US ZIPs are sent as-is. The
 * caller URL-encodes the result.
 *
 * @since 0.6.0
 *
 * @param string $code    Normalized code (no spaces).
 * @param string $country 'us' or 'ca'.
 * @return string
 */
function kgi_format_code_for_radius( string $code, string $country ): string {
	if ( 'ca' === $country && 6 === strlen( $code ) ) {
		return substr( $code, 0, 3 ) . ' ' . substr( $code, 3 );
	}

	return $code;
}

/**
 * Builds the base zipcodeapi.com REST URL for a country.
 *
 * Canada uses the versioned `/rest/v2/CA/` path; US uses the unversioned
 * `/rest/` path. Both take the API key as the next segment.
 *
 * @since 0.6.0
 *
 * @param string $country 'us' or 'ca'.
 * @param string $api_key zipcodeapi.com API key.
 * @return string Base URL ending before the endpoint name.
 */
function kgi_zipcodeapi_base_url( string $country, string $api_key ): string {
	if ( 'ca' === $country ) {
		return 'https://www.zipcodeapi.com/rest/v2/CA/' . rawurlencode( $api_key );
	}

	return 'https://www.zipcodeapi.com/rest/' . rawurlencode( $api_key );
}

/**
 * Fetches (and caches) the normalized codes within the fallback radius.
 *
 * Sourced from zipcodeapi.com's radius endpoint. Both the US and Canadian
 * endpoints return a `zip_codes` list of `{ zip_code, ... }` items (the
 * Canadian v2 endpoint keeps the `zip_codes`/`zip_code` naming); `postal_codes`
 * / `postal_code` are also accepted defensively. Returned codes are normalized
 * (spaces stripped, uppercased) so they match the ownership index. Results are
 * cached for a week keyed by code, country, and radius; API/transport failures
 * return an empty array and are not cached, so a transient outage is retried on
 * the next entry.
 *
 * @since 0.6.0
 *
 * @param string $code     API-ready code to search around (5-digit US ZIP or 6-char CA postal code).
 * @param string $country  'us' or 'ca'.
 * @param string $api_key  zipcodeapi.com API key.
 * @param int    $entry_id Gravity Forms entry ID for diagnostic logging.
 * @return string[] Normalized nearby codes.
 */
function kgi_get_zip_radius_codes( string $code, string $country, string $api_key, int $entry_id = 0 ): array {
	$radius    = kgi_get_zip_fallback_radius();
	$cache_key = KGI_ZIP_RADIUS_CACHE_PREFIX . md5( $country . '_' . $code . '_' . $radius );
	$cached    = get_transient( $cache_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$request_code = kgi_format_code_for_radius( $code, $country );

	$url = sprintf(
		'%s/radius.json/%s/%d/mile',
		kgi_zipcodeapi_base_url( $country, $api_key ),
		rawurlencode( $request_code ),
		$radius
	);

	$body = kgi_zipcodeapi_get( $url, 'radius', $country, $entry_id );

	if ( null === $body ) {
		return array();
	}

	$list = array();

	if ( ! empty( $body['zip_codes'] ) && is_array( $body['zip_codes'] ) ) {
		$list       = $body['zip_codes'];
		$code_field = 'zip_code';
	} elseif ( ! empty( $body['postal_codes'] ) && is_array( $body['postal_codes'] ) ) {
		$list       = $body['postal_codes'];
		$code_field = 'postal_code';
	} else {
		return array();
	}

	$codes = array();

	foreach ( $list as $item ) {
		if ( ! is_array( $item ) || ! isset( $item[ $code_field ] ) ) {
			continue;
		}

		$normalized = kgi_normalize_zip_code( $item[ $code_field ] );

		if ( '' !== $normalized ) {
			$codes[ $normalized ] = true;
		}
	}

	$codes = array_keys( $codes );

	set_transient( $cache_key, $codes, WEEK_IN_SECONDS );

	return $codes;
}

/**
 * Fetches (and caches) the distance in miles between two codes.
 *
 * Uses zipcodeapi.com's distance endpoint, which accepts space-stripped codes
 * for both countries (matching the Canadian theme). Results are cached for a
 * week keyed by the ordered pair and country. Returns null when the distance
 * can't be determined, so the caller skips that candidate.
 *
 * @since 0.6.0
 *
 * @param string $from     Normalized submitted code.
 * @param string $to       Normalized owned code to measure to.
 * @param string $country  'us' or 'ca'.
 * @param string $api_key  zipcodeapi.com API key.
 * @param int    $entry_id Gravity Forms entry ID for diagnostic logging.
 * @return float|null Distance in miles, or null.
 */
function kgi_get_zip_pair_distance( string $from, string $to, string $country, string $api_key, int $entry_id = 0 ): ?float {
	$cache_key = KGI_ZIP_RADIUS_CACHE_PREFIX . 'd_' . md5( $country . '_' . $from . '_' . $to );
	$cached    = get_transient( $cache_key );

	if ( is_numeric( $cached ) ) {
		return (float) $cached;
	}

	$url = sprintf(
		'%s/distance.json/%s/%s/mile',
		kgi_zipcodeapi_base_url( $country, $api_key ),
		rawurlencode( $from ),
		rawurlencode( $to )
	);

	$body = kgi_zipcodeapi_get( $url, 'distance', $country, $entry_id );

	if ( null === $body || ! isset( $body['distance'] ) || ! is_numeric( $body['distance'] ) ) {
		return null;
	}

	$distance = (float) $body['distance'];

	set_transient( $cache_key, $distance, WEEK_IN_SECONDS );

	return $distance;
}

/**
 * Performs a zipcodeapi.com GET request and returns the decoded JSON body.
 *
 * Centralizes transport-error, HTTP-status, and API `error_msg` handling (the
 * API returns error_msg inside an HTTP 200 body). Returns null on any failure
 * so callers degrade to the original location.
 *
 * @since 0.6.0
 *
 * @param string $url      Fully built request URL.
 * @param string $endpoint Endpoint label for logging ('radius' or 'distance').
 * @param string $country  'us' or 'ca'.
 * @param int    $entry_id Gravity Forms entry ID for diagnostic logging.
 * @return array<string, mixed>|null Decoded body, or null on failure.
 */
function kgi_zipcodeapi_get( string $url, string $endpoint, string $country, int $entry_id = 0 ): ?array {
	$response = wp_remote_get( $url, array( 'timeout' => 10 ) );

	if ( is_wp_error( $response ) ) {
		kgi_log(
			'zipcodeapi.com request failed.',
			array(
				'entry_id' => $entry_id,
				'endpoint' => $endpoint,
				'country'  => $country,
				'error'    => $response->get_error_message(),
			)
		);

		return null;
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	$body   = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $status || ! is_array( $body ) || isset( $body['error_msg'] ) ) {
		kgi_log(
			'zipcodeapi.com response was not usable.',
			array(
				'entry_id'  => $entry_id,
				'endpoint'  => $endpoint,
				'country'   => $country,
				'status'    => $status,
				'error_msg' => is_array( $body ) && isset( $body['error_msg'] ) ? $body['error_msg'] : null,
			)
		);

		return null;
	}

	return $body;
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
 * Resolves the location that exactly owns an entry's submitted ZIP/postal code.
 *
 * Used as a routing fallback at submission time when the location could not be
 * resolved from the request URL or the posted location-ID field, so a lead is
 * captured and routed instead of lost. Deliberately does only the cheap,
 * in-memory exact-owner lookup against the ownership index — no zipcodeapi.com
 * call — to keep the submission request fast. The nearest-owner API refinement
 * still runs later in the background job (see kgi_resolve_location_for_entry_zip()).
 *
 * @since 0.7.0
 *
 * @param mixed[] $entry Gravity Forms entry array.
 * @return WP_Post|null The owning location post, or null if none owns the ZIP.
 */
function kgi_resolve_location_by_zip_exact( array $entry ): ?WP_Post {
	$form_id      = (int) ( $entry['form_id'] ?? 0 );
	$field_map    = kgi_get_field_map_for_form( $form_id );
	$zip_field_id = $field_map['zip'] ?? '';
	$zip_code     = '' !== $zip_field_id ? kgi_normalize_zip_code( rgar( $entry, $zip_field_id ) ) : '';

	if ( '' === $zip_code ) {
		return null;
	}

	$owners = kgi_get_location_zip_index()[ $zip_code ] ?? array();

	if ( empty( $owners ) ) {
		return null;
	}

	$post = get_post( (int) $owners[0] );

	return ( $post instanceof WP_Post && kgi_get_location_post_type() === $post->post_type ) ? $post : null;
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
