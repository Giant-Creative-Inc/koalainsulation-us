<?php
/**
 * Regression checks for ZIP/postal-code lookups.
 *
 * Covers the single-request nearest-location lookup, its cache, failure
 * handling (unmatched routing, error log, alert emails) and the search-bar
 * response builder.
 *
 * Run with: php tests/zip-radius-regression.php
 *
 * @package Koala_Gravity_Integration
 */

define( 'ABSPATH', __DIR__ );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );
define( 'WEEK_IN_SECONDS', 604800 );

class WP_Post {
	public int $ID           = 0;
	public string $post_type = 'location';
	public string $post_name = '';
	public string $post_title = '';
}

class WP_Error {
	/**
	 * Returns a fixture error message.
	 *
	 * @return string
	 */
	public function get_error_message(): string {
		return 'Fixture transport error';
	}
}

/*
 * ---------------------------------------------------------------------------
 * In-memory WordPress stubs.
 * ---------------------------------------------------------------------------
 */

$GLOBALS['kgi_test_requests']   = array();
$GLOBALS['kgi_test_responses']  = array();
$GLOBALS['kgi_test_transients'] = array();
$GLOBALS['kgi_test_options']    = array();
$GLOBALS['kgi_test_meta']       = array();
$GLOBALS['kgi_test_mail']       = array();
$GLOBALS['kgi_test_errors']     = array();
$GLOBALS['kgi_test_posts']      = array();

function sanitize_key( string $value ): string {
	return strtolower( (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ) );
}

function is_wp_error( $value ): bool {
	return $value instanceof WP_Error;
}

function wp_remote_get( string $url, array $args = array() ) {
	unset( $args );
	$GLOBALS['kgi_test_requests'][] = $url;

	if ( empty( $GLOBALS['kgi_test_responses'] ) ) {
		throw new RuntimeException( 'Unexpected HTTP request: ' . $url );
	}

	return array_shift( $GLOBALS['kgi_test_responses'] );
}

function wp_remote_retrieve_response_code( $response ): int {
	return (int) ( $response['status'] ?? 0 );
}

function wp_remote_retrieve_body( $response ): string {
	return (string) ( $response['body'] ?? '' );
}

function get_transient( string $key ) {
	return $GLOBALS['kgi_test_transients'][ $key ] ?? false;
}

function set_transient( string $key, $value, int $expiration = 0 ): bool {
	unset( $expiration );
	$GLOBALS['kgi_test_transients'][ $key ] = $value;
	return true;
}

function delete_transient( string $key ): bool {
	unset( $GLOBALS['kgi_test_transients'][ $key ] );
	return true;
}

function apply_filters( string $hook_name, $value ) {
	unset( $hook_name );
	return $value;
}

function add_query_arg( array $args, string $url ): string {
	return $url . '?' . http_build_query( $args );
}

function get_option( string $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['kgi_test_options'] ) ? $GLOBALS['kgi_test_options'][ $name ] : $default;
}

function update_option( string $name, $value, $autoload = null ): bool {
	unset( $autoload );
	$GLOBALS['kgi_test_options'][ $name ] = $value;
	return true;
}

function kgi_get_field_map_for_form( int $form_id ): array {
	unset( $form_id );
	return array( 'zip' => '6' );
}

function kgi_get_location_post_type(): string {
	return 'location';
}

function rgar( array $array, string $key ) {
	return $array[ $key ] ?? null;
}

function gform_update_meta( int $entry_id, string $key, $value ): void {
	$GLOBALS['kgi_test_meta'][ $entry_id ][ $key ] = $value;
}

function gform_get_meta( int $entry_id, string $key ) {
	return $GLOBALS['kgi_test_meta'][ $entry_id ][ $key ] ?? false;
}

function get_post( $post_id ) {
	return $GLOBALS['kgi_test_posts'][ (int) $post_id ] ?? null;
}

function get_field( string $name, $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return null;
	}

	$fields = array(
		'location_name'         => $post->post_title,
		'location_phone_number' => '555-0100',
		'location_address'      => '1 Main St',
		'location_zipcode'      => 48215 === $post->ID ? '89123' : '',
	);

	return $GLOBALS['kgi_test_fields'][ $post_id ][ $name ] ?? ( $fields[ $name ] ?? null );
}

function get_permalink( $post ): string {
	return 'https://example.test/' . $post->post_name;
}

