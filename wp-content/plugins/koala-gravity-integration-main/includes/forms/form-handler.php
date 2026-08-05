<?php
/**
 * Quote form submission handler and validators.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Gravity Forms validation and submission hooks for the quote form.
 *
 * @since 0.1.0
 */
function kgi_register_form_hooks(): void {
	foreach ( kgi_get_all_quote_form_ids() as $form_id ) {
		add_filter(
			'gform_validation_' . $form_id,
			'kgi_validate_quote_form_location'
		);

		add_filter(
			'gform_validation_' . $form_id,
			'kgi_validate_phone_fields',
			20
		);

		add_action(
			'gform_after_submission_' . $form_id,
			'kgi_handle_quote_form_submission',
			10,
			2
		);

		add_filter(
			'gform_confirmation_' . $form_id,
			'kgi_redirect_after_quote_submission',
			10,
			3
		);
	}

	foreach ( kgi_get_fixed_location_forms() as $config ) {
		$fixed_form_id = (int) ( $config['form_id'] ?? 0 );

		if ( $fixed_form_id <= 0 ) {
			continue;
		}

		add_filter(
			'gform_validation_' . $fixed_form_id,
			'kgi_validate_phone_fields',
			20
		);

		add_action(
			'gform_after_submission_' . $fixed_form_id,
			'kgi_handle_fixed_location_form_submission',
			10,
			2
		);

		add_filter(
			'gform_confirmation_' . $fixed_form_id,
			'kgi_redirect_after_fixed_location_form_submission',
			10,
			3
		);
	}
}

/**
 * Handles a validated quote form submission.
 *
 * Resolves the franchise location, stores routing metadata on the entry,
 * and queues the entry for background processing.
 *
 * @since 0.1.0
 *
 * @param mixed[] $entry Gravity Forms entry array.
 * @param mixed[] $form  Gravity Forms form array.
 */
function kgi_handle_quote_form_submission( array $entry, array $form ): void {
	$entry_id = (int) $entry['id'];
	$form_id  = (int) $form['id'];
	$location = kgi_get_location_from_entry( $entry );

	if ( ! $location ) {
		gform_update_meta( $entry_id, 'kgi_location_status', 'missing_location' );
		gform_update_meta( $entry_id, 'kgi_submission_status', 'blocked_missing_location' );

		kgi_log(
			'Location could not be resolved.',
			array(
				'entry_id'      => $entry_id,
				'location_id'   => rgar( $entry, (string) kgi_get_location_field_id_for_form( $form_id, 'location_id' ) ),
				'location_slug' => rgar( $entry, (string) kgi_get_location_field_id_for_form( $form_id, 'location_slug' ) ),
				'page_url'      => rgar( $entry, (string) kgi_get_location_field_id_for_form( $form_id, 'page_url' ) ),
			)
		);

		return;
	}

	$location_id   = $location->ID;
	$location_name = get_field( 'location_name', $location_id );

	gform_update_meta( $entry_id, 'kgi_location_status', 'resolved' );
	gform_update_meta( $entry_id, 'kgi_location_id', $location_id );
	gform_update_meta( $entry_id, 'kgi_location_name', $location_name );
	gform_update_meta( $entry_id, 'kgi_submission_status', 'location_resolved' );

	kgi_log(
		'Location resolved.',
		array(
			'entry_id'      => $entry_id,
			'location_id'   => $location_id,
			'location_name' => $location_name,
		)
	);

	kgi_queue_quote_entry_job( $entry_id );
}

/**
 * Handles a submission from a fixed-location form.
 *
 * Unlike the main quote form, these forms always route to one predetermined
 * location (configured in Settings → Koala Gravity Integration → Fixed-Location
 * Forms) rather than resolving it dynamically from the request. Mirrors
 * `kgi_handle_quote_form_submission()`'s meta/logging/queueing behavior so
 * the rest of the pipeline (background job, entry detail sidebar) doesn't
 * need to know which routing mode an entry came from.
 *
 * @since 0.2.0
 *
 * @param mixed[] $entry Gravity Forms entry array.
 * @param mixed[] $form  Gravity Forms form array.
 */
