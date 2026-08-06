<?php
/**
 * Plugin settings page.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the settings page and field hooks.
 *
 * @since 0.1.0
 */
function kgi_register_settings_hooks(): void {
	add_action( 'admin_menu', 'kgi_register_settings_page' );
	add_action( 'admin_init', 'kgi_register_settings' );
	add_action( 'wp_ajax_kgi_get_form_fields', 'kgi_ajax_get_form_fields' );
}

/**
 * Adds the plugin settings page under the WordPress Settings menu.
 *
 * @since 0.1.0
 */
function kgi_register_settings_page(): void {
	add_options_page(
		__( 'Koala Gravity Integration', 'koala-gravity-integration' ),
		__( 'Koala Gravity', 'koala-gravity-integration' ),
		'manage_options',
		'koala-gravity-integration',
		'kgi_render_settings_page'
	);
}

/**
 * Registers the plugin settings and fields via the WordPress Settings API.
 *
 * @since 0.1.0
 */
function kgi_register_settings(): void {
	register_setting(
		'kgi_settings',
		'kgi_quote_form_id',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_location_post_type',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kgi_sanitize_location_post_type',
			'default'           => 'location',
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_n8n_webhook_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_field_map',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kgi_sanitize_field_map',
			'default'           => array(),
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_location_field_map',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kgi_sanitize_location_field_map',
			'default'           => KGI_DEFAULT_LOCATION_FIELD_MAP,
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_additional_quote_forms',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kgi_sanitize_additional_quote_forms',
			'default'           => array(),
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_fixed_location_forms',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kgi_sanitize_fixed_location_forms',
			'default'           => array(),
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_country',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kgi_sanitize_country',
			'default'           => 'us',
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_country_slug',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kgi_sanitize_letters_dashes_slug',
			'default'           => '',
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_thank_you_slug',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kgi_sanitize_letters_dashes_slug',
			'default'           => 'thank-you',
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_zipcodeapi_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_default_location_id',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);

	register_setting(
		'kgi_settings',
		'kgi_notification_email',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'kgi_sanitize_notification_email',
			'default'           => '',
		)
	);

	add_settings_section(
		'kgi_general_section',
		__( 'General', 'koala-gravity-integration' ),
		'__return_false',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_quote_form_id',
		__( 'Quote Form', 'koala-gravity-integration' ),
		'kgi_render_quote_form_field',
		'koala-gravity-integration',
		'kgi_general_section'
	);

	add_settings_field(
		'kgi_location_post_type',
		__( 'Location Post Type', 'koala-gravity-integration' ),
		'kgi_render_location_post_type_field',
		'koala-gravity-integration',
		'kgi_general_section'
	);

	add_settings_section(
		'kgi_thank_you_section',
		__( 'Thank You Page', 'koala-gravity-integration' ),
		'__return_false',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_country',
		__( 'Country', 'koala-gravity-integration' ),
		'kgi_render_country_field',
		'koala-gravity-integration',
		'kgi_thank_you_section'
	);

	add_settings_field(
		'kgi_country_slug',
		__( 'Country Slug', 'koala-gravity-integration' ),
		'kgi_render_country_slug_field',
		'koala-gravity-integration',
		'kgi_thank_you_section'
	);

	add_settings_field(
		'kgi_thank_you_slug',
		__( 'Thank You Page Slug', 'koala-gravity-integration' ),
		'kgi_render_thank_you_slug_field',
		'koala-gravity-integration',
		'kgi_thank_you_section'
	);

	add_settings_section(
		'kgi_n8n_section',
		__( 'n8n Integration', 'koala-gravity-integration' ),
		'__return_false',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_n8n_webhook_url',
		__( 'Webhook URL', 'koala-gravity-integration' ),
		'kgi_render_webhook_url_field',
		'koala-gravity-integration',
		'kgi_n8n_section'
	);

	add_settings_section(
		'kgi_zip_fallback_section',
		__( 'ZIP Location Fallback', 'koala-gravity-integration' ),
		'kgi_render_zip_fallback_section_intro',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_zipcodeapi_key',
		__( 'zipcodeapi.com API Key', 'koala-gravity-integration' ),
		'kgi_render_zipcodeapi_key_field',
		'koala-gravity-integration',
		'kgi_zip_fallback_section'
	);

	add_settings_section(
		'kgi_lead_routing_section',
		__( 'Lead Routing', 'koala-gravity-integration' ),
		'kgi_render_lead_routing_section_intro',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_default_location_id',
		__( 'Default Location', 'koala-gravity-integration' ),
		'kgi_render_default_location_field',
		'koala-gravity-integration',
		'kgi_lead_routing_section'
	);

	add_settings_field(
		'kgi_notification_email',
		__( 'Notification Email', 'koala-gravity-integration' ),
		'kgi_render_notification_email_field',
		'koala-gravity-integration',
		'kgi_lead_routing_section'
	);

	add_settings_section(
		'kgi_field_mapping_section',
		__( 'Field Mapping', 'koala-gravity-integration' ),
		'__return_false',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_field_map',
		__( 'Quote Form Fields', 'koala-gravity-integration' ),
		'kgi_render_field_map',
		'koala-gravity-integration',
		'kgi_field_mapping_section'
	);

	add_settings_field(
		'kgi_location_field_map',
		__( 'Location Routing Fields', 'koala-gravity-integration' ),
		'kgi_render_location_field_map',
		'koala-gravity-integration',
		'kgi_field_mapping_section'
	);

	add_settings_section(
		'kgi_additional_quote_forms_section',
		__( 'Additional Quote Forms', 'koala-gravity-integration' ),
		'__return_false',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_additional_quote_forms',
		__( 'Forms', 'koala-gravity-integration' ),
		'kgi_render_additional_quote_forms_field',
		'koala-gravity-integration',
		'kgi_additional_quote_forms_section'
	);

	add_settings_section(
		'kgi_fixed_location_forms_section',
		__( 'Fixed-Location Forms', 'koala-gravity-integration' ),
		'__return_false',
		'koala-gravity-integration'
	);

	add_settings_field(
		'kgi_fixed_location_forms',
		__( 'Forms', 'koala-gravity-integration' ),
		'kgi_render_fixed_location_forms_field',
		'koala-gravity-integration',
		'kgi_fixed_location_forms_section'
	);
}

/**
 * Returns the n8n payload fields available for mapping, keyed by payload key.
 *
 * Shared by the main Quote Form Fields table (`kgi_render_field_map()`), the
 * per-row field mapping in the Fixed-Location Forms repeater
 * (`kgi_render_fixed_location_form_row()`), and both sanitizers, so the set
 * of valid payload keys only needs to be maintained in one place.
 *
 * @since 0.2.0
 *
 * @return array<string, string> Payload key => translated label.
 */
