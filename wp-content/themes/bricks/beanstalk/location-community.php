<?php
/**
 * Dynamic location reviews, communities, contact details and offers.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Read relationship selections in their saved order.
 *
 * @param int    $id Location ID.
 * @param string $key Field name.
 * @return array
 */
function koala_beanstalk_location_selected_ids( int $id, string $key ): array {
	return array_values( array_unique( array_filter( array_map( 'intval', (array) get_post_meta( $id, $key, true ) ) ) ) );
}

/** Render rich location copy safely.
 *
 * @param int    $id Location ID.
 * @param string $key Field name.
 * @return string
 */
function koala_beanstalk_location_rich_copy( int $id, string $key ): string {
	return wp_kses_post( wpautop( (string) get_post_meta( $id, $key, true ) ) );
}

/** Format all-caps city-list labels without modifying city records or mixed case.
 *
 * @param string $name Saved city title.
 * @return string
 */
function koala_beanstalk_location_city_label( string $name ): string {
	if ( ! preg_match( '/\p{Lu}/u', $name ) || preg_match( '/\p{Ll}/u', $name ) ) {
		return $name;
	}
	$label = function_exists( 'mb_convert_case' ) ? mb_convert_case( $name, MB_CASE_TITLE, 'UTF-8' ) : ucwords( strtolower( $name ), " \t\r\n\f\v-'" );
	$label = preg_replace_callback(
		"/(['’]|\\bMc)(\\p{Ll})/u",
		static function ( $parts ) {
			return $parts[1] . strtoupper( $parts[2] );
		},
		$label
	);
	// Keep state abbreviations after a comma and the Air Force Base suffix.
	if ( preg_match( '/(,\s*)([A-Z]{2})$/', $name, $state ) ) {
		$label = preg_replace( '/,\s*[a-z]{2}$/i', $state[1] . $state[2], $label );
	}
	return preg_replace( '/\bAfb\b/', 'AFB', $label );
}

/** Render one ACF-backed section.
 *
 * @param array         $attributes Block attributes.
 * @param string        $content Saved content.
 * @param WP_Block|null $block Block context.
 * @return string
 */
