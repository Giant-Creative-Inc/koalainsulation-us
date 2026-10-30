<?php
/** Regression check: repeated form hooks emit one complete configuration. */
define( 'ABSPATH', __DIR__ );
define( 'KGI_PLUGIN_FILE', __FILE__ );
define( 'KGI_VERSION', 'test' );
$localized = array();
function wp_enqueue_style( ...$args ) {}
function wp_enqueue_script( ...$args ) {}
function plugins_url( $path, $file ) { return $path; }
function kgi_get_all_quote_form_ids() { return array( 12, 13 ); }
function kgi_get_fixed_location_forms() { return array( array( 'form_id' => 21, 'field_map' => array( 'zip' => '8' ) ) ); }
function kgi_get_field_map_for_form( $id ) { return array( 'zip' => '6' ); }
function kgi_get_location_field_id_for_form( $id, $key ) { return $id . '_' . $key; }
function kgi_get_tracking_field_ids_for_form( $id ) { return array( 'UtmSource' => (string) $id ); }
function wp_localize_script( $handle, $name, $data ) {
	$GLOBALS['localized'][] = $data;
	return true;
}
require __DIR__ . '/../includes/forms/assets.php';
foreach ( array( 13, 12, 13, 21 ) as $id ) {
	kgi_enqueue_quote_form_assets( array( 'id' => $id ) );
}
if ( 1 !== count( $localized ) ) {
	fwrite( STDERR, 'FAIL: expected one configuration, got ' . count( $localized ) . PHP_EOL );
	exit( 1 );
}
$data = $localized[0];
if ( array( 12, 13, 21 ) !== $data['formIds'] || '8' !== $data['zipFieldIds'][21] || '12' !== $data['trackingFieldIds'][12]['UtmSource'] || '13' !== $data['trackingFieldIds'][13]['UtmSource'] || '21' !== $data['trackingFieldIds'][21]['UtmSource'] || '12_location_slug' !== $data['locationFieldIds'][12]['locationSlug'] ) {
	fwrite( STDERR, 'FAIL: configuration lost mapped form fields.' . PHP_EOL );
	exit( 1 );
}
echo "PASS: repeated hooks emit one complete multi-form configuration.\n";
