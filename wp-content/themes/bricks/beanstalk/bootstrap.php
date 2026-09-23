<?php
/**
 * Beanstalk bootstrap.
 *
 * Provides an isolated Gutenberg rendering path for Resources Landing Pages
 * assigned to the Areas served taxonomy term.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/data.php';
require_once __DIR__ . '/city-page.php';
require_once __DIR__ . '/parts.php';

/**
 * Determine whether the current request should use the Beanstalk template.
 */
function koala_is_beanstalk_area_served_page(): bool {
	if ( ! is_singular( 'resources-landing-pa' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id > 0
		&& has_term( 'areas-served', 'resources-page-type', $post_id )
		&& function_exists( 'beanstalk_content_engine_is_city_page_enabled' )
		&& beanstalk_content_engine_is_city_page_enabled( $post_id );
}

/**
 * Restore native image attributes after the parent Bricks lazy-load filter.
 *
 * Beanstalk City Pages deliberately omit the Bricks frontend JavaScript that would
 * otherwise promote data-src and data-srcset onto the image. WordPress native
 * lazy loading remains intact through the original loading attribute.
 *
 * @param array<string, mixed> $attributes Image attributes.
 * @param mixed                $attachment Unused attachment object.
 * @param mixed                $size       Unused requested image size.
 * @return array<string, mixed>
 */
function koala_beanstalk_restore_native_image_attributes( array $attributes, $attachment = null, $size = null ): array {
	unset( $attachment, $size );

	if ( ! koala_is_beanstalk_area_served_page() ) {
		return $attributes;
	}

	if ( isset( $attributes['data-src'] ) ) {
		$attributes['src'] = $attributes['data-src'];
		unset( $attributes['data-src'] );
	}

	if ( isset( $attributes['data-srcset'] ) ) {
		$attributes['srcset'] = $attributes['data-srcset'];
		unset( $attributes['data-srcset'] );
	}

	if ( isset( $attributes['data-sizes'] ) ) {
		$attributes['sizes'] = $attributes['data-sizes'];
		unset( $attributes['data-sizes'] );
	}

	if ( isset( $attributes['class'] ) ) {
		$classes             = array_filter( explode( ' ', (string) $attributes['class'] ) );
		$classes             = array_values( array_diff( $classes, array( 'bricks-lazy-hidden' ) ) );
		$attributes['class'] = implode( ' ', $classes );
	}

	unset( $attributes['data-type'] );

	return $attributes;
}
add_filter( 'wp_get_attachment_image_attributes', 'koala_beanstalk_restore_native_image_attributes', 20, 3 );

/**
 * Route only matching Resources Landing Pages to Beanstalk.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function koala_beanstalk_template_include( string $template ): string {
	if ( ! koala_is_beanstalk_area_served_page() ) {
		return $template;
	}

	$beanstalk_template = __DIR__ . '/template.php';

	return is_readable( $beanstalk_template ) ? $beanstalk_template : $template;
}
add_filter( 'template_include', 'koala_beanstalk_template_include', 999 );

/**
 * Remove legacy theme markup that is printed directly instead of enqueued.
 */
function koala_beanstalk_remove_legacy_theme_output(): void {
	if ( koala_is_beanstalk_area_served_page() ) {
		remove_action( 'wp_footer', 'custom_body_code' );
	}
}
add_action( 'template_redirect', 'koala_beanstalk_remove_legacy_theme_output', 100 );

/**
 * Keep the Beanstalk page payload independent from Bricks' frontend assets.
 * Plugin assets, including Gravity Forms assets, remain available.
 */
function koala_beanstalk_manage_frontend_assets(): void {
	global $wp_scripts, $wp_styles;

	if ( ! koala_is_beanstalk_area_served_page() ) {
		return;
	}

	$style_handles = array(
		'bricks-frontend',
		'bricks-frontend-rtl',
		'bricks-admin',
		'bricks-default-content',
		'bricks-font-awesome-6',
		'bricks-font-awesome-6-brands',
		'bricks-global-classes-inline',
		'bricks-global-custom-css',
		'bricks-theme-style',
		'koala-custom-css',
		'custom-service-css',
	);

	$script_handles = array(
		'bricks-scripts',
		'bricks-filters',
		'bricks-fontfaceobserver',
		'all-pages-js',
		'custom-service-js',
	);

	foreach ( $style_handles as $handle ) {
		wp_dequeue_style( $handle );
	}

	foreach ( $script_handles as $handle ) {
		wp_dequeue_script( $handle );
	}

	// Catch generated and custom Bricks handles without maintaining an
	// ever-growing list. This only removes files served by the Bricks theme.
	$theme_url = trailingslashit( get_template_directory_uri() );

	if ( $wp_styles instanceof WP_Styles ) {
		foreach ( $wp_styles->queue as $handle ) {
			$source = $wp_styles->registered[ $handle ]->src ?? '';

			if ( is_string( $source ) && strpos( $source, $theme_url ) === 0 ) {
				wp_dequeue_style( $handle );
			}
		}
	}

	if ( $wp_scripts instanceof WP_Scripts ) {
		foreach ( $wp_scripts->queue as $handle ) {
			$source = $wp_scripts->registered[ $handle ]->src ?? '';

			if ( is_string( $source ) && strpos( $source, $theme_url ) === 0 ) {
				wp_dequeue_script( $handle );
			}
		}
	}

	// The parent theme removes these globally. Beanstalk pages use Gutenberg,
	// so restore the core block styles after the parent's dequeue callback.
	wp_enqueue_style( 'wp-block-library' );
	wp_enqueue_style( 'global-styles' );

	$header_css         = __DIR__ . '/assets/header.css';
	$header_js          = __DIR__ . '/assets/header.js';
	$quote_attention_js = __DIR__ . '/assets/city-page-quote-attention.js';
	$city_page_video_js = __DIR__ . '/assets/city-page-video.js';
	$footer_css         = __DIR__ . '/assets/footer.css';
	$city_page_css      = __DIR__ . '/assets/city-page.css';

	wp_enqueue_style(
		'koala-beanstalk-header',
		get_template_directory_uri() . '/beanstalk/assets/header.css',
		array(),
		is_readable( $header_css ) ? (string) filemtime( $header_css ) : null
	);

	wp_enqueue_script(
		'koala-beanstalk-header',
		get_template_directory_uri() . '/beanstalk/assets/header.js',
		array(),
		is_readable( $header_js ) ? (string) filemtime( $header_js ) : null,
		true
	);
	wp_script_add_data( 'koala-beanstalk-header', 'strategy', 'defer' );

	wp_enqueue_script(
		'koala-beanstalk-city-page-quote-attention',
		get_template_directory_uri() . '/beanstalk/assets/city-page-quote-attention.js',
		array(),
		is_readable( $quote_attention_js ) ? (string) filemtime( $quote_attention_js ) : null,
		true
	);
	wp_script_add_data( 'koala-beanstalk-city-page-quote-attention', 'strategy', 'defer' );

	wp_enqueue_script(
		'koala-beanstalk-city-page-video',
		get_template_directory_uri() . '/beanstalk/assets/city-page-video.js',
		array(),
		is_readable( $city_page_video_js ) ? (string) filemtime( $city_page_video_js ) : null,
		true
	);
	wp_script_add_data( 'koala-beanstalk-city-page-video', 'strategy', 'defer' );

	wp_enqueue_style(
		'koala-beanstalk-footer',
		get_template_directory_uri() . '/beanstalk/assets/footer.css',
		array( 'koala-beanstalk-header' ),
		is_readable( $footer_css ) ? (string) filemtime( $footer_css ) : null
	);

	wp_enqueue_style(
		'koala-beanstalk-city-page',
		get_template_directory_uri() . '/beanstalk/assets/city-page.css',
		array( 'koala-beanstalk-header' ),
		is_readable( $city_page_css ) ? (string) filemtime( $city_page_css ) : null
	);
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_manage_frontend_assets', 1000 );

/**
 * Remove Google Reviews assets when the related location has no valid place or feed.
 */
function koala_beanstalk_manage_review_assets(): void {
	if ( ! koala_is_beanstalk_area_served_page() ) {
		return;
	}

	$config = koala_beanstalk_get_location_review_config();
	if ( 'google-reviews' === $config['provider'] && ( $config['place_id'] || $config['feed_id'] ) ) {
		return;
	}

	wp_dequeue_script( 'grw-public-main-js' );
	wp_dequeue_style( 'grw-public-main-css' );
}
add_action( 'wp_enqueue_scripts', 'koala_beanstalk_manage_review_assets', 1001 );

/**
 * Upgrade legacy default pattern images at render time without overwriting an
 * editor-selected image or requiring a database rewrite.
 *
 * @param string               $block_content Rendered block HTML.
 * @param array<string, mixed> $block         Parsed block.
 * @return string
 */
function koala_beanstalk_optimize_default_city_images( string $block_content, array $block ): string {
	if ( ! koala_is_beanstalk_area_served_page() || 'core/image' !== ( $block['blockName'] ?? '' ) ) {
		return $block_content;
	}

	$name = $block['attrs']['metadata']['name'] ?? '';
	$maps = array(
		'koala-city-page-field-commercial-services-image-1' => array( 'commercial-space.png', 'commercial-space.webp', 1280, 853 ),
		'koala-city-page-field-commercial-services-image-2' => array( 'commercial-new-construction.jpg', 'commercial-new-construction.webp', 750, 502 ),
		'koala-city-page-field-why-koala-image' => array( 'why-koala-image.jpg', 'why-koala-image.webp', 1200, 769 ),
	);
	if ( ! isset( $maps[ $name ] ) || false === strpos( $block_content, $maps[ $name ][0] ) ) {
		return $block_content;
	}

	$block_content = str_replace( $maps[ $name ][0], $maps[ $name ][1], $block_content );
	return (string) preg_replace(
		'/<img\b/',
		'<img width="' . $maps[ $name ][2] . '" height="' . $maps[ $name ][3] . '" loading="lazy" decoding="async"',
		$block_content,
		1
	);
}
add_filter( 'render_block', 'koala_beanstalk_optimize_default_city_images', 20, 2 );

/**
 * Add a stable page-level hook for future Beanstalk styling.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function koala_beanstalk_filter_body_classes( array $classes ): array {
	if ( koala_is_beanstalk_area_served_page() ) {
		$classes[] = 'beanstalk-page';
	}

	return $classes;
}
add_filter( 'body_class', 'koala_beanstalk_filter_body_classes' );

/**
 * Read SEO fallbacks already stored on the resource landing page.
 * Explicit Yoast values remain authoritative; the ACF schema supplies missing
 * title and description values without duplicating content in the editor.
 */
function koala_beanstalk_get_seo_data(): array {
	static $data = null;

	if ( null !== $data ) {
		return $data;
	}

	$data    = array(
		'title'       => '',
		'description' => '',
		'schema'      => null,
	);
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return $data;
	}

	$schema_raw = (string) get_post_meta( $post_id, 'resource_lp_schema', true );
	$schema     = $schema_raw ? json_decode( $schema_raw, true ) : null;
	if ( is_array( $schema ) && JSON_ERROR_NONE === json_last_error() ) {
		$data['schema'] = $schema;
		$nodes          = isset( $schema['@graph'] ) && is_array( $schema['@graph'] ) ? $schema['@graph'] : array( $schema );
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) || empty( $node['@type'] ) ) {
				continue;
			}
			$types = (array) $node['@type'];
			if ( in_array( 'WebPage', $types, true ) ) {
				$data['title']       = isset( $node['name'] ) ? wp_strip_all_tags( (string) $node['name'] ) : '';
				$data['description'] = isset( $node['description'] ) ? wp_strip_all_tags( (string) $node['description'] ) : '';
				break;
			}
		}
	}

	$title_tag = (string) get_post_meta( $post_id, 'title_tag', true );
	if ( $title_tag ) {
		$data['title'] = $title_tag;
	} elseif ( ! $data['title'] ) {
		$hero_title    = (string) get_post_meta( $post_id, 'rl_hero_description', true );
		$data['title'] = $hero_title ? $hero_title : get_the_title( $post_id );
	}
	if ( ! $data['description'] ) {
		$data['description'] = wp_trim_words( wp_strip_all_tags( (string) get_post_meta( $post_id, 'rl_description', true ) ), 30, '…' );
	}

	return $data;
}

