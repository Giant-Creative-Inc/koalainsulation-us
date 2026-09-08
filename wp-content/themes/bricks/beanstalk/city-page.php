<?php
/**
 * Shared City Page pattern, Content Engine, and dynamic-value foundation.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a stable metadata.name target for an allowlisted City Page field.
 *
 * @param string $field Semantic field in namespace.field form.
 * @return string
 */
function koala_beanstalk_city_field_name( string $field ): string {
	$namespaces = array( 'hero', 'residential_services', 'commercial_services', 'why_koala', 'testimonials', 'faq', 'final_cta', 'seo' );
	$parts      = explode( '.', $field, 2 );

	if ( 2 !== count( $parts ) || ! in_array( $parts[0], $namespaces, true ) || ! preg_match( '/^[a-z][a-z0-9_]*$/', $parts[1] ) ) {
		return '';
	}

	return 'koala-city-page-field-' . str_replace( '_', '-', $parts[0] . '-' . $parts[1] );
}

/** Register the unstyled City Page Gutenberg pattern. */
function koala_beanstalk_register_city_page_pattern(): void {
	$pattern_path = __DIR__ . '/patterns/city-page.php';
	if ( ! is_readable( $pattern_path ) ) {
		return;
	}

	ob_start();
	include $pattern_path;
	$content = (string) ob_get_clean();

	register_block_pattern_category( 'koala-city-pages', array( 'label' => 'Koala City Pages' ) );
	register_block_pattern(
		'koala/city-page',
		array(
			'title'       => 'Koala City Page',
			'description' => 'Semantic foundation for a location-aware Koala City Page.',
			'categories'  => array( 'koala-city-pages' ),
			'postTypes'   => array( 'resources-landing-pa' ),
			'content'     => $content,
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_city_page_pattern' );

/**
 * Return this module's Content Engine provider definition.
 *
 * @return array<string, mixed>
 */
function koala_beanstalk_get_content_engine_provider(): array {
	return array(
		'id'                 => 'koala-beanstalk',
		'label'              => 'Koala Beanstalk',
		'source_type'        => 'parent-theme',
		'pattern_directory'  => __DIR__ . '/patterns',
		'manifest_directory' => __DIR__ . '/pattern-manifests',
		'priority'           => 200,
	);
}

/** Register this parent-theme module as a Content Engine provider. */
function koala_beanstalk_register_content_engine_provider(): void {
	if ( ! function_exists( 'beanstalk_content_engine_register_provider' ) ) {
		return;
	}

	beanstalk_content_engine_register_provider( koala_beanstalk_get_content_engine_provider() );
}
add_action( 'beanstalk_content_engine_register_providers', 'koala_beanstalk_register_content_engine_provider' );

/**
 * Return exact placeholder-to-context-key mappings.
 *
 * @return array<string, string>
 */
function koala_beanstalk_get_location_placeholders(): array {
	return array(
		'{{service_area_name}}'  => 'service_area_name',
		'{{location_name}}'      => 'location_name',
		'{{state_name}}'         => 'state_name',
		'{{state_abbreviation}}' => 'state_abbreviation',
		'{{location_phone}}'     => 'phone',
		'{{location_phone_url}}' => 'phone_url',
		'{{location_home_url}}'  => 'home_url',
	);
}

/**
 * Replace only allowlisted location placeholders for an HTML text context.
 *
 * Unknown placeholders remain visible so editorial mistakes are detectable.
 * Missing allowlisted values render as empty strings.
 *
 * @param string                    $content Editable placeholder-bearing copy.
 * @param array<string, mixed>|null $context Optional resolved context.
 * @return string
 */
function koala_beanstalk_replace_location_placeholders( string $content, ?array $context = null ): string {
	$context      = null === $context ? koala_beanstalk_get_location_context() : $context;
	$replacements = array();

	foreach ( koala_beanstalk_get_location_placeholders() as $placeholder => $context_key ) {
		$value                        = isset( $context[ $context_key ] ) && is_scalar( $context[ $context_key ] ) ? (string) $context[ $context_key ] : '';
		$replacements[ $placeholder ] = esc_html( $value );
	}

	return strtr( $content, $replacements );
}

/**
 * Replace placeholders only in named City Page editorial blocks.
 *
 * @param string                    $block_content Rendered block HTML.
 * @param array<string, mixed>      $block         Parsed block.
 * @param array<string, mixed>|null $context       Testable context override.
 * @param bool|null                 $eligible      Testable eligibility override.
 * @return string
 */
function koala_beanstalk_render_location_placeholders( string $block_content, array $block, ?array $context = null, ?bool $eligible = null ): string {
	$eligible = null === $eligible ? koala_is_beanstalk_area_served_page() : $eligible;
	if ( ! $eligible ) {
		return $block_content;
	}

	$block_name  = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';
	$target_name = isset( $block['attrs']['metadata']['name'] ) ? (string) $block['attrs']['metadata']['name'] : '';
	if ( ! in_array( $block_name, array( 'core/details', 'core/heading', 'core/paragraph' ), true ) || 0 !== strpos( $target_name, 'koala-city-page-field-' ) ) {
		return $block_content;
	}

	return koala_beanstalk_replace_location_placeholders( $block_content, $context );
}
add_filter( 'render_block', 'koala_beanstalk_render_location_placeholders', 10, 2 );

/** Register the server-rendered location phone block. */
function koala_beanstalk_register_city_page_blocks(): void {
	$editor_script_path = __DIR__ . '/assets/city-page-editor.js';

	wp_register_script(
		'koala-beanstalk-city-page-editor',
		get_template_directory_uri() . '/beanstalk/assets/city-page-editor.js',
		array( 'wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n' ),
		is_readable( $editor_script_path ) ? (string) filemtime( $editor_script_path ) : null,
		true
	);

	register_block_type(
		'koala/location-phone',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'attributes'      => array(
				'label' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'koala_beanstalk_render_location_phone_block',
		)
	);

	register_block_type(
		'koala/location-review-summary',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'render_callback' => 'koala_beanstalk_render_location_review_summary_block',
		)
	);

	register_block_type(
		'koala/location-gravity-form',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'attributes'      => array(
				'formId' => array(
					'type'    => 'integer',
					'default' => 13,
				),
			),
			'render_callback' => 'koala_beanstalk_render_location_gravity_form_block',
		)
	);

	register_block_type(
		'koala/location-services',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'render_callback' => 'koala_beanstalk_render_location_services_block',
		)
	);

	register_block_type(
		'koala/location-service-card-link',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'attributes'      => array(
				'serviceKey' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'koala_beanstalk_render_location_service_card_link_block',
		)
	);

	register_block_type(
		'koala/location-service-cta',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'attributes'      => array(
				'serviceKey' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'koala_beanstalk_render_location_service_cta_block',
		)
	);

	register_block_type(
		'koala/city-page-quote-cta',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'render_callback' => 'koala_beanstalk_render_city_page_quote_cta_block',
		)
	);

	register_block_type(
		'koala/why-koala-video',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'render_callback' => 'koala_beanstalk_render_why_koala_video_block',
		)
	);

	register_block_type(
		'koala/location-reviews',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-city-page-editor',
			'render_callback' => 'koala_beanstalk_render_location_reviews_block',
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_city_page_blocks' );

/**
 * Render the related location's phone link without saved frontend markup.
 *
 * @param array<string, mixed>      $attributes Block attributes.
 * @param string                    $content    Saved block content.
 * @param WP_Block|null             $block      Block instance.
 * @param array<string, mixed>|null $context    Testable context override.
 * @param array<string, mixed>|null $summary    Optional testable review summary.
 * @param bool|null                 $has_summary_block Whether the post contains the dedicated block.
 * @param bool|null                 $eligible   Optional City Page eligibility override.
 * @return string
 */
function koala_beanstalk_render_location_phone_block( array $attributes, string $content = '', $block = null, ?array $context = null, ?array $summary = null, ?bool $has_summary_block = null, ?bool $eligible = null ): string {
	unset( $content, $block );
	$context = null === $context ? koala_beanstalk_get_location_context() : $context;
	$phone   = isset( $context['phone'] ) ? (string) $context['phone'] : '';
	$url     = isset( $context['phone_url'] ) ? (string) $context['phone_url'] : '';
	if ( '' === $phone || '' === $url ) {
		return '';
	}

	$label = isset( $attributes['label'] ) && '' !== trim( (string) $attributes['label'] ) ? (string) $attributes['label'] : $phone;

	$phone_markup = sprintf(
		'<a class="koala-location-phone" href="%1$s" aria-label="%2$s">%3$s</a>',
		esc_url( $url ),
		esc_attr( $label ),
		esc_html( $label )
	);
	$eligible     = null === $eligible ? function_exists( 'koala_is_beanstalk_area_served_page' ) && koala_is_beanstalk_area_served_page() : $eligible;
	if ( ! $eligible ) {
		return $phone_markup;
	}

	$has_summary_block = null === $has_summary_block ? has_block( 'koala/location-review-summary' ) : $has_summary_block;
	if ( $has_summary_block ) {
		return $phone_markup;
	}

	$summary = null === $summary ? koala_beanstalk_get_location_google_review_summary( $context ) : $summary;

	return $phone_markup . koala_beanstalk_render_location_review_summary_block( array(), '', null, $summary );
}

/**
 * Read and normalize the rating totals from the configured Google Reviews feed.
 *
 * The Google Reviews plugin remains responsible for synchronizing remote data.
 * City Pages read its server-side feed API and never contact Google in-browser.
 *
 * @param int $feed_id Validated Google Reviews feed post ID.
 * @return array{rating:float,count:int}|array{}
 */
function koala_beanstalk_load_google_review_summary( int $feed_id ): array {
	$deserializer_class = 'WP_Rplg_Google_Reviews\\Includes\\Feed_Deserializer';
	$core_class         = 'WP_Rplg_Google_Reviews\\Includes\\Core\\Core';
	if ( $feed_id < 1 || ! class_exists( $deserializer_class ) || ! class_exists( $core_class ) || ! class_exists( 'WP_Query' ) ) {
		return array();
	}

	$feed = ( new $deserializer_class( new WP_Query() ) )->get_feed( $feed_id );
	if ( ! $feed ) {
		return array();
	}

	$data       = ( new $core_class() )->get_reviews( $feed );
	$businesses = isset( $data['businesses'] ) && is_array( $data['businesses'] ) ? $data['businesses'] : array();
	$count      = 0;
	$weighted   = 0.0;
	foreach ( $businesses as $business ) {
		$business_count  = isset( $business->review_count ) ? (int) $business->review_count : 0;
		$business_rating = isset( $business->rating ) ? (float) $business->rating : 0.0;
		if ( $business_count < 1 || $business_rating <= 0.0 || $business_rating > 5.0 ) {
			continue;
		}
		$count    += $business_count;
		$weighted += $business_rating * $business_count;
	}

	return $count > 0
		? array(
			'rating' => round( $weighted / $count, 1 ),
			'count'  => $count,
		)
		: array();
}

/**
 * Get a request-safe, location-and-feed-specific Google review summary.
 *
 * Clearing WordPress transients causes the next SSR request to read the latest
 * data synchronized by the Google Reviews plugin and repopulate this cache.
 *
 * @param array<string, mixed>|null $context Optional testable location context.
 * @param callable|null             $loader  Optional test-only feed loader.
 * @return array{rating:float,count:int}|array{}
 */
function koala_beanstalk_get_location_google_review_summary( ?array $context = null, ?callable $loader = null ): array {
	$context     = null === $context ? koala_beanstalk_get_location_context() : $context;
	$location_id = isset( $context['id'] ) ? (int) $context['id'] : 0;
	$config      = koala_beanstalk_get_location_review_config( $context );
	$feed_id     = (int) $config['feed_id'];
	if ( $location_id < 1 || $feed_id < 1 ) {
		return array();
	}

	$cache_key = sprintf( 'koala_city_review_summary_v1_%d_%d', $location_id, $feed_id );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) && isset( $cached['rating'], $cached['count'] ) && (float) $cached['rating'] > 0.0 && (float) $cached['rating'] <= 5.0 && (int) $cached['count'] > 0 ) {
		return array(
			'rating' => (float) $cached['rating'],
			'count'  => (int) $cached['count'],
		);
	}

	$summary = $loader ? $loader() : koala_beanstalk_load_google_review_summary( $feed_id );
	if ( ! is_array( $summary ) || ! isset( $summary['rating'], $summary['count'] ) || (float) $summary['rating'] <= 0.0 || (float) $summary['rating'] > 5.0 || (int) $summary['count'] < 1 ) {
		return array();
	}

	$summary = array(
		'rating' => round( (float) $summary['rating'], 1 ),
		'count'  => (int) $summary['count'],
	);
	set_transient( $cache_key, $summary, 43200 );

	return $summary;
}

