<?php
/** Isolated FAQ extraction test for the Koala service-area graph. */

namespace {
	$fixture_blocks = array();
	function parse_blocks( string $content ): array {
		global $fixture_blocks;
		return $fixture_blocks;
	}
	function wp_strip_all_tags( string $value ): string {
		return strip_tags( $value );
	}
}

namespace GiantCreative\BeanstalkContentEngine {
	require_once dirname( __DIR__ ) . '/src/ServiceAreaSchema.php';
	$fixture_blocks = array(
		array(
			'attrs'       => array( 'metadata' => array( 'name' => 'koala-city-page-field-faq-1-question' ) ),
			'innerHTML'   => '<details><summary>What does attic insulation cost?</summary></details>',
			'innerBlocks' => array(
				array(
					'attrs'       => array( 'metadata' => array( 'name' => 'koala-city-page-field-faq-1-answer' ) ),
					'innerHTML'   => '<p>Every written quote is free.</p>',
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
	$result = $method->invoke( new ServiceAreaSchema(), '<!-- serialized blocks -->' );
	if ( array( array( 'question' => 'What does attic insulation cost?', 'answer' => 'Every written quote is free.' ) ) !== $result ) {
		throw new \RuntimeException( 'FAQ extraction did not return only complete visible pairs.' );
	}
	echo "Service-area FAQ schema: PASS\n";
}
