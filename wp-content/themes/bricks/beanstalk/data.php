<?php
/**
 * Shared location and navigation data for Beanstalk templates.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an ACF relationship value to a published location post.
 *
 * @param mixed $value Stored relationship value.
 * @return WP_Post|null
 */
function koala_beanstalk_resolve_location_post( $value ): ?WP_Post {
	if ( is_array( $value ) ) {
		$value = reset( $value );
	}

	if ( $value instanceof WP_Post ) {
		$post = $value;
	} elseif ( is_numeric( $value ) ) {
		$post = get_post( (int) $value );
	} elseif ( is_string( $value ) && '' !== $value ) {
		$post = get_page_by_path( sanitize_title( $value ), OBJECT, 'location' );
	} else {
		$post = null;
	}

	if ( ! $post instanceof WP_Post || 'location' !== $post->post_type || 'publish' !== $post->post_status ) {
		return null;
	}

	return $post;
}

/**
 * Normalize supported US state and Canadian province names/abbreviations.
 *
 * @param string $state Stored location state value.
 * @return array{name:string, abbreviation:string}
 */
function koala_beanstalk_normalize_state( string $state ): array {
	$states = array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
		'AB' => 'Alberta',
		'BC' => 'British Columbia',
		'MB' => 'Manitoba',
		'NB' => 'New Brunswick',
		'NL' => 'Newfoundland and Labrador',
		'NS' => 'Nova Scotia',
		'NT' => 'Northwest Territories',
		'NU' => 'Nunavut',
		'ON' => 'Ontario',
		'PE' => 'Prince Edward Island',
		'QC' => 'Quebec',
		'SK' => 'Saskatchewan',
		'YT' => 'Yukon',
	);
	$value  = trim( $state );
	$code   = strtoupper( $value );
	if ( isset( $states[ $code ] ) ) {
		return array(
			'name'         => $states[ $code ],
			'abbreviation' => $code,
		);
	}

	$matched_code = array_search( strtolower( $value ), array_map( 'strtolower', $states ), true );
	if ( false !== $matched_code ) {
		return array(
			'name'         => $states[ $matched_code ],
			'abbreviation' => $matched_code,
		);
	}

	return array(
		'name'         => $value,
		'abbreviation' => '',
	);
}

/**
 * Remove a trailing state abbreviation already present in a service-area title.
 *
 * The City Page pattern renders the state separately, so retaining a title such
 * as "Ho-Ho-Kus, NJ" would produce "Ho-Ho-Kus, NJ, NJ".
 *
 * @param string $service_area_name Area-served page title.
 * @param string $state_abbreviation Normalized state or province abbreviation.
 * @return string
 */
function koala_beanstalk_normalize_service_area_name( string $service_area_name, string $state_abbreviation ): string {
	$name = trim( $service_area_name );
	if ( '' === $name || '' === $state_abbreviation ) {
		return $name;
	}

	return trim( (string) preg_replace( '/,\s*' . preg_quote( $state_abbreviation, '/' ) . '$/i', '', $name ) );
}

/**
 * Get the location associated with the current area-served page.
 *
 * @return WP_Post|null
 */
function koala_beanstalk_get_current_location(): ?WP_Post {
	$context = koala_beanstalk_get_location_context();

	return $context['post'];
}

/**
 * Normalize the related location once for all City Page consumers.
 *
 * The cache is keyed by the queried page so long-running PHP processes cannot
 * accidentally reuse one page's location for another page.
 *
 * @return array<string, mixed>
 */
