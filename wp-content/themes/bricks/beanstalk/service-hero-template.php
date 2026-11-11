<?php
/**
 * First section of the native Atlanta service preview.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$national_service = koala_beanstalk_is_national_service();
$editorial        = $national_service ? koala_beanstalk_national_editorial_copy() : array();
$national_copy    = $national_service ? koala_beanstalk_national_service_copy(
	parse_blocks( get_post_field( 'post_content', get_queried_object_id() ) ),
	(array) get_post_meta( get_queried_object_id(), '_koala_national_faq_images', true ),
	$editorial['additionalFaqs'] ?? array()
) : null;

if ( $editorial ) {
	$national_copy = $editorial + $national_copy;
}

if ( $national_service ) {
	add_filter(
		'body_class',
		static function ( array $classes ): array {
			return array_merge( $classes, array( 'beanstalk-service-hero-preview', 'koala-beanstalk-location', 'koala-beanstalk-national-service' ) );
		}
	);
	get_header();
} else {
	?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( array( 'beanstalk-page', 'beanstalk-service-hero-preview', 'koala-beanstalk-location' ) ); ?>>
	<?php wp_body_open(); ?>
	<?php koala_beanstalk_render_part( 'header' ); ?>
<?php } ?>
<div class="koala-location-layout">
	<main id="<?php echo $national_service ? 'brx-content' : 'beanstalk-content'; ?>" class="koala-location-content">
		<?php echo koala_beanstalk_service_hero_content( (int) get_queried_object_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered native blocks. ?>
		<?php require __DIR__ . '/service-trusted-template.php'; ?>
		<?php require __DIR__ . '/service-superior-template.php'; ?>
		<?php require __DIR__ . '/service-advantages-template.php'; ?>
		<?php require __DIR__ . '/service-financing-template.php'; ?>
		<?php require __DIR__ . '/service-process-template.php'; ?>
		<?php
		$service_expect_video = $national_service ? null : koala_beanstalk_service_expect_video( (int) get_queried_object_id() );
		echo do_blocks( koala_beanstalk_location_feature_content( 'expect', $service_expect_video ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered native blocks.
		?>
		<?php if ( isset( $editorial['why'] ) ) : ?>
			<?php
			$service_superior_override = $editorial['why'] + array( 'content' => '' );
			require __DIR__ . '/service-superior-template.php';
			?>
		<?php endif; ?>
		<?php require __DIR__ . '/service-reviews-template.php'; ?>
		<?php require __DIR__ . '/service-commercial-template.php'; ?>
		<?php
		$service_quote_heading = $editorial['quote'] ?? trim( (string) get_post_meta( (int) get_queried_object_id(), 'cta_title', true ) );
		echo do_blocks( koala_beanstalk_location_quote_banner_content( $service_quote_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered native blocks.
		?>
		<?php require __DIR__ . '/service-faq-template.php'; ?>
		<?php echo do_blocks( koala_beanstalk_location_community_content( 'final-cta', $service_quote_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered native blocks. ?>
	</main>
	<?php if ( ! $national_service ) : ?>
		<?php koala_beanstalk_render_part( 'footer' ); ?>
	<?php endif; ?>
</div>
<?php
if ( $national_service ) {
	get_footer();
	return;
}
wp_footer();
?>
</body>
</html>