/**
 * Supply a fallback Yoast title when the post has no explicit Yoast title.
 *
 * @param string $title Current SEO title.
 * @return string
 */
function koala_beanstalk_filter_seo_title( string $title ): string {
	if ( ! koala_is_beanstalk_area_served_page() || get_post_meta( get_queried_object_id(), '_yoast_wpseo_title', true ) ) {
		return $title;
	}

	$fallback_title = koala_beanstalk_get_seo_data()['title'];

	return $fallback_title ? $fallback_title : $title;
}
add_filter( 'wpseo_title', 'koala_beanstalk_filter_seo_title' );
add_filter( 'wpseo_opengraph_title', 'koala_beanstalk_filter_seo_title' );
add_filter( 'wpseo_twitter_title', 'koala_beanstalk_filter_seo_title' );

/**
 * Supply a fallback Yoast description when no explicit value is saved.
 *
 * @param string $description Current SEO description.
 * @return string
 */
function koala_beanstalk_filter_seo_description( string $description ): string {
	if ( ! koala_is_beanstalk_area_served_page() || get_post_meta( get_queried_object_id(), '_yoast_wpseo_metadesc', true ) ) {
		return $description;
	}

	$fallback_description = koala_beanstalk_get_seo_data()['description'];

	return $fallback_description ? $fallback_description : $description;
}
add_filter( 'wpseo_metadesc', 'koala_beanstalk_filter_seo_description' );
add_filter( 'wpseo_opengraph_desc', 'koala_beanstalk_filter_seo_description' );
add_filter( 'wpseo_twitter_description', 'koala_beanstalk_filter_seo_description' );

