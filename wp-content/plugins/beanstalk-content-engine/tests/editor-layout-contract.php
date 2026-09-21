<?php
class WP_Error { public function __construct( public $code, public $message ) {} }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function __( $value ) { return $value; }
require_once dirname( __DIR__ ) . '/src/PatternValidator.php';
require_once dirname( __DIR__ ) . '/src/AbilityRegistrar.php';

use GiantCreative\BeanstalkContentEngine\PatternValidator;

$manifest = json_decode( file_get_contents( dirname( __DIR__, 3 ) . '/themes/bricks/beanstalk/pattern-manifests/city-page.json' ), true );
$validator = new PatternValidator();
if ( '0.14.0' !== $manifest['version'] || ! isset( $manifest['structuredData'] ) ) throw new RuntimeException( 'The unified City Page contract version is incomplete.' );
if ( is_wp_error( $validator->validate( $manifest ) ) ) throw new RuntimeException( 'The Koala editor layout must validate.' );
$normalized = $validator->normalize_editor_layout( $manifest['editorLayout'], array_keys( $manifest['fields'] ) );
if ( is_wp_error( $normalized ) || 1 !== $normalized['contractVersion'] ) throw new RuntimeException( 'The normalized layout contract is missing.' );
$collect = function ( $node ) use ( &$collect ) { $ids = array(); if ( isset( $node['fieldId'] ) ) $ids[] = $node['fieldId']; if ( isset( $node['fieldIds'] ) ) $ids = array_merge( $ids, $node['fieldIds'] ); foreach ( $node['children'] ?? array() as $child ) $ids = array_merge( $ids, $collect( $child ) ); return $ids; };
$referenced = $collect( $normalized['root'] ); sort( $referenced ); $declared = array_keys( $manifest['fields'] ); sort( $declared );
if ( $referenced !== $declared ) throw new RuntimeException( 'The Koala layout must reference every field exactly once.' );
$registrar = ( new ReflectionClass( GiantCreative\BeanstalkContentEngine\AbilityRegistrar::class ) )->newInstanceWithoutConstructor();
$schema_method = new ReflectionMethod( $registrar, 'pattern_schema_output_schema' ); $schema_method->setAccessible( true ); $ability_schema = $schema_method->invoke( $registrar );
if ( ! isset( $ability_schema['properties']['editor_layout'], $ability_schema['properties']['structured_data'] ) || isset( $ability_schema['properties']['draft_context']['properties']['editor_layout'] ) ) throw new RuntimeException( 'The ability schema must expose both contracts at the top level.' );
$unsafe = $manifest['editorLayout']; $unsafe['root']['html'] = '<script>alert(1)</script>';
if ( ! is_wp_error( $validator->normalize_editor_layout( $unsafe, array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Unknown markup must fail closed.' );
$missing = $manifest['editorLayout']; $missing['root'] = array( 'type' => 'field', 'fieldId' => 'missing', 'display' => 'body' );
if ( ! is_wp_error( $validator->normalize_editor_layout( $missing, array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Missing fields must fail closed.' );
$duplicate = $manifest['editorLayout']; $duplicate['root']['children'][] = array( 'type' => 'field', 'fieldId' => 'hero_heading', 'display' => 'heading' );
if ( ! is_wp_error( $validator->normalize_editor_layout( $duplicate, array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Duplicate field references must fail closed.' );
$ownership = $manifest['editorLayout']; $ownership['root']['children'][1]['children'][2]['children'][0]['ownership'] = 'koala';
if ( ! is_wp_error( $validator->normalize_editor_layout( $ownership, array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Invalid ownership must fail closed.' );
$layout = $manifest['editorLayout']; $layout['root']['children'][0]['children'][0]['ratio'][0] = 'wide';
if ( ! is_wp_error( $validator->normalize_editor_layout( $layout, array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Invalid layout values must fail closed.' );
$deep = array( 'type' => 'label', 'label' => 'End' ); for ( $i = 0; $i < 9; $i++ ) $deep = array( 'type' => 'stack', 'children' => array( $deep ) );
if ( ! is_wp_error( $validator->normalize_editor_layout( array( 'contractVersion' => 1, 'label' => 'Deep', 'root' => $deep ), array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Excessive depth must fail closed.' );
$many = array(); for ( $i = 0; $i < 50; $i++ ) $many[] = array( 'type' => 'stack', 'children' => array_fill( 0, 4, array( 'type' => 'label', 'label' => 'Node' ) ) );
if ( ! is_wp_error( $validator->normalize_editor_layout( array( 'contractVersion' => 1, 'label' => 'Many', 'root' => array( 'type' => 'stack', 'children' => $many ) ), array_keys( $manifest['fields'] ) ) ) ) throw new RuntimeException( 'Excessive node counts must fail closed.' );
echo "Editor layout contract checks passed\n";
