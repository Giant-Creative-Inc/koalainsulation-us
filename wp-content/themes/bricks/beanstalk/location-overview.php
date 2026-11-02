<?php
/**
 * Location overview and Read More section using the original Bricks field sources.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read current location overview data without changing legacy heading behaviour.
 *
 * @param int $id Location ID.
 * @return array
 */
function koala_beanstalk_location_overview_data( int $id ): array {
	$phone = (string) get_post_meta( $id, 'location_phone_number', true );
	return array(
		'title'     => 'Koala Insulation Of ' . get_the_title( $id ),
		'intro'     => (string) get_post_meta( $id, 'location_insulation_rich_text_1', true ),
		'more'      => (string) get_post_meta( $id, 'location_insulation_rich_text_2', true ),
		'phone'     => $phone,
		'telephone' => preg_replace( '/[^0-9+]/', '', $phone ),
	);
}

/** Render the optional overview section with a native accessible disclosure. */
function koala_beanstalk_render_location_overview(): string {
	$id = (int) get_the_ID();
	if ( '1' === (string) get_post_meta( $id, 'hide_location_overview', true ) ) {
		return '';
	}
	$data    = koala_beanstalk_location_overview_data( $id );
	$asset   = get_template_directory_uri() . '/beanstalk/assets/location-overview/';
	$more_id = wp_unique_id( 'location-overview-more-' );
	ob_start();
	?>
	<section class="koala-location-overview" data-location-overview>
		<div class="koala-location-overview__copy">
			<h2><?php echo esc_html( $data['title'] ); ?></h2>
			<?php if ( '' !== trim( $data['intro'] ) ) : ?>
			<div class="koala-location-overview__intro"><?php echo wp_kses_post( wpautop( $data['intro'] ) ); ?></div>
			<?php endif; ?>
			<?php if ( '' !== trim( $data['more'] ) ) : ?>
			<details class="koala-location-overview__details">
				<summary aria-controls="<?php echo esc_attr( $more_id ); ?>">
					<span class="koala-location-overview__more-label">Read More</span><span class="koala-location-overview__less-label">Read Less</span>
					<img class="koala-location-overview__plus" src="<?php echo esc_url( $asset . 'plus.svg' ); ?>" width="24" height="24" alt="">
					<img class="koala-location-overview__minus" src="<?php echo esc_url( $asset . 'minus.svg' ); ?>" width="24" height="24" alt="">
				</summary>
				<div id="<?php echo esc_attr( $more_id ); ?>" class="koala-location-overview__more"><?php echo wp_kses_post( wpautop( $data['more'] ) ); ?></div>
			</details>
			<?php endif; ?>
		</div>
		<div class="koala-location-overview__aside">
			<div class="koala-location-overview__image"><?php echo wp_get_attachment_image( 48161, 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Original Bricks attachment and alt text. ?></div>
			<?php if ( '' !== trim( $data['phone'] ) && $data['telephone'] ) : ?>
			<a class="koala-location-overview__phone" href="<?php echo esc_url( 'tel:' . $data['telephone'] ); ?>">
				<picture><source media="(min-width:992px)" srcset="<?php echo esc_url( $asset . 'phone-desktop.svg' ); ?>"><img src="<?php echo esc_url( $asset . 'phone-mobile.svg' ); ?>" width="24" height="24" alt=""></picture>
				<span><?php echo esc_html( $data['phone'] ); ?></span>
			</a>
			<?php endif; ?>
			<?php echo koala_beanstalk_render_location_hero_quote( array( 'label' => 'Get a FREE Quote' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared future-popup trigger. ?>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/** Register an ACF-backed native block and reusable section pattern. */
function koala_beanstalk_register_location_overview(): void {
	wp_register_script( 'koala-location-overview-editor', get_template_directory_uri() . '/beanstalk/assets/location-overview-editor.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-server-side-render' ), (string) filemtime( __DIR__ . '/assets/location-overview-editor.js' ), true );
	register_block_type(
		'koala/location-overview',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-location-overview-editor',
			'render_callback' => 'koala_beanstalk_render_location_overview',
			'supports'        => array( 'html' => false ),
		)
	);
	register_block_pattern(
		'koala/location-overview',
		array(
			'title'      => 'Koala Location Overview',
			'categories' => array( 'featured' ),
			'postTypes'  => array( 'location' ),
			'content'    => '<!-- wp:koala/location-overview /-->',
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_overview' );
