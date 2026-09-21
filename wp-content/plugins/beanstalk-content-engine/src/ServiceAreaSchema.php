<?php
/**
 * Koala service-area JSON-LD renderer.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

/** Renders a template-owned graph from protected draft metadata. */
final class ServiceAreaSchema {
	/** Register narrow front-end and Rank Math hooks. */
	public function register(): void {
		add_action( 'wp_head', array( $this, 'render' ), 99 );
		add_filter( 'rank_math/json_ld', array( $this, 'filter_rank_math' ), 99 );
	}

	/**
	 * Remove only graph nodes this profile replaces on this exact post.
	 *
	 * @param mixed $data Rank Math graph.
	 */
	public function filter_rank_math( $data ) {
		if ( ! $this->is_service_area_post() || ! is_array( $data ) ) {
			return $data;
		}
		return array_filter(
			$data,
			static function ( $entity ) {
				$type = is_array( $entity ) ? ( $entity['@type'] ?? '' ) : '';
				return ! in_array( $type, array( 'WebPage', 'Service', 'HomeAndConstructionBusiness', 'LocalBusiness', 'BreadcrumbList', 'FAQPage' ), true );
			}
		);
	}

	/** Emit one safe JSON-LD graph for saved Beanstalk draft values. */
	public function render(): void {
		if ( ! $this->is_service_area_post() ) {
			return;
		}
		$post_id     = get_queried_object_id();
		$values      = json_decode( (string) get_post_meta( $post_id, '_beanstalk_structured_data_values', true ), true );
		$related     = get_post_meta( $post_id, 'rl_related_location', true );
		$location_id = (int) ( is_array( $related ) ? reset( $related ) : $related );
		$location    = get_post( $location_id );
		if ( ! is_array( $values ) || ! $location || 'location' !== $location->post_type || 'publish' !== $location->post_status ) {
			return;
		}

		$page_url     = trailingslashit( get_permalink( $post_id ) );
		$business_url = trailingslashit( get_permalink( $location_id ) );
		$address      = get_post_meta( $location_id, 'location_address', true );
		$address      = is_array( $address ) ? $address : array( 'address' => (string) $address );
		$content      = (string) get_post_field( 'post_content', $post_id );
		$offers       = $this->offered_services( $location_id, $content );
		$service      = array(
			'@type'       => 'Service',
			'@id'         => $page_url . '#service',
			'name'        => sprintf( 'Insulation Services in %s, %s', $values['service_area_name'], $values['state_abbreviation'] ),
			'serviceType' => $values['service_type'],
			'url'         => $page_url,
			'areaServed'  => array(
				'@type'            => 'City',
				'name'             => $values['service_area_name'],
				'containedInPlace' => array(
					'@type' => 'State',
					'name'  => $values['state_name'],
				),
			),
			'provider'    => array( '@id' => $business_url . '#business' ),
		);
		if ( $offers ) {
			$service['hasOfferCatalog'] = array(
				'@type'           => 'OfferCatalog',
				'name'            => 'Insulation Services',
				'itemListElement' => array_map(
					static fn( $name ) => array(
						'@type'       => 'Offer',
						'itemOffered' => array(
							'@type' => 'Service',
							'name'  => $name,
						),
					),
					$offers
				),
			);
		}
		$faq_items = $this->faq_items( (string) get_post_field( 'post_content', $post_id ) );
		$web_page  = array(
			'@type'      => 'WebPage',
			'@id'        => $page_url . '#webpage',
			'url'        => $page_url,
			'name'       => get_the_title( $post_id ),
			'mainEntity' => array( '@id' => $page_url . '#service' ),
			'breadcrumb' => array( '@id' => $page_url . '#breadcrumb' ),
		);
		if ( $faq_items ) {
			$web_page['hasPart'] = array( '@id' => $page_url . '#faq' );
		}
		$graph = array(
			$web_page,
			$service,
			array(
				'@type'     => 'HomeAndConstructionBusiness',
				'@id'       => $business_url . '#business',
				'name'      => get_the_title( $location_id ),
				'url'       => $business_url,
				'telephone' => (string) get_post_meta( $location_id, 'location_phone_number', true ),
				'address'   => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => (string) ( $address['street_address'] ?? $address['address'] ?? '' ),
					'addressLocality' => (string) ( $address['city'] ?? '' ),
					'addressRegion'   => (string) ( $address['state'] ?? '' ),
					'postalCode'      => (string) ( $address['post_code'] ?? $address['zip'] ?? '' ),
					'addressCountry'  => (string) ( $address['country'] ?? 'US' ),
				),
			),
			array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $page_url . '#breadcrumb',
				'itemListElement' => array(
					array(
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => 'Home',
						'item'     => home_url( '/' ),
					),
					array(
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => get_the_title( $location_id ),
						'item'     => $business_url,
					),
					array(
						'@type'    => 'ListItem',
						'position' => 3,
						'name'     => get_the_title( $post_id ),
						'item'     => $page_url,
					),
				),
			),
		);
		if ( $faq_items ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $page_url . '#faq',
				'mainEntity' => array_map(
					static fn( $item ) => array(
						'@type'          => 'Question',
						'name'           => $item['question'],
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $item['answer'],
						),
					),
					$faq_items
				),
			);
		}
		echo '<script type="application/ld+json" data-beanstalk-profile="koala/service-area">' . wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . '</script>';
	}

	/**
	 * Read complete, visibly rendered FAQ pairs from the named city-page blocks.
	 *
	 * @param string $content Serialized Gutenberg content.
	 * @return array<int,array{question:string,answer:string}>
	 */
	private function faq_items( string $content ): array {
		$values = array();
		$walk   = static function ( array $blocks ) use ( &$walk, &$values ): void {
			foreach ( $blocks as $block ) {
				$name = (string) ( $block['attrs']['metadata']['name'] ?? '' );
				if ( preg_match( '/^koala-city-page-field-faq-([1-5])-(question|answer)$/', $name, $match ) ) {
					$html = (string) ( $block['innerHTML'] ?? '' );
					if ( 'question' === $match[2] && preg_match( '/<summary[^>]*>(.*?)<\/summary>/is', $html, $summary ) ) {
						$html = $summary[1];
					}
					$values[ (int) $match[1] ][ $match[2] ] = trim( html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
				}
				if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
					$walk( $block['innerBlocks'] );
				}
			}
		};
		$walk( parse_blocks( $content ) );
		ksort( $values );
		return array_values(
			array_filter(
				$values,
				static fn( $item ) => ! empty( $item['question'] ) && ! empty( $item['answer'] )
			)
		);
	}

	/**
	 * Resolve offered services from the authoritative WordPress location record.
	 *
	 * Only published services visibly represented by the page are eligible for
	 * the OfferCatalog.
	 *
	 * @param int    $location_id Providing franchise post ID.
	 * @param string $content     Serialized Gutenberg content.
	 * @return array<int,string>
	 */
	private function offered_services( int $location_id, string $content ): array {
		$service_ids = get_post_meta( $location_id, 'location_service', true );
		if ( ! is_array( $service_ids ) ) {
			return array();
		}
		$visible = strtolower( wp_strip_all_tags( $content ) );
		$offers  = array();
		foreach ( $service_ids as $service_id ) {
			$service = get_post( (int) $service_id );
			if ( ! $service || 'location-service' !== $service->post_type || 'publish' !== $service->post_status ) {
				continue;
			}
			$name = trim( (string) get_post_meta( $service->ID, 'location_service_name', true ) );
			if ( '' === $name ) {
				$name = trim( (string) get_the_title( $service->ID ) );
			}
			if ( '' !== $name && false !== strpos( $visible, strtolower( $name ) ) ) {
				$offers[] = $name;
			}
		}
		return array_values( array_unique( $offers ) );
	}

	/** Whether the queried post owns the service-area profile. */
	private function is_service_area_post(): bool {
		$post_id = get_queried_object_id();
		return $post_id > 0 && 'koala/service-area' === get_post_meta( $post_id, '_beanstalk_structured_data_profile', true );
	}
}