/**
 * Render the related location's cached Google rating proof.
 *
 * @param array<string, mixed>                       $attributes Block attributes.
 * @param string                                     $content    Saved block content.
 * @param WP_Block|null                              $block      Block instance.
 * @param array{rating:float,count:int}|array{}|null $summary Optional testable summary.
 * @return string
 */
function koala_beanstalk_render_location_review_summary_block( array $attributes, string $content = '', $block = null, ?array $summary = null ): string {
	unset( $attributes, $content, $block );
	$summary = null === $summary ? koala_beanstalk_get_location_google_review_summary() : $summary;
	if ( ! isset( $summary['rating'], $summary['count'] ) || (float) $summary['rating'] <= 0.0 || (int) $summary['count'] < 1 ) {
		return '';
	}

	$rating_value = (float) $summary['rating'];
	$rating       = abs( $rating_value - round( $rating_value ) ) < 0.00001
		? number_format( $rating_value, 0, '.', '' )
		: number_format( $rating_value, 1, '.', '' );
	$count        = number_format( (int) $summary['count'] );
	$star_url     = get_template_directory_uri() . '/beanstalk/assets/city-page/rating-star.svg';
	$star         = '<img class="koala-city-hero__review-star" src="' . esc_url( $star_url ) . '" alt="" width="24" height="24">';
	$text         = sprintf( '%s/5 from %s+ verified reviews', $rating, $count );
	$aria_text    = sprintf( 'Rated %s out of 5 from %s verified Google reviews', $rating, $count );

	return '<div class="koala-city-hero__review-summary" aria-label="' . esc_attr( $aria_text ) . '">'
		. '<div class="koala-city-hero__review-stars" aria-hidden="true">' . str_repeat( $star, 5 ) . '</div>'
		. '<p class="koala-city-hero__review-text">' . esc_html( $text ) . '</p>'
		. '</div>';
}