function kgi_handle_fixed_location_form_submission( array $entry, array $form ): void {
	$entry_id = (int) $entry['id'];
	$form_id  = (int) $form['id'];
	$config   = kgi_get_fixed_location_form_config( $form_id );

	if ( ! $config ) {
		return;
	}

	$location = kgi_resolve_fixed_location( (int) ( $config['location_id'] ?? 0 ) );

	if ( ! $location ) {
		gform_update_meta( $entry_id, 'kgi_location_status', 'missing_location' );
		gform_update_meta( $entry_id, 'kgi_submission_status', 'blocked_missing_location' );

		kgi_log(
			'Fixed-location form submission blocked. Configured location not found.',
			array(
				'entry_id'               => $entry_id,
				'form_id'                => $form_id,
				'configured_location_id' => $config['location_id'] ?? null,
			)
		);

		return;
	}

	$location_id   = $location->ID;
	$location_name = get_field( 'location_name', $location_id );

	gform_update_meta( $entry_id, 'kgi_location_status', 'resolved' );
	gform_update_meta( $entry_id, 'kgi_location_id', $location_id );
	gform_update_meta( $entry_id, 'kgi_location_name', $location_name );
	gform_update_meta( $entry_id, 'kgi_submission_status', 'location_resolved' );

	kgi_log(
		'Fixed-location form submission resolved.',
		array(
			'entry_id'      => $entry_id,
			'form_id'       => $form_id,
			'location_id'   => $location_id,
			'location_name' => $location_name,
		)
	);

	kgi_queue_quote_entry_job( $entry_id );
}

/**
 * Validates that a resolvable location exists before accepting a quote submission.
 *
 * Blocks the form with a field-level error if no location can be determined
 * from the posted location ID or the current request URI.
 *
 * Hooked to `gform_validation_{form_id}`.
 *
 * @since 0.1.0
 *
 * @param mixed[] $validation_result Gravity Forms validation result array.
 * @return mixed[] Modified validation result.
 */
function kgi_validate_quote_form_location( array $validation_result ): array {
	$form              = $validation_result['form'];
	$form_id           = (int) $form['id'];
	$location_id_field = kgi_get_location_field_id_for_form( $form_id, 'location_id' );
	$location          = kgi_resolve_location(
		absint( rgpost( 'input_' . $location_id_field ) )
	);

	if ( $location instanceof WP_Post ) {
		return $validation_result;
	}

	foreach ( $form['fields'] as &$field ) {
		if ( $location_id_field === (int) $field->id ) {
			$field->failed_validation  = true;
			$field->validation_message = esc_html__( 'We could not determine the service location for this request. Please refresh the page and try again.', 'koala-gravity-integration' );
			break;
		}
	}

	$validation_result['is_valid'] = false;
	$validation_result['form']     = $form;

	kgi_log(
		'Quote form blocked. Location validation failed.',
		array(
			'form_id'       => $form_id,
			'location_id'   => rgpost( 'input_' . $location_id_field ),
			'location_slug' => rgpost( 'input_' . kgi_get_location_field_id_for_form( $form_id, 'location_slug' ) ),
			'page_url'      => rgpost( 'input_' . kgi_get_location_field_id_for_form( $form_id, 'page_url' ) ),
			'request_uri'   => isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '',
		)
	);

	return $validation_result;
}

/**
 * Validates all phone fields in the quote form against the expected formats.
 *
 * Accepts:
 *   (999) 999-9999       — 10-digit North American format
 *   9 (999) 999-9999     — 11-digit format with single-digit country code
 *
 * Hooked to `gform_validation_{form_id}` at priority 20.
 *
 * @since 0.1.0
 *
 * @param mixed[] $validation_result Gravity Forms validation result array.
 * @return mixed[] Modified validation result.
 */
function kgi_validate_phone_fields( array $validation_result ): array {
	$form = $validation_result['form'];

	foreach ( $form['fields'] as &$field ) {
		if ( 'phone' !== $field->type ) {
			continue;
		}

		$value = trim( rgpost( 'input_' . $field->id ) );

		if ( empty( $value ) ) {
			continue;
		}

		if ( ! preg_match( '/^(\d )?\(\d{3}\) \d{3}-\d{4}$/', $value ) ) {
			$field->failed_validation      = true;
			$field->validation_message     = esc_html__( 'Please enter a valid phone number, e.g. (555) 123-4567 or 1 (555) 123-4567.', 'koala-gravity-integration' );
			$validation_result['is_valid'] = false;
		}
	}

	$validation_result['form'] = $form;

	return $validation_result;
}

