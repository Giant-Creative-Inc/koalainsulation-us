<?php
/**
 * Frontend asset registration for the quote form and its thank-you page.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the form and thank-you page asset enqueue hooks.
 *
 * @since 0.1.0
 */
function kgi_register_form_asset_hooks(): void {
	foreach ( kgi_get_all_quote_form_ids() as $form_id ) {
		add_action(
			'gform_enqueue_scripts_' . $form_id,
			'kgi_enqueue_quote_form_assets',
			10,
			1
		);
	}

	foreach ( kgi_get_fixed_location_forms() as $config ) {
		$form_id = (int) ( $config['form_id'] ?? 0 );

		if ( $form_id <= 0 ) {
			continue;
		}

		add_action(
			'gform_enqueue_scripts_' . $form_id,
			'kgi_enqueue_quote_form_assets',
			10,
			1
		);
	}

	add_action( 'wp_enqueue_scripts', 'kgi_maybe_enqueue_thank_you_datalayer_push' );
	add_action( 'wp_enqueue_scripts', 'kgi_enqueue_attribution_capture' );
}

/**
 * Gravity Forms field map keys considered safe to expose in the dataLayer.
 *
 * Deliberately a subset of `kgi_field_map` — excludes PII (name, email,
 * phone, consent flags) so the dataLayer push never carries personal data,
 * only attribution/location context.
 *
 * @since 0.1.0
 */
const KGI_DATALAYER_FIELD_MAP_KEYS = array(
	'zip',
	'city',
	'state',
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
	'service',
	'cta_text',
);

/**
 * Enqueues the quote form stylesheet and validation script.
 *
 * Hooked to `gform_enqueue_scripts_{form_id}` for every dynamic quote form
 * and every configured fixed-location form, which fires once per matching
 * form ID actually present on the current page — so if a page has more than
 * one of this plugin's forms (e.g. an inline quote form plus a different
 * Gravity Form in a popup), this can run more than once per page load.
 *
 * `wp_localize_script()` fully overwrites the JS object on each call rather
 * than merging — a second call with only that second form's data would wipe
 * out the first form's, leaving the client-side phone mask and ZIP
 * formatter working for only whichever form's hook happened to fire last.
 * So instead of localizing data for just the `$form` that triggered this
 * particular call, every call rebuilds and localizes the *complete* map for
 * every form this plugin manages — reading from options directly rather
 * than the `$form` argument — so the result is identical and correct
 * regardless of which form triggered it or how many times this runs.
 *
 * @since 0.1.0
 *
 * @param mixed[] $form Gravity Forms form array for the form that triggered this enqueue. Unused — kept for hook signature compatibility.
 */
function kgi_enqueue_quote_form_assets( array $form = array() ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	wp_enqueue_style(
		'kgi-quote-form',
		plugins_url( 'assets/css/quote-form.css', KGI_PLUGIN_FILE ),
		array(),
		KGI_VERSION
	);

	wp_enqueue_script(
		'kgi-form-validation',
		plugins_url( 'assets/js/form-validation.js', KGI_PLUGIN_FILE ),
		array( 'jquery' ),
		KGI_VERSION,
		true
	);

	$form_ids           = kgi_get_all_quote_form_ids();
	$zip_field_ids      = array();
	$location_field_ids = array();

	foreach ( $form_ids as $id ) {
		$zip_field_id = kgi_get_field_map_for_form( $id )['zip'] ?? '';

		if ( '' !== $zip_field_id ) {
			$zip_field_ids[ $id ] = $zip_field_id;
		}

		$location_field_ids[ $id ] = array(
			'locationSlug' => kgi_get_location_field_id_for_form( $id, 'location_slug' ),
			'locationId'   => kgi_get_location_field_id_for_form( $id, 'location_id' ),
			'pageUrl'      => kgi_get_location_field_id_for_form( $id, 'page_url' ),
		);
	}

	foreach ( kgi_get_fixed_location_forms() as $config ) {
		$fixed_form_id = (int) ( $config['form_id'] ?? 0 );

		if ( $fixed_form_id <= 0 ) {
			continue;
		}

		$form_ids[]   = $fixed_form_id;
		$zip_field_id = $config['field_map']['zip'] ?? '';

		if ( '' !== $zip_field_id ) {
			$zip_field_ids[ $fixed_form_id ] = $zip_field_id;
		}
	}

	$form_ids           = array_values( array_unique( $form_ids ) );
	$tracking_field_ids = array();

	foreach ( $form_ids as $id ) {
		$tracking = kgi_get_tracking_field_ids_for_form( (int) $id );

		if ( ! empty( $tracking ) ) {
			$tracking_field_ids[ $id ] = $tracking;
		}
	}

	wp_localize_script(
		'kgi-form-validation',
		'kgiData',
		array(
			'formIds'          => $form_ids,
			'zipFieldIds'      => $zip_field_ids,
			'locationFieldIds' => $location_field_ids,
			'trackingFieldIds' => $tracking_field_ids,
		)
	);
}