/**
 * Replace an input submit value while preserving Gravity Forms markup.
 *
 * @param string $button Existing Gravity Forms submit control.
 * @param string $label  Editable button label.
 * @return string
 */
function koala_beanstalk_replace_gravity_form_submit_label( string $button, string $label ): string {
	if ( '' === trim( $label ) ) {
		return $button;
	}

	$replacement = 'value="' . esc_attr( $label ) . '"';
	$button      = preg_replace( '/value=("|\').*?\1/i', $replacement, $button, 1 );

	return is_string( $button ) ? $button : '';
}

/**
 * Render the configured Gravity Form using related-location context.
 *
 * @param array<string, mixed>      $attributes              Block attributes.
 * @param string                    $content                 Rendered inner submit-label block.
 * @param WP_Block|null             $block                   Block instance.
 * @param array<string, mixed>|null $context                 Testable context override.
 * @param bool|null                 $gravity_forms_available Testable availability override.
 * @return string
 */
function koala_beanstalk_render_location_gravity_form_block( array $attributes, string $content = '', $block = null, ?array $context = null, ?bool $gravity_forms_available = null ): string {
	unset( $block );
	$context                 = null === $context ? koala_beanstalk_get_location_context() : $context;
	$gravity_forms_available = null === $gravity_forms_available ? function_exists( 'gravity_form' ) : $gravity_forms_available;
	if ( ! $gravity_forms_available ) {
		return '';
	}

	$location_form_id = isset( $context['gravity_form_id'] ) ? absint( $context['gravity_form_id'] ) : 0;
	$default_form_id  = isset( $attributes['formId'] ) ? absint( $attributes['formId'] ) : 13;
	$form_id          = $location_form_id ? $location_form_id : $default_form_id;
	if ( ! $form_id ) {
		return '';
	}

	$submit_label = trim( wp_strip_all_tags( $content ) );
	$filter       = static function ( string $button ) use ( $submit_label ): string {
		return koala_beanstalk_replace_gravity_form_submit_label( $button, $submit_label );
	};
	$filter_name  = 'gform_submit_button_' . $form_id;
	add_filter( $filter_name, $filter );

	$form_markup = (string) gravity_form( $form_id, false, false, false, null, false, 0, false );
	remove_filter( $filter_name, $filter );

	return '<div class="koala-city-hero__form-body">' . $form_markup . '</div>';
}