function wp_mail( $to, string $subject, string $message, $headers = '' ): bool {
	unset( $headers );
	$GLOBALS['kgi_test_mail'][] = compact( 'to', 'subject', 'message' );
	return true;
}

function is_email( string $email ) {
	return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : false;
}

function sanitize_email( string $email ): string {
	return trim( $email );
}

function wp_json_encode( $data ) {
	return json_encode( $data );
}

function __( string $text, string $domain = '' ): string {
	unset( $domain );
	return $text;
}

function kgi_log( string $message, array $context = array() ): void {
	unset( $message, $context );
}

function kgi_log_error( string $message, array $context = array() ): void {
	$GLOBALS['kgi_test_errors'][] = array( $message, $context );
}

require_once dirname( __DIR__ ) . '/includes/location-resolver.php';
require_once dirname( __DIR__ ) . '/includes/zip-lookup-log.php';
require_once dirname( __DIR__ ) . '/includes/location-search.php';

/*
 * ---------------------------------------------------------------------------
 * Test helpers.
 * ---------------------------------------------------------------------------
 */

$kgi_test_failures = 0;
$kgi_test_assertions = 0;

/**
 * Records a failure when two values differ.
 *
 * @param mixed  $expected Expected value.
 * @param mixed  $actual   Actual value.
 * @param string $message  Failure message.
 * @return void
 */
function kgi_test_assert_same( $expected, $actual, string $message ): void {
	++$GLOBALS['kgi_test_assertions'];
	if ( $expected !== $actual ) {
		++$GLOBALS['kgi_test_failures'];
		fwrite( STDERR, 'FAIL: ' . $message . PHP_EOL . '  Expected: ' . var_export( $expected, true ) . PHP_EOL . '  Actual:   ' . var_export( $actual, true ) . PHP_EOL );
	}
}

/**
 * Resets all in-memory state and loads the location fixtures.
 *
 * @param string $country 'us' or 'ca'.
 * @return void
 */
function kgi_test_reset( string $country = 'us' ): void {
	$GLOBALS['kgi_test_payload']    = null;
	$GLOBALS['kgi_test_fields']     = array();
	$GLOBALS['kgi_test_requests']   = array();
	$GLOBALS['kgi_test_responses']  = array();
	$GLOBALS['kgi_test_transients'] = array();
	$GLOBALS['kgi_test_meta']       = array();
	$GLOBALS['kgi_test_mail']       = array();
	$GLOBALS['kgi_test_errors']     = array();
	$GLOBALS['kgi_test_options']    = array(
		'kgi_country'        => $country,
		'kgi_zipcodeapi_key' => 'fixture-key',
	);

	$fixtures = array(
		48215 => array( 'las-vegas', 'Las Vegas' ),
		22949 => array( 'north-and-west-phoenix', 'North and West Phoenix' ),
		70001 => array( 'henderson-test', 'Henderson Test' ),
		20180 => array( 'east-toronto', 'East Toronto' ),
		20181 => array( 'halton', 'Halton' ),
	);

	$GLOBALS['kgi_test_posts'] = array();

	foreach ( $fixtures as $id => $fixture ) {
		$post             = new WP_Post();
		$post->ID         = $id;
		$post->post_name  = $fixture[0];
		$post->post_title = $fixture[1];

		$GLOBALS['kgi_test_posts'][ $id ] = $post;
	}

	// Ownership index, exactly as kgi_build_location_zip_index() stores it.
	$GLOBALS['kgi_test_transients'][ KGI_LOCATION_ZIP_INDEX_TRANSIENT ] = array(
		'89012'  => array( 48215 ),
		'89014'  => array( 48215 ),
		'89123'  => array( 48215 ),
		'89074'  => array( 70001 ),
		'85310'  => array( 22949 ),
		'L7L4X9' => array( 20181 ),
		'M1V3S2' => array( 20180 ),
	);
}

/**
 * Queues a fake zipcodeapi.com response.
 *
 * @param int   $status HTTP status.
 * @param mixed $body   Array (JSON-encoded) or raw string.
 * @return void
 */
function kgi_test_queue_response( int $status, $body ): void {
	$GLOBALS['kgi_test_responses'][] = array(
		'status' => $status,
		'body'   => is_array( $body ) ? json_encode( $body ) : (string) $body,
	);
}

