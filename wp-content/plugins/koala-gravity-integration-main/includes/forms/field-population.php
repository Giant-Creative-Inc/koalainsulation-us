<?php
/**
 * Hidden field injection for the quote form.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the form pre-render hook for location field injection.
 *
 * @since 0.1.0
 */
function kgi_register_field_population_hooks(): void {
	foreach ( kgi_get_all_quote_form_ids() as $form_id ) {
		add_filter(
			'gform_pre_render_' . $form_id,
			'kgi_inject_location_field_values'
		);
	}

	foreach ( kgi_get_fixed_location_forms() as $config ) {
		$form_id = (int) ( $config['form_id'] ?? 0 );

		if ( $form_id <= 0 || empty( $config['page_url_field_id'] ) ) {
			continue;
		}

		add_filter(
			'gform_pre_render_' . $form_id,
			'kgi_inject_fixed_location_page_url_field_value'
		);
	}
}

/**
 * Injects location data into a dynamic quote form's hidden fields before
 * rendering — the main Quote Form or an additional quote form (see
 * `kgi_get_all_quote_form_ids()`), each read via its own field mapping since
 * an additional quote form is a distinct Gravity Form with its own field IDs.
 *
 * Sets defaultValue directly on the field objects so the values are written
 * into the rendered HTML inputs and saved to the entry on submission — no GF
 * dynamic population configuration required.
 *
 * @since 0.1.0
 *
 * @param mixed[] $form Gravity Forms form array.
 * @return mixed[] Modified form array.
 */
function kgi_inject_location_field_values( array $form ): array {
	$form_id  = (int) $form['id'];
	$location = kgi_get_current_location_from_request();
	$page_url = esc_url_raw( add_query_arg( array() ) );

	$location_slug_field_id = kgi_get_location_field_id_for_form( $form_id, 'location_slug' );
	$location_id_field_id   = kgi_get_location_field_id_for_form( $form_id, 'location_id' );
	$page_url_field_id      = kgi_get_location_field_id_for_form( $form_id, 'page_url' );

	foreach ( $form['fields'] as &$field ) {
		switch ( (int) $field->id ) {
			case $location_slug_field_id:
				$field->defaultValue = $location instanceof WP_Post ? $location->post_name : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				break;
			case $location_id_field_id:
				$field->defaultValue = $location instanceof WP_Post ? (string) $location->ID : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				break;
			case $page_url_field_id:
				$field->defaultValue = $page_url; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				break;
		}
	}

	return $form;
}

/**
 * Injects the current page URL into a fixed-location form's page-URL hidden field, if configured.
 *
 * Fixed-location forms don't need location_slug/location_id injected — the
 * location is already known from static config, not derived from the page —
 * but the page URL is still request-specific and useful for the Google Sheet
 * logging webhook, so it's populated the same way as the main quote form's.
 *
 * @since 0.2.0
 *
 * @param mixed[] $form Gravity Forms form array.
 * @return mixed[] Modified form array.
 */
function kgi_inject_fixed_location_page_url_field_value( array $form ): array {
	$config = kgi_get_fixed_location_form_config( (int) $form['id'] );

	if ( ! $config || empty( $config['page_url_field_id'] ) ) {
		return $form;
	}

	$page_url_field_id = (int) $config['page_url_field_id'];
	$page_url          = esc_url_raw( add_query_arg( array() ) );

	foreach ( $form['fields'] as &$field ) {
		if ( $page_url_field_id === (int) $field->id ) {
			$field->defaultValue = $page_url; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			break;
		}
	}

	return $form;
}