/**
 * Resolve one allowlisted residential service URL from the shared context.
 *
 * All related services are loaded while building the request-scoped context;
 * rendering six cards only loops over that in-memory collection.
 *
 * @param string                    $service_key Stable residential service key.
 * @param array<string, mixed>|null $context     Optional testable context.
 * @return string
 */
function koala_beanstalk_get_location_service_url( string $service_key, ?array $context = null ): string {
	$patterns = array(
		'spray_foam'            => 'spray-foam',
		'blown_in'              => 'blown-in',
		'fiberglass_batt'       => 'batt-insulation',
		'solar_attic_fans'      => 'solar-attic-fans',
		'air_sealing'           => 'air-sealing',
		'insulation_removal'    => 'insulation-removal',
		'commercial_insulation' => 'commercial-insulation',
	);
	if ( ! isset( $patterns[ $service_key ] ) ) {
		return '';
	}

	$context  = null === $context ? koala_beanstalk_get_location_context() : $context;
	$services = isset( $context['service_urls'] ) && is_array( $context['service_urls'] ) ? $context['service_urls'] : array();
	foreach ( $services as $service ) {
		$url = isset( $service['url'] ) ? (string) $service['url'] : '';
		if ( '' !== $url && false !== strpos( strtolower( $url ), $patterns[ $service_key ] ) ) {
			return $url;
		}
	}

	return '';
}