function kgi_get_payload_field_labels(): array {
	return array(
		'first_name'     => __( 'First Name', 'koala-gravity-integration' ),
		'last_name'      => __( 'Last Name', 'koala-gravity-integration' ),
		'email'          => __( 'Email', 'koala-gravity-integration' ),
		'mobile_number'  => __( 'Mobile Number', 'koala-gravity-integration' ),
		'address1'       => __( 'Address Line 1', 'koala-gravity-integration' ),
		'address2'       => __( 'Address Line 2', 'koala-gravity-integration' ),
		'city'           => __( 'City', 'koala-gravity-integration' ),
		'state'          => __( 'State / Province', 'koala-gravity-integration' ),
		'zip'            => __( 'ZIP / Postal Code', 'koala-gravity-integration' ),
		'DoNotText'      => __( 'Do Not Text', 'koala-gravity-integration' ),
		'DoNotEmail'     => __( 'Do Not Email', 'koala-gravity-integration' ),
		'UtmSource'      => __( 'UTM Source', 'koala-gravity-integration' ),
		'UtmMedium'      => __( 'UTM Medium', 'koala-gravity-integration' ),
		'UtmCampaign'    => __( 'UTM Campaign', 'koala-gravity-integration' ),
		'UtmTerm'        => __( 'UTM Term', 'koala-gravity-integration' ),
		'UtmContent'     => __( 'UTM Content', 'koala-gravity-integration' ),
		'gclid'          => __( 'Google Click ID (gclid)', 'koala-gravity-integration' ),
		'gbraid'         => __( 'Google gbraid', 'koala-gravity-integration' ),
		'wbraid'         => __( 'Google wbraid', 'koala-gravity-integration' ),
		'fbclid'         => __( 'Facebook Click ID (fbclid)', 'koala-gravity-integration' ),
		'msclkid'        => __( 'Microsoft Click ID (msclkid)', 'koala-gravity-integration' ),
		'landing_page'   => __( 'Landing Page', 'koala-gravity-integration' ),
		'referrer'       => __( 'Referrer', 'koala-gravity-integration' ),
		'form_timestamp' => __( 'Timestamp', 'koala-gravity-integration' ),
		'service'        => __( 'Service', 'koala-gravity-integration' ),
		'cta_text'       => __( 'CTA Text', 'koala-gravity-integration' ),
	);
}

/**
 * Sanitizes the field map option.
 *
 * @since 0.1.0
 *
 * @param mixed $value Raw option value.
 * @return array<string, string> Sanitized field map.
 */
function kgi_sanitize_field_map( $value ): array {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$allowed_keys = array_keys( kgi_get_payload_field_labels() );

	$sanitized = array();

	foreach ( $value as $key => $field_id ) {
		if ( in_array( $key, $allowed_keys, true ) ) {
			$sanitized[ $key ] = sanitize_text_field( $field_id );
		}
	}

	return $sanitized;
}

/**
 * Sanitizes the location field map option.
 *
 * @since 0.1.0
 *
 * @param mixed $value Raw option value.
 * @return array<string, string> Sanitized location field map.
 */
function kgi_sanitize_location_field_map( $value ): array {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$allowed_keys = array_keys( KGI_DEFAULT_LOCATION_FIELD_MAP );
	$sanitized    = array();

	foreach ( $value as $key => $field_id ) {
		if ( in_array( $key, $allowed_keys, true ) ) {
			$sanitized[ $key ] = sanitize_text_field( $field_id );
		}
	}

	return $sanitized;
}

/**
 * Sanitizes the additional quote forms option.
 *
 * Drops any row with no form selected (e.g. a template row added via the
 * repeater's "Add" button but never filled in). Each row behaves like the
 * main Quote Form — location resolved dynamically per-request — but needs
 * its own location and payload field maps, since it's a distinct Gravity
 * Form with its own field IDs.
 *
 * @since 0.4.0
 *
 * @param mixed $value Raw option value.
 * @return array<int, array<string, mixed>> Sanitized list of additional quote form configs.
 */
function kgi_sanitize_additional_quote_forms( $value ): array {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$sanitized = array();

	foreach ( $value as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$form_id = absint( $row['form_id'] ?? 0 );

		if ( $form_id <= 0 ) {
			continue;
		}

		$sanitized[] = array(
			'form_id'            => $form_id,
			'location_field_map' => kgi_sanitize_location_field_map( $row['location_field_map'] ?? array() ),
			'field_map'          => kgi_sanitize_field_map( $row['field_map'] ?? array() ),
		);
	}

	return $sanitized;
}

/**
 * Sanitizes the fixed-location forms option.
 *
 * Drops any row with no form selected (e.g. a template row added via the
 * repeater's "Add" button but never filled in), and re-sanitizes each row's
 * own field map with the same rules as the main field map.
 *
 * @since 0.2.0
 *
 * @param mixed $value Raw option value.
 * @return array<int, array<string, mixed>> Sanitized list of fixed-location form configs.
 */
function kgi_sanitize_fixed_location_forms( $value ): array {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$sanitized = array();

	foreach ( $value as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$form_id = absint( $row['form_id'] ?? 0 );

		if ( $form_id <= 0 ) {
			continue;
		}

		$sanitized[] = array(
			'form_id'           => $form_id,
			'location_id'       => absint( $row['location_id'] ?? 0 ),
			'page_url_field_id' => absint( $row['page_url_field_id'] ?? 0 ),
			'field_map'         => kgi_sanitize_fixed_location_field_map( $row['field_map'] ?? array(), $row['custom_fields'] ?? array() ),
		);
	}

	return $sanitized;
}

/**
 * Sanitizes a fixed-location form row's full field map.
 *
 * Merges the row's predefined payload fields (same allowed keys as the main
 * Quote Form Fields map) with its custom fields — arbitrary admin-defined
 * payload keys entered via the "+ Add Custom Field" repeater — into one flat
 * field_map. Both flow into `kgi_build_entry_payload()` identically, so no
 * downstream code needs to know which fields are "custom".
 *
 * @since 0.3.0
 *
 * @param mixed $field_map     Raw predefined field map (payload key => GF field ID).
 * @param mixed $custom_fields Raw custom field rows, each with a 'key' and 'field_id'.
 * @return array<string, string> Sanitized, merged field map.
 */
function kgi_sanitize_fixed_location_field_map( $field_map, $custom_fields ): array {
	$sanitized = kgi_sanitize_field_map( $field_map );

	if ( ! is_array( $custom_fields ) ) {
		return $sanitized;
	}

	$reserved_keys = array_keys( kgi_get_payload_field_labels() );

	foreach ( $custom_fields as $custom_field ) {
		if ( ! is_array( $custom_field ) ) {
			continue;
		}

		$key      = sanitize_key( $custom_field['key'] ?? '' );
		$field_id = sanitize_text_field( $custom_field['field_id'] ?? '' );

		if ( '' === $key || '' === $field_id || in_array( $key, $reserved_keys, true ) ) {
			continue;
		}

		$sanitized[ $key ] = $field_id;
	}

	return $sanitized;
}

/**
 * Sanitizes the location post type slug.
 *
 * @since 0.1.0
 *
 * @param mixed $value Raw option value.
 * @return string Sanitized post type slug, falls back to 'location' if empty.
 */
function kgi_sanitize_location_post_type( $value ): string {
	$value = sanitize_key( $value );

	return $value ? $value : 'location';
}

/**
 * Sanitizes the country option to one of the three supported values.
 *
 * @since 0.1.0
 *
 * @param mixed $value Raw option value.
 * @return string 'us', 'ca', or 'other'. Falls back to 'us' for anything else.
 */
function kgi_sanitize_country( $value ): string {
	$allowed = array( 'us', 'ca', 'other' );
	$value   = sanitize_key( $value );

	return in_array( $value, $allowed, true ) ? $value : 'us';
}

/**
 * Sanitizes a URL slug to letters and dashes only.
 *
 * Shared by the country slug and thank-you page slug fields. Unlike
 * sanitize_title(), this strips digits too, since neither field is
 * expected to contain a number (e.g. "mx", "ca", "thank-you").
 *
 * @since 0.1.0
 *
 * @param mixed $value Raw option value.
 * @return string Lowercase letters and dashes only, no leading/trailing dashes.
 */
function kgi_sanitize_letters_dashes_slug( $value ): string {
	$value = strtolower( sanitize_text_field( $value ) );
	$value = preg_replace( '/[^a-z-]/', '', $value );

	return trim( $value, '-' );
}

