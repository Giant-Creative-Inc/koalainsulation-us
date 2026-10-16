<?php
/**
 * Native location shell for incremental Gutenberg section migration.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( array( 'beanstalk-page', 'koala-beanstalk-location' ) ); ?>>
<?php wp_body_open(); ?>
<div class="koala-location-layout">
	<?php koala_beanstalk_render_part( 'header' ); ?>
	<main id="beanstalk-content" class="koala-location-content">
		<?php
		while ( have_posts() ) {
			the_post();
			$content = get_the_content();
			if ( strpos( $content, '"name":"koala-location-hero"' ) === false ) {
				echo do_blocks( koala_beanstalk_location_hero_content( get_the_ID() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Native block rendering.
			}
			// Render saved Gutenberg blocks; never invoke the Bricks content renderer.
			echo do_blocks( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Native block rendering.
			echo koala_beanstalk_location_community_missing( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted location shortcode output.
		}
		?>
	</main>
	<?php koala_beanstalk_render_part( 'footer' ); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