/**
 * Render every published service related to the shared location context.
 *
 * @param array<string, mixed>      $attributes Block attributes.
 * @param string                    $content    Saved block content.
 * @param WP_Block|null             $block      Block instance.
 * @param array<string, mixed>|null $context    Optional testable context.
 * @return string
 */
function koala_beanstalk_render_location_services_block( array $attributes, string $content = '', $block = null, ?array $context = null ): string {
	unset( $attributes, $content, $block );
	$context    = null === $context ? koala_beanstalk_get_location_context() : $context;
	$services   = isset( $context['service_urls'] ) && is_array( $context['service_urls'] ) ? $context['service_urls'] : array();
	$cards      = '';
	$card_count = 0;

	foreach ( $services as $service ) {
		$title    = isset( $service['title'] ) ? trim( (string) $service['title'] ) : '';
		$url      = isset( $service['url'] ) ? (string) $service['url'] : '';
		$image_id = isset( $service['image_id'] ) ? absint( $service['image_id'] ) : 0;
		if ( '' === $title || '' === $url ) {
			continue;
		}
		++$card_count;

		$image = '';
		if ( $image_id ) {
			$image_alt = isset( $service['image_alt'] ) && '' !== trim( (string) $service['image_alt'] )
				? (string) $service['image_alt']
				: $title;
			$image     = '<figure class="wp-block-image size-large koala-city-residential-services__image">'
				. wp_get_attachment_image(
					$image_id,
					'large',
					false,
					array(
						'alt'     => $image_alt,
						'class'   => 'koala-city-residential-services__image-file',
						'loading' => 'lazy',
					)
				)
				. '</figure>';
		}

		$cards .= '<a class="koala-city-residential-services__card-link" href="' . esc_url( $url ) . '">'
			. '<div class="wp-block-group koala-city-residential-services__card">'
			. $image
			. '<p class="koala-city-residential-services__card-title">' . esc_html( $title ) . '</p>'
			. '</div></a>';
	}

	if ( 0 === $card_count ) {
		return '';
	}

	$items_class = 'koala-city-residential-services__items';
	if ( $card_count > 6 ) {
		$items_class .= ' koala-city-residential-services__items--many';
	}

	return '<div class="' . esc_attr( $items_class ) . '">' . $cards . '</div>';
}

