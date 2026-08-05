<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php do_action( 'bricks_meta_tags' ); ?>
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/custom-service-template.css" type="text/css" media="all">
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/custom-blog-template.css" type="text/css" media="all">
</head>

<?php
do_action( 'bricks_body' );

do_action( 'bricks_before_site_wrapper' );

do_action( 'bricks_before_header' );

do_action( 'render_header' );

do_action( 'bricks_after_header' );
