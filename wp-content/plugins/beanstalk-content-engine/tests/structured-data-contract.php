<?php
/** Isolated contract validation test for template-owned structured data. */

namespace {
	final class WP_Error {}
	function __( string $value ): string {
		return $value; }
	function is_wp_error( $value ): bool {
		return $value instanceof WP_Error; }
}

namespace GiantCreative\BeanstalkContentEngine {
	require_once dirname( __DIR__ ) . '/src/PatternValidator.php';
	$field     = array(
		'label'    => 'Heading',
		'type'     => 'text',
		'required' => true,
		'target'   => array(
			'block'     => 'core/heading',
			'name'      => 'test-page-field-heading',
			'attribute' => 'content',
		),
	);
	$contract  = array(
		'contractVersion' => 1,
		'profile'         => 'koala/service-area',
		'label'           => 'Koala service-area schema',
		'produces'        => array( 'WebPage', 'Service', 'HomeAndConstructionBusiness', 'BreadcrumbList', 'FAQPage' ),
		'fields'          => array(
			'service_area_name' => array(
				'label'    => 'Service area',
				'type'     => 'text',
				'required' => true,
				'source'   => 'content-center',
			),
		),
	);
	$manifest  = array(
		'id'             => 'test/page',
		'label'          => 'Test',
		'description'    => 'Test.',
		'version'        => '1.0.0',
		'postTypes'      => array( 'page' ),
		'template'       => 'page.php',
		'fields'         => array( 'heading' => $field ),
		'structuredData' => $contract,
	);
	$validator = new PatternValidator();
	if ( true !== $validator->validate( $manifest ) ) {
		throw new \RuntimeException( 'Valid structured-data contract was rejected.' );
	}
	$legacy = $manifest;
	unset( $legacy['structuredData'] );
	if ( true !== $validator->validate( $legacy ) ) {
		throw new \RuntimeException( 'Legacy manifest was rejected.' );
	}
	foreach ( array(
		'profile'         => 'bad',
		'contractVersion' => 2,
	) as $key => $invalid ) {
		$copy                           = $manifest;
		$copy['structuredData'][ $key ] = $invalid;
		if ( ! is_wp_error( $validator->validate( $copy ) ) ) {
			throw new \RuntimeException( "Invalid {$key} was accepted." );
		}
	}
	$copy = $manifest;
	$copy['structuredData']['fields']['service_area_name']['source'] = 'browser';
	if ( ! is_wp_error( $validator->validate( $copy ) ) ) {
		throw new \RuntimeException( 'Invalid source was accepted.' );
	}
	echo "Structured-data contract: PASS\n";
}