function koala_beanstalk_get_location_context(): array {
	static $contexts = array();

	$page_id = (int) get_queried_object_id();
	if ( isset( $contexts[ $page_id ] ) ) {
		return $contexts[ $page_id ];
	}

	$location = koala_beanstalk_resolve_location_post( get_post_meta( $page_id, 'rl_related_location', true ) );
	if ( ! $location ) {
		$request_path = isset( $_SERVER['REQUEST_URI'] )
			? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH )
			: '';
		$segments     = array_values( array_filter( explode( '/', trim( $request_path, '/' ) ) ) );
		$location     = ! empty( $segments[0] ) ? koala_beanstalk_resolve_location_post( $segments[0] ) : null;
	}

	$empty_context = array(
		'post'                    => null,
		'id'                      => 0,
		'location_name'           => '',
		'display_name'            => '',
		'service_area_name'       => '',
		'state_name'              => '',
		'state_abbreviation'      => '',
		'phone'                   => '',
		'phone_url'               => '',
		'address'                 => '',
		'home_url'                => '',
		'service_urls'            => array(),
		'resource_urls'           => array(),
		'review_data'             => array(),
		'nicejob_id'              => '',
		'google_place_id'         => '',
		'google_review_shortcode' => '',
		'review_url'              => '',
		'gravity_form_id'         => 0,
	);
	if ( ! $location ) {
		$contexts[ $page_id ] = $empty_context;
		return $contexts[ $page_id ];
	}

	$location_name = (string) get_post_meta( $location->ID, 'location_name', true );
	$location_name = $location_name ? $location_name : get_the_title( $location->ID );
	$display_name  = (string) get_post_meta( $location->ID, 'location_display_name', true );
	$state         = (string) get_post_meta( $location->ID, 'location_state', true );
	$state_data    = koala_beanstalk_normalize_state( $state );
	$phone         = (string) get_post_meta( $location->ID, 'location_phone_number', true );
	$reviews       = get_post_meta( $location->ID, 'location_reviews', true );
	$location_url  = (string) get_permalink( $location->ID );

	$service_area_name    = $page_id ? get_the_title( $page_id ) : '';
	$contexts[ $page_id ] = array(
		'post'                    => $location,
		'id'                      => (int) $location->ID,
		'location_name'           => $location_name,
		'display_name'            => $display_name ? $display_name : $location_name,
		'service_area_name'       => koala_beanstalk_normalize_service_area_name( $service_area_name, $state_data['abbreviation'] ),
		'state_name'              => $state_data['name'],
		'state_abbreviation'      => $state_data['abbreviation'],
		'phone'                   => $phone,
		'phone_url'               => $phone ? 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) : '',
		'address'                 => (string) get_post_meta( $location->ID, 'location_address', true ),
		'home_url'                => $location_url,
		'service_urls'            => koala_beanstalk_get_location_relationship_links( $location->ID, 'location_service', 'location_service_name', 'location_service_image' ),
		'resource_urls'           => koala_beanstalk_get_location_relationship_links( $location->ID, 'related_rl_page' ),
		'review_data'             => is_array( $reviews ) ? $reviews : array(),
		'nicejob_id'              => (string) get_post_meta( $location->ID, 'location_nicejob_id', true ),
		'google_place_id'         => (string) get_post_meta( $location->ID, 'google_place_id', true ),
		'google_review_shortcode' => (string) get_post_meta( $location->ID, 'google_review_shortcode', true ),
		'review_url'              => trailingslashit( $location_url ) . 'testimonials/',
		'gravity_form_id'         => (int) get_post_meta( $location->ID, 'location_gravity_form_id', true ),
	);

	return $contexts[ $page_id ];
}

/**
 * Convert a location relationship field into navigation links.
 *
 * @param int    $location_id Location post ID.
 * @param string $field       Relationship field name.
 * @param string $title_field Optional custom title field.
 * @param string $image_field Optional attachment-ID field.
 * @return array<int, array<string, int|string>>
 */
function koala_beanstalk_get_location_relationship_links( int $location_id, string $field, string $title_field = '', string $image_field = '' ): array {
	$related_posts = get_post_meta( $location_id, $field, true );
	$links         = array();

	if ( ! is_array( $related_posts ) ) {
		return $links;
	}

	foreach ( $related_posts as $related_post ) {
		$related_post_id = $related_post instanceof WP_Post ? $related_post->ID : (int) $related_post;

		if ( ! $related_post_id || 'publish' !== get_post_status( $related_post_id ) ) {
			continue;
		}

		$title = $title_field ? get_post_meta( $related_post_id, $title_field, true ) : get_the_title( $related_post_id );
		$url   = get_permalink( $related_post_id );

		if ( $title && $url ) {
			$link = array(
				'title' => (string) $title,
				'url'   => $url,
			);

			if ( $image_field ) {
				$image_id          = (int) get_post_thumbnail_id( $related_post_id );
				$image_id          = $image_id ? $image_id : (int) get_post_meta( $related_post_id, $image_field, true );
				$link['id']        = $related_post_id;
				$link['image_id']  = $image_id;
				$link['image_alt'] = $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
			}

			$links[] = $link;
		}
	}

	return $links;
}

/**
 * Build the shared server-rendered navigation model once per request.
 *
 * @return array<string, mixed>
 */
function koala_beanstalk_get_navigation_data(): array {
	static $navigation_data = null;

	if ( null !== $navigation_data ) {
		return $navigation_data;
	}

	$context   = koala_beanstalk_get_location_context();
	$location  = $context['post'];
	$base_url  = $location ? untrailingslashit( $context['home_url'] ) : untrailingslashit( home_url( '/' ) );
	$services  = $context['service_urls'];
	$resources = array();

	if ( $location ) {
		foreach ( $context['resource_urls'] as $resource ) {
			$resource_id = url_to_postid( $resource['url'] );

			if ( $resource_id && has_term( 'areas-served', 'resources-page-type', $resource_id ) ) {
				continue;
			}

			$resources[] = $resource;
		}
	}

	$default_resources = array(
		array(
			'title' => 'Testimonials',
			'url'   => $base_url . '/testimonials',
		),
		array(
			'title' => 'Areas Served',
			'url'   => $base_url . '/areas-served',
		),
		array(
			'title' => 'Blog',
			'url'   => $base_url . '/blog',
		),
		array(
			'title' => 'Homeowner Incentives',
			'url'   => $base_url . '/homeowner-incentives',
		),
	);

	$location_name = $location ? $context['location_name'] : get_bloginfo( 'name' );

	$navigation_data              = array(
		'location'  => $location,
		'base'      => $base_url,
		'name'      => $location_name,
		'state'     => $context['state_name'],
		'phone'     => $context['phone'],
		'address'   => $context['address'],
		'services'  => $services,
		'resources' => array_merge( $default_resources, $resources ),
	);
	$navigation_data['phone_url'] = $context['phone_url'];

	return $navigation_data;
}