/**
 * Render an editable service card linked to the related location's service.
 *
 * @param array<string, mixed>      $attributes Block attributes.
 * @param string                    $content    Saved editable card blocks.
 * @param WP_Block|null             $block      Block instance.
 * @param array<string, mixed>|null $context    Optional testable context.
 * @return string
 */
function koala_beanstalk_render_location_service_card_link_block( array $attributes, string $content = '', $block = null, ?array $context = null ): string {
	unset( $block );
	$service_key = isset( $attributes['serviceKey'] ) ? sanitize_key( (string) $attributes['serviceKey'] ) : '';
	$url         = koala_beanstalk_get_location_service_url( $service_key, $context );
	if ( '' === $url || '' === trim( $content ) ) {
		return '';
	}

	return '<a class="koala-city-residential-services__card-link" href="' . esc_url( $url ) . '">' . $content . '</a>';
}

/**
 * Render the editable commercial CTA linked to the City Page quote form.
 *
 * @param array<string, mixed>      $attributes Block attributes.
 * @param string                    $content    Saved editable label block.
 * @param WP_Block|null             $block      Block instance.
 * @param array<string, mixed>|null $context    Optional testable context.
 * @return string
 */
function koala_beanstalk_render_location_service_cta_block( array $attributes, string $content = '', $block = null, ?array $context = null ): string {
	unset( $attributes, $block, $context );
	$label = trim( wp_strip_all_tags( $content ) );
	if ( '' === $label ) {
		return '';
	}

	return '<a class="koala-city-commercial-services__cta koala-city-page__quote-cta" href="#city-page-quote">' . esc_html( $label ) . '</a>';
}

/**
 * Render an editable CTA label linked to the City Page quote form.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Saved editable label block.
 * @return string
 */
function koala_beanstalk_render_city_page_quote_cta_block( array $attributes, string $content = '' ): string {
	unset( $attributes );
	$label = trim( wp_strip_all_tags( $content ) );
	if ( '' === $label ) {
		return '';
	}

	return '<a class="koala-city-page__quote-cta koala-city-why-koala__cta" href="#city-page-quote">' . esc_html( $label ) . '</a>';
}

/**
 * Turn the editable Why Koala image into a lazy Vimeo player.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Saved editable image block.
 * @return string
 */