/**
 * Returns a US radius response around 89052 (Henderson, NV).
 *
 * @return array<string, mixed>
 */
function kgi_test_us_radius_body(): array {
	return array(
		'zip_codes' => array(
			array( 'zip_code' => '89052', 'distance' => 0, 'city' => 'Henderson', 'state' => 'NV' ),
			array( 'zip_code' => '89053', 'distance' => 1.729, 'city' => 'Henderson', 'state' => 'NV' ),
			array( 'zip_code' => '89014', 'distance' => 6.1, 'city' => 'Henderson', 'state' => 'NV' ),
			array( 'zip_code' => '89012', 'distance' => 3.99, 'city' => 'Henderson', 'state' => 'NV' ),
			array( 'zip_code' => '89074', 'distance' => 5.788, 'city' => 'Henderson', 'state' => 'NV' ),
		),
	);
}

/*
 * ---------------------------------------------------------------------------
 * 1. Request count: owned, unowned, cached.
 * ---------------------------------------------------------------------------
 */

kgi_test_reset();
$result = kgi_lookup_nearby_locations( '89012', 'search' );
kgi_test_assert_same( 'match', $result['status'], 'An owned ZIP must be an exact match.' );
kgi_test_assert_same( 48215, $result['locations'][0]['location_id'] ?? null, 'An owned ZIP must return its owner.' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_requests'] ), 'An owned ZIP must make no API requests.' );

kgi_test_reset();
kgi_test_queue_response( 200, kgi_test_us_radius_body() );
$result = kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 'nearby', $result['status'], 'An unowned ZIP with owners in range must be "nearby".' );
kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'An unowned ZIP must make exactly one API request.' );
kgi_test_assert_same(
	'https://www.zipcodeapi.com/rest/fixture-key/radius.json/89052/60/mile',
	$GLOBALS['kgi_test_requests'][0] ?? '',
	'The US lookup must use the radius endpoint (which includes distances).'
);
kgi_test_assert_same(
	array( 48215, 70001 ),
	array_column( $result['locations'], 'location_id' ),
	'Locations must be sorted closest-first, one row per location.'
);
kgi_test_assert_same( 3.99, $result['locations'][0]['distance'] ?? null, 'A location must use its closest owned ZIP distance.' );
kgi_test_assert_same( '89012', $result['locations'][0]['matched_code'] ?? null, 'The closest owned ZIP must be reported.' );

$result = kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'A repeat lookup must be served from the cache.' );
kgi_test_assert_same( 'nearby', $result['status'], 'A cached lookup must return the same result.' );

/*
 * ---------------------------------------------------------------------------
 * 2. Canadian simple response (postal codes + parallel distances).
 * ---------------------------------------------------------------------------
 */

kgi_test_reset( 'ca' );
kgi_test_queue_response(
	200,
	array(
		'postal_codes' => array( 'L4Z 1T9', 'L7L 4X9', 'M1V 3S2' ),
		'distances'    => array( 0, 19.4, 31.2 ),
	)
);
$result = kgi_lookup_nearby_locations( 'l4z 1t9', 'search' );
kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'A Canadian lookup must make exactly one API request.' );
kgi_test_assert_same(
	true,
	str_ends_with( $GLOBALS['kgi_test_requests'][0] ?? '', 'radius.json/L4Z%201T9/60/mile?simple=true&limit=50000' ),
	'A Canadian lookup must request the simple radius response with distances.'
);
kgi_test_assert_same( array( 20181, 20180 ), array_column( $result['locations'], 'location_id' ), 'Canadian locations must be sorted closest-first.' );
kgi_test_assert_same( 19.4, $result['locations'][0]['distance'] ?? null, 'Canadian distances must come from the parallel distances array.' );

/*
 * ---------------------------------------------------------------------------
 * 3. No match, not found, invalid input.
 * ---------------------------------------------------------------------------
 */

kgi_test_reset();
kgi_test_queue_response( 200, array( 'zip_codes' => array( array( 'zip_code' => '04101', 'distance' => 0 ) ) ) );
$result = kgi_lookup_nearby_locations( '04101', 'search' );
kgi_test_assert_same( 'no_match', $result['status'], 'A ZIP with no owners in range must be "no_match".' );

