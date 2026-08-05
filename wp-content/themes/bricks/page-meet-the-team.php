<?php
/* Template Name: Meet the Team */

// Get the current URL and strip query strings (like ?nonitro=1)
$current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove '/meet-the-team' AND any trailing/leading slashes so the slug is clean
$location_slug = trim(str_replace('/meet-the-team', '', $current_url), '/');

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location'); // Replace 'location' with your custom post type slug for location

if ($location) {
  // Get the location ID
  $location_id = $location->ID;

  // Query for "Meet the Team" posts related to the current location
  $args = array(
    'post_type' => 'resources-landing-pa', // The custom post type slug for "Meet the Team"
    'posts_per_page' => 1, // Just need one for meta
    'meta_query' => array(
      array(
        'key' => 'rl_related_location', // The ACF relationship field key in "Meet the Team"
        'value' => $location_id, // The location ID from the URL
        'compare' => 'LIKE', // Ensure it checks for the location ID in the relationship field
      ),
    ),
    'tax_query' => array(
      array(
        'taxonomy' => 'resources-page-type',
        'field' => 'slug',
        'terms' => 'meet-the-team', // Replace with the term you want to filter by
      ),
    ),
  );

  $meta_query = new WP_Query($args);

  if ($meta_query->have_posts()) {
      $meta_query->the_post();
      $why_koala_post_id = get_the_ID();

      // Inject Yoast meta using this Why Koala post
      add_filter('wpseo_title', function ($title) use ($why_koala_post_id) {
          return get_the_title($why_koala_post_id) . ' - ' . get_bloginfo('name');
      });

      add_filter('wpseo_metadesc', function ($desc) use ($why_koala_post_id) {
          $custom_desc = get_post_meta($why_koala_post_id, '_yoast_wpseo_metadesc', true);
          return $custom_desc ?: $desc;
      });

      add_filter('wpseo_opengraph_title', function ($og_title) use ($why_koala_post_id) {
          return get_the_title($why_koala_post_id) . ' - ' . get_bloginfo('name');
      });

      add_filter('wpseo_opengraph_desc', function ($og_desc) use ($why_koala_post_id) {
          $custom_desc = get_post_meta($why_koala_post_id, '_yoast_wpseo_metadesc', true);
          return $custom_desc ?: $og_desc;
      });

      wp_reset_postdata(); // Reset before main loop
  }

  get_header();

$term = get_term_by('slug', 'meet-the-team', 'resources-page-type');

$query = new WP_Query(array(
  'post_type' => 'resources-landing-pa',
  'posts_per_page' => -1,
  'meta_query' => array(
    array(
      'key' => 'rl_related_location',
      'value' => $location_id,
      'compare' => 'LIKE',
    ),
  ),
  'tax_query' => array(
    array(
      'taxonomy' => 'resources-page-type',
      'field' => 'term_id',
      'terms' => array($term->term_id),
      'include_children' => false,
    ),
  ),
));

  // Display related team members
  if ($query->have_posts()):
    while ($query->have_posts()):
      $query->the_post();
      ?>
      <main id="brx-content">
        <section id="brxe-toxqeb" class="brxe-section section">
          <div id="brxe-ykpymv" class="brxe-container padding-global">
            <div id="brxe-ezcjwt" class="brxe-block section-component">
              <div id="brxe-mtktyr" class="brxe-block brx-grid">
                <div id="brxe-qwenkp" class="brxe-block hero_content-wrapper">
                  <div id="brxe-ixvlsr" class="brxe-div image-wrapper absolute">
                    <img width="572" height="214"
                      src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                      class="brxe-image image-contain css-filter size-full" alt="" id="brxe-trylcc" decoding="async"
                      data-type="string" sizes="(max-width: 572px) 100vw, 572px"
                      srcset="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?> 572w, <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w" />
                  </div>
                  <h2 id="brxe-mqypve" class="brxe-heading heading-style-h2 font-weight-bold is-all-caps" data-animi="up"
                    data-duration="0.6">
                    <?php the_title(); ?>
                  </h2>
                  <div id="brxe-phxlqj" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                    data-duration="0.6" data-delay="0.2">
                    <?php
                    $heroContent = get_field('rl_hero_description');
                    if ($heroContent) {
                      echo '<div>' . esc_html($heroContent) . '</div>';
                    }
                    ?>
                  </div>
                </div>
                <div id="brxe-vdwwag" class="brxe-block hero_image-wrapper">
                  <?php
                  $rl_image = wp_get_attachment_image_url(get_field('rl_image'), 'full');
                  if ($rl_image): ?>
                    <img width="1024" height="952" src="<?php echo esc_url($rl_image); ?>"
                      class="brxe-image image-cover css-filter size-large" alt="Meet the Team" id="brxe-xpkvbi" loading="eager"
                      decoding="async" srcset="<?php echo esc_url($rl_image); ?>" sizes="(max-width: 1024px) 100vw, 1024px" />
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section id="brxe-wpwydx" class="brxe-section section">
          <div id="brxe-uebymw" class="brxe-container padding-global">
            <div id="brxe-ifwoob" class="brxe-block section-component">
              <div id="brxe-sqbaqq" class="brxe-block" data-animi="up" data-duration="0.6">
                <div id="brxe-vqlorg" class="brxe-text">
                  <?php
                  $content = get_field('rl_description');
                  if ($content) {
                    echo $content;
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
      <?php
    endwhile;
  else:
    echo '<p>No related post found for this location.</p>';
  endif;

  wp_reset_postdata(); // Reset the query
} else {
  echo '<p>Location not found.</p>';
}

get_footer();
?>