/**
 * Return the City Page social image, preferring an editorial featured image.
 */
function koala_beanstalk_get_social_image_url(): string {
	$post_id  = (int) get_queried_object_id();
	$image_id = $post_id ? (int) get_post_thumbnail_id( $post_id ) : 0;
	$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : false;

	return $image
		? (string) $image
		: get_template_directory_uri() . '/beanstalk/assets/city-page/why-koala-image.webp';
}

/**
 * Prevent a page URL or another non-image value from becoming og:image.
 *
 * @param string $image Current Yoast image URL.
 * @return string
 */
function koala_beanstalk_filter_social_image( string $image ): string {
	if ( ! koala_is_beanstalk_area_served_page() ) {
		return $image;
	}

	$path = (string) wp_parse_url( $image, PHP_URL_PATH );
	if ( $image && preg_match( '/\.(?:avif|gif|jpe?g|png|webp)$/i', $path ) ) {
		return $image;
	}

	return koala_beanstalk_get_social_image_url();
}
add_filter( 'wpseo_opengraph_image', 'koala_beanstalk_filter_social_image' );
add_filter( 'wpseo_twitter_image', 'koala_beanstalk_filter_social_image' );

/** Preload only the font used by the above-the-fold H1. */
function koala_beanstalk_preload_heading_font(): void {
	if ( ! koala_is_beanstalk_area_served_page() ) {
		return;
	}

	$url = content_url( '/uploads/fonts/0e906f7812e74c80074fecaa9d86c59c/alegreya-sans--v28-normal-700.woff2' );
	echo '<link rel="preload" as="font" type="font/woff2" href="' . esc_url( $url ) . '" crossorigin>' . "\n";
}
add_action( 'wp_head', 'koala_beanstalk_preload_heading_font', 1 );

