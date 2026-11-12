<?php
/**
 * Local Atlanta service hero preview.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the service slugs supported by this Local preview. */
function koala_beanstalk_service_preview_slugs(): array {
	return array( 'spray-foam-insulation', 'blown-in-attic-insulation', 'insulation-removal-services' );
}

/** Identify a national service supported by this Local preview. */
function koala_beanstalk_is_national_service(): bool {
	return is_singular( 'service' ) && in_array( get_queried_object()->post_name ?? '', koala_beanstalk_service_preview_slugs(), true );
}

/**
 * Read the National USA editorial copy for a service preview.
 *
 * @param string $slug National service slug. Defaults to the queried service.
 */
function koala_beanstalk_national_editorial_copy( string $slug = '' ): array {
	if ( '' === $slug ) {
		$slug = get_queried_object()->post_name ?? '';
	}
	static $copy = null;
	if ( null === $copy ) {
		// Local copy from the service sheets and the existing Atlanta spray foam page.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Bundled local data file.
		$copy = json_decode( file_get_contents( __DIR__ . '/national-service-copy.json' ), true );
	}
	return $copy[ $slug ] ?? array();
}

/** Allow Local and the explicitly enabled U.S. staging rollout. */
function koala_beanstalk_service_environment_enabled(): bool {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	return 'koalainsulation.local' === $host
		|| ( 'staging.koalainsulation.com' === $host
			&& 'staging' === wp_get_environment_type()
			&& (bool) get_option( 'koala_beanstalk_services_enabled', false ) );
}

/** Determine whether to render the native service template. */
function koala_beanstalk_service_hero_preview(): bool {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only builder switch.
	return ! isset( $_GET['bricks'] )
		&& koala_beanstalk_service_environment_enabled()
		&& ( koala_beanstalk_is_national_service() || ( is_singular( 'location-service' ) && in_array(
			get_query_var( 'service_slug' ),
			koala_beanstalk_service_preview_slugs(),
			true
		) ) )
		&& ! ( function_exists( 'bricks_is_builder' ) && bricks_is_builder() );
}

/** Allow WordPress to select the preview before the Bricks service route exits. */
function koala_beanstalk_prepare_service_hero(): void {
	if ( ! koala_beanstalk_service_hero_preview() ) {
		return;
	}
	remove_action( 'template_redirect', 'custom_location_service_template' );

	// Local serves its own media. Preserve the parser if the original files were offloaded.
	if ( apply_filters( 'cf_images_module_enabled', false, 'full-offload' ) ) {
		return;
	}
	foreach ( $GLOBALS['wp_filter']['template_redirect']->callbacks[1] ?? array() as $hook ) {
		$callback = $hook['function'];
		if ( is_array( $callback ) && $callback[0] instanceof \CF_Images\App\Modules\Page_Parser && 'output_buffering' === $callback[1] ) {
			remove_action( 'template_redirect', $callback, 1 );
		}
	}
}
add_action( 'template_redirect', 'koala_beanstalk_prepare_service_hero', 0 );

/**
 * Pick the native preview shell.
 *
 * @param string $template Original template.
 */
function koala_beanstalk_service_hero_template( string $template ): string {
	return koala_beanstalk_service_hero_preview() ? __DIR__ . '/service-hero-template.php' : $template;
}
add_filter( 'template_include', 'koala_beanstalk_service_hero_template', 1001 );

/**
 * Read the existing ACF schema or describe the service from its own page data.
 *
 * @param int $service_id Franchise service post ID.
 */
