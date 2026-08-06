<?php
/**
 * Entry and location payload builders for n8n.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the customer data payload from a Gravity Forms entry.
 *
 * Translates GF field IDs into the payload keys expected by n8n, using the
 * field map that applies to whichever form the entry came from (see
 * `kgi_get_field_map_for_form()` — the main quote form uses the plugin-wide
 * `kgi_field_map` option, fixed-location forms use their own field map).
 * Unmapped fields are omitted from the payload.
 *
 * @since 0.1.0
 *
 * @param mixed[]               $entry     Gravity Forms entry array.
 * @param array<string, string> $field_map Payload key => GF field ID map for this entry's form.
 * @return array<string, string> Payload of customer data keyed by n8n field name.
 */
function kgi_build_entry_payload( array $entry, array $field_map ): array {
	$payload = array();

	foreach ( $field_map as $payload_key => $field_id ) {
		if ( '' === $field_id ) {
			continue;
		}
		$value = rgar( $entry, $field_id );

		if ( 'mobile_number' === $payload_key ) {
			$value = kgi_strip_phone_country_code( $value );
		}

		$payload[ $payload_key ] = $value;
	}

	return $payload;
}

/**
 * Strips a leading single-digit country code from a validated phone value.
 *
 * The quote form's phone field accepts both `(999) 999-9999` and
 * `9 (999) 999-9999` (see kgi_validate_phone_fields()). Downstream systems
 * (ServiceMinder) expect a 10-digit number, so the leading country code is
 * removed here rather than passed through to n8n.
 *
 * @since 0.1.0
 *
 * @param string $value Raw phone value from the entry.
 * @return string Phone value without a leading country code.
 */
function kgi_strip_phone_country_code( string $value ): string {
	return (string) preg_replace( '/^\d \(/', '(', trim( $value ) );
}

/**
 * Builds the customer data payload for the Google Sheet logging webhook.
 *
 * Reuses the same field-mapped entry data sent to n8n (kgi_build_entry_payload()),
 * relabeled to match the key names the receiving Google Sheet expects, plus
 * the page URL captured on the form and a send timestamp/source.
 *
 * @since 0.1.0
 *
 * @param mixed[]               $entry              Gravity Forms entry array.
 * @param array<string, string> $field_map          Payload key => GF field ID map for this entry's form.
 * @param int                   $page_url_field_id  GF field ID holding the captured page URL, or 0 if unmapped.
 * @return array<string, string> Payload keyed by Google Sheet column name.
 */
function kgi_build_google_sheet_payload( array $entry, array $field_map, int $page_url_field_id ): array {
	$entry_payload = kgi_build_entry_payload( $entry, $field_map );

	$key_renames = array(
		'UtmSource'   => 'utm_source',
		'UtmMedium'   => 'utm_medium',
		'UtmCampaign' => 'utm_campaign',
		'UtmTerm'     => 'utm_term',
		'UtmContent'  => 'utm_content',
	);

	$payload = array();

	foreach ( $entry_payload as $key => $value ) {
		$payload[ $key_renames[ $key ] ?? $key ] = $value;
	}

	$payload['page_url']  = rgar( $entry, (string) $page_url_field_id );
	$payload['timestamp'] = current_time( 'mysql' );
	$payload['source']    = 'wordpress'; // phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText -- data value expected by the receiving sheet, not text.

	return $payload;
}

/**
 * Builds an empty location payload for a lead with no resolvable location.
 *
 * Keeps the same keys as `kgi_build_location_payload()` so the n8n webhook
 * always receives a consistent shape — the values are just empty. Paired with
 * the `location_found`/`location_source` flags added by the background job, this
 * lets the n8n workflow detect a "no location found" lead and route it to an
 * alert (e.g. Slack) instead of a franchise CRM.
 *
 * @since 0.7.0
 *
 * @return mixed[] Location payload with empty values.
 */
function kgi_build_unresolved_location_payload(): array {
	return array(
		'location_id'                    => null,
		'location_name'                  => '',
		'housecall_pro_api_key'          => '',
		'location_serviceminder_api_key' => '',
		'location_serviceminder_id'      => '',
	);
}

/**
 * Builds the location data payload for outbound API calls.
 *
 * Fetches all per-location credentials from ACF fields. Called by the
 * background job when sending the entry to n8n.
 *
 * @since 0.1.0
 *
 * @param WP_Post $location The resolved location CPT post.
 * @return mixed[] Associative array of location data and API credentials.
 */
function kgi_build_location_payload( WP_Post $location ): array {
	$location_id = $location->ID;

	return array(
		'location_id'                    => $location_id,
		'location_name'                  => get_field( 'location_name', $location_id ),
		'housecall_pro_api_key'          => get_field( 'housecall_pro_api_key', $location_id ),
		'location_serviceminder_api_key' => get_field( 'location_serviceminder_api_key', $location_id ),
		'location_serviceminder_id'      => get_field( 'location_serviceminder_id', $location_id ),
	);
}
