<?php
/**
 * Location hero pattern, ACF synchronization and dynamic media/review blocks.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read the authoritative Page Content values without exposing integration secrets.
 *
 * @param int $post_id Location ID.
 * @return array
 */
function koala_beanstalk_location_hero_values( int $post_id ): array {
	$provider = (string) get_post_meta( $post_id, 'hero_video_types', true );
	$youtube  = (string) get_post_meta( $post_id, 'location_hero_video', true );
	$vimeo    = (string) get_post_meta( $post_id, 'vimeo_video_id', true );
	if ( ! in_array( $provider, array( 'YouTube', 'Vimeo' ), true ) || ( 'Vimeo' === $provider ? '' === $vimeo : '' === $youtube ) ) {
		$provider = '' !== $youtube ? 'YouTube' : ( '' !== $vimeo ? 'Vimeo' : '' );
	}
	$heading = (string) get_post_meta( $post_id, 'location_hero_title', true );
	if ( '' === trim( $heading ) ) {
		$name    = (string) get_post_meta( $post_id, 'location_name', true );
		$heading = $name ? $name . "'s Insulation Installation Experts" : 'Insulation Installation Experts';
	}
	return array(
		'heading'       => $heading,
		'introduction'  => (string) get_post_meta( $post_id, 'location_hero_description', true ),
		'imageId'       => (int) get_post_meta( $post_id, 'location_image', true ),
		'provider'      => $provider,
		'videoId'       => (string) get_post_meta( $post_id, 'Vimeo' === $provider ? 'vimeo_video_id' : 'location_hero_video', true ),
		'privacyHash'   => (string) get_post_meta( $post_id, 'vimeo_privacy_hash', true ),
		'posterId'      => (int) get_post_meta( $post_id, 'hero_video_image', true ),
		'reviewHeading' => (string) get_post_meta( $post_id, 'review_widget_heading', true ),
	);
}

/**
 * Patch only ACF-managed hero fields, preserving other sections and the CTA label.
 *
 * @param array $blocks Parsed blocks.
 * @param array $values ACF values.
 * @param bool  $inside Whether inside the named hero.
 * @return array
 */
function koala_beanstalk_patch_location_hero( array $blocks, array $values, bool $inside = false ): array {
	foreach ( $blocks as &$block ) {
		$name   = $block['attrs']['metadata']['name'] ?? '';
		$active = $inside || 'koala-location-hero' === $name;
		if ( ! $active ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				$block['innerBlocks'] = koala_beanstalk_patch_location_hero( $block['innerBlocks'], $values );
			}
			continue;
		}
		if ( 'koala-location-hero-heading' === $name ) {
			$html = '<h1 class="wp-block-heading">' . esc_html( $values['heading'] ) . '</h1>';
		} elseif ( 'koala-location-hero-introduction' === $name ) {
			$html = '<p class="koala-location-hero__introduction">' . nl2br( esc_html( $values['introduction'] ), false ) . '</p>';
		} else {
			$html = null;
		}
		if ( null !== $html ) {
			$block['innerHTML']    = $html;
			$block['innerContent'] = array( $html );
		}
		if ( 'koala/location-hero-media' === ( $block['blockName'] ?? '' ) ) {
			foreach ( array( 'imageId', 'provider', 'videoId', 'privacyHash', 'posterId' ) as $key ) {
				$block['attrs'][ $key ] = $values[ $key ];
			}
		}
		if ( 'koala/location-hero-reviews' === ( $block['blockName'] ?? '' ) ) {
			$block['attrs']['heading'] = $values['reviewHeading'];
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = koala_beanstalk_patch_location_hero( $block['innerBlocks'], $values, true );
		}
	}
	unset( $block );
	return $blocks;
}

/**
 * Generate a populated native block pattern for initial insertion.
 *
 * @param int $post_id Location ID.
 * @return string
 */
function koala_beanstalk_location_hero_content( int $post_id ): string {
	ob_start();
	include __DIR__ . '/patterns/location-hero.php';
	$pattern = (string) ob_get_clean();
	return serialize_blocks( koala_beanstalk_patch_location_hero( parse_blocks( $pattern ), koala_beanstalk_location_hero_values( $post_id ) ) );
}

/**
 * Synchronize existing hero blocks after ACF saves; never insert into legacy pages.
 *
 * @param mixed $post_id ACF post ID.
 */
