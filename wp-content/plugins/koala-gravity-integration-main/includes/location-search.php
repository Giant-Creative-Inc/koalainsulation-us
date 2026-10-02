<?php
/**
 * Location search endpoint for the theme's ZIP/postal-code search bars.
 *
 * One AJAX action replaces the theme's own zipcodeapi.com calls, so every
 * search makes at most one cached request with the server-side API key.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maximum searches per visitor per minute.
 */
const KGI_LOCATION_SEARCH_RATE_LIMIT = 20;

/**
 * Registers the location search AJAX action for visitors and logged-in users.
 *
 * @since 0.8.0
 */
function kgi_register_location_search_hooks(): void {
	add_action( 'wp_ajax_kgi_find_location', 'kgi_handle_location_search_request' );
	add_action( 'wp_ajax_nopriv_kgi_find_location', 'kgi_handle_location_search_request' );
}

/**
 * Handles a location search request.
 *
 * Expects POST `code` (the ZIP/postal code). Pages are served from full-page
 * caches, so a nonce would expire inside cached HTML; requests are instead
 * limited to this site's own pages (Origin/Referer) and rate limited per
 * visitor before any API call.
 *
 * Responds with `status`, a visitor-facing `message` and `locations`.
 *
 * @since 0.8.0
 */
function kgi_handle_location_search_request(): void {
	if ( ! kgi_location_search_request_is_same_site() ) {
		wp_send_json(
			array(
				'status'    => 'forbidden',
				'message'   => '',
				'locations' => array(),
			),
			403
		);
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Public, cache-safe endpoint; see function docblock.
	$code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';

	wp_send_json( kgi_build_location_search_response( $code, kgi_get_location_search_visitor_ip() ) );
}

/**
 * Builds the search response for a ZIP/postal code.
 *
 * @since 0.8.0
 *
 * @param string $raw_code   Code as typed by the visitor.
 * @param string $visitor_ip Visitor IP address, for the rate limit.
 * @return array{status: string, message: string, code: string, locations: array<int, array<string, mixed>>}
 */
function kgi_build_location_search_response( string $raw_code, string $visitor_ip ): array {
	if ( kgi_location_search_is_rate_limited( $visitor_ip ) ) {
		return array(
			'status'    => 'rate_limited',
			'message'   => kgi_get_location_search_message( 'rate_limited' ),
			'code'      => kgi_normalize_zip_code( $raw_code ),
			'locations' => array(),
		);
	}

	$lookup    = kgi_lookup_nearby_locations( $raw_code, 'search' );
	$locations = array();

	foreach ( $lookup['locations'] as $row ) {
		$post = get_post( (int) $row['location_id'] );

		if ( ! $post instanceof WP_Post || kgi_get_location_post_type() !== $post->post_type ) {
			continue;
		}

		$locations[] = array(
			'id'           => $post->ID,
			'slug'         => $post->post_name,
			'title'        => html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' ),
			'address'      => (string) get_field( 'location_address', $post->ID ),
			'phone'        => (string) get_field( 'location_phone_number', $post->ID ),
			'website'      => (string) get_permalink( $post ),
			'zipcode'      => (string) get_field( 'location_zipcode', $post->ID ),
			'matched_code' => $row['matched_code'],
			'distance'     => $row['distance'],
		);
	}

	$status = $lookup['status'];

	if ( in_array( $status, array( 'match', 'nearby' ), true ) && empty( $locations ) ) {
		$status = 'no_match';
	}

	return array(
		'status'    => $status,
		'message'   => kgi_get_location_search_message( $status ),
		'code'      => $lookup['code'],
		'locations' => $locations,
	);
}

/**
 * Returns the visitor-facing message for a search status.
 *
 * Uses "postal code" on the Canadian site and "ZIP code" elsewhere. Matches
 * return an empty message because the theme shows the locations instead.
 *
 * @since 0.8.0
 *
 * @param string $status Search status.
 * @return string
 */
function kgi_get_location_search_message( string $status ): string {
	$is_canada = 'ca' === get_option( 'kgi_country', 'us' );

	switch ( $status ) {
		case 'invalid':
			$message = $is_canada
				? __( 'Please enter a valid postal code, like A1A 1A1.', 'koala-gravity-integration' )
				: __( 'Please enter a valid 5-digit ZIP code.', 'koala-gravity-integration' );
			break;
		case 'no_match':
			$message = $is_canada
				? __( "We don't have a location near that postal code yet.", 'koala-gravity-integration' )
				: __( "We don't have a location near that ZIP code yet.", 'koala-gravity-integration' );
			break;
		case 'lookup_failed':
			$message = __( "We're having trouble looking up your area right now. Please try again later.", 'koala-gravity-integration' );
			break;
		case 'rate_limited':
			$message = __( 'Too many searches. Please wait a minute and try again.', 'koala-gravity-integration' );
			break;
		default:
			$message = '';
	}

	/**
	 * Filters the visitor-facing location search message.
	 *
	 * @since 0.8.0
	 *
	 * @param string $message Message text.
	 * @param string $status  Search status.
	 */
	return (string) apply_filters( 'kgi_location_search_message', $message, $status );
}

/**
 * Counts a search for a visitor and reports whether they are over the limit.
 *
 * @since 0.8.0
 *
 * @param string $visitor_ip Visitor IP address.
 * @return bool True when the visitor has used up this minute's searches.
 */
function kgi_location_search_is_rate_limited( string $visitor_ip ): bool {
	$key    = 'kgi_search_rl_' . md5( $visitor_ip );
	$window = get_transient( $key );
	$now    = time();

	if ( ! is_array( $window ) || $now - (int) ( $window['start'] ?? 0 ) >= MINUTE_IN_SECONDS ) {
		$window = array(
			'start' => $now,
			'count' => 0,
		);
	}

	if ( (int) $window['count'] >= KGI_LOCATION_SEARCH_RATE_LIMIT ) {
		return true;
	}

	++$window['count'];

	set_transient( $key, $window, MINUTE_IN_SECONDS );

	return false;
}

/**
 * Returns the visitor's IP address.
 *
 * Prefers Cloudflare's `CF-Connecting-IP` header (the sites sit behind
 * Cloudflare), then `REMOTE_ADDR`.
 *
 * @since 0.8.0
 *
 * @return string
 */
function kgi_get_location_search_visitor_ip(): string {
	$candidates = array( 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR' );

	foreach ( $candidates as $header ) {
		if ( empty( $_SERVER[ $header ] ) ) {
			continue;
		}

		$ip = filter_var( wp_unslash( $_SERVER[ $header ] ), FILTER_VALIDATE_IP ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated by FILTER_VALIDATE_IP.

		if ( false !== $ip ) {
			/**
			 * Filters the visitor IP used for the location search rate limit.
			 *
			 * @since 0.8.0
			 *
			 * @param string $ip Visitor IP address.
			 */
			return (string) apply_filters( 'kgi_location_search_visitor_ip', $ip );
		}
	}

	return '';
}

/**
 * Checks that a search request came from one of this site's pages.
 *
 * @since 0.8.0
 *
 * @return bool
 */
function kgi_location_search_request_is_same_site(): bool {
	$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
	$source    = '';

	if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) ) {
		$source = esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$source = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
	}

	if ( '' === $source || ! $site_host ) {
		return false;
	}

	return strtolower( (string) wp_parse_url( $source, PHP_URL_HOST ) ) === strtolower( $site_host );
}