kgi_test_reset();
kgi_test_queue_response( 404, array( 'error_code' => 404, 'error_msg' => 'Zip code "00001" not found.' ) );
$result = kgi_lookup_nearby_locations( '00001', 'search' );
kgi_test_assert_same( 'no_match', $result['status'], 'An unknown ZIP (404) must be treated as "no_match", not a failure.' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_errors'] ), 'An unknown ZIP must not be logged as an API error.' );
kgi_lookup_nearby_locations( '00001', 'search' );
kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'An unknown ZIP result must be cached.' );

kgi_test_reset();
$result = kgi_lookup_nearby_locations( 'ABC', 'search' );
kgi_test_assert_same( 'invalid', $result['status'], 'A malformed ZIP must be "invalid".' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_requests'] ), 'A malformed ZIP must make no API requests.' );

kgi_test_reset( 'ca' );
$result = kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 'invalid', $result['status'], 'A US ZIP on the Canadian site must be "invalid".' );

/*
 * ---------------------------------------------------------------------------
 * 4. Failures: typed, not cached, logged, cooldown after 429.
 * ---------------------------------------------------------------------------
 */

$failure_fixtures = array(
	'rate_limited'     => array( 429, '{"error_code":429,"error_msg":"Usage limit exceeded."}' ),
	'auth_error'       => array( 401, '{"error_code":401,"error_msg":"The API key was not found."}' ),
	'http_503'         => array( 503, '{}' ),
	'invalid_response' => array( 200, 'not-json' ),
	'provider'         => array( 200, '{"error_msg":"fixture"}' ),
);

foreach ( $failure_fixtures as $expected_type => $fixture ) {
	kgi_test_reset();
	kgi_test_queue_response( $fixture[0], $fixture[1] );
	$result = kgi_lookup_nearby_locations( '89052', 'search' );
	kgi_test_assert_same( 'lookup_failed', $result['status'], "A {$expected_type} response must be \"lookup_failed\"." );
	kgi_test_assert_same( $expected_type, $result['error_type'], "A {$expected_type} response must keep its error type." );
}

kgi_test_reset();
$GLOBALS['kgi_test_responses'][] = new WP_Error();
$result = kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 'transport', $result['error_type'], 'A transport error must be typed "transport".' );

kgi_test_reset();
$GLOBALS['kgi_test_options']['kgi_zipcodeapi_key'] = '';
$result = kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 'missing_api_key', $result['error_type'], 'A missing key must be typed "missing_api_key".' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_requests'] ), 'A missing key must make no API requests.' );

kgi_test_reset();
kgi_test_queue_response( 503, '{}' );
kgi_test_queue_response( 200, kgi_test_us_radius_body() );
kgi_lookup_nearby_locations( '89052', 'search' );
$result = kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 'nearby', $result['status'], 'Failures must never be cached.' );
kgi_test_assert_same( 2, count( $GLOBALS['kgi_test_requests'] ), 'A lookup after a failure must call the API again.' );

kgi_test_reset();
kgi_test_queue_response( 429, '{"error_msg":"Usage limit exceeded."}' );
kgi_lookup_nearby_locations( '89052', 'search' );
$result = kgi_lookup_nearby_locations( '89183', 'form', 5714 );
kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'After a 429, lookups must stop calling the API until the hour resets.' );
kgi_test_assert_same( 'rate_limited', $result['error_type'], 'Lookups during the cooldown must fail as "rate_limited".' );

/*
 * ---------------------------------------------------------------------------
 * 5. Error log and alert emails.
 * ---------------------------------------------------------------------------
 */

kgi_test_reset();
$GLOBALS['kgi_test_options']['kgi_zip_lookup_alert_emails'] = 'alerts@example.test, second@example.test';
kgi_test_queue_response( 429, '{"error_code":429,"error_msg":"Usage limit exceeded."}' );
kgi_lookup_nearby_locations( '89052', 'form', 5714 );
kgi_lookup_nearby_locations( '89183', 'search' );

