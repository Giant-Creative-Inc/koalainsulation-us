<?php
/**
 * Plugin-wide constants and configuration helpers.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KGI_VERSION', '0.7.3' );
define( 'KGI_PLUGIN_FILE', dirname( __DIR__ ) . '/koala-gravity-integration.php' );
define( 'KGI_PLUGIN_DIR', plugin_dir_path( KGI_PLUGIN_FILE ) );
define( 'KGI_PLUGIN_BASENAME', plugin_basename( KGI_PLUGIN_FILE ) );
define( 'KGI_DEFAULT_NOTIFICATION_EMAIL', 'marketingteam@koalainsulation.com' );
/**
 * Returns the configured quote form ID from WordPress options.
 *
 * @since 0.1.0
 *
 * @return int Form ID, or 0 if not yet configured.
 */
function kgi_get_quote_form_id(): int {
	return (int) get_option( 'kgi_quote_form_id', 0 );
}

/**
 * Returns the configured franchise location custom post type slug.
 *
 * @since 0.1.0
 *
 * @return string Post type slug, defaults to 'location' if not yet configured.
 */
function kgi_get_location_post_type(): string {
	$post_type = get_option( 'kgi_location_post_type', 'location' );

	return $post_type ? $post_type : 'location';
}

/**
 * Default Gravity Forms field IDs for the location routing fields.
 *
 * Used as the fallback when the admin hasn't (re)saved the location field
 * map yet, so upgrades from the previous hardcoded constants behave the same.
 *
 * @since 0.1.0
 */
const KGI_DEFAULT_LOCATION_FIELD_MAP = array(
	'location_slug' => 17,
	'location_id'   => 18,
	'page_url'      => 19,
);

/**
 * Returns the configured Gravity Forms field ID for a location routing field.
 *
 * @since 0.1.0
 *
 * @param string $key One of 'location_slug', 'location_id', 'page_url'.
 * @return int Field ID, or 0 if not mapped and no default exists.
 */
function kgi_get_location_field_id( string $key ): int {
	$map = get_option( 'kgi_location_field_map', KGI_DEFAULT_LOCATION_FIELD_MAP );

	if ( isset( $map[ $key ] ) && '' !== $map[ $key ] ) {
		return absint( $map[ $key ] );
	}

	return absint( KGI_DEFAULT_LOCATION_FIELD_MAP[ $key ] ?? 0 );
}

/**
 * Returns the configured list of additional dynamic quote forms.
 *
 * Each entry behaves exactly like the main Quote Form — location is
 * resolved dynamically from the request URL on every submission, not fixed
 * to one location — but is a distinct Gravity Form with its own field IDs.
 * Use this for a duplicate of the quote form embedded elsewhere on the same
 * page (e.g. inside a popup), so the two never share a DOM ID. Structure per
 * entry:
 *   - form_id             (int)   Gravity Forms form ID.
 *   - location_field_map  (array) Same shape as `kgi_location_field_map`, scoped to this form's own field IDs.
 *   - field_map           (array) Same shape as `kgi_field_map`, scoped to this form's own field IDs.
 *
 * @since 0.4.0
 *
 * @return array<int, array<string, mixed>> List of additional quote form configs.
 */
function kgi_get_additional_quote_forms(): array {
	return get_option( 'kgi_additional_quote_forms', array() );
}

/**
 * Finds the additional quote form config for a given Gravity Forms form ID.
 *
 * @since 0.4.0
 *
 * @param int $form_id Gravity Forms form ID.
 * @return mixed[]|null The matching config, or null if this form ID isn't configured.
 */
function kgi_get_additional_quote_form_config( int $form_id ): ?array {
	foreach ( kgi_get_additional_quote_forms() as $config ) {
		if ( (int) ( $config['form_id'] ?? 0 ) === $form_id ) {
			return $config;
		}
	}

	return null;
}

/**
 * Returns every Gravity Forms form ID that behaves as a dynamic quote form —
 * the main Quote Form plus every configured additional quote form.
 *
 * @since 0.4.0
 *
 * @return int[] Form IDs.
 */