/**
 * Builds the thank-you page URL for a resolved location.
 *
 * No country prefix is added here — home_url() already resolves to the
 * correct country-specific base on this install, so adding kgi_country's
 * prefix again would double it up. The Country setting still drives how
 * kgi_get_current_location_from_request() parses *incoming* URLs; it just
 * isn't needed when building this *outgoing* one.
 *
 * @since 0.1.0
 *
 * @param WP_Post $location Resolved location CPT post.
 * @return string Absolute thank-you page URL.
 */
function kgi_build_thank_you_url( WP_Post $location ): string {
	$thank_you_slug = get_option( 'kgi_thank_you_slug', 'thank-you' );

	$segments = array();

	$segments[] = $location->post_name;
	$segments[] = $thank_you_slug ? sanitize_title( $thank_you_slug ) : 'thank-you';

	return home_url( '/' . implode( '/', $segments ) );
}

/**
 * Builds the signed thank-you redirect confirmation array for a resolved (or unresolved) location.
 *
 * Shared by `kgi_redirect_after_quote_submission()` and
 * `kgi_redirect_after_fixed_location_form_submission()` so both routing
 * modes produce the same URL shape and signed-entry query args.
 *
 * @since 0.2.0
 *
 * @param WP_Post|null $location Resolved location CPT post, or null if unresolved.
 * @param int          $entry_id Gravity Forms entry ID.
 * @return mixed[] Redirect confirmation array.
 */
function kgi_build_signed_confirmation_redirect( ?WP_Post $location, int $entry_id ): array {
	if ( $location instanceof WP_Post ) {
		$redirect_url = kgi_build_thank_you_url( $location );
	} else {
		$thank_you_slug = get_option( 'kgi_thank_you_slug', 'thank-you' );
		$redirect_url   = home_url( '/' . ( $thank_you_slug ? $thank_you_slug : 'thank-you' ) );
	}

	$redirect_url = add_query_arg(
		array(
			'kgi_entry' => $entry_id,
			'kgi_sig'   => kgi_sign_entry_id( $entry_id ),
		),
		$redirect_url
	);

	kgi_log(
		'Redirecting after quote submission.',
		array(
			'entry_id'     => $entry_id,
			'redirect_url' => $redirect_url,
		)
	);

	return array( 'redirect' => $redirect_url );
}

/**
 * Redirects to the location-specific thank-you page after a successful submission.
 *
 * Resolves the location directly from the entry's own posted field value
 * (via kgi_get_location_from_entry()) rather than from entry meta written
 * by kgi_handle_quote_form_submission() on gform_after_submission — that
 * hook is not guaranteed to have run yet by the time gform_confirmation
 * fires, so reading meta here could see a not-yet-written value.
 *
 * Hooked to `gform_confirmation_{form_id}`.
 *
 * @since 0.1.0
 *
 * @param mixed   $confirmation Existing confirmation value.
 * @param mixed[] $form         Gravity Forms form array.
 * @param mixed[] $entry        Gravity Forms entry array.
 * @return mixed[] Redirect confirmation array.
 */
function kgi_redirect_after_quote_submission( $confirmation, array $form, array $entry ): array {
	$entry_id = (int) $entry['id'];
	$location = kgi_get_location_from_entry( $entry );

	return kgi_build_signed_confirmation_redirect( $location, $entry_id );
}

/**
 * Redirects to the location-specific thank-you page after a fixed-location form submission.
 *
 * Resolves the location from this form's static config rather than from the
 * entry, since a fixed-location form's location never varies per submission.
 *
 * Hooked to `gform_confirmation_{form_id}`.
 *
 * @since 0.2.0
 *
 * @param mixed   $confirmation Existing confirmation value.
 * @param mixed[] $form         Gravity Forms form array.
 * @param mixed[] $entry        Gravity Forms entry array.
 * @return mixed[] Redirect confirmation array.
 */
function kgi_redirect_after_fixed_location_form_submission( $confirmation, array $form, array $entry ): array {
	$entry_id = (int) $entry['id'];
	$config   = kgi_get_fixed_location_form_config( (int) $form['id'] );
	$location = $config ? kgi_resolve_fixed_location( (int) ( $config['location_id'] ?? 0 ) ) : null;

	return kgi_build_signed_confirmation_redirect( $location, $entry_id );
}