$log = kgi_get_zip_lookup_log();
kgi_test_assert_same( 2, count( $log ), 'Every failed lookup must be written to the error log.' );
kgi_test_assert_same( 'search', $log[0]['source'] ?? null, 'The log must be newest-first.' );
kgi_test_assert_same( 'form', $log[1]['source'] ?? null, 'The log must record the lookup source.' );
kgi_test_assert_same( '89052', $log[1]['code'] ?? null, 'The log must record the ZIP/postal code.' );
kgi_test_assert_same( 'rate_limited', $log[1]['error_type'] ?? null, 'The log must record the error type.' );
kgi_test_assert_same( 429, $log[1]['http_status'] ?? null, 'The log must record the HTTP status.' );
kgi_test_assert_same( 'Usage limit exceeded.', $log[1]['message'] ?? null, 'The log must record the zipcodeapi.com message.' );
kgi_test_assert_same( 5714, $log[1]['entry_id'] ?? null, 'The log must record the entry ID for form lookups.' );
kgi_test_assert_same( false, str_contains( json_encode( $log ), 'fixture-key' ), 'The log must never contain the API key.' );
kgi_test_assert_same( 2, count( $GLOBALS['kgi_test_errors'] ), 'Every failed lookup must be written to the server error log.' );

kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_mail'] ), 'Alerts must be limited to one email per error type per hour.' );
kgi_test_assert_same(
	array( 'alerts@example.test', 'second@example.test' ),
	$GLOBALS['kgi_test_mail'][0]['to'] ?? null,
	'Alerts must go to every configured address.'
);

kgi_test_reset();
kgi_test_queue_response( 429, '{}' );
kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_mail'] ), 'No alert must be sent when no alert email is configured.' );

kgi_test_reset();
$old_entry = array(
	'time'       => time() - ( 31 * DAY_IN_SECONDS ),
	'error_type' => 'rate_limited',
);
$GLOBALS['kgi_test_options'][ KGI_ZIP_LOOKUP_LOG_OPTION ] = array( $old_entry );
kgi_test_queue_response( 503, '{}' );
kgi_lookup_nearby_locations( '89052', 'search' );
kgi_test_assert_same( 1, count( kgi_get_zip_lookup_log() ), 'Log entries older than 30 days must be removed.' );

/*
 * ---------------------------------------------------------------------------
 * 6. Form routing: failed lookups go to unmatched routing with a reason.
 * ---------------------------------------------------------------------------
 */

kgi_test_reset();
kgi_test_queue_response( 429, '{}' );
$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => '89052' ), get_post( 48215 ), 5714 );
kgi_test_assert_same( null, $routed, 'A failed lookup must send the lead to unmatched routing.' );
kgi_test_assert_same(
	'lookup_failed_rate_limited',
	$GLOBALS['kgi_test_meta'][5714]['kgi_zip_routing_status'] ?? null,
	'A failed lookup must record why the lead was unmatched.'
);
kgi_test_assert_same(
	48215,
	$GLOBALS['kgi_test_meta'][5714]['kgi_original_location_id'] ?? null,
	'A failed lookup must keep the page location for manual routing.'
);

kgi_test_reset();
kgi_test_queue_response( 200, kgi_test_us_radius_body() );
$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => '89052' ), get_post( 48215 ), 5714 );
kgi_test_assert_same( 48215, $routed->ID ?? null, 'A nearby owner must keep a lead with its page location when it is closest.' );

kgi_test_reset();
$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => '89074' ), get_post( 48215 ), 5715 );
kgi_test_assert_same( 70001, $routed->ID ?? null, 'An exactly owned ZIP must route to its owner with no API request.' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_requests'] ), 'Exact ownership must not call the API.' );

kgi_test_reset();
kgi_test_queue_response( 200, array( 'zip_codes' => array( array( 'zip_code' => '04101', 'distance' => 0 ) ) ) );
$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => '04101' ), get_post( 48215 ), 5716 );
kgi_test_assert_same( null, $routed, 'A ZIP with no owner in range must go to unmatched routing.' );
kgi_test_assert_same(
	'no_location_in_range',
	$GLOBALS['kgi_test_meta'][5716]['kgi_zip_routing_status'] ?? null,
	'An out-of-range ZIP must record "no_location_in_range".'
);