function koala_beanstalk_render_location_community( array $attributes, string $content = '', $block = null ): string {
	$id      = (int) ( $block->context['postId'] ?? get_the_ID() );
	$section = $attributes['section'] ?? '';
	$name    = (string) get_post_meta( $id, 'location_name', true );
	if ( 'education' === $section ) {
		$title       = trim( (string) get_post_meta( $id, 'insulation_education_title', true ) );
		$description = trim( (string) get_post_meta( $id, 'insulation_education_description', true ) );
		if ( '' === $title && ( '' === $description || ( '' === trim( wp_strip_all_tags( $description ) ) && ! preg_match( '/<(img|video|iframe|audio)\b/i', $description ) ) ) ) {
			return '';
		}
		return '<section class="koala-location-lower koala-location-education"><div>' . ( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ) . koala_beanstalk_location_rich_copy( $id, 'insulation_education_description' ) . '</div></section>';
	}
	if ( 'reviews' === $section ) {
		$title = get_post_meta( $id, 'satisfaction_title', true );
		return '<section id="location-reviews" class="koala-location-lower koala-location-customer-reviews"><div class="koala-location-lower__intro"><h2>' . esc_html( $title ? $title : 'Customer Reviews' ) . '</h2>' . koala_beanstalk_location_rich_copy( $id, 'satisfaction_description' ) . '</div><div class="koala-location-customer-reviews__feed">' . koala_beanstalk_location_review_content( $id ) . '</div></section>';
	}
	if ( 'communities' === $section ) {
		if ( ! get_post_meta( $id, 'show_location_areas_serviced', true ) ) {
			return '';
		}
		$html = '<section class="koala-location-lower koala-location-communities"><div class="koala-location-lower__intro"><h2>' . esc_html( get_post_meta( $id, 'local_cities_title', true ) ) . '</h2>' . koala_beanstalk_location_rich_copy( $id, 'local_cities_description' ) . koala_beanstalk_render_location_hero_quote( array( 'label' => 'Get a FREE Quote' ) ) . '</div>';
		return $html . koala_beanstalk_location_communities_list( koala_beanstalk_location_selected_ids( $id, 'local_cities' ) ) . '</section>';
	}
	if ( 'nap' === $section ) {
		$html = '<section class="koala-location-lower koala-location-nap"><div class="koala-location-nap__map">' . do_shortcode( '[zip_shape_map post_id="' . $id . '"]' ) . '</div><div class="koala-location-nap__details"><h2>Koala Insulation of <span>' . esc_html( $name ) . '</span></h2><div class="koala-location-nap__contact"><div><h3>Address</h3><p>' . esc_html( get_post_meta( $id, 'location_address', true ) ) . '</p><h3>Phone</h3><p><a href="tel:' . esc_attr( preg_replace( '/[^+0-9]/', '', (string) get_post_meta( $id, 'location_phone_number', true ) ) ) . '">' . esc_html( get_post_meta( $id, 'location_phone_number', true ) ) . '</a></p></div>';
		if ( get_post_meta( $id, 'show_hours', true ) ) {
			$html .= '<div><h3>Hours of Operation</h3><dl>';
			foreach ( array(
				'mon'   => 'Mon',
				'tues'  => 'Tues',
				'wed'   => 'Wed',
				'thurs' => 'Thurs',
				'fri'   => 'Fri',
				'sat'   => 'Sat',
				'sun'   => 'Sun',
			) as $day => $label ) {
				$hours = (string) get_post_meta( $id, 'location_hours_' . $day, true );
				if ( '' !== trim( $hours ) ) {
					$html .= '<div><dt>' . esc_html( $label ) . ':</dt><dd>' . esc_html( koala_beanstalk_location_display_hours( $hours ) ) . '</dd></div>';
				}
			}
			$html .= '</dl></div>';
		}
		return $html . '</div></div></section>';
	}
	if ( 'offers' === $section ) {
		$offers = koala_beanstalk_location_selected_ids( $id, 'location_promos' );
		if ( ! $offers ) {
			return '';
		}
		$html = '<section class="koala-location-lower koala-location-offers"><div class="koala-location-lower__intro"><h2>Exclusive Local Offers</h2></div><div class="koala-location-offers__grid">';
		foreach ( $offers as $offer ) {
			if ( 'publish' !== get_post_status( $offer ) ) {
				continue;
			}
			$label = (string) get_post_meta( $offer, 'promotion_button_text', true );
			$html .= '<article class="koala-location-offers__card"><div><h3>' . esc_html( get_the_title( $offer ) ) . '</h3><p>' . esc_html( get_post_meta( $offer, 'promotion_short_description', true ) ) . '</p>' . koala_beanstalk_render_location_hero_quote( array( 'label' => $label ? $label : 'Claim This Offer' ) ) . '</div></article>';
		}
		return $html . '</div></section>';
	}
	return '';
}

/** Format saved 24-hour ranges for display, retaining free-form hours copy.
 *
 * @param string $hours Saved hours text.
 * @return string
 */
function koala_beanstalk_location_display_hours( string $hours ): string {
	if ( ! preg_match( '/^\s*([01]?\d|2[0-3]):([0-5]\d)\s*[-–—]\s*([01]?\d|2[0-4]):([0-5]\d)\s*$/u', $hours, $parts ) || ( '24' === $parts[3] && '00' !== $parts[4] ) ) {
		return $hours;
	}
	if ( 0 === (int) $parts[1] && '00' === $parts[2] && '24' === $parts[3] ) {
		return 'Open 24 hours';
	}
	$start = (int) $parts[1];
	$end   = (int) $parts[3] % 24;
	return sprintf( '%d:%s %s–%d:%s %s', $start % 12 ? $start % 12 : 12, $parts[2], $start < 12 ? 'AM' : 'PM', $end % 12 ? $end % 12 : 12, $parts[4], $end < 12 ? 'AM' : 'PM' );
}

/** Return one reusable pattern.
 *
 * @param string      $name Section name.
 * @param string|null $copy Optional service copy for the final CTA.
 * @return string
 */
function koala_beanstalk_location_community_content( string $name, ?string $copy = null ): string {
	if ( ! in_array( $name, array( 'reviews', 'communities', 'nap', 'offers', 'education', 'final-cta' ), true ) ) {
		return '';
	}
	ob_start();
	$final_cta_copy = $copy;
	include __DIR__ . '/patterns/location-' . $name . '.php';
	return (string) ob_get_clean();
}

