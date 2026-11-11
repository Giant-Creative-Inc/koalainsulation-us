<?php
/**
 * Service FAQ content, media and optional offers.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faq_service = (int) get_queried_object_id();
$faq_rows    = $national_copy['faqs'] ?? koala_beanstalk_service_faq_rows( $faq_service );
if ( isset( $editorial['faqs'] ) ) {
	$faq_images = (array) get_post_meta( $faq_service, '_koala_national_faq_images', true );
	foreach ( $faq_rows as &$faq_row ) {
		$faq_row += array(
			'image'      => (int) ( $faq_images[ $faq_row['title'] ] ?? 0 ),
			'offer'      => false,
			'videoLink'  => '',
			'videoCopy'  => '',
			'videoImage' => 0,
		);
	}
	unset( $faq_row );
}
if ( ! $faq_rows ) {
	return;
}
$faq_title    = $national_copy['faqTitle'] ?? (string) get_post_meta( $faq_service, 'faq_title', true );
$faq_label    = $editorial['faqLabel'] ?? trim( (string) get_post_meta( $faq_service, 'faq_pre_header', true ) );
$faq_intro    = $editorial['faqIntro'] ?? trim( (string) get_post_meta( $faq_service, 'faq_content', true ) );
$faq_location = koala_beanstalk_resolve_location_post( get_post_meta( $faq_service, 'related_location', true ) );
$faq_phone    = $faq_location ? (string) get_post_meta( $faq_location->ID, 'location_phone_number', true ) : '';
?>
<section id="service-faq" class="koala-service-faq" aria-labelledby="service-faq-heading">
	<header class="koala-service-faq__intro">
		<h2 id="service-faq-heading">
			<span><?php echo esc_html( '' !== $faq_label ? $faq_label : 'Frequently Asked Questions' ); ?></span>
			<?php echo esc_html( $faq_title ); ?>
		</h2>
		<?php if ( '' !== $faq_intro ) : ?>
			<?php echo wp_kses_post( wpautop( $faq_intro ) ); ?>
		<?php elseif ( '' !== $faq_phone ) : ?>
			<p>Get all your questions answered by our local experts. If we didn’t address your question here, give us a call at <a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $faq_phone ) ); ?>"><?php echo esc_html( $faq_phone ); ?></a> or schedule a free quote today!</p>
		<?php endif; ?>
	</header>
	<div class="koala-service-faq__items">
		<?php foreach ( $faq_rows as $faq ) : ?>
			<article class="koala-location-feature__item">
				<h3><?php echo esc_html( $faq['title'] ); ?></h3>
				<?php echo wp_kses_post( wpautop( $faq['content'] ) ); ?>
				<?php foreach ( $faq['images'] ?? ( $faq['image'] ? array( $faq['image'] ) : array() ) as $faq_image ) : ?>
					<?php
					echo wp_get_attachment_image(
						$faq_image,
						'large',
						false,
						( isset( $faq['imageAlt'] ) ? array( 'alt' => $faq['imageAlt'] ) : array() ) + array(
							'class'   => 'koala-service-faq__image',
							'loading' => 'lazy',
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup.
					?>
				<?php endforeach; ?>
				<?php if ( esc_url( $faq['videoLink'] ) ) : ?>
					<a class="koala-service-faq__video" href="<?php echo esc_url( $faq['videoLink'] ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="koala-service-faq__video-preview<?php echo $faq['videoImage'] ? ' has-poster' : ''; ?>">
							<?php if ( $faq['videoImage'] ) : ?>
								<?php echo wp_get_attachment_image( $faq['videoImage'], 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?>
							<?php else : ?>
								<img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/service-faq-video.webp' ); ?>" width="87" height="61" alt="" loading="lazy">
							<?php endif; ?>
						</span>
						<div class="koala-service-faq__video-content">
							<?php if ( '' !== $faq['videoCopy'] ) : ?>
								<?php $video_title = preg_replace( '/(?<=\p{L})<\/span>(?=\p{L})/u', '</span> ', $faq['videoCopy'] ) ?? $faq['videoCopy']; ?>
								<div class="koala-service-faq__video-title">
								<?php
								echo wp_kses(
									$video_title,
									array(
										'p'      => array(),
										'span'   => array( 'style' => true ),
										'br'     => array(),
										'strong' => array(),
										'em'     => array(),
									)
								);
								?>
																			</div>
							<?php endif; ?>
							<strong>Watch Now</strong>
						</div>
					</a>
				<?php endif; ?>
				<?php if ( $faq['offer'] ) : ?>
					<div class="koala-service-process__coupon koala-service-faq__offer">
						<div class="koala-service-process__coupon-inner">
							<h3>Free Insulation Assessment</h3>
							<p>The Koala Insulation efficiency experts will perform a thorough examination of your home’s current efficiency.</p>
							<?php echo koala_beanstalk_render_location_hero_quote( array( 'label' => 'Claim This Offer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared quote popup trigger. ?>
						</div>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