function koala_beanstalk_sync_location_hero( $post_id ): void {
	static $syncing = false;
	if ( $syncing || ! is_numeric( $post_id ) || 'location' !== get_post_type( (int) $post_id ) || wp_is_post_revision( (int) $post_id ) || wp_is_post_autosave( (int) $post_id ) ) {
		return;
	}
	$post_id = (int) $post_id;
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( strpos( $content, '"name":"koala-location-hero"' ) === false ) {
		return;
	}
	$updated = serialize_blocks( koala_beanstalk_patch_location_hero( parse_blocks( $content ), koala_beanstalk_location_hero_values( $post_id ) ) );
	if ( $updated !== $content ) {
		$syncing = true;
		wp_update_post(
			wp_slash(
				array(
					'ID'           => $post_id,
					'post_content' => $updated,
				)
			)
		);
		$syncing = false;
	}
}
add_action( 'acf/save_post', 'koala_beanstalk_sync_location_hero', 20 );

/**
 * Build allowlisted embeds from video IDs, never arbitrary embed markup.
 *
 * @param array $attributes Media attributes.
 * @return string
 */
function koala_beanstalk_location_hero_video_url( array $attributes ): string {
	$id = $attributes['videoId'] ?? '';
	if ( 'YouTube' === ( $attributes['provider'] ?? '' ) && preg_match( '/^[a-zA-Z0-9_-]{11}$/', $id ) ) {
		return 'https://www.youtube-nocookie.com/embed/' . $id . '?autoplay=1';
	}
	if ( 'Vimeo' === ( $attributes['provider'] ?? '' ) && preg_match( '/^[0-9]+$/', $id ) ) {
		$hash = $attributes['privacyHash'] ?? '';
		$hash = preg_match( '/^[a-zA-Z0-9]+$/', $hash ) ? 'h=' . $hash . '&' : '';
		return 'https://player.vimeo.com/video/' . $id . '?' . $hash . 'autoplay=1&muted=1&loop=1&color=043968';
	}
	return '';
}

/**
 * Render the location-specific hero image or click-to-play video poster.
 *
 * @param array $attributes Media attributes.
 * @return string
 */
function koala_beanstalk_render_location_hero_media( array $attributes ): string {
	$video = koala_beanstalk_location_hero_video_url( $attributes );
	$id    = $video && ! empty( $attributes['posterId'] ) ? (int) $attributes['posterId'] : (int) ( $attributes['imageId'] ?? 0 );
	$image = $id ? wp_get_attachment_image(
		$id,
		'large',
		false,
		array(
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
		)
	) : '';
	if ( ! $image ) {
		$image = '<img src="' . esc_url( get_template_directory_uri() . '/beanstalk/assets/location-hero/default-image-930.webp' ) . '" srcset="' . esc_url( get_template_directory_uri() . '/beanstalk/assets/location-hero/default-image-480.webp' ) . ' 480w, ' . esc_url( get_template_directory_uri() . '/beanstalk/assets/location-hero/default-image-768.webp' ) . ' 768w, ' . esc_url( get_template_directory_uri() . '/beanstalk/assets/location-hero/default-image-930.webp' ) . ' 930w" sizes="(max-width:991px) calc(100vw - 40px), (min-width:1230px) calc((100vw - 350px) / 2), 50vw" width="930" height="692" alt="Koala Insulation installer applying blown-in insulation in an attic" loading="eager" fetchpriority="high" decoding="async">';
	}
	$control = $video ? '<button class="koala-location-hero__play" type="button" data-location-hero-video="' . esc_url( $video ) . '" aria-label="Play location video">&#9654;</button>' : '';
	return '<div class="koala-location-hero__media">' . $image . $control . '</div>';
}

/**
 * Open the separate native location estimate popup.
 *
 * @param array $attributes Quote attributes.
 * @return string
 */
function koala_beanstalk_render_location_hero_quote( array $attributes ): string {
	return '<button class="koala-location-hero__quote" type="button" data-location-quote-popup-trigger aria-haspopup="dialog" aria-controls="koala-location-estimate">' . esc_html( $attributes['label'] ?? 'Get a FREE Quote' ) . '</button>';
}

/**
 * Render current review data using this location, independently of city metadata.
 *
 * @param array $attributes Review heading attributes.
 * @return string
 */
