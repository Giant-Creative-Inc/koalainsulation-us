<?php
/**
 * ZIP/postal-code lookup error log and alert emails.
 *
 * Every failed zipcodeapi.com lookup, from the search bar or a form
 * submission, is stored here for 30 days, written to the server error log and
 * (when alert addresses are configured) emailed, at most once per error type
 * per hour.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option holding the ZIP lookup error log (not autoloaded).
 */
const KGI_ZIP_LOOKUP_LOG_OPTION = 'kgi_zip_lookup_log';

/**
 * How long log entries are kept, in seconds (30 days).
 */
const KGI_ZIP_LOOKUP_LOG_MAX_AGE = 2592000;

/**
 * Maximum number of log entries kept, so a long outage can't bloat the option.
 */
const KGI_ZIP_LOOKUP_LOG_MAX_ENTRIES = 1000;

/**
 * Records a failed ZIP/postal-code lookup.
 *
 * @since 0.8.0
 *
 * @param array{source?: string, code?: string, error_type?: string, http_status?: int, message?: string, entry_id?: int} $event Failure details.
 */
function kgi_record_zip_lookup_error( array $event ): void {
	$message = (string) ( $event['message'] ?? '' );
	$api_key = kgi_get_zipcodeapi_key();

	// Transport errors can echo the request URL, which contains the API key.
	if ( '' !== $api_key ) {
		$message = str_replace( $api_key, '[api key]', $message );
	}

	$entry = array(
		'time'        => time(),
		'source'      => sanitize_key( (string) ( $event['source'] ?? '' ) ),
		'code'        => (string) ( $event['code'] ?? '' ),
		'error_type'  => sanitize_key( (string) ( $event['error_type'] ?? '' ) ),
		'http_status' => (int) ( $event['http_status'] ?? 0 ),
		'message'     => substr( $message, 0, 300 ),
		'entry_id'    => (int) ( $event['entry_id'] ?? 0 ),
	);

	$log = kgi_get_zip_lookup_log();

	array_unshift( $log, $entry );

	update_option( KGI_ZIP_LOOKUP_LOG_OPTION, array_slice( $log, 0, KGI_ZIP_LOOKUP_LOG_MAX_ENTRIES ), false );

	kgi_log_error( 'ZIP lookup failed.', $entry );
	kgi_maybe_send_zip_lookup_alert( $entry );
}

/**
 * Returns the ZIP lookup error log, newest first, without expired entries.
 *
 * @since 0.8.0
 *
 * @return array<int, array<string, mixed>>
 */
function kgi_get_zip_lookup_log(): array {
	$log = get_option( KGI_ZIP_LOOKUP_LOG_OPTION, array() );

	if ( ! is_array( $log ) ) {
		return array();
	}

	$cutoff = time() - KGI_ZIP_LOOKUP_LOG_MAX_AGE;

	return array_values(
		array_filter(
			$log,
			static function ( $row ) use ( $cutoff ): bool {
				return is_array( $row ) && (int) ( $row['time'] ?? 0 ) >= $cutoff;
			}
		)
	);
}

/**
 * Returns a readable label for a ZIP lookup error type.
 *
 * @since 0.8.0
 *
 * @param string $error_type Error type from kgi_zipcodeapi_get().
 * @return string
 */
function kgi_get_zip_lookup_error_label( string $error_type ): string {
	$labels = array(
		'rate_limited'     => __( 'Hourly request limit reached (429)', 'koala-gravity-integration' ),
		'auth_error'       => __( 'API key rejected (401)', 'koala-gravity-integration' ),
		'missing_api_key'  => __( 'No zipcodeapi.com API key configured', 'koala-gravity-integration' ),
		'transport'        => __( 'Could not connect or timed out', 'koala-gravity-integration' ),
		'invalid_response' => __( 'Unreadable response', 'koala-gravity-integration' ),
		'provider'         => __( 'zipcodeapi.com returned an error', 'koala-gravity-integration' ),
	);

	if ( isset( $labels[ $error_type ] ) ) {
		return $labels[ $error_type ];
	}

	if ( str_starts_with( $error_type, 'http_' ) ) {
		/* translators: %s: HTTP status code */
		return sprintf( __( 'HTTP error %s', 'koala-gravity-integration' ), substr( $error_type, 5 ) );
	}

	return $error_type;
}

