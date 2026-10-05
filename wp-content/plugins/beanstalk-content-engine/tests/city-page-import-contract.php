<?php
/** Isolated contract test for the City Page CSV reader and activation boundary. */

namespace {
	final class WP_Error {
		public function __construct( private string $code = '', private string $message = '' ) {}
		public function get_error_code(): string { return $this->code; }
		public function get_error_message(): string { return $this->message; }
	}
	function __( string $value ): string { return $value; }
	function is_wp_error( $value ): bool { return $value instanceof WP_Error; }
	function wp_slash( $value ) { return is_array( $value ) ? array_map( 'wp_slash', $value ) : ( is_string( $value ) ? addslashes( $value ) : $value ); }
	function update_post_meta( $id, $key, $value ) { $GLOBALS['import_meta'][ $key ] = stripslashes( $value ); }
	function get_post_meta( $id, $key, $single ) { return $GLOBALS['import_meta'][ $key ] ?? ''; }
}

namespace GiantCreative\BeanstalkContentEngine {
	require_once dirname( __DIR__ ) . '/src/CityPageCliCommand.php';
	require_once dirname( __DIR__ ) . '/src/CityPageUpdater.php';
	$service = ( new \ReflectionClass( CityPageUpdater::class ) )->newInstanceWithoutConstructor();
	$write = new \ReflectionMethod( CityPageUpdater::class, 'write_meta' );
	$write->setAccessible( true );
	$json = json_encode( array( 'service_area_name' => 'O’Fallon' ) );
	if ( ! $write->invoke( $service, 71831, '_beanstalk_structured_data_values', $json )
		|| $json !== get_post_meta( 71831, '_beanstalk_structured_data_values', true ) ) {
		throw new \RuntimeException( 'WordPress metadata unslashing must not corrupt Unicode JSON.' );
	}

	function assert_import_error( string $expected, $actual ): void {
		if ( ! is_wp_error( $actual ) || $expected !== $actual->get_error_code() ) {
			throw new \RuntimeException( "Expected {$expected}." );
		}
	}

	$path = tempnam( sys_get_temp_dir(), 'beanstalk-city-page-' );
	file_put_contents(
		$path,
		"post_id,slug,related_location_id,service_area_name,state_name,state_abbreviation,service_type,hero_heading\n" .
		"101,example-city,77,Example City,New Jersey,NJ,Insulation Services,Example heading\n"
	);
	$rows = CityPageCliCommand::read_csv( $path );
	unlink( $path );
	if ( ! is_array( $rows ) || 1 !== count( $rows ) || '101' !== $rows[0]['post_id'] || 'Example City' !== $rows[0]['service_area_name'] ) {
		throw new \RuntimeException( 'A valid City Page CSV was not parsed exactly.' );
	}

	$path = tempnam( sys_get_temp_dir(), 'beanstalk-city-page-' );
	file_put_contents( $path, "post_id,slug,related_location_id\n101,example-city,77\n" );
	assert_import_error( 'beanstalk_import_missing_header', CityPageCliCommand::read_csv( $path ) );
	unlink( $path );

	$path = tempnam( sys_get_temp_dir(), 'beanstalk-city-page-' );
	file_put_contents(
		$path,
		"post_id,post_id,slug,related_location_id,service_area_name,state_name,state_abbreviation,service_type\n" .
		"101,101,example-city,77,Example City,New Jersey,NJ,Insulation Services\n"
	);
	assert_import_error( 'beanstalk_import_invalid_headers', CityPageCliCommand::read_csv( $path ) );
	unlink( $path );

	$updater = file_get_contents( dirname( __DIR__ ) . '/src/CityPageUpdater.php' );
	if ( false === strpos( $updater, "write_meta( \$post->ID, self::TEMPLATE_META, self::TEMPLATE_VALUE )" ) ) {
		throw new \RuntimeException( 'The explicit Beanstalk activation write is missing.' );
	}
	$activation_position = strpos( $updater, "write_meta( \$post->ID, self::TEMPLATE_META, self::TEMPLATE_VALUE )" );
	$context_position    = strpos( $updater, 'apply_existing' );
	if ( false === $context_position || $activation_position < $context_position ) {
		throw new \RuntimeException( 'The template must activate only after City Page context is applied.' );
	}

	$bootstrap = file_get_contents( dirname( __DIR__ ) . '/src/Bootstrap.php' );
	$provider_position = strpos( $bootstrap, 'register_cli_theme_provider' );
	$command_position  = strpos( $bootstrap, "add_command( 'beanstalk city-pages update'" );
	if ( false === $provider_position || false === $command_position || $provider_position > $command_position ) {
		throw new \RuntimeException( 'The active theme provider must be registered before the City Page CLI command runs.' );
	}

	echo "City Page import contract: PASS\n";
}
