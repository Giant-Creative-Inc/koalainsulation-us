<?php
/* Template Name: Areas Served */
get_header();

// Get the current URL to extract the location slug
$current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Strip query string (e.g. NitroPack params) before parsing
$location_slug = trim(str_replace('/areas-served', '', $current_url), '/'); // Remove '/areas-served' from the URL to get the location slug

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location'); // Replace 'location' with your custom post type slug for location

if ($location) {
    // Get the location ID
    $location_id = $location->ID;

    //$areas_served = get_field('location_area_serviced', $location_id);
    $areas_served = get_posts(array(
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
                'field' => 'slug',
                'terms' => 'areas-served',
            ),
        ),
    ));
    ?>
    <main id="brx-content">
        <section id="brxe-cigjsz" class="brxe-section section">
            <div id="brxe-nrbpzr" class="brxe-container padding-global">
                <div id="brxe-aukyrh" class="brxe-block padding-section-medium">
                    <div id="brxe-ahalnk" class="brxe-block">
                        <h1 id="brxe-hwooji" class="brxe-heading heading-style-display" data-animi="up" data-duration="0.6">
                            Areas Served
                        </h1>
                        <div id="brxe-mjmuai" class="brxe-block brx-grid" data-animi="up" data-duration="0.6"
                            data-delay="0.2">
                            <?php if ($areas_served): ?>
                                <?php foreach ($areas_served as $area): ?>
                                    <?php
                                    $area_id = $area->ID;
                                    $area_title = get_the_title($area_id);
                                    $area_link = get_permalink($area_id);
                                    ?>
                                    <a id="brxe-hvsime" class="brxe-block" href="<?php echo $area_link ?>">
                                        <div id="brxe-qwtyko" class="brxe-text-basic">
                                            <?php echo $area_title ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No serviced areas found for this location.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="cta-quote" class="brxe-section section">
            <div id="brxe-bggdwc" class="brxe-block section-component">
                <div id="brxe-akzgbv" class="brxe-block">
                    <div id="brxe-dckkyl" class="brxe-block">
                        <h2 id="brxe-cfdbrz" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                            data-animi="up" data-duration="0.6">
                            Get a quote
                        </h2>
                        <div id="brxe-sushnb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                            data-delay="0.2" data-duration="0.6">
                            Ready to start your insulation project? Get a free quote from your
                            local Koala Insulation team today.
                        </div>
                        <div id="brxe-dbvghn" class="brxe-div btn is-no-icon"
                            data-interactions='[{"id":"pigxzw","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                            data-interaction-id="9f6f9b">
                            <div id="brxe-efxxnv" class="brxe-text-basic">
                                Get a Free Estimate
                            </div>
                        </div>
                    </div>
                    <div id="brxe-ltloqb" class="brxe-div">
                        <img width="296" height="143"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-bdqbqz" decoding="async"
                            loading="lazy" data-type="string" />
                    </div>
                    <div id="brxe-vjmidr" class="brxe-div">
                        <img width="560" height="352"
                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                            class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-kuxsvn"
                            decoding="async" loading="lazy" data-type="string" sizes="(max-width: 560px) 100vw, 560px"
                            srcset="
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
              <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
            " />
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php


} else {
    render_fallback_content();
}

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
    ?>
    <h1>No serviced areas found for this location.</h1>
    <?php
}
get_footer();
?>