/**
 * Enqueues the attribution capture/fill script on every frontend page.
 *
 * Loaded site-wide (not just on form pages) because first-touch attribution —
 * the click IDs, landing page, and referrer — must be captured on the visitor's
 * *landing* page, which is frequently not the page the form lives on. The
 * script writes a first-party `kgi_attrib` cookie on first visit and later
 * fills whichever hidden tracking fields the current page's form(s) have mapped
 * (it reads the field IDs from the `kgiData` object localized in
 * `kgi_enqueue_quote_form_assets()`, and is a no-op for fields/forms that
 * aren't present).
 *
 * Populating these client-side is required by this site's full-page caching:
 * server-side render injection would bake one visitor's values into the shared
 * cached HTML. A static enqueued script is itself cache-safe.
 *
 * @since 0.7.0
 */
function kgi_enqueue_attribution_capture(): void {
	wp_enqueue_script(
		'kgi-attribution',
		plugins_url( 'assets/js/attribution.js', KGI_PLUGIN_FILE ),
		array(),
		KGI_VERSION,
		true
	);
}

/**
 * Enqueues the dataLayer push script on the thank-you page, if this request
 * is a valid signed redirect for a quote entry.
 *
 * The thank-you page is a plain WP page with no direct knowledge of the
 * submitted entry — the only link back to it is the signed `kgi_entry`/
 * `kgi_sig` query args in its own URL (see kgi_redirect_after_quote_submission()
 * and kgi_sign_entry_id()), the same scheme already used to trigger the
 * background job. Reloading the entry here lets the push happen on the
 * thank-you page itself instead of before the redirect away from the form.
 *
 * Hooked to `wp_enqueue_scripts`, so it runs on every frontend page load but
 * bails immediately unless the signed args are present and valid.
 *
 * @since 0.1.0
 */
function kgi_maybe_enqueue_thank_you_datalayer_push(): void {
	if ( ! isset( $_GET['kgi_entry'], $_GET['kgi_sig'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$entry_id = absint( $_GET['kgi_entry'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sig      = sanitize_text_field( wp_unslash( $_GET['kgi_sig'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( $entry_id <= 0 || ! hash_equals( kgi_sign_entry_id( $entry_id ), $sig ) ) {
		return;
	}

	$entry = GFAPI::get_entry( $entry_id );

	if ( is_wp_error( $entry ) ) {
		return;
	}

	$field_map = kgi_get_field_map_for_form( (int) $entry['form_id'] );
	$payload   = array(
		'event'        => 'quote_form_submission',
		'locationName' => (string) gform_get_meta( $entry_id, 'kgi_location_name' ),
		'locationId'   => (int) gform_get_meta( $entry_id, 'kgi_location_id' ),
	);

	foreach ( KGI_DATALAYER_FIELD_MAP_KEYS as $key ) {
		if ( empty( $field_map[ $key ] ) ) {
			continue;
		}

		$payload[ $key ] = rgar( $entry, $field_map[ $key ] );
	}

	wp_enqueue_script(
		'kgi-thank-you-datalayer',
		plugins_url( 'assets/js/thank-you-datalayer.js', KGI_PLUGIN_FILE ),
		array(),
		KGI_VERSION,
		true
	);

	wp_localize_script(
		'kgi-thank-you-datalayer',
		'kgiThankYouData',
		array(
			'entryId' => $entry_id,
			'payload' => $payload,
		)
	);
}