function koala_beanstalk_franchise_service_schema( int $service_id ): array {
	$schema = json_decode( (string) get_post_meta( $service_id, 'schema', true ), true );
	if ( is_array( $schema ) && $schema ) {
		return $schema;
	}

	$location_slug = (string) get_query_var( 'location_slug' );
	$service_slug  = (string) get_query_var( 'service_slug' );
	$title         = html_entity_decode( wp_strip_all_tags( get_the_title( $service_id ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	if ( '' === $location_slug || '' === $service_slug || '' === $title ) {
		return array();
	}

	$url    = home_url( '/' . $location_slug . '/services/' . $service_slug );
	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Service',
		'@id'      => $url . '#service',
		'name'     => $title,
		'url'      => $url,
	);

	$location = koala_beanstalk_resolve_location_post( get_post_meta( $service_id, 'related_location', true ) );
	if ( $location ) {
		$schema['provider'] = array(
			'@type' => 'LocalBusiness',
			'name'  => html_entity_decode( wp_strip_all_tags( get_the_title( $location->ID ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
			'url'   => get_permalink( $location->ID ),
		);
	}
	return $schema;
}

/** Restore JSON-LD on franchise services whose native template replaces the Bricks renderer. */
function koala_beanstalk_output_franchise_service_schema(): void {
	if ( ! is_singular( 'location-service' ) || ! koala_beanstalk_service_hero_preview() ) {
		return;
	}

	$service_id = (int) get_queried_object_id();
	if ( 8449 === $service_id && has_action( 'wp_head', 'koala_add_schema_post_8449' ) ) {
		return; // Atlanta spray foam already has a dedicated schema graph.
	}

	$schema  = koala_beanstalk_franchise_service_schema( $service_id );
	$encoded = $schema ? wp_json_encode( $schema, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : false;
	if ( false === $encoded ) {
		return;
	}

	remove_action( 'wp_head', 'koala_output_location_service_schema', 2 );
	echo '<script type="application/ld+json">' . $encoded . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON_HEX_TAG prevents script tag breakout.
}
add_action( 'wp_head', 'koala_beanstalk_output_franchise_service_schema', 1 );

/** Load the existing chrome and hero styles. */
function koala_beanstalk_service_hero_assets(): void {
	if ( ! koala_beanstalk_service_hero_preview() ) {
		return;
	}
	$national = koala_beanstalk_is_national_service();
	if ( ! $national ) {
		koala_beanstalk_dequeue_bricks_assets();
	}
	wp_enqueue_style( 'wp-block-library' );
	wp_enqueue_style( 'global-styles' );
	$url = get_template_directory_uri() . '/beanstalk/assets/';
	wp_dequeue_style( 'koala-location-sidebar' );
	foreach ( array( 'location-critical', 'footer', 'service-hero', 'service-trusted', 'service-superior', 'service-advantages', 'location-financing', 'service-process', 'location-quote-banner', 'location-features', 'location-video', 'service-expect', 'service-reviews', 'location-community', 'service-faq', 'city-page-final-cta', 'location-final-cta' ) as $asset ) {
		if ( $national && 'footer' === $asset ) {
			continue;
		}
		wp_enqueue_style( 'koala-beanstalk-' . $asset, $url . $asset . '.css', array(), (string) filemtime( __DIR__ . '/assets/' . $asset . '.css' ) );
	}
	foreach ( array( 'location-features', 'location-hero', 'service-reviews', 'location-community', 'service-video' ) as $script ) {
		wp_enqueue_script( 'koala-' . $script, $url . $script . '.js', array(), (string) filemtime( __DIR__ . '/assets/' . $script . '.js' ), true );
	}
	if ( ! $national ) {
		wp_enqueue_script( 'koala-beanstalk-header', $url . 'header.js', array(), (string) filemtime( __DIR__ . '/assets/header.js' ), true );
	} else {
		wp_enqueue_script( 'koala-national-location', $url . 'national-location.js', array(), (string) filemtime( __DIR__ . '/assets/national-location.js' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_service_hero_assets', 1006 );

/**
 * Drop legacy map and carousel scripts on native service templates.
 *
 * Keep the Maps handle registered so an actual map widget can still depend on it.
 */
function koala_beanstalk_service_remove_legacy_map_and_slider_assets(): void {
	if ( ! koala_beanstalk_service_hero_preview() ) {
		return;
	}

	wp_dequeue_script( 'location-page' );
	wp_dequeue_script( 'google-maps' );
	wp_dequeue_script( 'swiper-bundle' );
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_service_remove_legacy_map_and_slider_assets', 1007 );

/**
 * Let WordPress defer the known jQuery consumers in dependency order.
 */
function koala_beanstalk_service_defer_jquery(): void {
	if ( ! koala_beanstalk_service_hero_preview() ) {
		return;
	}

	$handles = array(
		'jquery-core',
		'jquery-migrate',
		'gform_json',
		'gform_gravityforms',
		'gform_gravityforms_theme_vendors',
		'gform_gravityforms_theme',
		'gform_masked_input',
		'gform_placeholder',
		'kgi-form-validation',
		'afl-wc-utm-public',
	);
	foreach ( $handles as $handle ) {
		wp_script_add_data( $handle, 'strategy', 'defer' );
	}
}
add_action( 'wp_print_scripts', 'koala_beanstalk_service_defer_jquery', 100 );

/**
 * Load supporting styles without blocking the service hero.
 *
 * @param string $html Stylesheet link.
 * @param string $handle WordPress style handle.
 */
function koala_beanstalk_service_style_tag( string $html, string $handle ): string {
	$critical = array(
		'koala-beanstalk-location-critical' => 'location-critical.css',
		'koala-beanstalk-service-hero'      => 'service-hero.css',
	);
	if ( isset( $critical[ $handle ] ) && koala_beanstalk_service_hero_preview() ) {
		return koala_beanstalk_inline_asset_stylesheet( $html, $handle, __DIR__ . '/assets/' . $critical[ $handle ], get_template_directory_uri() . '/beanstalk/assets/' );
	}

	$sections = array( 'footer', 'service-trusted', 'service-superior', 'service-advantages', 'location-financing', 'service-process', 'location-quote-banner', 'location-features', 'location-video', 'service-expect', 'service-reviews', 'location-community', 'service-faq', 'city-page-final-cta', 'location-final-cta' );
	$forms    = array(
		'kgi-quote-form',
		'gravity_forms_theme_reset',
		'gravity_forms_theme_foundation',
		'gravity_forms_theme_framework',
		'gravity_forms_orbital_theme',
	);
	$deferred = array_merge( $forms, array_map( static fn( $asset ) => 'koala-beanstalk-' . $asset, $sections ) );
	if ( ! in_array( $handle, $deferred, true ) || ! koala_beanstalk_service_hero_preview() ) {
		return $html;
	}
	return koala_beanstalk_defer_stylesheet( $html );
}
add_filter( 'style_loader_tag', 'koala_beanstalk_service_style_tag', 110, 2 );

/**
 * Bind service fields to the shared location hero pattern.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_hero_values( int $service_id ): array {
	$heading = trim( (string) get_post_meta( $service_id, 'service_hero_titile', true ) );
	$name    = (string) get_post_meta( $service_id, 'related_location_name', true );
	if ( koala_beanstalk_is_national_service() ) {
		return ( koala_beanstalk_national_editorial_copy()['hero'] ?? array() ) + array(
			'heading'            => get_the_title( $service_id ),
			'introduction'       => (string) get_post_meta( $service_id, 'service_description', true ),
			'imageId'            => (int) get_post_meta( $service_id, 'services_detail_image', true ),
			'inlineIntroduction' => true,
			'provider'           => '',
			'videoId'            => '',
			'privacyHash'        => '',
			'posterId'           => 0,
			'reviewHeading'      => '',
		);
	}
	if ( '' === $heading ) {
		$heading = $name ? 'Professional Blown-In Insulation in ' . $name : get_the_title( $service_id );
	}
	return array(
		'heading'            => $heading,
		'introduction'       => (string) get_post_meta( $service_id, 'service_hero_description', true ),
		'imageId'            => (int) get_post_meta( $service_id, 'service_hero_image', true ),
		'provider'           => '',
		'videoId'            => '',
		'privacyHash'        => '',
		'posterId'           => 0,
		'inlineIntroduction' => true,
		'reviewHeading'      => (string) get_post_meta( $service_id, 'review_widget_heading', true ),
	);
}

/**
 * Start the service hero image request in the head using its responsive sources.
 *
 * @param array $resources Existing preload resources.
 */
function koala_beanstalk_service_preload_hero_image( array $resources ): array {
	if ( ! koala_beanstalk_service_hero_preview() ) {
		return $resources;
	}

	$media = new WP_HTML_Tag_Processor( koala_beanstalk_render_location_hero_media( koala_beanstalk_service_hero_values( (int) get_queried_object_id() ) ) );
	if ( ! $media->next_tag( 'IMG' ) ) {
		return $resources;
	}

	$image  = array(
		'as'            => 'image',
		'fetchpriority' => 'high',
	);
	$srcset = $media->get_attribute( 'srcset' );
	$sizes  = $media->get_attribute( 'sizes' );
	$src    = $media->get_attribute( 'src' );
	if ( $srcset && $sizes ) {
		$image['imagesrcset'] = $srcset;
		$image['imagesizes']  = $sizes;
	} elseif ( $src ) {
		$image['href'] = $src;
	} else {
		return $resources;
	}
	array_unshift( $resources, $image );
	return $resources;
}
add_filter( 'wp_preload_resources', 'koala_beanstalk_service_preload_hero_image' );

/**
 * Render the shared homepage hero with the service data and sidebar target.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_hero_content( int $service_id ): string {
	$values   = koala_beanstalk_service_hero_values( $service_id );
	$location = koala_beanstalk_resolve_location_post( get_post_meta( $service_id, 'related_location', true ) );
	$reviews  = static function () use ( $values, $location ): string {
		if ( ! $location ) {
			return '';
		}
		return koala_beanstalk_render_location_hero_reviews(
			array(
				'heading'    => $values['reviewHeading'],
				'locationId' => $location->ID,
				'url'        => trailingslashit( get_permalink( $location ) ) . '#location-reviews',
			)
		);
	};
	add_filter( 'render_block_koala/location-hero-reviews', $reviews );
	ob_start();
	include __DIR__ . '/patterns/location-hero.php';
	$pattern = (string) ob_get_clean();
	try {
		return do_blocks( serialize_blocks( koala_beanstalk_patch_location_hero( parse_blocks( $pattern ), $values ) ) );
	} finally {
		remove_filter( 'render_block_koala/location-hero-reviews', $reviews );
	}
}

/**
 * Prefer precompressed WebP copies of local raster images when available.
 *
 * @param string $urls Image source or responsive source list.
 * @param string $base_url Uploads URL.
 * @param string $base_dir Uploads directory.
 */
function koala_beanstalk_service_webp_urls( string $urls, string $base_url, string $base_dir ): string {
	$prefix = preg_quote( rtrim( $base_url, '/' ) . '/', '~' );
	return preg_replace_callback(
		'~(^|[\s,])(' . $prefix . '([^\s,?#]+\.(?:jpe?g|png)))(?=[\s,?#]|$)~i',
		static function ( $image_match ) use ( $base_dir ): string {
			$relative = rawurldecode( $image_match[3] );
			if ( in_array( '..', explode( '/', $relative ), true ) || str_contains( $relative, "\0" ) ) {
				return $image_match[0];
			}
			return is_file( $base_dir . '/' . $relative . '.webp' ) ? $image_match[0] . '.webp' : $image_match[0];
		},
		$urls
	);
}

/**
 * Keep Local attachment URLs on the protocol used by the preview.
 *
 * @param array $attributes WordPress image attributes.
 */
function koala_beanstalk_service_hero_image_attributes( array $attributes ): array {
	if ( ! koala_beanstalk_service_hero_preview() ) {
		return $attributes;
	}

	$uploads = wp_get_upload_dir();
	foreach ( array( 'src', 'srcset' ) as $key ) {
		if ( ! isset( $attributes[ $key ] ) ) {
			continue;
		}
		foreach ( array( 'https', 'http' ) as $scheme ) {
			$attributes[ $key ] = koala_beanstalk_service_webp_urls( $attributes[ $key ], set_url_scheme( $uploads['baseurl'], $scheme ), $uploads['basedir'] );
		}
		if ( ! is_ssl() ) {
			$attributes[ $key ] = str_replace( home_url( '/', 'https' ), '/', $attributes[ $key ] );
		}
	}
	return $attributes;
}
add_filter( 'wp_get_attachment_image_attributes', 'koala_beanstalk_service_hero_image_attributes', 20 );

/**
 * Read the original Bricks Trusted section fields.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_trusted_data( int $service_id ): array {
	return array(
		'title'       => trim( (string) get_post_meta( $service_id, 'trust_title', true ) ),
		'content'     => trim( (string) get_post_meta( $service_id, 'trust_content', true ) ),
		'imageId'     => (int) get_post_meta( $service_id, 'trust_image', true ),
		'videoId'     => trim( (string) get_post_meta( $service_id, 'trust_video_id', true ) ),
		'privacyHash' => trim( (string) get_post_meta( $service_id, 'trust_video_hash', true ) ),
	);
}

/**
 * Read the existing Bricks upgrade panel fields for the current service.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_superior_data( int $service_id ): array {
	$items = array();
	for ( $index = 1; $index <= 3; ++$index ) {
		$item = trim( (string) get_post_meta( $service_id, 'superior_list_item_' . $index, true ) );
		if ( '' !== $item ) {
			$items[] = $item;
		}
	}

	return array(
		'title'   => trim( (string) get_post_meta( $service_id, 'superior_title', true ) ),
		'content' => trim( (string) get_post_meta( $service_id, 'superior_content', true ) ),
		'items'   => $items,
	);
}

/**
 * Build the advantages cards using the existing Bricks visibility rule.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_advantage_cards( int $service_id ): array {
	$labels = array(
		'improved_air_quality'         => 'Improved Air Quality',
		'water_fire_resistance'        => 'Water & Fire Resistance',
		'pest_and_wildlife_protection' => 'Pest and Wildlife Protection',
		'strengthens_buildings'        => 'Strengthens Buildings',
		'soundproofing'                => 'Soundproofing',
		'environmentally_friendly'     => 'Environmentally Friendly',
	);
	$cards  = array();
	if ( koala_beanstalk_is_national_service() ) {
		$editorial = koala_beanstalk_national_editorial_copy();
		if ( array_key_exists( 'advantages', $editorial ) ) {
			foreach ( $editorial['advantages']['cards'] ?? array() as $field => $content ) {
				$cards[ $field ] = array(
					'label'   => $labels[ $field ],
					'content' => $content,
				);
			}
			return $cards;
		}
		foreach ( koala_beanstalk_location_selected_ids( $service_id, 'benefits' ) as $id ) {
			$cards[ $id ] = array(
				'label'   => get_the_title( $id ),
				'content' => (string) get_post_meta( $id, 'benefit_description_', true ),
				'icon'    => (int) get_post_meta( $id, 'benefit_icon', true ),
			);
		}
		return $cards;
	}
	foreach ( $labels as $field => $label ) {
		$content = (string) get_post_meta( $service_id, $field, true );
		if ( 'strengthens_buildings' === $field && '' === $content ) {
			continue;
		}
		$cards[ $field ] = array(
			'label'   => $label,
			'content' => $content,
		);
	}
	return $cards;
}

/**
 * Check whether the service has any editorial content for its advantages.
 *
 * @param string $title   ACF heading.
 * @param string $content ACF introduction.
 * @param array  $cards   Cards with their ACF descriptions.
 */
function koala_beanstalk_service_has_advantages( string $title, string $content, array $cards ): bool {
	foreach ( array_merge( array( $title, $content ), array_column( $cards, 'content' ) ) as $value ) {
		if ( '' !== trim( html_entity_decode( wp_strip_all_tags( $value ) ), " \t\n\r\xC2\xA0" ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Read the financing fields in the same service post context as Bricks.
 *
 * @param int  $service_id Service post ID.
 * @param bool $preview    Show the banner for a Local design preview.
 */
function koala_beanstalk_service_financing_data( int $service_id, bool $preview = false ): ?array {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$enabled = '1' === (string) get_field( 'show_financing', $service_id );
	$link    = (string) get_field( 'financing_link', $service_id );
	if ( ! $enabled && ! metadata_exists( 'post', $service_id, 'show_financing' ) ) {
		$location = koala_beanstalk_resolve_location_post( get_post_meta( $service_id, 'related_location', true ) );
		if ( $location && koala_beanstalk_location_financing_enabled( $location->ID ) ) {
			$enabled = true;
			$link    = koala_beanstalk_location_financing_link( $location->ID );
		}
	}
	if ( ! $enabled && ! $preview ) {
		return null;
	}

	return array(
		'content' => (string) get_field( 'financing_content', $service_id ),
		'link'    => $link,
	);
}

/**
 * Read configured process steps in their original ACF order.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_process_steps( int $service_id ): array {
	$steps = array();
	if ( koala_beanstalk_is_national_service() ) {
		$editorial       = koala_beanstalk_national_editorial_copy();
		$editorial_steps = $editorial['steps'] ?? array();
		if ( $editorial_steps ) {
			foreach ( $editorial_steps as $number => $step ) {
				$steps[ $number ] = $step + array( 'image' => (int) get_post_meta( $service_id, 'process_step_' . $number . '_image', true ) );
			}
			return $steps;
		}
		foreach ( array_slice( koala_beanstalk_location_selected_ids( $service_id, 'process' ), 0, 5 ) as $index => $id ) {
			$image_override      = (int) get_post_meta( $service_id, 'process_step_' . ( $index + 1 ) . '_image', true );
			$steps[ $index + 1 ] = ( $editorial['processOverrides'][ $index + 1 ] ?? array() ) + array(
				'title'      => (string) get_post_meta( $id, 'service_process_title', true ),
				'content'    => (string) get_post_meta( $id, 'service_process_description', true ),
				'image'      => $image_override,
				'difference' => trim( (string) get_post_meta( $service_id, 'process_step_' . ( $index + 1 ) . '_difference', true ) ),
			);
		}
		return $steps;
	}
	for ( $number = 1; $number <= 5; ++$number ) {
		$key     = 'process_step_' . $number . '_';
		$title   = trim( (string) get_post_meta( $service_id, $key . 'title', true ) );
		$content = trim( (string) get_post_meta( $service_id, $key . 'content', true ) );
		if ( '' === $title && '' === $content ) {
			continue;
		}
		$steps[ $number ] = array(
			'title'      => $title,
			'content'    => $content,
			'image'      => (int) get_post_meta( $service_id, $key . 'image', true ),
			'difference' => trim( (string) get_post_meta( $service_id, $key . 'difference', true ) ),
		);
	}
	return $steps;
}

/**
 * Resolve the custom or default What to Expect video using Bricks' paired fields.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_expect_video( int $service_id ): string {
	$id   = trim( (string) get_post_meta( $service_id, 'expect_video_id', true ) );
	$hash = trim( (string) get_post_meta( $service_id, 'expect_video_hash', true ) );
	if ( '' === $id && '' === $hash ) {
		$id   = '1138098612';
		$hash = 'b3ed173b57';
	}
	if ( ! preg_match( '/^[0-9]+$/D', $id ) || ! preg_match( '/^[a-zA-Z0-9]+$/D', $hash ) ) {
		return '';
	}

	return 'https://player.vimeo.com/video/' . $id . '?h=' . $hash . '&autoplay=1';
}


/**
 * Read service testimonials and gallery with the related location review source.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_reviews_data( int $service_id ): array {
	$location      = koala_beanstalk_resolve_location_post( get_post_meta( $service_id, 'related_location', true ) );
	$images        = array();
	$gallery_field = koala_beanstalk_is_national_service() ? 'service_related_images' : 'location_service_related_images';
	foreach ( koala_beanstalk_location_selected_ids( $service_id, $gallery_field ) as $gallery_id ) {
		$image_id = (int) get_post_meta( $gallery_id, 'service_image', true );
		if ( $image_id ) {
			$images[] = $image_id;
		}
	}
	return array(
		'locationId' => $location ? $location->ID : 0,
		'title'      => (string) get_post_meta( $service_id, 'testimonials_title', true ),
		'content'    => (string) get_post_meta( $service_id, 'testimonials_content', true ),
		'images'     => $images,
	);
}

/**
 * Read the service's commercial copy and the related location's Bricks city list.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_commercial_data( int $service_id ): ?array {
	if ( '1' !== (string) get_post_meta( $service_id, 'show_local_cities', true ) ) {
		return null;
	}

	$location = koala_beanstalk_resolve_location_post( get_post_meta( $service_id, 'related_location', true ) );
	return array(
		'title'   => (string) get_post_meta( $service_id, 'local_cities_title', true ),
		'content' => (string) get_post_meta( $service_id, 'local_cities_content', true ),
		'cities'  => $location ? koala_beanstalk_location_selected_ids( $location->ID, 'location_area_serviced' ) : array(),
	);
}

/**
 * Read the service FAQ repeater in its saved order.
 *
 * @param int $service_id Service post ID.
 */
function koala_beanstalk_service_faq_rows( int $service_id ): array {
	$rows = function_exists( 'get_field' ) ? get_field( 'faqs', $service_id ) : array();
	$faqs = array();
	foreach ( (array) $rows as $row ) {
		$title = trim( (string) ( $row['faq_title'] ?? '' ) );
		if ( '' === $title ) {
			continue;
		}
		$image       = $row['faq_image'] ?? 0;
		$video_image = $row['video_image'] ?? 0;
		$faqs[]      = array(
			'title'      => $title,
			'content'    => (string) ( $row['faq_content'] ?? '' ),
			'image'      => (int) ( is_array( $image ) ? ( $image['ID'] ?? 0 ) : $image ),
			'offer'      => '1' === (string) ( $row['show_cta'] ?? '' ),
			'videoLink'  => '1' === (string) ( $row['show_video_section'] ?? '' ) ? (string) ( $row['video_link'] ?? '' ) : '',
			'videoCopy'  => (string) ( $row['video_content'] ?? '' ),
			'videoImage' => (int) ( is_array( $video_image ) ? ( $video_image['ID'] ?? 0 ) : $video_image ),
		);
	}
	return $faqs;
}

/**
 * Separate the national page's existing FAQ headings and answers from its editorial blocks.
 *
 * @param array $blocks Parsed blocks from the national service.
 * @param array $faq_images      Attachment IDs keyed by national FAQ question.
 * @param array $additional_faqs Existing editorial questions to append.
 */
function koala_beanstalk_national_service_copy( array $blocks, array $faq_images = array(), array $additional_faqs = array() ): array {
	$copy      = array(
		'sections' => array(),
		'faqs'     => array(),
		'faqTitle' => '',
	);
	$section   = '';
	$checklist = false;
	$faq_level = null;
	foreach ( $blocks as $block ) {
		if ( empty( $block['blockName'] ) || 'core/spacer' === $block['blockName'] ) {
			continue;
		}
		if ( 'core/heading' === $block['blockName'] ) {
			$title = html_entity_decode( wp_strip_all_tags( $block['innerHTML'], true ), ENT_QUOTES, 'UTF-8' );
			$level = $block['attrs']['level'] ?? 2;
			if ( 2 === $level && count( $copy['sections'] ) < 3 && '' === $copy['faqTitle'] ) {
				$section                      = array( 'trusted', 'superior', 'finder' )[ count( $copy['sections'] ) ];
				$checklist                    = 'superior' === $section && str_starts_with( $title, 'Why Choose ' );
				$copy['sections'][ $section ] = array(
					'title'   => $title,
					'content' => '',
					'items'   => array(),
					'label'   => '',
				);
				continue;
			}
			if ( null === $faq_level && 'superior' === $section && 3 === $level && ! str_ends_with( $title, 'FAQs' ) ) {
				$copy['sections'][ $section ]['label'] = $title;
				$checklist                             = true;
				continue;
			}
			if ( null === $faq_level && str_ends_with( $title, 'FAQs' ) ) {
				$copy['faqTitle'] = $title;
				$faq_level        = $level;
				continue;
			}
			if ( null !== $faq_level && $level === $faq_level + 1 ) {
				$copy['faqs'][] = array(
					'title'      => $title,
					'content'    => '',
					'image'      => (int) ( $faq_images[ $title ] ?? 0 ),
					'offer'      => false,
					'videoLink'  => '',
					'videoCopy'  => '',
					'videoImage' => 0,
				);
				continue;
			}
		}
		$last = count( $copy['faqs'] ) - 1;
		if ( $last >= 0 ) {
			$copy['faqs'][ $last ]['content'] .= render_block( $block );
		} elseif ( 'superior' === $section && $checklist && 'core/paragraph' === $block['blockName'] ) {
			$copy['sections'][ $section ]['items'][] = $block['innerHTML'];
		} elseif ( 'superior' === $section && 'core/list' === $block['blockName'] && ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $item ) {
				$copy['sections'][ $section ]['items'][] = preg_replace( '~^<li[^>]*>|</li>$~', '', trim( render_block( $item ) ) );
			}
		} elseif ( '' !== $section ) {
			$copy['sections'][ $section ]['content'] .= render_block( $block );
		}
	}
	foreach ( $additional_faqs as $faq ) {
		$copy['faqs'][] = $faq + array(
			'image'      => (int) ( $faq_images[ $faq['title'] ] ?? 0 ),
			'offer'      => false,
			'videoLink'  => '',
			'videoCopy'  => '',
			'videoImage' => 0,
		);
	}
	return $copy;
}
