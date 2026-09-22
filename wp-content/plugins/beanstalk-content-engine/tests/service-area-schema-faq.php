<?php
/** Isolated FAQ extraction test for the Koala service-area graph. */

namespace {
	$fixture_blocks = array();
	$fixture_meta   = array(
		80587 => array( 'location_phone_number' => '(201) 627-0087' ),
	);
	function parse_blocks( string $content ): array {
		global $fixture_blocks;
		return $fixture_blocks;
	}
	function wp_strip_all_tags( string $value ): string {
		return strip_tags( $value );
	}
	function get_post_meta( int $post_id, string $key, bool $single ) {
		global $fixture_meta;
		return $fixture_meta[ $post_id ][ $key ] ?? '';
	}
	function get_the_title( int $post_id ): string {
		return 80587 === $post_id ? 'Bergen County' : '';
	}
}

namespace GiantCreative\BeanstalkContentEngine {
	require_once dirname( __DIR__ ) . '/src/ServiceAreaSchema.php';
	$fixture_blocks = array(
		array(
			'attrs'       => array( 'metadata' => array( 'name' => 'koala-city-page-field-faq-1-question' ) ),
			'innerHTML'   => '<details><summary>What does attic insulation cost in {{service_area_name}}?</summary></details>',
			'innerBlocks' => array(
				array(
					'attrs'       => array( 'metadata' => array( 'name' => 'koala-city-page-field-faq-1-answer' ) ),
					'innerHTML'   => '<p>Every written quote from {{location_name}} is free. Call {{location_phone}}.</p>',
					'innerBlocks' => array(),
				),
			),
		),
		array(
			'attrs'       => array( 'metadata' => array( 'name' => 'koala-city-page-field-faq-2-question' ) ),
			'innerHTML'   => '<details><summary>Incomplete question?</summary></details>',
			'innerBlocks' => array(),
		),
	);
	$method = new \ReflectionMethod( ServiceAreaSchema::class, 'faq_items' );
	$method->setAccessible( true );
	$result = $method->invoke( new ServiceAreaSchema(), '<!-- serialized blocks -->', array( 'service_area_name' => 'Ho-Ho-Kus' ), 80587, 'https://example.test/bergen-county/' );
	if ( array( array( 'question' => 'What does attic insulation cost in Ho-Ho-Kus?', 'answer' => 'Every written quote from Bergen County is free. Call (201) 627-0087.' ) ) !== $result ) {
		throw new \RuntimeException( 'FAQ extraction did not return only complete visible pairs.' );
	}
	echo "Service-area FAQ schema: PASS\n";
}
