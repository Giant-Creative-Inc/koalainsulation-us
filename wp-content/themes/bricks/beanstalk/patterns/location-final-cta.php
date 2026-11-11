<?php
/**
 * Final location quote pattern using the shared city CTA layout.
 *
 * @package Koala_Beanstalk
 */

?>
<!-- wp:group {"tagName":"section","metadata":{"name":"koala-location-final-cta"},"className":"koala-city-final-cta koala-location-final-cta"} -->
<section class="wp-block-group koala-city-final-cta koala-location-final-cta">
<!-- wp:group {"className":"koala-city-final-cta__content","layout":{"type":"default"}} -->
<div class="wp-block-group koala-city-final-cta__content">
<!-- wp:heading {"className":"koala-city-final-cta__heading"} -->
<h2 class="wp-block-heading koala-city-final-cta__heading"><?php echo isset( $final_cta_copy ) ? 'Get a Quote' : 'Get a quote'; ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"koala-city-final-cta__supporting-copy"} -->
<p class="koala-city-final-cta__supporting-copy"><?php echo wp_kses_post( '' !== ( $final_cta_copy ?? '' ) ? $final_cta_copy : 'Ready to start your insulation project? Get a free quote from your local Koala Insulation team today.' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:koala/location-hero-quote {"label":"Get a FREE Quote"} /-->
</div>
<!-- /wp:group -->
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"koala-city-final-cta__decoration"} -->
<figure class="wp-block-image size-full koala-city-final-cta__decoration"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/location-final-cta/decoration.webp' ); ?>" width="560" height="352" alt="" loading="lazy"/></figure>
<!-- /wp:image -->
</section>
<!-- /wp:group -->
