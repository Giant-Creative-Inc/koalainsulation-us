<?php
/**
 * Service advantages grid.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$advantages_service_id = (int) get_queried_object_id();
$national_advantages   = koala_beanstalk_is_national_service();
$advantages_editorial  = $editorial['advantages'] ?? array();
$advantages_title      = $advantages_editorial['title'] ?? ( $national_advantages ? get_the_title( $advantages_service_id ) : (string) get_post_meta( $advantages_service_id, 'advantages_title', true ) );
if ( array_key_exists( 'advantages', $editorial ) && ! $advantages_editorial ) {
	return;
}
$advantages_content = $advantages_editorial['content'] ?? (string) get_post_meta( $advantages_service_id, 'advantages_content', true );
$advantages_cards   = koala_beanstalk_service_advantage_cards( $advantages_service_id );
if ( ! koala_beanstalk_service_has_advantages( $advantages_title, $advantages_content, $advantages_cards ) ) {
	return;
}


$advantages_assets = get_template_directory_uri() . '/beanstalk/assets/';
?>
<section class="koala-service-advantages" aria-labelledby="koala-service-advantages-heading">
	<header class="koala-service-advantages__header">
		<h2 id="koala-service-advantages-heading">
			<span><?php echo esc_html( $advantages_editorial['label'] ?? ( $national_advantages ? 'Benefits of' : 'Advantages of' ) ); ?></span>
			<?php echo esc_html( $advantages_title ); ?>
		</h2>
		<?php if ( '' !== $advantages_content ) : ?>
			<div><?php echo wp_kses_post( wpautop( $advantages_content ) ); ?></div>
		<?php endif; ?>
	</header>
	<div class="koala-service-advantages__grid">
		<?php foreach ( $advantages_cards as $advantages_field => $advantages_card ) : ?>
			<div class="koala-service-advantages__card">
				<?php if ( isset( $advantages_card['icon'] ) ) : ?>
					<?php
					echo wp_get_attachment_image(
						$advantages_card['icon'],
						array( 48, 48 ),
						false,
						array(
							'alt'     => '',
							'loading' => 'lazy',
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup.
					?>
				<?php else : ?>
					<img src="<?php echo esc_url( $advantages_assets . 'service-advantage-' . $advantages_field . '.svg' ); ?>" width="48" height="48" alt="" loading="lazy">
				<?php endif; ?>
				<h3><?php echo esc_html( $advantages_card['label'] ); ?></h3>
				<?php if ( '' !== $advantages_card['content'] ) : ?>
					<div><?php echo wp_kses_post( wpautop( $advantages_card['content'] ) ); ?></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
