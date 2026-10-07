<?php
/**
 * Minimal Beanstalk template for area-served Resources Landing Pages.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
ob_start( 'koala_beanstalk_lazy_page_images' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php koala_beanstalk_render_part( 'header' ); ?>
<main id="beanstalk-content">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
</main>
<?php koala_beanstalk_render_part( 'footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>

<?php ob_end_flush(); ?>