// Prefer any owned code of the original location, even when another is closer.
foreach ( array( 'us', 'ca' ) as $country ) {
	foreach ( array( 33.0, 60.0, 60.01 ) as $distance ) {
		kgi_test_reset( $country );
		$page_id = 'ca' === $country ? 20180 : 70001;
		$near_id = 'ca' === $country ? 20181 : 48215;
		$code = 'ca' === $country ? 'l4z 1t9' : '89052';
		$body = 'ca' === $country
			? array( 'postal_codes' => array( 'L7L 4X9', 'M1V 3S2' ), 'distances' => array( 3.0, $distance ) )
			: array( 'zip_codes' => array( array( 'zip_code' => '89012', 'distance' => 3.0 ), array( 'zip_code' => '89074', 'distance' => $distance ) ) );
		kgi_test_queue_response( 200, $body );
		$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => $code ), get_post( $page_id ), 5800 );
		kgi_test_assert_same( $distance <= 60 ? $page_id : $near_id, $routed->ID ?? null, 'Page location must win when any owned code is within 60 miles, regardless of office coordinates.' );
		kgi_test_assert_same( $distance <= 60 ? 'page_radius' : 'nearest_owned_zip', gform_get_meta( 5800, 'kgi_zip_routing_status' ), 'Routing reason must reflect owned-code preference.' );
		kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'Routing must make only one radius request and no coordinate request.' );
		$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => $code ), get_post( $page_id ), 5801 );
		kgi_test_assert_same( 1, count( $GLOBALS['kgi_test_requests'] ), 'Repeated routing must reuse cached radius data.' );
	}
	kgi_test_reset( $country );
	$owned = 'ca' === $country ? 'l7l 4x9' : '89012';
	$owner = 'ca' === $country ? 20181 : 48215;
	$page = 'ca' === $country ? 20180 : 70001;
	$routed = kgi_resolve_location_for_entry_zip( array( 'form_id' => 13, '6' => $owned ), get_post( $page ), 5802 );
	kgi_test_assert_same( $owner, $routed->ID ?? null, 'Exact ownership must still override the page location.' );
	kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_requests'] ), 'Exact owners must require no API request.' );
}

// Exercise the real background job, with all outbound delivery mocked.
class GFAPI {
	public static function get_entry( $id ) {
		return array( 'id' => $id, 'form_id' => 13, '6' => '89052', 'date_created' => '2026-10-16 12:00:00' );
	}
}
function absint( $value ) { return abs( (int) $value ); }
function kgi_notify_unresolved_lead( $id, $entry ) {}
function kgi_build_location_payload( $location ) { return array( 'routed_id' => $location->ID ); }
function kgi_build_unresolved_location_payload() { return array( 'routed_id' => 0 ); }
function kgi_build_entry_payload( $entry, $map ) { return array(); }
function kgi_get_tracking_field_keys() { return array(); }
function wp_remote_post( $url, $args ) {
	$GLOBALS['kgi_test_payload'] = json_decode( $args['body'], true );
	return array( 'status' => 200 );
}
require_once dirname( __DIR__ ) . '/includes/jobs/background-jobs.php';
kgi_test_reset();
gform_update_meta( 5900, 'kgi_location_source', 'unresolved' );
$GLOBALS['kgi_test_options']['kgi_n8n_webhook_url'] = 'https://example.test/webhook';
kgi_test_queue_response( 200, kgi_test_us_radius_body() );
kgi_process_quote_entry_job( 5900 );
kgi_test_assert_same( 'succeeded', gform_get_meta( 5900, 'kgi_submission_status' ), 'No-page background job must deliver successfully.' );
kgi_test_assert_same( 48215, $GLOBALS['kgi_test_payload']['routed_id'] ?? null, 'No-page background job must deliver to nearest owner.' );
kgi_test_assert_same( false, $GLOBALS['kgi_test_payload']['needs_review'] ?? null, 'Resolved no-page leads must clear review flags.' );

kgi_test_reset();
gform_update_meta( 5901, 'kgi_location_source', 'unresolved' );
$GLOBALS['kgi_test_options']['kgi_n8n_webhook_url'] = 'https://example.test/webhook';
$GLOBALS['kgi_test_options']['kgi_unresolved_serviceminder_api_key'] = 'fixture-parking-key';
kgi_test_queue_response( 200, array( 'zip_codes' => array() ) );
kgi_process_quote_entry_job( 5901 );
kgi_test_assert_same( 'succeeded', gform_get_meta( 5901, 'kgi_submission_status' ), 'No qualifying franchise must deliver to parking-lot routing.' );
kgi_test_assert_same( 0, $GLOBALS['kgi_test_payload']['routed_id'] ?? null, 'Parking-lot payload must be used when no owner qualifies.' );
kgi_test_assert_same( true, $GLOBALS['kgi_test_payload']['needs_review'] ?? null, 'Parking-lot delivery must retain review flag.' );