/**
 * Sanitizes the unresolved-lead notification email address.
 *
 * An empty value is preserved (so `kgi_get_notification_email()` falls back to
 * the site admin email); a non-empty value must be a valid email or it is
 * discarded.
 *
 * @since 0.7.0
 *
 * @param mixed $value Raw option value.
 * @return string Sanitized email, or '' to use the admin-email fallback.
 */
function kgi_sanitize_notification_email( $value ): string {
	$value = is_string( $value ) ? trim( $value ) : '';

	if ( '' === $value ) {
		return '';
	}

	$email = sanitize_email( $value );

	return is_email( $email ) ? $email : '';
}

/**
 * Renders the quote form selector dropdown.
 *
 * @since 0.1.0
 */
function kgi_render_quote_form_field(): void {
	$saved = kgi_get_quote_form_id();
	$forms = GFAPI::get_forms();
	?>
	<select name="kgi_quote_form_id" id="kgi_quote_form_id">
		<option value="0"><?php esc_html_e( '— Select a form —', 'koala-gravity-integration' ); ?></option>
		<?php foreach ( $forms as $form ) : ?>
			<option value="<?php echo esc_attr( $form['id'] ); ?>" <?php selected( $saved, (int) $form['id'] ); ?>>
				<?php echo esc_html( $form['title'] . ' (ID: ' . $form['id'] . ')' ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p class="description">
		<?php esc_html_e( 'After changing the form, save and re-map your fields below.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the location post type input field.
 *
 * @since 0.1.0
 */
function kgi_render_location_post_type_field(): void {
	$value = kgi_get_location_post_type();
	?>
	<input
		type="text"
		name="kgi_location_post_type"
		id="kgi_location_post_type"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		placeholder="location"
	/>
	<p class="description">
		<?php esc_html_e( 'The custom post type slug used for franchise locations. Defaults to "location".', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the country selector dropdown.
 *
 * Controls the thank-you page URL prefix: none for US, a hardcoded "ca" for
 * Canada, or the custom slug below for Other.
 *
 * @since 0.1.0
 */
function kgi_render_country_field(): void {
	$value = get_option( 'kgi_country', 'us' );
	?>
	<select name="kgi_country" id="kgi_country">
		<option value="us" <?php selected( $value, 'us' ); ?>><?php esc_html_e( 'US', 'koala-gravity-integration' ); ?></option>
		<option value="ca" <?php selected( $value, 'ca' ); ?>><?php esc_html_e( 'Canada', 'koala-gravity-integration' ); ?></option>
		<option value="other" <?php selected( $value, 'other' ); ?>><?php esc_html_e( 'Other', 'koala-gravity-integration' ); ?></option>
	</select>
	<p class="description">
		<?php esc_html_e( 'Thank-you URL becomes /{location-slug}/{thank-you-slug} for US, /ca/{location-slug}/{thank-you-slug} for Canada, or /{country-slug}/{location-slug}/{thank-you-slug} for Other.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the country slug input field.
 *
 * Only used when Country is set to "Other".
 *
 * @since 0.1.0
 */
function kgi_render_country_slug_field(): void {
	$value = get_option( 'kgi_country_slug', '' );
	?>
	<input
		type="text"
		name="kgi_country_slug"
		id="kgi_country_slug"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		placeholder="mx"
		pattern="[a-z]+(-[a-z]+)*"
		title="<?php esc_attr_e( 'Letters and dashes only.', 'koala-gravity-integration' ); ?>"
	/>
	<p class="description">
		<?php esc_html_e( 'Only used when Country is set to "Other", e.g. "mx" for Mexico. Letters and dashes only.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the thank-you page slug input field.
 *
 * @since 0.1.0
 */
function kgi_render_thank_you_slug_field(): void {
	$value = get_option( 'kgi_thank_you_slug', 'thank-you' );
	?>
	<input
		type="text"
		name="kgi_thank_you_slug"
		id="kgi_thank_you_slug"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		placeholder="thank-you"
		pattern="[a-z]+(-[a-z]+)*"
		title="<?php esc_attr_e( 'Letters and dashes only.', 'koala-gravity-integration' ); ?>"
	/>
	<p class="description">
		<?php esc_html_e( 'The final URL segment for the thank-you page, e.g. "thank-you". Letters and dashes only.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Builds a flat list of selectable form fields from a Gravity Form.
 *
 * Expands compound fields (name, address) into their individual inputs
 * so admins can map sub-fields like "First Name" or "City" directly.
 *
 * @since 0.1.0
 *
 * @param int|null $form_id Form ID to read fields from, 0 for "no form selected"
 *                          (returns an empty list), or null to default to the
 *                          configured Quote Form.
 * @return array<string, string> Keyed by field ID, valued by display label.
 */
function kgi_get_form_field_options( ?int $form_id = null ): array {
	if ( null === $form_id ) {
		$form_id = kgi_get_quote_form_id();
	}

	if ( $form_id <= 0 ) {
		return array();
	}

	$form = GFAPI::get_form( $form_id );

	if ( ! $form || is_wp_error( $form ) ) {
		return array();
	}

	$options = array();

	foreach ( $form['fields'] as $field ) {
		if ( ! empty( $field->inputs ) ) {
			foreach ( $field->inputs as $input ) {
				$options[ (string) $input['id'] ] = $field->label . ': ' . $input['label'] . ' (ID: ' . $input['id'] . ')';
			}
		} else {
			$options[ (string) $field->id ] = $field->label . ' (ID: ' . $field->id . ')';
		}
	}

	return $options;
}

/**
 * AJAX handler that returns a Gravity Form's field options as a JSON list.
 *
 * Powers the "Form Field" dropdowns in the Fixed-Location Forms repeater
 * (`kgi_render_fixed_location_form_row()`), since each row can point at a
 * different form and re-rendering the whole settings page just to refresh
 * one row's dropdowns would be overkill. Returns a list of `{id, label}`
 * rather than an object so JS can preserve field order — Gravity Forms sub-
 * field IDs like "2.3" sort oddly if merged with plain numeric keys in a JS
 * object.
 *
 * @since 0.3.0
 */
function kgi_ajax_get_form_fields(): void {
	check_ajax_referer( 'kgi_get_form_fields', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( null, 403 );
	}

	$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
	$options = kgi_get_form_field_options( $form_id );

	$fields = array();

	foreach ( $options as $field_id => $label ) {
		$fields[] = array(
			'id'    => (string) $field_id,
			'label' => $label,
		);
	}

	wp_send_json_success( $fields );
}

/**
 * Renders the webhook URL input field.
 *
 * @since 0.1.0
 */
function kgi_render_webhook_url_field(): void {
	$value = get_option( 'kgi_n8n_webhook_url', '' );
	?>
	<input
		type="url"
		name="kgi_n8n_webhook_url"
		id="kgi_n8n_webhook_url"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		placeholder="https://..."
	/>
	<p class="description">
		<?php esc_html_e( 'The n8n webhook URL that quote submissions will be sent to.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the intro text for the ZIP Location Fallback section.
 *
 * @since 0.6.0
 */
function kgi_render_zip_fallback_section_intro(): void {
	?>
	<p class="description">
		<?php
		printf(
			/* translators: %d: search radius in miles. */
			esc_html__( 'When a submitted ZIP or postal code matches no location, the lead is routed to the nearest location within %d miles using zipcodeapi.com (US ZIP codes and Canadian postal codes). Leave the key blank to disable this and keep routing unmatched codes to the form\'s original location.', 'koala-gravity-integration' ),
			(int) kgi_get_zip_fallback_radius()
		);
		?>
	</p>
	<?php
}

/**
 * Renders the zipcodeapi.com API key input field.
 *
 * @since 0.6.0
 */
function kgi_render_zipcodeapi_key_field(): void {
	$value = get_option( 'kgi_zipcodeapi_key', '' );
	?>
	<input
		type="text"
		name="kgi_zipcodeapi_key"
		id="kgi_zipcodeapi_key"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		autocomplete="off"
	/>
	<p class="description">
		<?php esc_html_e( 'API key from zipcodeapi.com, used for the nearest-location fallback. Leave blank to disable the fallback.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the intro text for the Lead Routing section.
 *
 * @since 0.7.0
 */
function kgi_render_lead_routing_section_intro(): void {
	?>
	<p class="description">
		<?php esc_html_e( 'Submissions are never rejected for a missing location. When a lead\'s location can\'t be resolved from the page URL or its ZIP/postal code, it is routed to the Default Location below so the lead is still received, and the Notification Email is alerted to review it.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the default (overflow) location selector.
 *
 * @since 0.7.0
 */
function kgi_render_default_location_field(): void {
	$selected  = (int) get_option( 'kgi_default_location_id', 0 );
	$locations = get_posts(
		array(
			'post_type'      => kgi_get_location_post_type(),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	?>
	<select name="kgi_default_location_id" id="kgi_default_location_id">
		<option value="0"><?php esc_html_e( '— None (hold unresolved leads for manual routing) —', 'koala-gravity-integration' ); ?></option>
		<?php foreach ( $locations as $location ) : ?>
			<option value="<?php echo esc_attr( $location->ID ); ?>" <?php selected( $selected, (int) $location->ID ); ?>>
				<?php echo esc_html( $location->post_title . ' (ID: ' . $location->ID . ')' ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p class="description">
		<?php esc_html_e( 'Overflow location for leads whose location can\'t be resolved from the page or ZIP. Leave as "None" to keep such leads in Gravity Forms (not sent onward) and only notify.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the notification email input field.
 *
 * @since 0.7.0
 */
function kgi_render_notification_email_field(): void {
	$value = get_option( 'kgi_notification_email', '' );
	?>
	<input
		type="email"
		name="kgi_notification_email"
		id="kgi_notification_email"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"
	/>
	<p class="description">
		<?php esc_html_e( 'Where to send an alert when a lead is routed to the default location or can\'t be routed at all. Defaults to the site admin email if left blank.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders the field mapping table.
 *
 * Each row maps a payload key (sent to n8n) to a Gravity Forms field
 * from the quote form. Dropdowns are populated dynamically from the form.
 *
 * @since 0.1.0
 */
function kgi_render_field_map(): void {
	$saved   = get_option( 'kgi_field_map', array() );
	$options = kgi_get_form_field_options();

	$payload_fields = kgi_get_payload_field_labels();

	if ( empty( $options ) ) {
		echo '<p>' . esc_html__( 'No fields found. Ensure the quote form exists and Gravity Forms is active.', 'koala-gravity-integration' ) . '</p>';
		return;
	}
	?>
	<table class="widefat" style="max-width: 640px;">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Payload Field', 'koala-gravity-integration' ); ?></th>
				<th><?php esc_html_e( 'Form Field', 'koala-gravity-integration' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $payload_fields as $key => $label ) : ?>
				<?php $selected = $saved[ $key ] ?? ''; ?>
				<tr>
					<td>
						<label for="kgi_field_map_<?php echo esc_attr( $key ); ?>">
							<?php echo esc_html( $label ); ?>
						</label>
					</td>
					<td>
						<select
							name="kgi_field_map[<?php echo esc_attr( $key ); ?>]"
							id="kgi_field_map_<?php echo esc_attr( $key ); ?>"
						>
							<option value=""><?php esc_html_e( '— Not mapped —', 'koala-gravity-integration' ); ?></option>
							<?php foreach ( $options as $field_id => $field_label ) : ?>
								<option value="<?php echo esc_attr( $field_id ); ?>" <?php selected( $selected, $field_id ); ?>>
									<?php echo esc_html( $field_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Renders the location routing field mapping table.
 *
 * These hidden fields carry the location slug, location post ID, and page
 * URL that `kgi_inject_location_field_values()` populates on render and
 * that the submission handler and location resolver read back from the
 * entry. Unlike the n8n payload fields above, these are not sent to n8n
 * directly — they drive which franchise location an entry routes to.
 *
 * @since 0.1.0
 */
function kgi_render_location_field_map(): void {
	$saved   = get_option( 'kgi_location_field_map', KGI_DEFAULT_LOCATION_FIELD_MAP );
	$options = kgi_get_form_field_options();

	$location_fields = array(
		'location_slug' => __( 'Location Slug (hidden field)', 'koala-gravity-integration' ),
		'location_id'   => __( 'Location ID (hidden field)', 'koala-gravity-integration' ),
		'page_url'      => __( 'Page URL (hidden field)', 'koala-gravity-integration' ),
	);

	if ( empty( $options ) ) {
		return;
	}
	?>
	<table class="widefat" style="max-width: 640px;">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Routing Field', 'koala-gravity-integration' ); ?></th>
				<th><?php esc_html_e( 'Form Field', 'koala-gravity-integration' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $location_fields as $key => $label ) : ?>
				<?php $selected = $saved[ $key ] ?? ( KGI_DEFAULT_LOCATION_FIELD_MAP[ $key ] ?? '' ); ?>
				<tr>
					<td>
						<label for="kgi_location_field_map_<?php echo esc_attr( $key ); ?>">
							<?php echo esc_html( $label ); ?>
						</label>
					</td>
					<td>
						<select
							name="kgi_location_field_map[<?php echo esc_attr( $key ); ?>]"
							id="kgi_location_field_map_<?php echo esc_attr( $key ); ?>"
						>
							<option value=""><?php esc_html_e( '— Not mapped —', 'koala-gravity-integration' ); ?></option>
							<?php foreach ( $options as $field_id => $field_label ) : ?>
								<option value="<?php echo esc_attr( $field_id ); ?>" <?php selected( (string) $selected, (string) $field_id ); ?>>
									<?php echo esc_html( $field_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description">
		<?php esc_html_e( 'These hidden fields receive the resolved franchise location on page load and are read back after submission to route the lead. They must exist on the quote form as hidden fields.', 'koala-gravity-integration' ); ?>
	</p>
	<?php
}

/**
 * Renders one row of the additional quote forms repeater.
 *
 * Mirrors `kgi_render_fixed_location_form_row()`, but for a form that
 * resolves its location dynamically from the request URL on every
 * submission — same as the main Quote Form — rather than routing to one
 * fixed location. So instead of a location picker, each row gets its own
 * full Location Routing Fields map (location_slug/location_id/page_url),
 * same shape as the main Quote Form's, since this is a distinct Gravity
 * Form with its own field IDs.
 *
 * Used both for each saved config and for the hidden `<template>` row that
 * `kgi_render_additional_quote_forms_field()`'s JS clones when "Add" is
 * clicked — in that case `$index` is the literal placeholder string
 * `__INDEX__`, which the JS replaces with a real integer before inserting
 * the row.
 *
 * @since 0.4.0
 *
 * @param int|string            $index          Row index (or `__INDEX__` for the template row).
 * @param array<string, mixed>  $config         Saved config for this row, or an empty array for the template.
 * @param mixed[]               $forms          All Gravity Forms, from GFAPI::get_forms().
 * @param array<string, string> $payload_fields Payload key => label, from kgi_get_payload_field_labels().
 */
function kgi_render_additional_quote_form_row( $index, array $config, array $forms, array $payload_fields ): void {
	$form_id            = $config['form_id'] ?? '';
	$location_field_map = $config['location_field_map'] ?? array();
	$field_map          = $config['field_map'] ?? array();

	$name          = 'kgi_additional_quote_forms[' . $index . ']';
	$field_options = kgi_get_form_field_options( '__INDEX__' === $index ? 0 : (int) $form_id );

	$location_fields = array(
		'location_slug' => __( 'Location Slug (hidden field)', 'koala-gravity-integration' ),
		'location_id'   => __( 'Location ID (hidden field)', 'koala-gravity-integration' ),
		'page_url'      => __( 'Page URL (hidden field)', 'koala-gravity-integration' ),
	);

	$form_label = __( '— Select a form —', 'koala-gravity-integration' );

	foreach ( $forms as $form ) {
		if ( (string) $form['id'] === (string) $form_id ) {
			$form_label = $form['title'] . ' (ID: ' . $form['id'] . ')';
			break;
		}
	}

	$collapsed = ! empty( $config );
	?>
	<div class="kgi-additional-quote-form-row postbox<?php echo $collapsed ? ' kgi-collapsed' : ''; ?>" style="margin-bottom: 12px; max-width: 640px;">
		<div class="kgi-additional-quote-form-header" style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 12px; cursor: pointer; background: #f6f7f7; border-bottom: 1px solid #dcdcde;">
			<strong class="kgi-additional-quote-form-summary"><?php echo esc_html( $form_label ); ?></strong>
			<button type="button" class="button-link kgi-additional-quote-form-toggle" aria-expanded="<?php echo $collapsed ? 'false' : 'true'; ?>">
				<span class="dashicons <?php echo $collapsed ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-up-alt2'; ?>" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle this quote form', 'koala-gravity-integration' ); ?></span>
			</button>
		</div>
		<div class="kgi-additional-quote-form-body" style="padding: 12px;<?php echo $collapsed ? ' display: none;' : ''; ?>">
			<p>
				<label>
					<strong><?php esc_html_e( 'Gravity Form', 'koala-gravity-integration' ); ?></strong><br>
					<select name="<?php echo esc_attr( $name ); ?>[form_id]">
						<option value="0"><?php esc_html_e( '— Select a form —', 'koala-gravity-integration' ); ?></option>
						<?php foreach ( $forms as $form ) : ?>
							<option value="<?php echo esc_attr( $form['id'] ); ?>" <?php selected( (string) $form_id, (string) $form['id'] ); ?>>
								<?php echo esc_html( $form['title'] . ' (ID: ' . $form['id'] . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>

			<h4><?php esc_html_e( 'Location Routing Fields', 'koala-gravity-integration' ); ?></h4>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Routing Field', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Form Field', 'koala-gravity-integration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $location_fields as $key => $label ) : ?>
						<?php $selected = $location_field_map[ $key ] ?? ''; ?>
						<tr>
							<td><?php echo esc_html( $label ); ?></td>
							<td>
								<select
									class="kgi-field-select"
									name="<?php echo esc_attr( $name ); ?>[location_field_map][<?php echo esc_attr( $key ); ?>]"
								>
									<option value=""><?php esc_html_e( '— Not mapped —', 'koala-gravity-integration' ); ?></option>
									<?php foreach ( $field_options as $field_id_option => $field_label ) : ?>
										<option value="<?php echo esc_attr( $field_id_option ); ?>" <?php selected( (string) $selected, (string) $field_id_option ); ?>>
											<?php echo esc_html( $field_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="description">
				<?php esc_html_e( 'These hidden fields must exist on this form and work the same way as the main Quote Form\'s: populated with the resolved location on page load, then read back after submission to route the lead.', 'koala-gravity-integration' ); ?>
			</p>

			<h4><?php esc_html_e( 'Quote Form Fields', 'koala-gravity-integration' ); ?></h4>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Payload Field', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Form Field', 'koala-gravity-integration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $payload_fields as $key => $label ) : ?>
						<?php $selected = $field_map[ $key ] ?? ''; ?>
						<tr>
							<td><?php echo esc_html( $label ); ?></td>
							<td>
								<select
									class="kgi-field-select"
									name="<?php echo esc_attr( $name ); ?>[field_map][<?php echo esc_attr( $key ); ?>]"
								>
									<option value=""><?php esc_html_e( '— Not mapped —', 'koala-gravity-integration' ); ?></option>
									<?php foreach ( $field_options as $field_id_option => $field_label ) : ?>
										<option value="<?php echo esc_attr( $field_id_option ); ?>" <?php selected( (string) $selected, (string) $field_id_option ); ?>>
											<?php echo esc_html( $field_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p>
				<button type="button" class="button kgi-remove-additional-quote-form">
					<?php esc_html_e( 'Remove This Form', 'koala-gravity-integration' ); ?>
				</button>
			</p>
		</div>
	</div>
	<?php
}

/**
 * Renders the additional quote forms repeater.
 *
 * Each row is a distinct Gravity Form that behaves exactly like the main
 * Quote Form above — location resolved dynamically per-request, not fixed —
 * but has its own field IDs. Use this for a duplicate of the quote form
 * embedded elsewhere on the same page (e.g. inside a popup), so the two
 * never share a DOM ID (see the "Fixed-Location Forms" section below for the
 * different case of a form that always means one specific location). Rows
 * are added/removed client-side with plain JS; a removed row is simply
 * absent from the submitted `$_POST` array, and
 * `kgi_sanitize_additional_quote_forms()` drops any row left with no form
 * selected.
 *
 * @since 0.4.0
 */
function kgi_render_additional_quote_forms_field(): void {
	$saved = get_option( 'kgi_additional_quote_forms', array() );
	$forms = GFAPI::get_forms();

	$payload_fields = kgi_get_payload_field_labels();
	?>
	<p class="description">
		<?php esc_html_e( 'Add another Gravity Form that should behave exactly like the main Quote Form above — resolving its location dynamically from whatever page it\'s on — but is a separate form with its own field IDs. Use this for a duplicate of the quote form embedded elsewhere on the same page (e.g. a popup), instead of embedding the same form twice, which causes duplicate-ID conflicts in Gravity Forms\' own AJAX handling.', 'koala-gravity-integration' ); ?>
	</p>

	<div id="kgi-additional-quote-forms">
		<?php foreach ( array_values( $saved ) as $index => $config ) : ?>
			<?php kgi_render_additional_quote_form_row( $index, $config, $forms, $payload_fields ); ?>
		<?php endforeach; ?>
	</div>

	<p>
		<button type="button" class="button" id="kgi-add-additional-quote-form">
			<?php esc_html_e( '+ Add Quote Form', 'koala-gravity-integration' ); ?>
		</button>
	</p>

	<template id="kgi-additional-quote-form-template">
		<?php kgi_render_additional_quote_form_row( '__INDEX__', array(), $forms, $payload_fields ); ?>
	</template>

	<script>
	( function () {
		var container      = document.getElementById( 'kgi-additional-quote-forms' );
		var template       = document.getElementById( 'kgi-additional-quote-form-template' );
		var addButton      = document.getElementById( 'kgi-add-additional-quote-form' );
		var nextIndex      = <?php echo (int) count( $saved ); ?>;
		var ajaxUrl        = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var nonce          = <?php echo wp_json_encode( wp_create_nonce( 'kgi_get_form_fields' ) ); ?>;
		var notMappedLabel = <?php echo wp_json_encode( __( '— Not mapped —', 'koala-gravity-integration' ) ); ?>;

		if ( ! container || ! template || ! addButton ) {
			return;
		}

		function setSelectOptions( select, fields ) {
			var currentValue = select.value;
			select.innerHTML = '';

			var empty = document.createElement( 'option' );
			empty.value = '';
			empty.textContent = notMappedLabel;
			select.appendChild( empty );

			fields.forEach( function ( field ) {
				var option = document.createElement( 'option' );
				option.value = field.id;
				option.textContent = field.label;
				select.appendChild( option );
			} );

			if ( fields.some( function ( field ) { return String( field.id ) === currentValue; } ) ) {
				select.value = currentValue;
			}
		}

		function setRowCollapsed( row, collapsed ) {
			var body   = row.querySelector( '.kgi-additional-quote-form-body' );
			var toggle = row.querySelector( '.kgi-additional-quote-form-toggle' );
			var icon   = toggle ? toggle.querySelector( '.dashicons' ) : null;

			row.classList.toggle( 'kgi-collapsed', collapsed );

			if ( body ) {
				body.style.display = collapsed ? 'none' : '';
			}

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
			}

			if ( icon ) {
				icon.classList.toggle( 'dashicons-arrow-down-alt2', collapsed );
				icon.classList.toggle( 'dashicons-arrow-up-alt2', ! collapsed );
			}
		}

		function updateRowSummary( row ) {
			var summaryEl  = row.querySelector( '.kgi-additional-quote-form-summary' );
			var formSelect = row.querySelector( 'select[name$="[form_id]"]' );

			if ( ! summaryEl || ! formSelect ) {
				return;
			}

			summaryEl.textContent = formSelect.selectedIndex >= 0 ? formSelect.options[ formSelect.selectedIndex ].textContent : '';
		}

		function refreshRowFields( row, formId ) {
			if ( ! formId || '0' === String( formId ) ) {
				row.querySelectorAll( '.kgi-field-select' ).forEach( function ( select ) {
					setSelectOptions( select, [] );
				} );
				return;
			}

			var body = new URLSearchParams();
			body.set( 'action', 'kgi_get_form_fields' );
			body.set( 'nonce', nonce );
			body.set( 'form_id', formId );

			fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
				.then( function ( response ) { return response.json(); } )
				.then( function ( json ) {
					var fields = ( json && json.success && json.data ) ? json.data : [];
					row.querySelectorAll( '.kgi-field-select' ).forEach( function ( select ) {
						setSelectOptions( select, fields );
					} );
				} );
		}

		addButton.addEventListener( 'click', function () {
			var html    = template.innerHTML.replace( /__INDEX__/g, String( nextIndex ) );
			var wrapper = document.createElement( 'div' );
			wrapper.innerHTML = html.trim();
			container.appendChild( wrapper.firstElementChild );
			nextIndex++;
		} );

		container.addEventListener( 'click', function ( event ) {
			if ( event.target && event.target.closest( '.kgi-additional-quote-form-header' ) ) {
				var headerRow = event.target.closest( '.kgi-additional-quote-form-row' );
				setRowCollapsed( headerRow, ! headerRow.classList.contains( 'kgi-collapsed' ) );
				return;
			}

			if ( event.target && event.target.classList.contains( 'kgi-remove-additional-quote-form' ) ) {
				event.target.closest( '.kgi-additional-quote-form-row' ).remove();
			}
		} );

		container.addEventListener( 'change', function ( event ) {
			var target = event.target;

			if ( ! target ) {
				return;
			}

			if ( target.matches( 'select[name$="[form_id]"]' ) ) {
				refreshRowFields( target.closest( '.kgi-additional-quote-form-row' ), target.value );
				updateRowSummary( target.closest( '.kgi-additional-quote-form-row' ) );
			}
		} );
	} )();
	</script>
	<?php
}

/**
 * Renders one custom field row inside a fixed-location form's repeater.
 *
 * A custom field lets an admin push an arbitrary payload key — one not in
 * `kgi_get_payload_field_labels()` — alongside the fixed set, e.g. a field
 * specific to this one form. Used both for each saved custom field and for
 * the parent row's own hidden `<template class="kgi-custom-field-template">`
 * that "+ Add Custom Field" clones — in that case `$index` is the literal
 * placeholder string `__CUSTOM_INDEX__`, which the JS replaces with a real
 * integer before inserting the row.
 *
 * @since 0.3.0
 *
 * @param string                $row_name     The parent row's field name prefix, e.g. `kgi_fixed_location_forms[0]`.
 * @param int|string            $index        Custom field index within the row (or `__CUSTOM_INDEX__` for the template row).
 * @param array<string, mixed>  $custom_field Saved custom field (`key`, `field_id`), or an empty array for the template.
 * @param array<string, string> $field_options Field ID => label options for this row's currently selected Gravity Form.
 */
function kgi_render_fixed_location_custom_field_row( string $row_name, $index, array $custom_field, array $field_options ): void {
	$key      = $custom_field['key'] ?? '';
	$field_id = $custom_field['field_id'] ?? '';
	$name     = $row_name . '[custom_fields][' . $index . ']';
	?>
	<tr class="kgi-custom-field-row">
		<td>
			<input
				type="text"
				class="regular-text"
				name="<?php echo esc_attr( $name ); ?>[key]"
				value="<?php echo esc_attr( $key ); ?>"
				placeholder="<?php esc_attr_e( 'e.g. referral_source', 'koala-gravity-integration' ); ?>"
			/>
		</td>
		<td>
			<select class="kgi-field-select" name="<?php echo esc_attr( $name ); ?>[field_id]">
				<option value=""><?php esc_html_e( '— Not mapped —', 'koala-gravity-integration' ); ?></option>
				<?php foreach ( $field_options as $field_id_option => $field_label ) : ?>
					<option value="<?php echo esc_attr( $field_id_option ); ?>" <?php selected( (string) $field_id, (string) $field_id_option ); ?>>
						<?php echo esc_html( $field_label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<button type="button" class="button kgi-remove-custom-field">
				<?php esc_html_e( 'Remove', 'koala-gravity-integration' ); ?>
			</button>
		</td>
	</tr>
	<?php
}

/**
 * Renders one row of the fixed-location forms repeater.
 *
 * Used both for each saved config and for the hidden `<template>` row that
 * `kgi_render_fixed_location_forms_field()`'s JS clones when "Add" is clicked
 * — in that case `$index` is the literal placeholder string `__INDEX__`,
 * which the JS replaces with a real integer before inserting the row.
 *
 * "Form Field" dropdowns are populated from this row's currently selected
 * Gravity Form. Since each row can point at a different form, changing the
 * "Gravity Form" select re-fetches that form's fields via AJAX
 * (`kgi_ajax_get_form_fields()`) and repopulates every `.kgi-field-select` in
 * the row client-side — see the script in `kgi_render_fixed_location_forms_field()`.
 *
 * Rows for already-saved configs render collapsed, showing just the chosen
 * form and location name in the header — a row with a form and location
 * already saved is one an admin usually only needs to glance at, not
 * re-edit. The template row (and any row freshly added via "+ Add
 * Fixed-Location Form") renders expanded since it still needs filling in.
 * The header's summary text is kept live by JS as the Gravity Form/Location
 * selects change.
 *
 * @since 0.2.0
 *
 * @param int|string            $index          Row index (or `__INDEX__` for the template row).
 * @param array<string, mixed>  $config         Saved config for this row, or an empty array for the template.
 * @param mixed[]               $forms          All Gravity Forms, from GFAPI::get_forms().
 * @param WP_Post[]             $locations      All franchise location posts.
 * @param array<string, string> $payload_fields Payload key => label, from kgi_get_payload_field_labels().
 */
function kgi_render_fixed_location_form_row( $index, array $config, array $forms, array $locations, array $payload_fields ): void {
	$form_id           = $config['form_id'] ?? '';
	$location_id       = $config['location_id'] ?? '';
	$page_url_field_id = $config['page_url_field_id'] ?? '';
	$field_map         = $config['field_map'] ?? array();

	$name          = 'kgi_fixed_location_forms[' . $index . ']';
	$field_options = kgi_get_form_field_options( '__INDEX__' === $index ? 0 : (int) $form_id );

	$known_keys    = array_keys( $payload_fields );
	$custom_fields = array();

	foreach ( $field_map as $map_key => $map_field_id ) {
		if ( ! in_array( $map_key, $known_keys, true ) ) {
			$custom_fields[] = array(
				'key'      => $map_key,
				'field_id' => $map_field_id,
			);
		}
	}

	$form_label = __( '— Select a form —', 'koala-gravity-integration' );

	foreach ( $forms as $form ) {
		if ( (string) $form['id'] === (string) $form_id ) {
			$form_label = $form['title'] . ' (ID: ' . $form['id'] . ')';
			break;
		}
	}

	$location_label = __( '— Select a location —', 'koala-gravity-integration' );

	foreach ( $locations as $location ) {
		if ( (string) $location->ID === (string) $location_id ) {
			$location_label = $location->post_title . ' (ID: ' . $location->ID . ')';
			break;
		}
	}

	$collapsed = ! empty( $config );
	?>
	<div class="kgi-fixed-location-form-row postbox<?php echo $collapsed ? ' kgi-collapsed' : ''; ?>" style="margin-bottom: 12px; max-width: 640px;" data-next-custom-index="<?php echo esc_attr( count( $custom_fields ) ); ?>">
		<div class="kgi-fixed-location-form-header" style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 12px; cursor: pointer; background: #f6f7f7; border-bottom: 1px solid #dcdcde;">
			<strong class="kgi-fixed-location-form-summary"><?php echo esc_html( $form_label . ' — ' . $location_label ); ?></strong>
			<button type="button" class="button-link kgi-fixed-location-form-toggle" aria-expanded="<?php echo $collapsed ? 'false' : 'true'; ?>">
				<span class="dashicons <?php echo $collapsed ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-up-alt2'; ?>" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle this fixed-location form', 'koala-gravity-integration' ); ?></span>
			</button>
		</div>
		<div class="kgi-fixed-location-form-body" style="padding: 12px;<?php echo $collapsed ? ' display: none;' : ''; ?>">
			<p>
				<label>
					<strong><?php esc_html_e( 'Gravity Form', 'koala-gravity-integration' ); ?></strong><br>
					<select name="<?php echo esc_attr( $name ); ?>[form_id]">
						<option value="0"><?php esc_html_e( '— Select a form —', 'koala-gravity-integration' ); ?></option>
						<?php foreach ( $forms as $form ) : ?>
							<option value="<?php echo esc_attr( $form['id'] ); ?>" <?php selected( (string) $form_id, (string) $form['id'] ); ?>>
								<?php echo esc_html( $form['title'] . ' (ID: ' . $form['id'] . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
			<p>
				<label>
					<strong><?php esc_html_e( 'Location', 'koala-gravity-integration' ); ?></strong><br>
					<select name="<?php echo esc_attr( $name ); ?>[location_id]">
						<option value="0"><?php esc_html_e( '— Select a location —', 'koala-gravity-integration' ); ?></option>
						<?php foreach ( $locations as $location ) : ?>
							<option value="<?php echo esc_attr( $location->ID ); ?>" <?php selected( (string) $location_id, (string) $location->ID ); ?>>
								<?php echo esc_html( $location->post_title . ' (ID: ' . $location->ID . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
			</p>
			<p>
				<label>
					<strong><?php esc_html_e( 'Page URL Field ID', 'koala-gravity-integration' ); ?></strong><br>
					<input
						type="number"
						min="0"
						class="small-text"
						name="<?php echo esc_attr( $name ); ?>[page_url_field_id]"
						value="<?php echo esc_attr( $page_url_field_id ); ?>"
					/>
				</label>
				<p class="description">
					<?php esc_html_e( 'Optional hidden field ID that captures the page URL for the Google Sheet log. Leave blank if this form doesn\'t need it.', 'koala-gravity-integration' ); ?>
				</p>
			</p>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Payload Field', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Form Field', 'koala-gravity-integration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $payload_fields as $key => $label ) : ?>
						<?php $selected = $field_map[ $key ] ?? ''; ?>
						<tr>
							<td><?php echo esc_html( $label ); ?></td>
							<td>
								<select
									class="kgi-field-select"
									name="<?php echo esc_attr( $name ); ?>[field_map][<?php echo esc_attr( $key ); ?>]"
								>
									<option value=""><?php esc_html_e( '— Not mapped —', 'koala-gravity-integration' ); ?></option>
									<?php foreach ( $field_options as $field_id_option => $field_label ) : ?>
										<option value="<?php echo esc_attr( $field_id_option ); ?>" <?php selected( (string) $selected, (string) $field_id_option ); ?>>
											<?php echo esc_html( $field_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h4><?php esc_html_e( 'Custom Fields', 'koala-gravity-integration' ); ?></h4>
			<p class="description">
				<?php esc_html_e( 'Add extra fields to push that aren\'t listed above. The name entered becomes the key sent in the payload.', 'koala-gravity-integration' ); ?>
			</p>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Field Name', 'koala-gravity-integration' ); ?></th>
						<th><?php esc_html_e( 'Form Field', 'koala-gravity-integration' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody class="kgi-custom-fields-body">
					<?php foreach ( $custom_fields as $custom_index => $custom_field ) : ?>
						<?php kgi_render_fixed_location_custom_field_row( $name, $custom_index, $custom_field, $field_options ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p>
				<button type="button" class="button kgi-add-custom-field">
					<?php esc_html_e( '+ Add Custom Field', 'koala-gravity-integration' ); ?>
				</button>
			</p>
			<template class="kgi-custom-field-template">
				<?php kgi_render_fixed_location_custom_field_row( $name, '__CUSTOM_INDEX__', array(), array() ); ?>
			</template>

			<p>
				<button type="button" class="button kgi-remove-fixed-location-form">
					<?php esc_html_e( 'Remove This Form', 'koala-gravity-integration' ); ?>
				</button>
			</p>
		</div>
	</div>
	<?php
}

/**
 * Renders the fixed-location forms repeater.
 *
 * Each row configures a Gravity Form that always routes to one specific
 * franchise location (rather than resolving it dynamically per-request like
 * the main Quote Form). Rows are added/removed client-side with plain JS;
 * a removed row is simply absent from the submitted `$_POST` array, and
 * `kgi_sanitize_fixed_location_forms()` drops any row left with no form
 * selected.
 *
 * @since 0.2.0
 */
function kgi_render_fixed_location_forms_field(): void {
	$saved     = get_option( 'kgi_fixed_location_forms', array() );
	$forms     = GFAPI::get_forms();
	$locations = get_posts(
		array(
			'post_type'      => kgi_get_location_post_type(),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$payload_fields = kgi_get_payload_field_labels();
	?>
	<p class="description">
		<?php esc_html_e( 'Route additional Gravity Forms to one fixed franchise location each — e.g. a dedicated form embedded on a single location\'s landing page — instead of resolving the location dynamically from the page URL like the main Quote Form above. Each row has its own field mapping since it can be a completely different form.', 'koala-gravity-integration' ); ?>
	</p>

	<div id="kgi-fixed-location-forms">
		<?php foreach ( array_values( $saved ) as $index => $config ) : ?>
			<?php kgi_render_fixed_location_form_row( $index, $config, $forms, $locations, $payload_fields ); ?>
		<?php endforeach; ?>
	</div>

	<p>
		<button type="button" class="button" id="kgi-add-fixed-location-form">
			<?php esc_html_e( '+ Add Fixed-Location Form', 'koala-gravity-integration' ); ?>
		</button>
	</p>

	<template id="kgi-fixed-location-form-template">
		<?php kgi_render_fixed_location_form_row( '__INDEX__', array(), $forms, $locations, $payload_fields ); ?>
	</template>

	<script>
	( function () {
		var container      = document.getElementById( 'kgi-fixed-location-forms' );
		var template       = document.getElementById( 'kgi-fixed-location-form-template' );
		var addButton      = document.getElementById( 'kgi-add-fixed-location-form' );
		var nextIndex      = <?php echo (int) count( $saved ); ?>;
		var ajaxUrl        = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var nonce          = <?php echo wp_json_encode( wp_create_nonce( 'kgi_get_form_fields' ) ); ?>;
		var notMappedLabel = <?php echo wp_json_encode( __( '— Not mapped —', 'koala-gravity-integration' ) ); ?>;

		if ( ! container || ! template || ! addButton ) {
			return;
		}

		function setSelectOptions( select, fields ) {
			var currentValue = select.value;
			select.innerHTML = '';

			var empty = document.createElement( 'option' );
			empty.value = '';
			empty.textContent = notMappedLabel;
			select.appendChild( empty );

			fields.forEach( function ( field ) {
				var option = document.createElement( 'option' );
				option.value = field.id;
				option.textContent = field.label;
				select.appendChild( option );
			} );

			if ( fields.some( function ( field ) { return String( field.id ) === currentValue; } ) ) {
				select.value = currentValue;
			}
		}

		function cacheFields( row, fields ) {
			row.setAttribute( 'data-kgi-fields', JSON.stringify( fields ) );
		}

		function getCachedFields( row ) {
			try {
				return JSON.parse( row.getAttribute( 'data-kgi-fields' ) || '[]' );
			} catch ( e ) {
				return [];
			}
		}

		function setRowCollapsed( row, collapsed ) {
			var body   = row.querySelector( '.kgi-fixed-location-form-body' );
			var toggle = row.querySelector( '.kgi-fixed-location-form-toggle' );
			var icon   = toggle ? toggle.querySelector( '.dashicons' ) : null;

			row.classList.toggle( 'kgi-collapsed', collapsed );

			if ( body ) {
				body.style.display = collapsed ? 'none' : '';
			}

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
			}

			if ( icon ) {
				icon.classList.toggle( 'dashicons-arrow-down-alt2', collapsed );
				icon.classList.toggle( 'dashicons-arrow-up-alt2', ! collapsed );
			}
		}

		function updateRowSummary( row ) {
			var summaryEl       = row.querySelector( '.kgi-fixed-location-form-summary' );
			var formSelect      = row.querySelector( 'select[name$="[form_id]"]' );
			var locationSelect  = row.querySelector( 'select[name$="[location_id]"]' );

			if ( ! summaryEl ) {
				return;
			}

			var formLabel     = formSelect && formSelect.selectedIndex >= 0 ? formSelect.options[ formSelect.selectedIndex ].textContent : '';
			var locationLabel = locationSelect && locationSelect.selectedIndex >= 0 ? locationSelect.options[ locationSelect.selectedIndex ].textContent : '';

			summaryEl.textContent = formLabel + ' — ' + locationLabel;
		}

		function refreshRowFields( row, formId ) {
			if ( ! formId || '0' === String( formId ) ) {
				cacheFields( row, [] );
				row.querySelectorAll( '.kgi-field-select' ).forEach( function ( select ) {
					setSelectOptions( select, [] );
				} );
				return;
			}

			var body = new URLSearchParams();
			body.set( 'action', 'kgi_get_form_fields' );
			body.set( 'nonce', nonce );
			body.set( 'form_id', formId );

			fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
				.then( function ( response ) { return response.json(); } )
				.then( function ( json ) {
					var fields = ( json && json.success && json.data ) ? json.data : [];
					cacheFields( row, fields );
					row.querySelectorAll( '.kgi-field-select' ).forEach( function ( select ) {
						setSelectOptions( select, fields );
					} );
				} );
		}

		addButton.addEventListener( 'click', function () {
			var html    = template.innerHTML.replace( /__INDEX__/g, String( nextIndex ) );
			var wrapper = document.createElement( 'div' );
			wrapper.innerHTML = html.trim();
			var row = wrapper.firstElementChild;
			row.setAttribute( 'data-next-custom-index', '0' );
			container.appendChild( row );
			nextIndex++;
		} );

		container.addEventListener( 'click', function ( event ) {
			if ( event.target && event.target.closest( '.kgi-fixed-location-form-header' ) ) {
				var headerRow = event.target.closest( '.kgi-fixed-location-form-row' );
				setRowCollapsed( headerRow, ! headerRow.classList.contains( 'kgi-collapsed' ) );
				return;
			}

			if ( event.target && event.target.classList.contains( 'kgi-remove-fixed-location-form' ) ) {
				event.target.closest( '.kgi-fixed-location-form-row' ).remove();
				return;
			}

			if ( event.target && event.target.classList.contains( 'kgi-add-custom-field' ) ) {
				var row            = event.target.closest( '.kgi-fixed-location-form-row' );
				var customTemplate = row.querySelector( '.kgi-custom-field-template' );
				var body           = row.querySelector( '.kgi-custom-fields-body' );

				if ( ! customTemplate || ! body ) {
					return;
				}

				var nextCustomIndex = parseInt( row.getAttribute( 'data-next-custom-index' ) || '0', 10 );
				var html            = customTemplate.innerHTML.replace( /__CUSTOM_INDEX__/g, String( nextCustomIndex ) );
				var wrapper         = document.createElement( 'tbody' );
				wrapper.innerHTML = html.trim();
				var newRow = wrapper.firstElementChild;
				body.appendChild( newRow );
				setSelectOptions( newRow.querySelector( '.kgi-field-select' ), getCachedFields( row ) );
				row.setAttribute( 'data-next-custom-index', String( nextCustomIndex + 1 ) );
				return;
			}

			if ( event.target && event.target.classList.contains( 'kgi-remove-custom-field' ) ) {
				event.target.closest( '.kgi-custom-field-row' ).remove();
			}
		} );

		container.addEventListener( 'change', function ( event ) {
			var target = event.target;

			if ( ! target ) {
				return;
			}

			if ( target.matches( 'select[name$="[form_id]"]' ) ) {
				refreshRowFields( target.closest( '.kgi-fixed-location-form-row' ), target.value );
			}

			if ( target.matches( 'select[name$="[form_id]"]' ) || target.matches( 'select[name$="[location_id]"]' ) ) {
				updateRowSummary( target.closest( '.kgi-fixed-location-form-row' ) );
			}
		} );

		// Seed each existing row's field cache from its server-rendered options,
		// so "+ Add Custom Field" has a field list to draw from without an
		// AJAX round trip for rows that already have a form selected.
		container.querySelectorAll( '.kgi-fixed-location-form-row' ).forEach( function ( row ) {
			var customBody = row.querySelector( '.kgi-custom-fields-body' );
			row.setAttribute( 'data-next-custom-index', String( customBody ? customBody.children.length : 0 ) );

			var fields       = [];
			var seenFieldIds = {};

			row.querySelectorAll( '.kgi-field-select' ).forEach( function ( select ) {
				Array.prototype.forEach.call( select.options, function ( option ) {
					if ( option.value && ! seenFieldIds[ option.value ] ) {
						seenFieldIds[ option.value ] = true;
						fields.push( { id: option.value, label: option.textContent } );
					}
				} );
			} );

			cacheFields( row, fields );
		} );
	} )();
	</script>
	<?php
}

/**
 * Renders the settings page.
 *
 * @since 0.1.0
 */
function kgi_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Koala Gravity Integration', 'koala-gravity-integration' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'kgi_settings' );
			do_settings_sections( 'koala-gravity-integration' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