function koala_beanstalk_render_location_hero_reviews( array $attributes ): string {
	$id      = (int) ( get_the_ID() ? get_the_ID() : get_queried_object_id() );
	$name    = (string) get_post_meta( $id, 'location_name', true );
	$heading = ! empty( $attributes['heading'] ) ? esc_html( $attributes['heading'] ) : 'Top Insulation Contractor in <span>' . esc_html( $name ) . '</span>';
	$summary = koala_beanstalk_get_location_google_review_summary(
		array(
			'id'                      => $id,
			'google_review_shortcode' => (string) get_post_meta( $id, 'google_review_shortcode', true ),
		)
	);
	$proof   = '';
	if ( ! empty( $summary ) ) {
		$star  = '<img src="' . esc_url( get_template_directory_uri() . '/beanstalk/assets/location-hero/star.svg' ) . '" width="19.7368" height="19.7368" alt="">';
		$stars = str_repeat( $star, 5 );
		$proof = '<div class="koala-location-hero__proof" aria-label="' . esc_attr( sprintf( 'Rated %s out of 5 from %s Google reviews', $summary['rating'], $summary['count'] ) ) . '"><span class="koala-location-hero__stars" aria-hidden="true"><span>' . $stars . '</span><span style="clip-path:inset(0 ' . esc_attr( (string) ( 100 - $summary['rating'] * 20 ) ) . '% 0 0)">' . $stars . '</span></span><span>' . esc_html( number_format_i18n( $summary['count'] ) . ' Reviews' ) . '</span></div>';
	}
	return '<a class="koala-location-hero__reviews" href="#location-reviews"><p>' . $heading . '</p>' . $proof . '</a>';
}