// Missing parking-lot credentials hold the lead without outbound delivery.
kgi_test_reset();
gform_update_meta( 5902, 'kgi_location_source', 'unresolved' );
kgi_test_queue_response( 200, array( 'zip_codes' => array() ) );
kgi_process_quote_entry_job( 5902 );
kgi_test_assert_same( 'held_for_review', gform_get_meta( 5902, 'kgi_submission_status' ), 'Missing parking-lot credentials must hold the lead.' );
kgi_test_assert_same( null, $GLOBALS['kgi_test_payload'], 'Held leads must not send an outbound payload.' );

// Successful entries remain idempotent and must not send a second time.
kgi_test_reset();
gform_update_meta( 5903, 'kgi_submission_status', 'succeeded' );
kgi_process_quote_entry_job( 5903 );
kgi_test_assert_same( null, $GLOBALS['kgi_test_payload'], 'Successful entries must not be delivered again.' );
kgi_test_assert_same( 0, count( $GLOBALS['kgi_test_requests'] ), 'Successful entries must not redo ZIP lookups.' );

/*
 * ---------------------------------------------------------------------------
 * 7. Search-bar responses and per-visitor rate limit.
 * ---------------------------------------------------------------------------
 */

kgi_test_reset();
kgi_test_queue_response( 200, kgi_test_us_radius_body() );
$response = kgi_build_location_search_response( '89052', '203.0.113.5' );
kgi_test_assert_same( 'nearby', $response['status'], 'The search response must pass through the lookup status.' );
kgi_test_assert_same( 'las-vegas', $response['locations'][0]['slug'] ?? null, 'The search response must include location details.' );
kgi_test_assert_same( 3.99, $response['locations'][0]['distance'] ?? null, 'The search response must include the distance.' );
kgi_test_assert_same( '89123', $response['locations'][0]['zipcode'] ?? null, 'The search response must include the location\'s main ZIP code.' );
kgi_test_assert_same( '89012', $response['locations'][0]['matched_code'] ?? null, 'The search response must include the closest owned ZIP code.' );
kgi_test_assert_same( false, array_key_exists( 'key', $response['locations'][0] ?? array() ), 'The search response must not expose location API keys.' );

kgi_test_reset();
kgi_test_queue_response( 429, '{}' );
$response = kgi_build_location_search_response( '89052', '203.0.113.5' );
kgi_test_assert_same( 'lookup_failed', $response['status'], 'A failed search must report "lookup_failed".' );
kgi_test_assert_same(
	"We're having trouble looking up your area right now. Please try again later.",
	$response['message'],
	'A failed search must show the friendly try-again message.'
);

kgi_test_reset();
$response = kgi_build_location_search_response( '8905', '203.0.113.5' );
kgi_test_assert_same( 'Please enter a valid 5-digit ZIP code.', $response['message'], 'US invalid input must ask for a ZIP code.' );

kgi_test_reset( 'ca' );
$response = kgi_build_location_search_response( 'L4Z', '203.0.113.5' );
kgi_test_assert_same( 'Please enter a valid postal code, like A1A 1A1.', $response['message'], 'Canadian invalid input must ask for a postal code.' );

kgi_test_reset();
for ( $i = 0; $i < KGI_LOCATION_SEARCH_RATE_LIMIT; $i++ ) {
	kgi_build_location_search_response( '89012', '203.0.113.9' );
}
$response = kgi_build_location_search_response( '89012', '203.0.113.9' );
kgi_test_assert_same( 'rate_limited', $response['status'], 'Too many searches from one visitor must be blocked.' );
$response = kgi_build_location_search_response( '89012', '203.0.113.10' );
kgi_test_assert_same( 'match', $response['status'], 'The rate limit must be per visitor.' );

/*
 * ---------------------------------------------------------------------------
 * 8. The per-ZIP distance endpoint must never be called.
 * ---------------------------------------------------------------------------
 */

kgi_test_assert_same( false, function_exists( 'kgi_get_zip_pair_distance' ), 'The per-ZIP distance lookup must be removed.' );

if ( $kgi_test_failures > 0 ) {
	fwrite( STDERR, PHP_EOL . $kgi_test_failures . ' check(s) failed.' . PHP_EOL );
	exit( 1 );
}

echo "ZIP lookup regression checks passed ({$kgi_test_assertions} assertions).\n";
