<?php
/**
 * Read-only lazy form rendering for published Local locations.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the public form-rendering route; no entry is submitted here.
 */
function koala_beanstalk_register_estimate_route(): void {
	register_rest_route(
		'koala-beanstalk/v1',
		'/estimate/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'permission_callback' => 'koala_beanstalk_estimate_permission',
			'callback'            => 'koala_beanstalk_estimate_response',
		)
	);
}
add_action( 'rest_api_init', 'koala_beanstalk_register_estimate_route' );

/**
 * Allow only published location forms on this bounded Local preview.
 *
 * @param  WP_REST_Request $request Request.
 * @return bool
 */
function koala_beanstalk_estimate_permission( $request ): bool {
	$post = get_post( (int) $request['id'] );
	return koala_beanstalk_location_environment_enabled() && $post && 'location' === $post->post_type && 'publish' === $post->post_status && function_exists( 'gravity_form' );
}

/**
 * Return form markup, initialization and only assets not shared with Form 12.
 *
 * @param  WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function koala_beanstalk_estimate_response( $request ) {
	$id              = (int) $request['id'];
	$GLOBALS['post'] = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Establish public location context for form population.
	setup_postdata( $GLOBALS['post'] );
	gravity_form_enqueue_scripts( 12, true );
	$scripts          = wp_scripts();
	$styles           = wp_styles();
	$baseline_scripts = $scripts->queue;
	$baseline_styles  = $styles->queue;
	foreach ( array( $scripts, $styles ) as $registry ) {
		$registry->all_deps( $registry->queue );
		$registry->done  = array_unique( array_merge( $registry->done, $registry->to_do ) );
		$registry->to_do = array();
	}
	gravity_form_enqueue_scripts( 13, true );
	$action = static function ( $tag ) use ( $id ) {
		$tags = new WP_HTML_Tag_Processor( $tag );
		if ( $tags->next_tag( 'FORM' ) ) {
			$tags->set_attribute( 'action', add_query_arg( 'koala_beanstalk_sidebar', '1', get_permalink( $id ) ) . '#gf_13' );
		}
		return $tags->get_updated_html();
	};
	add_filter( 'gform_init_scripts_footer', '__return_false' );
	add_filter( 'gform_form_tag_13', $action );
	$form = gravity_form( 13, false, false, false, null, true, 0, false );
	remove_filter( 'gform_form_tag_13', $action );
	remove_filter( 'gform_init_scripts_footer', '__return_false' );
	ob_start();
	wp_print_styles( array_values( array_diff( $styles->queue, $baseline_styles ) ) );
	wp_print_scripts( array_values( array_diff( $scripts->queue, $baseline_scripts ) ) );
	$assets   = (string) ob_get_clean();
	$response = new WP_REST_Response(
		array(
			'html' => $assets . $form,
			'css'  => get_template_directory_uri() . '/beanstalk/assets/location-estimate-popup.css?ver=' . filemtime( __DIR__ . '/assets/location-estimate-popup.css' ),
		)
	);
	$response->header( 'Cache-Control', 'no-store' );
	return $response;
}
