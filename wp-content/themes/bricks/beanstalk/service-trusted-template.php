<?php
/**
 * Trusted section of the native service preview.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$trusted = koala_beanstalk_service_trusted_data( (int) get_queried_object_id() );
if ( isset( $national_copy['sections']['trusted'] ) ) {
	$trusted            = $national_copy['sections']['trusted'] + $trusted;
	$gallery            = koala_beanstalk_location_selected_ids( (int) get_queried_object_id(), 'service_related_images' );
	$trusted['imageId'] = (int) get_post_meta( $gallery[0] ?? 0, 'service_image', true );
}
if ( '' === $trusted['title'] && '' === $trusted['content'] ) {
	return;
}

$trusted_assets = get_template_directory_uri() . '/beanstalk/assets/';
$trusted_video  = koala_beanstalk_location_hero_video_url( $trusted + array( 'provider' => 'Vimeo' ) );
$trusted_label  = 'insulation-removal-services' === get_query_var( 'service_slug' ) ? 'Helpful Tips' : 'Trusted';
?>
<section class="koala-service-trusted" aria-labelledby="koala-service-trusted-heading">
	<div class="koala-service-trusted__inner">
		<div class="koala-service-trusted__copy">
			<p class="koala-service-trusted__label"><?php echo esc_html( $trusted_label ); ?></p>
			<h2 id="koala-service-trusted-heading"><?php echo esc_html( $trusted['title'] ? $trusted['title'] : $trusted_label ); ?></h2>
			<img class="koala-service-trusted__leaf" src="<?php echo esc_url( $trusted_assets . 'service-trusted-leaf.webp' ); ?>" width="164" height="54" alt="" loading="lazy">
			<div class="koala-service-trusted__body"><?php echo wp_kses_post( wpautop( $trusted['content'] ) ); ?></div>
		</div>
		<div class="koala-service-trusted__media">
			<?php if ( $trusted_video ) : ?>
				<iframe tabindex="-1" data-service-autoplay="<?php echo esc_url( $trusted_video ); ?>" title="<?php echo esc_attr( $trusted['title'] ? $trusted['title'] : 'Insulation advice' ); ?>" loading="lazy" allow="autoplay; fullscreen" allowfullscreen></iframe>
			<?php elseif ( $trusted['imageId'] ) : ?>
				<?php echo wp_get_attachment_image( $trusted['imageId'], 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated image markup. ?>
			<?php endif; ?>
			<?php echo koala_beanstalk_render_location_hero_quote( array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared estimate popup trigger. ?>
		</div>
	</div>
	<img class="koala-service-trusted__obie" src="<?php echo esc_url( $trusted_assets . 'service-trusted-obie.webp' ); ?>" width="283" height="366" alt="" loading="lazy">
</section>
