<?php
/**
 * Reusable editable location quote banner.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the portable core-block pattern markup. */
function koala_beanstalk_location_quote_banner_content(): string {
	ob_start();
	include __DIR__ . '/patterns/location-quote-banner.php';
	return (string) ob_get_clean();
}

/** Register the banner in the Gutenberg pattern inserter. */
function koala_beanstalk_register_location_quote_banner(): void {
	register_block_pattern(
		'koala/location-quote-banner',
		array(
			'title'       => 'Koala Location — Get a Quote Banner',
			'description' => 'Editable quote banner with the shared location popup trigger.',
			'categories'  => array( 'featured', 'call-to-action' ),
			'postTypes'   => array( 'location' ),
			'content'     => koala_beanstalk_location_quote_banner_content(),
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_quote_banner' );
