<?php
/**
 * Upgrade panel below the Trusted section.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$superior_heading_id = isset( $service_superior_override ) ? 'koala-service-why-heading' : 'koala-service-superior-heading';
$superior            = $service_superior_override ?? $national_copy['sections']['superior'] ?? koala_beanstalk_service_superior_data( (int) get_queried_object_id() );
if ( '' === $superior['title'] && '' === $superior['content'] && ! $superior['items'] ) {
	return;
}

$superior_assets = get_template_directory_uri() . '/beanstalk/assets/';
?>
<section class="koala-service-superior" aria-labelledby="<?php echo esc_attr( $superior_heading_id ); ?>">
	<div class="koala-service-superior__panel">
		<div>
			<?php if ( '' !== ( $superior['label'] ?? 'A Superior' ) ) : ?>
			<p class="koala-service-superior__label"><?php echo esc_html( $superior['label'] ?? 'A Superior' ); ?></p>
			<?php endif; ?>
			<h2 id="<?php echo esc_attr( $superior_heading_id ); ?>"><?php echo esc_html( '' !== $superior['title'] ? $superior['title'] : 'Home Insulation Upgrade' ); ?></h2>
			<img src="<?php echo esc_url( $superior_assets . 'service-trusted-leaf.webp' ); ?>" width="164" height="54" alt="" loading="lazy">
			<?php if ( '' !== $superior['content'] ) : ?>
				<div class="koala-service-superior__body"><?php echo wp_kses_post( wpautop( $superior['content'] ) ); ?></div>
			<?php endif; ?>
			<?php echo koala_beanstalk_render_location_hero_quote( array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared estimate popup trigger. ?>
		</div>
		<?php if ( $superior['items'] ) : ?>
			<ul class="koala-service-superior__list" role="list">
				<?php foreach ( $superior['items'] as $superior_item ) : ?>
					<li>
						<img src="<?php echo esc_url( $superior_assets . 'service-superior-check.svg' ); ?>" width="29" height="29" alt="" loading="lazy">
						<div><?php echo wp_kses_post( wpautop( $superior_item ) ); ?></div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