/**
 * Build FAQ schema from the same visible native details blocks.
 *
 * @param string                    $content Post block content.
 * @param array<string, mixed>|null $location Optional test context.
 * @return array<int, array<string, mixed>>
 */
function koala_beanstalk_get_faq_schema_items( string $content, ?array $location = null ): array {
	$location = null === $location ? koala_beanstalk_get_location_context() : $location;
	$items    = array();
	if ( ! preg_match_all( '/<details\b[^>]*>\s*<summary>(.*?)<\/summary>(.*?)<\/details>/is', $content, $details, PREG_SET_ORDER ) ) {
		return $items;
	}

	foreach ( $details as $detail ) {
		if ( false === strpos( $detail[2], 'koala-city-faq__answer' ) ) {
			continue;
		}
		$question = koala_beanstalk_replace_location_placeholders( $detail[1], $location );
		$answer   = koala_beanstalk_replace_location_placeholders( $detail[2], $location );
		$question = trim( html_entity_decode( wp_strip_all_tags( $question ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		$answer   = trim( html_entity_decode( wp_strip_all_tags( $answer ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		if ( '' === $question || '' === $answer ) {
			continue;
		}
		$items[] = array(
			'@type'          => 'Question',
			'name'           => $question,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $answer,
			),
		);
	}

	return $items;
}

/**
 * Add the related location and its allowlisted services to Yoast's graph.
 * Existing node types remain authoritative and are never duplicated.
 *
 * @param array<int, array<string, mixed>> $graph   Yoast graph.
 * @param mixed                            $context Yoast context.
 * @param array<string, mixed>|null        $location Optional test context.
 * @return array<int, array<string, mixed>>
 */
function koala_beanstalk_filter_schema_graph( array $graph, $context = null, ?array $location = null ): array {
	unset( $context );
	if ( null === $location && ! koala_is_beanstalk_area_served_page() ) {
		return $graph;
	}

	$location = null === $location ? koala_beanstalk_get_location_context() : $location;
	if ( empty( $location['id'] ) ) {
		return $graph;
	}

	$page_url      = (string) get_permalink( get_queried_object_id() );
	$page_id_base  = trailingslashit( $page_url );
	$business_id   = trailingslashit( (string) $location['home_url'] ) . '#localbusiness';
	$service_id    = $page_id_base . '#insulation-service';
	$catalog_id    = $page_id_base . '#insulation-services';
	$breadcrumb_id = $page_id_base . '#breadcrumb';
	$existing      = array();
	foreach ( $graph as $node ) {
		foreach ( (array) ( $node['@type'] ?? array() ) as $type ) {
			$existing[ $type ] = true;
		}
	}
	foreach ( $graph as &$node ) {
		if ( in_array( 'WebPage', (array) ( $node['@type'] ?? array() ), true ) ) {
			$node['about']      = array( '@id' => $business_id );
			$node['mainEntity'] = array( '@id' => $service_id );
			$node['breadcrumb'] = array( '@id' => $breadcrumb_id );
			break;
		}
	}
	unset( $node );

	if ( empty( $existing['HomeAndConstructionBusiness'] ) ) {
		$business = array(
			'@type'      => 'HomeAndConstructionBusiness',
			'@id'        => $business_id,
			'name'       => (string) $location['location_name'],
			'url'        => (string) $location['home_url'],
			'telephone'  => (string) $location['phone'],
			'areaServed' => array(
				'@type' => 'Place',
				'name'  => trim( (string) $location['service_area_name'] . ', ' . (string) $location['state_name'], ', ' ),
			),
		);
		if ( ! empty( $location['address'] ) ) {
			$business['address'] = array(
				'@type'         => 'PostalAddress',
				'streetAddress' => (string) $location['address'],
			);
		}
		$graph[] = $business;
	}

	if ( empty( $existing['Service'] ) ) {
		$graph[] = array(
			'@type'      => 'Service',
			'@id'        => $service_id,
			'name'       => 'Insulation services',
			'provider'   => array( '@id' => $business_id ),
			'areaServed' => (string) $location['service_area_name'],
		);
	}

	if ( empty( $existing['OfferCatalog'] ) ) {
		$items = array();
		foreach ( (array) $location['service_urls'] as $service ) {
			if ( empty( $service['title'] ) || empty( $service['url'] ) ) {
				continue;
			}
			$items[] = array(
				'@type'       => 'Offer',
				'itemOffered' => array(
					'@type' => 'Service',
					'name'  => (string) $service['title'],
					'url'   => (string) $service['url'],
				),
			);
		}
		$graph[] = array(
			'@type'           => 'OfferCatalog',
			'@id'             => $catalog_id,
			'name'            => 'Insulation services',
			'itemListElement' => $items,
		);
	}

	if ( empty( $existing['BreadcrumbList'] ) ) {
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $breadcrumb_id,
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => (string) $location['location_name'],
					'item'     => (string) $location['home_url'],
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => (string) $location['service_area_name'],
					'item'     => $page_url,
				),
			),
		);
	}

	if ( empty( $existing['FAQPage'] ) ) {
		$post_content = (string) get_post_field( 'post_content', get_queried_object_id() );
		$faq_items    = koala_beanstalk_get_faq_schema_items( $post_content, $location );
		if ( $faq_items ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $page_id_base . '#faq',
				'mainEntity' => $faq_items,
			);
		}
	}

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'koala_beanstalk_filter_schema_graph', 20, 2 );

/**
 * Remove FAQPage nodes from legacy fallback schema.
 *
 * FAQ structured data remains owned by the SEO plugin so editable Gutenberg
 * FAQs cannot create a second or stale FAQPage graph.
 *
 * @param array<string, mixed> $schema Stored schema graph.
 * @return array<string, mixed>
 */
function koala_beanstalk_remove_faq_schema_nodes( array $schema ): array {
	if ( isset( $schema['@graph'] ) && is_array( $schema['@graph'] ) ) {
		$schema['@graph'] = array_values(
			array_filter(
				$schema['@graph'],
				static function ( $node ): bool {
					$types = is_array( $node ) && isset( $node['@type'] ) ? (array) $node['@type'] : array();
					return ! in_array( 'FAQPage', $types, true );
				}
			)
		);
		return $schema;
	}

	$types = isset( $schema['@type'] ) ? (array) $schema['@type'] : array();
	return in_array( 'FAQPage', $types, true ) ? array() : $schema;
}

/**
 * Output the resource-page schema that the legacy theme overlooks because it
 * is stored under resource_lp_schema instead of schema.
 */
function koala_beanstalk_output_resource_schema(): void {
	if ( ! koala_is_beanstalk_area_served_page() ) {
		return;
	}
	if ( defined( 'WPSEO_VERSION' ) ) {
		return; // Yoast receives the connected City Page nodes through wpseo_schema_graph.
	}

	$post_id = get_queried_object_id();
	if ( get_post_meta( $post_id, 'schema', true ) ) {
		return; // The legacy theme already prints this field.
	}

	$schema = koala_beanstalk_get_seo_data()['schema'];
	if ( ! is_array( $schema ) ) {
		return;
	}
	$schema = koala_beanstalk_remove_faq_schema_nodes( $schema );
	if ( empty( $schema ) || ( isset( $schema['@graph'] ) && empty( $schema['@graph'] ) ) ) {
		return;
	}

	$location      = koala_beanstalk_get_current_location();
	$location_name = $location ? get_the_title( $location ) : '';
	$encoded       = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE );
	$encoded       = str_replace( '{acf_related_location_name}', $location_name, $encoded );

	echo '<script type="application/ld+json">' . $encoded . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'koala_beanstalk_output_resource_schema', 3 );