function koala_beanstalk_render_why_koala_video_block( array $attributes, string $content = '' ): string {
	unset( $attributes );
	$embed_url = 'https://player.vimeo.com/video/1208167466?h=900b1374b3&byline=0&title=0&portrait=0';
	$page_url  = 'https://vimeo.com/1208167466/900b1374b3';

	return '<div class="koala-city-why-koala__media" data-video-src="' . esc_url( $embed_url ) . '">'
		. $content
		. '<button type="button" class="koala-city-why-koala__video-trigger" aria-label="Play the What to Expect video" aria-controls="koala-city-why-koala-video"><span class="koala-city-why-koala__play" aria-hidden="true"></span></button>'
		. '<iframe id="koala-city-why-koala-video" class="koala-city-why-koala__video" title="What to Expect from Koala Insulation" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen hidden></iframe>'
		. '<noscript><a href="' . esc_url( $page_url ) . '">Watch the What to Expect video on Vimeo</a></noscript>'
		. '</div>';
}

/**
 * Reduce the related location's review settings to public, allowlisted data.
 *
 * City Pages prefer the related location's Google Place ID and retain the
 * existing registered feed as a compatibility fallback. Both values are
 * reduced to strict allowlists so stored shortcode text is never executed.
 *
 * @param array<string, mixed>|null $context Optional testable context.
 * @return array{provider:string,place_id:string,feed_id:int,review_url:string}
 */
function koala_beanstalk_get_location_review_config( ?array $context = null ): array {
	$context          = null === $context ? koala_beanstalk_get_location_context() : $context;
	$stored_place_id  = isset( $context['google_place_id'] ) ? trim( (string) $context['google_place_id'] ) : '';
	$google_shortcode = isset( $context['google_review_shortcode'] ) ? trim( (string) $context['google_review_shortcode'] ) : '';
	$place_id         = preg_match( '/^[A-Za-z0-9_-]{10,255}$/', $stored_place_id ) ? $stored_place_id : '';
	$feed_id          = 0;

	if ( preg_match( '/^\[grw\s+id=(?:"|\')?([1-9][0-9]*)(?:"|\')?\s*\/?\]$/', $google_shortcode, $matches ) ) {
		$feed_id = (int) $matches[1];
	}

	return array(
		'provider'   => ( $place_id || $feed_id ) ? 'google-reviews' : '',
		'place_id'   => $place_id,
		'feed_id'    => $feed_id,
		'review_url' => isset( $context['review_url'] ) ? (string) $context['review_url'] : '',
	);
}

/**
 * Render the related location's allowlisted Google Reviews feed server-side.
 *
 * @param array<string, mixed>      $attributes Block attributes.
 * @param string                    $content    Editable fallback label block.
 * @param WP_Block|null             $block      Block instance.
 * @param array<string, mixed>|null $context    Optional testable context.
 * @return string
 */
function koala_beanstalk_render_location_reviews_block( array $attributes, string $content = '', $block = null, ?array $context = null ): string {
	unset( $attributes, $block );
	$config      = koala_beanstalk_get_location_review_config( $context );
	$label       = trim( wp_strip_all_tags( $content ) );
	$label       = $label ? $label : 'Read customer reviews';
	$link        = $config['review_url'];
	$link_markup = $link
		? '<a class="koala-city-testimonials__fallback-link" href="' . esc_url( $link ) . '">' . esc_html( $label ) . '</a>'
		: '';

	if ( 'google-reviews' !== $config['provider'] || ( ! $config['place_id'] && ! $config['feed_id'] ) || ! shortcode_exists( 'grw' ) ) {
		return '<div class="koala-city-testimonials__fallback">' . $link_markup . '</div>';
	}

	$widget = $config['place_id']
		? do_shortcode( sprintf( '[grw place_id="%s" view_mode="slider"]', esc_attr( $config['place_id'] ) ) )
		: do_shortcode( sprintf( '[grw id="%d"]', $config['feed_id'] ) );
	if ( '' === trim( $widget ) ) {
		return '<div class="koala-city-testimonials__fallback">' . $link_markup . '</div>';
	}

	return '<div class="koala-city-testimonials__widget" data-review-provider="google-reviews">'
		. '<div class="koala-city-testimonials__widget-space">' . $widget . '</div>'
		. '<noscript><div class="koala-city-testimonials__noscript">' . $link_markup . '</div></noscript>'
		. '</div>';
}