function kgi_get_all_quote_form_ids(): array {
	$ids = array( kgi_get_quote_form_id() );

	foreach ( kgi_get_additional_quote_forms() as $config ) {
		$ids[] = (int) ( $config['form_id'] ?? 0 );
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Returns the configured Gravity Forms field ID for a location routing
 * field, scoped to a specific dynamic quote form.
 *
 * The main Quote Form uses the plugin-wide `kgi_location_field_map` option
 * (with the legacy hardcoded field IDs as a fallback, see
 * `kgi_get_location_field_id()`). Any additional quote form uses its own
 * `location_field_map` instead, since field IDs are specific to each form's
 * own structure — there's no shared fallback to use there.
 *
 * @since 0.4.0
 *
 * @param int    $form_id Gravity Forms form ID.
 * @param string $key     One of 'location_slug', 'location_id', 'page_url'.
 * @return int Field ID, or 0 if not mapped.
 */
function kgi_get_location_field_id_for_form( int $form_id, string $key ): int {
	if ( kgi_get_quote_form_id() === $form_id ) {
		return kgi_get_location_field_id( $key );
	}

	$config = kgi_get_additional_quote_form_config( $form_id );
	$map    = $config['location_field_map'] ?? array();

	return isset( $map[ $key ] ) && '' !== $map[ $key ] ? absint( $map[ $key ] ) : 0;
}

/**
 * Returns the configured list of fixed-location forms.
 *
 * Each entry routes a distinct Gravity Form to a single, predetermined
 * franchise location (set on a per-location landing page) rather than
 * resolving the location dynamically from the request URL like the main
 * quote form does. Structure per entry:
 *   - form_id            (int)    Gravity Forms form ID.
 *   - location_id        (int)    Post ID of the franchise location this form always routes to.
 *   - field_map           (array) Same shape as `kgi_field_map`, but scoped to this form's own field IDs.
 *   - page_url_field_id   (int)   Optional hidden field ID to capture the page URL for logging. 0 if unused.
 *
 * @since 0.2.0
 *
 * @return array<int, array<string, mixed>> List of fixed-location form configs.
 */
function kgi_get_fixed_location_forms(): array {
	return get_option( 'kgi_fixed_location_forms', array() );
}

/**
 * Finds the fixed-location form config for a given Gravity Forms form ID.
 *
 * @since 0.2.0
 *
 * @param int $form_id Gravity Forms form ID.
 * @return mixed[]|null The matching config, or null if this form ID isn't configured.
 */
function kgi_get_fixed_location_form_config( int $form_id ): ?array {
	foreach ( kgi_get_fixed_location_forms() as $config ) {
		if ( (int) ( $config['form_id'] ?? 0 ) === $form_id ) {
			return $config;
		}
	}

	return null;
}

/**
 * Returns the n8n payload field map that applies to a given Gravity Forms form.
 *
 * The main quote form uses the plugin-wide `kgi_field_map` option; an
 * additional quote form (see `kgi_get_additional_quote_forms()`) or a
 * fixed-location form uses its own field map instead, since field IDs are
 * specific to each form's own structure.
 *
 * @since 0.2.0
 *
 * @param int $form_id Gravity Forms form ID.
 * @return array<string, string> Payload field map, or an empty array if the form isn't configured.
 */
function kgi_get_field_map_for_form( int $form_id ): array {
	if ( kgi_get_quote_form_id() === $form_id ) {
		return get_option( 'kgi_field_map', array() );
	}

	$additional_config = kgi_get_additional_quote_form_config( $form_id );

	if ( null !== $additional_config ) {
		return $additional_config['field_map'] ?? array();
	}

	$config = kgi_get_fixed_location_form_config( $form_id );

	return $config['field_map'] ?? array();
}

/**
 * Returns the page URL hidden field ID that applies to a given Gravity Forms form.
 *
 * @since 0.2.0
 *
 * @param int $form_id Gravity Forms form ID.
 * @return int Field ID, or 0 if unmapped/unconfigured.
 */
function kgi_get_page_url_field_id_for_form( int $form_id ): int {
	if ( kgi_get_quote_form_id() === $form_id || null !== kgi_get_additional_quote_form_config( $form_id ) ) {
		return kgi_get_location_field_id_for_form( $form_id, 'page_url' );
	}

	$config = kgi_get_fixed_location_form_config( $form_id );

	return absint( $config['page_url_field_id'] ?? 0 );
}

/**
 * Returns the configured default (overflow) franchise location.
 *
 * A quote submission whose location can't be resolved from the request URL or
 * the submitted ZIP is routed here instead of being rejected, so no lead is
 * lost (see `kgi_handle_quote_form_submission()`). Returns null when no default
 * has been configured, in which case such a lead is captured and flagged for
 * manual routing rather than sent onward.
 *
 * @since 0.7.0
 *
 * @return WP_Post|null The default location post, or null if unset/invalid.
 */
function kgi_get_default_location(): ?WP_Post {
	$location_id = (int) get_option( 'kgi_default_location_id', 0 );

	if ( $location_id <= 0 ) {
		return null;
	}

	$post = get_post( $location_id );

	return ( $post instanceof WP_Post && kgi_get_location_post_type() === $post->post_type ) ? $post : null;
}

/**
 * Returns the email address that receives unresolved-lead notifications.
 *
 * Falls back to the Koala marketing team when no dedicated address is
 * configured.
 *
 * @since 0.7.0
 *
 * @return string Email address.
 */
function kgi_get_notification_email(): string {
	$email = get_option( 'kgi_notification_email', '' );
	$email = is_string( $email ) ? trim( $email ) : '';

	return '' !== $email ? $email : KGI_DEFAULT_NOTIFICATION_EMAIL;
}

/**
 * Returns the attribution/tracking payload keys populated client-side.
 *
 * These are a subset of `kgi_get_payload_field_labels()` (so they map and flow
 * to n8n/Google Sheets like any other payload field), but unlike the rest they
 * are filled by `assets/js/attribution.js` in the browser rather than by the
 * user typing them — the click IDs, landing page, referrer, etc. needed for
 * attribution and offline-conversion uploads. Populating them client-side is a
 * deliberate requirement of this site's full-page caching (see the plugin
 * README): server-side render injection would bake one visitor's values into
 * the shared cached HTML.
 *
 * @since 0.7.0
 *
 * @return string[] Tracking payload keys.
 */
function kgi_get_tracking_field_keys(): array {
	return array(
		'UtmSource',
		'UtmMedium',
		'UtmCampaign',
		'UtmTerm',
		'UtmContent',
		'gclid',
		'gbraid',
		'wbraid',
		'fbclid',
		'msclkid',
		'landing_page',
		'referrer',
		'form_timestamp',
		'service',
		'cta_text',
	);
}

/**
 * Returns the mapped Gravity Forms field IDs for a form's tracking fields.
 *
 * Filters the form's own payload field map (see `kgi_get_field_map_for_form()`)
 * down to the tracking keys that are actually mapped to a field, so the value
 * can be localized to `assets/js/attribution.js` — which needs to know which
 * hidden input on each form receives each tracking value. Unmapped tracking
 * keys are omitted.
 *
 * @since 0.7.0
 *
 * @param int $form_id Gravity Forms form ID.
 * @return array<string, string> Tracking key => GF field ID.
 */
function kgi_get_tracking_field_ids_for_form( int $form_id ): array {
	$field_map     = kgi_get_field_map_for_form( $form_id );
	$tracking_keys = kgi_get_tracking_field_keys();
	$ids           = array();

	foreach ( $tracking_keys as $key ) {
		if ( isset( $field_map[ $key ] ) && '' !== $field_map[ $key ] ) {
			$ids[ $key ] = $field_map[ $key ];
		}
	}

	return $ids;
}

/**
 * Returns the URL path segment that precedes the location slug.
 *
 * Driven by the Country setting (Settings → Koala Gravity Integration):
 * empty for US (no prefix, /{location-slug}), "ca" for Canada
 * (/ca/{location-slug}), or the configured country slug for Other
 * (/{country-slug}/{location-slug}). Falls back to no prefix if Country is
 * "Other" but no country slug has been set.
 *
 * Shared by the inbound location resolver and the outbound thank-you URL
 * builder so both agree on the same URL structure.
 *
 * @since 0.1.0
 *
 * @return string The country prefix segment, or '' for no prefix.
 */
function kgi_get_country_url_prefix(): string {
	$country = get_option( 'kgi_country', 'us' );

	if ( 'ca' === $country ) {
		return 'ca';
	}

	if ( 'other' === $country ) {
		$country_slug = get_option( 'kgi_country_slug', '' );

		return $country_slug ? sanitize_title( $country_slug ) : '';
	}

	return '';
}
