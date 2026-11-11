<?php
/**
 * Service process and sticky quote card.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$process_service = (int) get_queried_object_id();
$process_steps   = koala_beanstalk_service_process_steps( $process_service );
$process_title   = (string) get_post_meta( $process_service, 'process_title', true );
$process_label   = (string) get_post_meta( $process_service, 'process_pre_header', true );
$offer_title     = (string) get_post_meta( $process_service, 'process_offer_title', true );
$offer_content   = (string) get_post_meta( $process_service, 'process_offer_content', true );
if ( isset( $editorial['process'] ) ) {
	$process_title = $editorial['process']['title'];
	$process_label = $editorial['process']['label'];
	$offer_title   = $editorial['process']['offerTitle'];
	$offer_content = $editorial['process']['offerContent'];
} elseif ( $national_service ) {
	$offer_title   = 'Find a Location';
	$offer_content = 'Ready to start your insulation project? Get a free quote from your local Koala Insulation team today.';
}
$video_id      = (string) get_post_meta( $process_service, 'process_video_id', true );
$video_hash    = (string) get_post_meta( $process_service, 'process_video_hash', true );
$process_video = '';
if ( preg_match( '/^[0-9]+$/D', $video_id ) && preg_match( '/^[a-zA-Z0-9]+$/D', $video_hash ) ) {
	$process_video = 'https://player.vimeo.com/video/' . $video_id . '?h=' . $video_hash;
}

if ( ! $process_steps && '' === $process_video ) {
	return;
}

$process_assets = get_template_directory_uri() . '/beanstalk/assets/';
?>
<section id="service-process" class="koala-service-process" aria-labelledby="service-process-heading">
	<header class="koala-service-process__header">
		<?php if ( '' !== $process_label ) : ?>
			<p><?php echo esc_html( $process_label ); ?></p>
		<?php endif; ?>
		<h2 id="service-process-heading"><?php echo esc_html( '' !== $process_title ? $process_title : 'Our Process' ); ?></h2>
		<img src="<?php echo esc_url( $process_assets . 'service-trusted-leaf.webp' ); ?>" width="164" height="54" alt="" loading="lazy">
	</header>
	<div class="koala-service-process__layout<?php echo '' !== $offer_title || '' !== $offer_content ? ' koala-service-process__layout--offer' : ''; ?>">
		<?php if ( '' !== $offer_title || '' !== $offer_content ) : ?>
			<aside class="koala-service-process__offer" aria-label="Insulation assessment offer">
				<img src="<?php echo esc_url( $process_assets . 'service-process-obie.svg' ); ?>" width="220" height="285" alt="" loading="lazy">
				<div class="koala-service-process__coupon">
					<div class="koala-service-process__coupon-inner">
						<?php if ( '' !== $offer_title ) : ?>
							<h3><?php echo esc_html( $offer_title ); ?></h3>
						<?php endif; ?>
						<?php echo wp_kses_post( wpautop( $offer_content ) ); ?>
						<?php echo koala_beanstalk_render_location_hero_quote( array( 'label' => 'Claim This Offer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared quote trigger. ?>
					</div>
				</div>
			</aside>
		<?php endif; ?>
		<div class="koala-service-process__details">
			<?php if ( '' !== $process_video ) : ?>
				<button type="button" class="koala-service-process__video koala-service-process__video-play" data-service-video-play="<?php echo esc_url( $process_video . '&autoplay=1' ); ?>">Play insulation process video</button>
			<?php endif; ?>
			<?php if ( $process_steps ) : ?>
				<ol class="koala-service-process__steps" role="list">
					<?php foreach ( $process_steps as $process_number => $process_step ) : ?>
						<li value="<?php echo esc_attr( (string) $process_number ); ?>">
							<h3>
								<img src="<?php echo esc_url( $process_assets . 'service-process-' . $process_number . '.svg?ver=' . filemtime( __DIR__ . '/assets/service-process-' . $process_number . '.svg' ) ); ?>" width="29" height="29" alt="" loading="lazy">
								<?php echo esc_html( $process_step['title'] ); ?>
							</h3>
							<?php echo wp_kses_post( wpautop( $process_step['content'] ) ); ?>
							<?php if ( $process_step['image'] ) : ?>
								<?php
								echo wp_get_attachment_image(
									$process_step['image'],
									'large',
									false,
									array(
										'class'   => 'koala-service-process__step-image',
										'loading' => 'lazy',
									)
								); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup.
								?>
							<?php endif; ?>
							<?php if ( '' !== $process_step['difference'] ) : ?>
								<div class="koala-service-process__difference"><strong>Koala Difference:</strong> <?php echo wp_kses_post( $process_step['difference'] ); ?></div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
	</div>
</section>