/** Register native hero blocks and the reusable section pattern. */
function koala_beanstalk_register_location_hero(): void {
	wp_register_script( 'koala-location-hero-editor', get_template_directory_uri() . '/beanstalk/assets/location-hero-editor.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-hooks' ), (string) filemtime( __DIR__ . '/assets/location-hero-editor.js' ), true );
	$media = array();
	foreach ( array( 'imageId', 'provider', 'videoId', 'privacyHash', 'posterId' ) as $key ) {
		$media[ $key ] = array( 'type' => in_array( $key, array( 'imageId', 'posterId' ), true ) ? 'number' : 'string' );
	}
	foreach ( array(
		'media'   => array(
			'attributes'      => $media,
			'render_callback' => 'koala_beanstalk_render_location_hero_media',
		),
		'reviews' => array(
			'attributes'      => array( 'heading' => array( 'type' => 'string' ) ),
			'render_callback' => 'koala_beanstalk_render_location_hero_reviews',
		),
		'quote'   => array(
			'attributes'      => array(
				'label' => array(
					'type'    => 'string',
					'default' => 'Get a FREE Quote',
				),
			),
			'render_callback' => 'koala_beanstalk_render_location_hero_quote',
		),
	) as $name => $settings ) {
		register_block_type(
			'koala/location-hero-' . $name,
			array_merge(
				array(
					'api_version'   => 3,
					'editor_script' => 'koala-location-hero-editor',
					'supports'      => array( 'html' => false ),
				),
				$settings
			)
		);
	}
	ob_start();
	include __DIR__ . '/patterns/location-hero.php';
	$content = (string) ob_get_clean();
	register_block_pattern(
		'koala/location-hero',
		array(
			'title'      => 'Koala Location Hero',
			'categories' => array( 'featured' ),
			'postTypes'  => array( 'location' ),
			'content'    => $content,
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_hero' );

/** Load scoped section styling in the location block editor. */
function koala_beanstalk_location_hero_editor_styles(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'location' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_style( 'koala-location-financing-editor', get_template_directory_uri() . '/beanstalk/assets/location-financing.css', array(), (string) filemtime( __DIR__ . '/assets/location-financing.css' ) );
	wp_enqueue_style( 'koala-location-features-editor', get_template_directory_uri() . '/beanstalk/assets/location-features.css', array(), (string) filemtime( __DIR__ . '/assets/location-features.css' ) );
	wp_enqueue_style( 'koala-location-quote-banner-editor', get_template_directory_uri() . '/beanstalk/assets/location-quote-banner.css', array(), (string) filemtime( __DIR__ . '/assets/location-quote-banner.css' ) );
	wp_enqueue_style( 'koala-location-overview-editor', get_template_directory_uri() . '/beanstalk/assets/location-overview.css', array(), (string) filemtime( __DIR__ . '/assets/location-overview.css' ) );
	wp_enqueue_style( 'koala-location-services-editor', get_template_directory_uri() . '/beanstalk/assets/location-services.css', array(), (string) filemtime( __DIR__ . '/assets/location-services.css' ) );
	wp_enqueue_style( 'koala-location-partners-editor', get_template_directory_uri() . '/beanstalk/assets/location-partners.css', array(), (string) filemtime( __DIR__ . '/assets/location-partners.css' ) );
	wp_enqueue_style( 'koala-location-hero-editor', get_template_directory_uri() . '/beanstalk/assets/location-hero.css', array(), (string) filemtime( __DIR__ . '/assets/location-hero.css' ) );
}
add_action( 'enqueue_block_editor_assets', 'koala_beanstalk_location_hero_editor_styles' );

/**
 * Queue metadata writes until the request finishes so imports synchronize complete records.
 *
 * @param mixed  $meta_id Meta ID or deleted IDs.
 * @param int    $post_id Post ID.
 * @param string $key Meta key.
 */
function koala_beanstalk_queue_location_hero_sync( $meta_id, $post_id, $key ): void {
	$keys = array( 'location_name', 'location_hero_title', 'location_hero_description', 'location_image', 'hero_video_types', 'location_hero_video', 'vimeo_video_id', 'vimeo_privacy_hash', 'hero_video_image', 'review_widget_heading' );
	if ( in_array( $key, $keys, true ) && 'location' === get_post_type( $post_id ) ) {
		$GLOBALS['koala_location_hero_sync_queue'][ (int) $post_id ] = true;
	}
}
add_action( 'added_post_meta', 'koala_beanstalk_queue_location_hero_sync', 10, 3 );
add_action( 'updated_post_meta', 'koala_beanstalk_queue_location_hero_sync', 10, 3 );
add_action( 'deleted_post_meta', 'koala_beanstalk_queue_location_hero_sync', 10, 3 );

/** Flush once after ACF, REST or importer metadata writes have finished. */
function koala_beanstalk_flush_location_hero_sync(): void {
	$queue = $GLOBALS['koala_location_hero_sync_queue'] ?? array();
	unset( $GLOBALS['koala_location_hero_sync_queue'] );
	foreach ( array_keys( $queue ) as $post_id ) {
		koala_beanstalk_sync_location_hero( $post_id );
	}
}
add_action( 'shutdown', 'koala_beanstalk_flush_location_hero_sync', 20 );

/**
 * Read trusted administrator-managed booking integration markup at render time.
 *
 * @param int $post_id Location ID.
 * @return string
 */
function koala_beanstalk_location_booking_code( int $post_id ): string {
	$key = 'service_minder' === get_post_meta( $post_id, 'lead_service', true ) ? 'service_minder_button' : 'housecall_pro_button_code';
	return (string) get_post_meta( $post_id, $key, true );
}

/** Render the optional ACF promo attachment directly below the hero. */
function koala_beanstalk_render_location_hero_promo(): string {
	$id    = (int) get_post_meta( get_the_ID(), 'hero_promo_image', true );
	$image = $id ? wp_get_attachment_image( $id, 'large', false, array( 'loading' => 'lazy' ) ) : '';
	return $image ? '<section class="koala-location-promo" aria-label="Local promotion">' . $image . '</section>' : '';
}

/**
 * Keep template-owned integrations outside saved Gutenberg content.
 *
 * @param string $html Rendered block.
 * @param array  $block Parsed block.
 * @return string
 */
function koala_beanstalk_location_hero_extras( string $html, array $block ): string {
	if ( ! koala_beanstalk_sidebar_preview() || 'koala-location-hero' !== ( $block['attrs']['metadata']['name'] ?? '' ) ) {
		return $html;
	}
	$html .= koala_beanstalk_render_location_hero_promo();
	if ( ! has_block( 'koala/location-partners', get_post_field( 'post_content', get_the_ID() ) ) ) {
		$html .= koala_beanstalk_render_location_partners();
	}
	if ( ! has_block( 'koala/location-services', get_post_field( 'post_content', get_the_ID() ) ) ) {
		$html .= koala_beanstalk_render_location_services();
	}
	if ( ! has_block( 'koala/location-overview', get_post_field( 'post_content', get_the_ID() ) ) ) {
		$html .= koala_beanstalk_render_location_overview();
	}
	if ( strpos( (string) get_post_field( 'post_content', get_the_ID() ), '"name":"koala-location-quote-banner"' ) === false ) {
		$html .= do_blocks( koala_beanstalk_location_quote_banner_content() );
	}
	$code = koala_beanstalk_location_booking_code( (int) get_the_ID() );
	if ( '' !== trim( $code ) ) {
		$html .= '<div class="koala-location-booking">' . $code . '</div>';
	}
	return $html;
}
// Append sections after core/group has finished adding its inner wrapper.
add_filter( 'render_block_core/group', 'koala_beanstalk_location_hero_extras', 1000, 2 );

/** Render the location review feed and matching hero scroll destination. */
function koala_beanstalk_render_location_review_feed(): string {
	$shortcode = (string) get_post_meta( get_the_ID(), 'google_review_shortcode', true );
	if ( '' === trim( $shortcode ) ) {
		return '<section id="location-reviews" class="koala-location-reviews"><h2>Customer reviews</h2><p>Reviews are currently unavailable.</p></section>';
	}
	return '<section id="location-reviews" class="koala-location-reviews" aria-label="Customer reviews">' . do_shortcode( $shortcode ) . '</section>';
}
