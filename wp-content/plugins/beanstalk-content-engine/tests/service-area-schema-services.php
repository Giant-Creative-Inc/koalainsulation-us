<?php
/** Isolated WordPress-owned offered-services test for the service-area graph. */

namespace {
	$fixture_posts = array(
		11 => (object) array( 'ID' => 11, 'post_type' => 'location-service', 'post_status' => 'publish' ),
		12 => (object) array( 'ID' => 12, 'post_type' => 'location-service', 'post_status' => 'publish' ),
		13 => (object) array( 'ID' => 13, 'post_type' => 'location-service', 'post_status' => 'draft' ),
	);
	$fixture_meta = array(
		99 => array( 'location_service' => array( 11, 12, 13 ) ),
		11 => array( 'location_service_name' => 'Spray Foam Insulation' ),
		12 => array( 'location_service_name' => '' ),
		13 => array( 'location_service_name' => 'Draft Service' ),
	);
	function get_post_meta( int $post_id, string $key, bool $single ) {
		global $fixture_meta;
		return $fixture_meta[ $post_id ][ $key ] ?? '';
	}
	function get_post( int $post_id ) {
		global $fixture_posts;
		return $fixture_posts[ $post_id ] ?? null;
	}
	function get_the_title( int $post_id ): string {
		return 12 === $post_id ? 'Air Sealing' : '';
	}
	function wp_strip_all_tags( string $value ): string {
		return strip_tags( $value );
	}
}

namespace GiantCreative\BeanstalkContentEngine {
	require_once dirname( __DIR__ ) . '/src/ServiceAreaSchema.php';
	$method = new \ReflectionMethod( ServiceAreaSchema::class, 'offered_services' );
	$method->setAccessible( true );
	$result = $method->invoke(
		new ServiceAreaSchema(),
		99,
		'<p>We install Spray Foam Insulation and Air Sealing for local homes. Draft Service is not eligible.</p>'
	);
	if ( array( 'Spray Foam Insulation', 'Air Sealing' ) !== $result ) {
		throw new \RuntimeException( 'Services were not resolved from the published WordPress location relationship.' );
	}
	if ( array() !== $method->invoke( new ServiceAreaSchema(), 100, '<p>Spray Foam Insulation</p>' ) ) {
		throw new \RuntimeException( 'A location without WordPress service relationships produced offers.' );
	}
	echo "Service-area WordPress services: PASS\n";
}