/** Register dynamic blocks and reusable patterns. */
function koala_beanstalk_register_location_community(): void {
	register_block_type(
		'koala/location-community',
		array(
			'api_version'     => 3,
			'attributes'      => array( 'section' => array( 'type' => 'string' ) ),
			'uses_context'    => array( 'postId' ),
			'render_callback' => 'koala_beanstalk_render_location_community',
			'editor_script'   => 'koala-location-community-editor',
		)
	);
	foreach ( array(
		'reviews'     => 'Customer Reviews',
		'communities' => 'Communities Served',
		'nap'         => 'NAP and Service Area Map',
		'offers'      => 'Local Offers',
		'education'   => 'Insulation Education',
		'final-cta'   => 'Final Get a Quote',
	) as $name => $title ) {
		register_block_pattern(
			'koala/location-' . $name,
			array(
				'title'      => 'Koala Location — ' . $title,
				'categories' => array( 'featured' ),
				'postTypes'  => array( 'location' ),
				'content'    => koala_beanstalk_location_community_content( $name ),
			)
		);
	}
}
add_action( 'init', 'koala_beanstalk_register_location_community' );

/** Load the ACF section editor previews. */
function koala_beanstalk_location_community_editor(): void {
	wp_enqueue_script( 'koala-location-community-editor', get_template_directory_uri() . '/beanstalk/assets/location-community-editor.js', array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-server-side-render' ), (string) filemtime( __DIR__ . '/assets/location-community-editor.js' ), true );
	wp_enqueue_style( 'koala-location-final-layout-editor', get_template_directory_uri() . '/beanstalk/assets/city-page-final-cta.css', array(), (string) filemtime( __DIR__ . '/assets/city-page-final-cta.css' ) );
	wp_enqueue_style( 'koala-location-final-editor', get_template_directory_uri() . '/beanstalk/assets/location-final-cta.css', array(), (string) filemtime( __DIR__ . '/assets/location-final-cta.css' ) );
	wp_enqueue_style( 'koala-location-community-editor', get_template_directory_uri() . '/beanstalk/assets/location-community.css', array(), (string) filemtime( __DIR__ . '/assets/location-community.css' ) );
}
add_action( 'enqueue_block_editor_assets', 'koala_beanstalk_location_community_editor' );

/** Render only lower patterns absent from saved content.
 *
 * @param string $saved Saved post content.
 * @return string
 */
function koala_beanstalk_location_community_missing( string $saved ): string {
	$html = '';
	foreach ( array( 'reviews', 'communities', 'nap', 'offers', 'education', 'final-cta' ) as $name ) {
		if ( strpos( $saved, '"name":"koala-location-' . $name . '"' ) === false ) {
			$html .= do_blocks( koala_beanstalk_location_community_content( $name ) );
		}
	}
	return $html;
}

/** Replace legacy saved education copy with the current location fields.
 *
 * @param string $html Rendered saved group.
 * @param array  $block Parsed group.
 * @return string
 */
function koala_beanstalk_location_education_saved( string $html, array $block ): string {
	if ( 'koala-location-education' !== ( $block['attrs']['metadata']['name'] ?? '' ) ) {
		return $html;
	}
	return koala_beanstalk_render_location_community( array( 'section' => 'education' ) );
}
add_filter( 'render_block_core/group', 'koala_beanstalk_location_education_saved', 1001, 2 );


/**
 * Render the configured review feed for a location.
 *
 * @param int $location_id Related location ID.
 */
function koala_beanstalk_location_review_content( int $location_id ): string {
	$shortcode = (string) get_post_meta( $location_id, 'google_review_shortcode', true );
	return '' !== trim( $shortcode ) ? do_shortcode( $shortcode ) : '<p>Reviews are currently unavailable.</p>';
}

/**
 * Render the shared community grid and Show More control.
 *
 * @param array $cities      City post IDs in display order.
 * @param bool  $allow_links Use links for cities marked as linked resources.
 */
function koala_beanstalk_location_communities_list( array $cities, bool $allow_links = false ): string {
	$html  = '';
	$count = 0;
	foreach ( $cities as $city ) {
		if ( 'publish' !== get_post_status( $city ) ) {
			continue;
		}
		$label = esc_html( koala_beanstalk_location_city_label( (string) get_the_title( $city ) ) );
		if ( $allow_links && get_post_meta( $city, 'linked_resource', true ) ) {
			$url = esc_url( (string) get_permalink( $city ) );
			if ( '' !== $url ) {
				$label = '<a href="' . $url . '">' . $label . '</a>';
			}
		}
		$html .= '<li' . ( $count >= 9 ? ' hidden' : '' ) . '>' . $label . '</li>';
		++$count;
	}
	if ( 0 === $count ) {
		return '';
	}

	$html = '<ul class="koala-location-communities__list">' . $html . '</ul>';
	if ( $count > 9 ) {
		$html .= '<button class="koala-location-communities__more" type="button" aria-expanded="false">Show More</button>';
	}
	return $html;
}