/**
 * Describes an entry's `kgi_zip_routing_status` for staff.
 *
 * @since 0.8.0
 *
 * @param string $status Routing status meta value.
 * @return string Readable description, or the raw status when unknown.
 */
function kgi_describe_zip_routing_status( string $status ): string {
	$descriptions = array(
		'original_location'    => __( 'Kept the page location', 'koala-gravity-integration' ),
		'reassigned'           => __( 'Reassigned by ZIP/postal code', 'koala-gravity-integration' ),
		'no_location_in_range' => __( 'No location within the search radius', 'koala-gravity-integration' ),
		'invalid_code'         => __( 'ZIP/postal code is not valid', 'koala-gravity-integration' ),
		'unresolved'           => __( 'Could not be matched to a location', 'koala-gravity-integration' ),
	);

	if ( isset( $descriptions[ $status ] ) ) {
		return $descriptions[ $status ];
	}

	if ( str_starts_with( $status, 'lookup_failed_' ) ) {
		/* translators: %s: error description, e.g. "Hourly request limit reached (429)" */
		return sprintf( __( 'ZIP lookup failed: %s', 'koala-gravity-integration' ), kgi_get_zip_lookup_error_label( substr( $status, 14 ) ) );
	}

	return $status;
}

/**
 * Returns the configured ZIP lookup alert addresses.
 *
 * @since 0.8.0
 *
 * @return string[] Valid email addresses.
 */
function kgi_get_zip_lookup_alert_emails(): array {
	$raw    = (string) get_option( 'kgi_zip_lookup_alert_emails', '' );
	$emails = array();

	foreach ( (array) preg_split( '/[\s,;]+/', $raw ) as $candidate ) {
		$candidate = sanitize_email( (string) $candidate );

		if ( '' !== $candidate && is_email( $candidate ) ) {
			$emails[] = $candidate;
		}
	}

	return array_values( array_unique( $emails ) );
}

/**
 * Emails the alert addresses about a failed lookup, at most once per error
 * type per hour.
 *
 * @since 0.8.0
 *
 * @param array<string, mixed> $entry Log entry from kgi_record_zip_lookup_error().
 */
function kgi_maybe_send_zip_lookup_alert( array $entry ): void {
	$to = kgi_get_zip_lookup_alert_emails();

	if ( empty( $to ) ) {
		return;
	}

	$throttle_key = 'kgi_zip_alert_' . $entry['error_type'];

	if ( false !== get_transient( $throttle_key ) ) {
		return;
	}

	set_transient( $throttle_key, 1, HOUR_IN_SECONDS );

	$label   = kgi_get_zip_lookup_error_label( (string) $entry['error_type'] );
	$urgent  = in_array( $entry['error_type'], array( 'auth_error', 'missing_api_key' ), true );
	$subject = sprintf(
		/* translators: 1: "URGENT: " prefix or empty, 2: error description */
		__( '[Koala] %1$sZIP lookups are failing: %2$s', 'koala-gravity-integration' ),
		$urgent ? __( 'URGENT: ', 'koala-gravity-integration' ) : '',
		$label
	);

	$lines = array(
		__( 'The site could not look up a ZIP/postal code with zipcodeapi.com.', 'koala-gravity-integration' ),
		'',
		__( 'Error:', 'koala-gravity-integration' ) . ' ' . $label,
		__( 'Code searched:', 'koala-gravity-integration' ) . ' ' . $entry['code'],
		__( 'Where:', 'koala-gravity-integration' ) . ' ' . ( 'form' === $entry['source'] ? __( 'Quote form', 'koala-gravity-integration' ) : __( 'Location search', 'koala-gravity-integration' ) ),
	);

	if ( '' !== $entry['message'] ) {
		$lines[] = __( 'zipcodeapi.com message:', 'koala-gravity-integration' ) . ' ' . $entry['message'];
	}

	$lines[] = '';
	$lines[] = __( 'Search visitors see a "please try again later" message, and quote form leads go to unmatched lead routing for manual review.', 'koala-gravity-integration' );
	$lines[] = __( 'You will get at most one email per error type per hour. See Settings > Koala ZIP Lookup Log for every failure.', 'koala-gravity-integration' );

	wp_mail( $to, $subject, implode( "\n", $lines ) );
}
