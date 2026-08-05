<?php
/* Template Name: Why Reinsulate */

// Get the current URL path to extract the location slug
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_path = trim($current_path, '/'); // Remove leading/trailing slashes
$path_segments = explode('/', $current_path);

// The location slug should be the first segment before 'why-reinsulate'
$location_slug = '';
if (count($path_segments) >= 2 && $path_segments[count($path_segments) - 1] === 'why-reinsulate') {
    $location_slug = $path_segments[count($path_segments) - 2];
}

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location'); // Replace 'location' with your custom post type slug for location

if ($location) {
    // Get the location ID
    $location_id = $location->ID;

    $location_name = get_field('location_name', $location_id);

    $location_url = get_the_permalink($location_id);

    // Query for "Why Reinsulate" posts related to the current location
    $args = array(
        'post_type' => 'location-why-reinsul', // The custom post type slug for "Why Reinsulate"
        'posts_per_page' => 1, // Just need one for meta
        'meta_query' => array(
            array(
                'key' => 'wr_related_location', // The ACF relationship field key in "Why Reinsulate"
                'value' => $location_id, // The location ID from the URL
                'compare' => 'LIKE', // Ensure it checks for the location ID in the relationship field
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

    $query = new WP_Query(array(
        'post_type' => 'location-why-reinsul',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'wr_related_location',
                'value' => $location_id,
                'compare' => 'LIKE',
            ),
        ),
    ));

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();

            // Get photos from the gallery
            $gallery = get_posts(array(
                'post_type' => 'photo-gallery',
                'posts_per_page' => -1,
            ));

            //created related homeowner incentives link
            $homeowner_url = trailingslashit($location_url) . 'homeowner-incentives';
            ?>
            <main id="brx-content">
                <section id="brxe-savjiv" class="brxe-section section">
                    <div id="brxe-vnslhk" class="brxe-container padding-global">
                        <div id="brxe-zprecc" class="brxe-block section-component">
                            <div id="brxe-arwzhx" class="brxe-block brx-grid hero-block-grid">
                                <div id="brxe-mxsiaq" class="brxe-block hero_content-wrapper" data-animation="up"
                                    data-duration="0.6">
                                    <div id="brxe-srhrwv" class="brxe-block">
                                        <h1 id="brxe-qabuzj" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                            data-animi="up" data-duration="0.6" data-delay="0.2">
                                            <?php if ($location_name) {
                                              echo 'Why Reinsulate Your ' . $location_name . ' Home';
                                            } else {
                                              the_title();
                                            } ?>
                                        </h1>
                                        <div id="brxe-ouaald" class="brxe-text-basic text-size-regular text-color-mute"
                                            data-animi="up" data-duration="0.6" data-delay="0.2">
                                            <?php
                                            $heroContent = get_field('wr_hero_description');
                                            if ($heroContent) {
                                                echo $heroContent;
                                            }
                                            ?>
                                        </div>
                                        <div id="national_estimate-btn" class="brxe-div btn is-no-icon" data-animi="up"
                                            data-delay="0.4" data-duration="0.6"
                                            data-interactions='[{"id":"arkdpe","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                            data-interaction-id="3d91a8">
                                            <div id="brxe-hnllfz" class="brxe-text-basic">
                                                Get a Free Estimate
                                            </div>
                                        </div>
                                        <div id="get-estimate-btn1" class="brxe-div btn is-no-icon bricks-lazy-hidden"
                                            data-animi="up" data-delay="0.4" data-duration="0.6">
                                            <div id="brxe-tfpafh" class="brxe-text-basic">
                                                Get a Free Estimate
                                            </div>
                                        </div>
                                    </div>
                                    <div id="brxe-hiicha" class="brxe-div image-wrapper absolute">
                                        <img width="572" height="214"
                                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-lhbwaa"
                                            decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                                    </div>
                                </div>
                                <div id="brxe-ombtav" class="brxe-block hero_image-wrapper">
                                    <?php
                                    $heroImage = get_field('wr_hero_image');
                                    if ($heroImage): ?>
                                        <img width="1024" height="952" src="<?php echo esc_url($heroImage); ?>"
                                            class="brxe-image image-cover css-filter size-large" alt="<?php the_title(); ?>"
                                            id="brxe-xpkvbi" loading="eager" decoding="async"
                                            srcset="<?php echo esc_url($heroImage); ?>" sizes="(max-width: 1024px) 100vw, 1024px" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-zocmqt" class="brxe-section section">
                    <div id="brxe-puuyxk" class="brxe-container padding-global">
                        <div id="brxe-pmsxct" class="brxe-block section-component">
                            <div id="brxe-kexobw" class="brxe-block">
                                <div id="brxe-huyhml" data-script-id="huyhml" class="brxe-tabs-nested">
                                    <div id="brxe-iqsgji" class="brxe-block tab-menu">
                                        <div id="brxe-ncvtwr" class="brxe-block" data-animi="up" data-duration="0.6">
                                            <h3 id="brxe-ipnkoa" class="brxe-heading heading-style-display text-color-green">
                                                Benefits
                                            </h3>
                                            <img width="164" height="54"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-wzoqat" decoding="async"
                                                loading="lazy" data-type="string" />
                                        </div>
                                        <div id="brxe-majicq" class="brxe-div tab-title brx-open">
                                            <img width="54" height="54"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/23.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-hjglkd" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-beywzx" class="brxe-text-basic tab-heading">
                                                Temperature Control All Year
                                            </div>
                                        </div>
                                        <div id="brxe-vgamkp" class="brxe-div tab-title">
                                            <img width="45" height="45"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/1.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-mdgovv" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-cggknr" class="brxe-text-basic tab-heading">
                                                Feel the Comfort
                                            </div>
                                        </div>
                                        <div id="brxe-mraazt" class="brxe-div tab-title">
                                            <img width="45" height="45"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-mczink" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-mbvthl" class="brxe-text-basic tab-heading">
                                                Save on Bills
                                            </div>
                                        </div>
                                        <div id="brxe-omfpbb" class="brxe-div tab-title">
                                            <img width="45" height="45"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-gsuyjt" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-yuzojk" class="brxe-text-basic tab-heading">
                                                Keep Moisture at Bay
                                            </div>
                                        </div>
                                        <div id="brxe-rrjvqf" class="brxe-div tab-title">
                                            <img width="45" height="45"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-tytkju" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-htxjrj" class="brxe-text-basic tab-heading">
                                                Boost Your Home’s Value
                                            </div>
                                        </div>
                                        <div id="brxe-ciyzkp" class="brxe-div tab-title">
                                            <img width="45" height="45"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-bewrvd" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-jcwytb" class="brxe-text-basic tab-heading">
                                                Go Green and Save
                                            </div>
                                        </div>
                                        <div id="brxe-bmzqkg" class="brxe-div tab-title">
                                            <img width="45" height="45"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                class="brxe-image css-filter size-full" alt="" id="brxe-yeibea" decoding="async"
                                                loading="lazy" data-type="string" />
                                            <div id="brxe-zcrpna" class="brxe-text-basic tab-heading">
                                                Increase the life of your HVAC and Roof
                                            </div>
                                        </div>
                                    </div>
                                    <div id="brxe-gnkbou" class="brxe-block tab-content" data-animi="up" data-duration="0.6">
                                        <div id="brxe-yhhvwg" class="brxe-block tab-pane brx-open">
                                            <div id="brxe-dieqkl" class="brxe-block">
                                                <div id="brxe-zvtzog" class="brxe-block">
                                                    <img width="930" height="692"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-23.jpg'); ?>"
                                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-sxlyws"
                                                        decoding="async" loading="lazy" data-type="string"
                                                        sizes="(max-width: 930px) 100vw, 930px" srcset="
                        <?php echo home_url('/wp-content/uploads/2024/09/Frame-23.jpg'); ?>         930w,
                        <?php echo home_url('/wp-content/uploads/2024/09/Frame-23-300x223.jpg'); ?> 300w,
                        <?php echo home_url('/wp-content/uploads/2024/09/Frame-23-768x571.jpg'); ?> 768w
                      " />
                                                </div>
                                            </div>
                                            <div id="brxe-evhobr" class="brxe-block">
                                                <h4 id="brxe-prbqbi"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Temperature Control All Year<br />
                                                </h4>
                                                <div id="brxe-odacqy" class="brxe-text">
                                                    <p>
                                                        Proper batt insulation, spray foam, or blown in insulation
                                                        keeps your home just the right temperature, making it
                                                        easier to stay warm in the winter and cool in summer.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-lzcldg" class="brxe-block tab-pane bricks-lazy-hidden">
                                            <div id="brxe-yzkqfv" class="brxe-block bricks-lazy-hidden">
                                                <div id="brxe-lbyipr" class="brxe-block bricks-lazy-hidden">
                                                    <img width="930" height="692"
                                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20930%20692'%3E%3C/svg%3E"
                                                        class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                        alt="" id="brxe-bxnrje" decoding="async" loading="lazy"
                                                        data-src="<?php echo home_url('/wp-content/uploads/2024/06/2.png'); ?>"
                                                        data-type="string" data-sizes="(max-width: 930px) 100vw, 930px"
                                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/06/2.png'); ?> 930w, <?php echo home_url('/wp-content/uploads/2024/06/2-300x223.png'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/06/2-768x571.png'); ?> 768w" />
                                                </div>
                                            </div>
                                            <div id="brxe-yocgvh" class="brxe-block bricks-lazy-hidden">
                                                <h4 id="brxe-ozrinr"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Feel the Comfort
                                                </h4>
                                                <div id="brxe-oxzuqd" class="brxe-text">
                                                    <p>
                                                        With fresh insulation, including spray foam insulation,
                                                        you’ll notice fewer drafts and a more consistent indoor
                                                        climate, creating a cozy atmosphere all year round.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-vutpor" class="brxe-block tab-pane bricks-lazy-hidden">
                                            <div id="brxe-epgzvo" class="brxe-block bricks-lazy-hidden">
                                                <div id="brxe-svbmif" class="brxe-block bricks-lazy-hidden">
                                                    <img width="930" height="692"
                                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20930%20692'%3E%3C/svg%3E"
                                                        class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                        alt="" id="brxe-btmlka" decoding="async" loading="lazy"
                                                        data-src="<?php echo home_url('/wp-content/uploads/2024/09/6.jpg'); ?>"
                                                        data-type="string" data-sizes="(max-width: 930px) 100vw, 930px"
                                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/09/6.jpg'); ?> 930w, <?php echo home_url('/wp-content/uploads/2024/09/6-300x223.jpg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/09/6-768x571.jpg'); ?> 768w" />
                                                </div>
                                            </div>
                                            <div id="brxe-rvetlo" class="brxe-block bricks-lazy-hidden">
                                                <h4 id="brxe-epwddv"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Save on Bills
                                                </h4>
                                                <div id="brxe-ahgamh" class="brxe-text">
                                                    <p>
                                                        Upgrading your insulation can lead to noticeable saving on
                                                        your energy bills by reducing the need for heating and
                                                        cooling. On average, homeowners save 15% on energy costs
                                                        with proper insulation.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-bdgfam" class="brxe-block tab-pane bricks-lazy-hidden">
                                            <div id="brxe-kfdrqg" class="brxe-block bricks-lazy-hidden">
                                                <div id="brxe-rbkhat" class="brxe-block bricks-lazy-hidden">
                                                    <img width="930" height="692"
                                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20930%20692'%3E%3C/svg%3E"
                                                        class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                        alt="" id="brxe-wszjzh" decoding="async" loading="lazy"
                                                        data-src="<?php echo home_url('/wp-content/uploads/2024/09/5.jpg'); ?>"
                                                        data-type="string" data-sizes="(max-width: 930px) 100vw, 930px"
                                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/09/5.jpg'); ?> 930w, <?php echo home_url('/wp-content/uploads/2024/09/5-300x223.jpg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/09/5-768x571.jpg'); ?> 768w" />
                                                </div>
                                            </div>
                                            <div id="brxe-apqczp" class="brxe-block bricks-lazy-hidden">
                                                <h4 id="brxe-qjpayg"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Keep Moisture at Bay
                                                </h4>
                                                <div id="brxe-brvigf" class="brxe-text">
                                                    <p>
                                                        New insulation helps manage moisture better, cutting down
                                                        on the chances of mold and mildew forming in your home,
                                                        especially in areas like the crawl space.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-hfmlwg" class="brxe-block tab-pane bricks-lazy-hidden">
                                            <div id="brxe-gxnvwp" class="brxe-block bricks-lazy-hidden">
                                                <div id="brxe-uhrdub" class="brxe-block bricks-lazy-hidden">
                                                    <img width="1080" height="1438"
                                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201080%201438'%3E%3C/svg%3E"
                                                        class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                        alt="" id="brxe-kaywzg" decoding="async" loading="lazy"
                                                        data-src="<?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837.jpeg'); ?>"
                                                        data-type="string" data-sizes="(max-width: 1080px) 100vw, 1080px"
                                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837.jpeg'); ?> 1080w, <?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837-225x300.jpeg'); ?> 225w, <?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837-769x1024.jpeg'); ?> 769w, <?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837-768x1023.jpeg'); ?> 768w" />
                                                </div>
                                            </div>
                                            <div id="brxe-videvv" class="brxe-block bricks-lazy-hidden">
                                                <h4 id="brxe-etqlgg"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Boost Your Home’s Value
                                                </h4>
                                                <div id="brxe-ynrjxx" class="brxe-text">
                                                    <p>
                                                        Better insulation, such as spray foam insulation or
                                                        fiberglass batt insulation, not only makes your home more
                                                        comfortable, but can also increase its value when it’s
                                                        time to sell.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-kkpqwk" class="brxe-block tab-pane bricks-lazy-hidden">
                                            <div id="brxe-cfqlln" class="brxe-block bricks-lazy-hidden">
                                                <div id="brxe-znyani" class="brxe-block bricks-lazy-hidden">
                                                    <img width="2000" height="1333"
                                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%202000%201333'%3E%3C/svg%3E"
                                                        class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                        alt="" id="brxe-ukfund" decoding="async" loading="lazy"
                                                        data-src="<?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821.jpeg'); ?>"
                                                        data-type="string" data-sizes="(max-width: 2000px) 100vw, 2000px"
                                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821.jpeg'); ?> 2000w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-300x200.jpeg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-1024x682.jpeg'); ?> 1024w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-768x512.jpeg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-1536x1024.jpeg'); ?> 1536w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-600x400.jpeg'); ?> 600w" />
                                                </div>
                                            </div>
                                            <div id="brxe-qnmvhe" class="brxe-block bricks-lazy-hidden">
                                                <h4 id="brxe-jpsxpm"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Go Green and Save
                                                </h4>
                                                <div id="brxe-yghxni" class="brxe-text">
                                                    <p>
                                                        Upgrading your insulation is not only great for the environment, but it helps reduce energy waste, lowers monthly utility bills, and makes your home more comfortable year-round. Koala Insulation provides high-quality, eco-friendly insulation solutions that help you save while going green.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-sbwlih" class="brxe-block tab-pane bricks-lazy-hidden">
                                            <div id="brxe-lrhwzu" class="brxe-block bricks-lazy-hidden">
                                                <div id="brxe-dmesfi" class="brxe-block bricks-lazy-hidden">
                                                    <img width="1024" height="822"
                                                        src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201024%20822'%3E%3C/svg%3E"
                                                        class="brxe-image image-cover css-filter size-large bricks-lazy-hidden"
                                                        alt="" id="brxe-pfrgzl" decoding="async" loading="lazy"
                                                        data-src="<?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-1024x822.jpg'); ?>"
                                                        data-type="string" data-sizes="(max-width: 1024px) 100vw, 1024px"
                                                        data-srcset="<?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-1024x822.jpg'); ?> 1024w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-300x241.jpg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-768x616.jpg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-1536x1233.jpg'); ?> 1536w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-2048x1644.jpg'); ?> 2048w" />
                                                </div>
                                            </div>
                                            <div id="brxe-ehbpwb" class="brxe-block bricks-lazy-hidden">
                                                <h4 id="brxe-vhbzee"
                                                    class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                                    Increase the life of your HVAC and Roof
                                                </h4>
                                                <div id="brxe-ushkpa" class="brxe-text">
                                                    <p>
                                                        Proper insulation takes the stress of your HVAC unit and
                                                        reduces moisture in the home, improving the life of your
                                                        HVAC and roof.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php /*
                <section id="brxe-kgjsns" class="brxe-section section">
                    <div id="brxe-owdcag" class="brxe-container padding-global">
                        <div id="brxe-kvbgcu" class="brxe-block section-component">
                            <div id="brxe-ofjvfk" class="brxe-block brx-grid hero-block-grid">
                                <div id="brxe-ergqvd" class="brxe-block hero_content-wrapper" data-animation="up"
                                    data-duration="0.6">
                                    <div id="brxe-jvmaaz" class="brxe-block">
                                        <h1 id="brxe-ttdiar" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                            data-animi="up" data-duration="0.6">
                                            Homeowner’s Incentives
                                        </h1>
                                        <div id="brxe-bnexob" class="brxe-text-basic text-size-regular text-color-mute"
                                            data-animi="up" data-duration="0.6" data-delay="0.2">
                                            Homeowners can benefit from several regional and national
                                            incentives when reinsulating their homes. Here are some options
                                            to explore.
                                        </div>
                                        <a href=<?php echo $homeowner_url ?> class="brxe-jvgelr brxe-div btn-secondary"
                                            data-animi="up" data-duration="0.6" data-delay="0.3">
                                            <div class="brxe-nahywj brxe-text-basic">Read More</div>
                                        </a><!--brx-loop-start-jvgelr-->
                                    </div>
                                    <div id="brxe-abcsgm" class="brxe-div image-wrapper absolute">
                                        <img width="572" height="214"
                                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mylqtd"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                                    </div>
                                </div>
                                <div id="brxe-yihatq" class="brxe-block hero_image-wrapper">
                                    <img width="1005" height="1024"
                                        src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-large" alt="" id="brxe-suhdva" loading="eager"
                                        decoding="async" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?> 1005w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-294x300.jpg'); ?>    294w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-768x783.jpg'); ?>    768w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2.jpg'); ?>           1032w
              " sizes="(max-width: 1005px) 100vw, 1005px" />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                */ ?>
                <section id="brxe-wkdvqs" class="brxe-section section">
                    <div id="brxe-kxbgqo" class="brxe-container padding-global">
                        <div id="brxe-hxkyve" class="brxe-block padding-section-medium">
                            <div id="brxe-vsronv" class="brxe-block section-component">
                                <div id="brxe-nzsudu" class="brxe-block slider-photo-wrapper" data-animi="up" data-delay="0.1"
                                    data-duration="0.6">
                                    <div id="brxe-jpghca" class="brxe-div">
                                        <div id="brxe-idvblp" class="brxe-div swiper is-slider-main-swiper">
                                            <div id="brxe-kiflap" class="brxe-div swiper-wrapper is-slider-main-wrapper">
                                                <?php foreach ($gallery as $gallery_item): ?>
                                                    <?php
                                                    $gallery_item_id = $gallery_item->ID;
                                                    $gallery_item_image = get_field('image', $gallery_item_id);
                                                    $gallery_item_image_url = is_array($gallery_item_image) ? $gallery_item_image['url'] : '';
                                                    ?>
                                                    <div class="brxe-div swiper-slide is-slider-main-slide">
                                                        <div class="brxe-ejfkti brxe-block">
                                                            <div class="brxe-ijfxwu brxe-block">
                                                                <img width="489" height="440"
                                                                    src="<?php echo $gallery_item_image_url; ?>" alt=""
                                                                    class="brxe-yxvlnv brxe-image image-cover css-filter size-large"
                                                                    decoding="async" loading="lazy" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="brxe-zxkkfi" class="brxe-block">
                                        <div id="brxe-dutpxn" class="brxe-div" data-animi="up" data-duration="0.6">
                                            <div id="brxe-xiilep" class="brxe-div swiper-prev" tabindex="0" role="button"
                                                aria-label="Previous slide" aria-controls="brxe-kiflap">
                                                <svg class="brxe-svg" id="brxe-ccuqvz" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none">
                                                    <mask id="mask0_6621_163" style="mask-type: alpha" maskUnits="userSpaceOnUse"
                                                        x="0" y="0" width="24" height="24">
                                                        <rect width="24" height="24" transform="matrix(-1 0 0 1 24 0)"
                                                            fill="#D9D9D9"></rect>
                                                    </mask>
                                                    <g mask="url(#mask0_6621_163)">
                                                        <path
                                                            d="M7.825 13H20V11H7.825L13.425 5.4L12 4L4 12L12 20L13.425 18.6L7.825 13Z"
                                                            fill="#043968">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div id="brxe-wjepmf" class="brxe-div swiper-next" tabindex="0" role="button"
                                                aria-label="Next slide" aria-controls="brxe-kiflap">
                                                <svg class="brxe-svg" id="brxe-kgojkt" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none">
                                                    <mask id="mask0_6621_238" style="mask-type: alpha" maskUnits="userSpaceOnUse"
                                                        x="0" y="0" width="24" height="24">
                                                        <rect width="24" height="24" fill="#D9D9D9"></rect>
                                                    </mask>
                                                    <g mask="url(#mask0_6621_238)">
                                                        <path
                                                            d="M16.175 13H4V11H16.175L10.575 5.4L12 4L20 12L12 20L10.575 18.6L16.175 13Z"
                                                            fill="#191919"></path>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
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
        endwhile;
        echo '</div>';
    else:
        render_fallback_content();
    endif;

    wp_reset_postdata(); // Reset the query
} else {
    render_fallback_content();
}
get_footer();

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
    get_header();

    $location_wr = get_posts(array(
        'post_type' => 'location-why-reinsul',
        'posts_per_page' => -1,
    ));
    ?>
    <main id="brx-content">
        <section id="brxe-savjiv" class="brxe-section section">
            <div id="brxe-vnslhk" class="brxe-container padding-global">
                <div id="brxe-zprecc" class="brxe-block section-component">
                    <div id="brxe-arwzhx" class="brxe-block brx-grid hero-block-grid">
                        <div id="brxe-mxsiaq" class="brxe-block hero_content-wrapper" data-animation="up"
                            data-duration="0.6">
                            <div id="brxe-srhrwv" class="brxe-block">
                                <h1 id="brxe-qabuzj" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6" data-delay="0.2">
                                    When To Reinsulate - And Why It Matters
                                </h1>
                                <div id="brxe-ouaald" class="brxe-text-basic text-size-regular text-color-mute"
                                    data-animi="up" data-duration="0.6" data-delay="0.2">
                                    Is your insulation no longer keeping your home as comfortable as it used to? Removing
                                    insulation and
                                    reinsulating might be the solution you need. Over time, insulation can lose its
                                    effectiveness due to
                                    wear and tear, moisture, or settling. By upgrading your insulation with options like
                                    spray foam
                                    insulation or blown-in insulation, you can boost your home’s energy efficiency, enhance
                                    comfort, and
                                    save on energy costs. Discover the benefits of reinsulating and see how a simple upgrade
                                    can make a big
                                    difference in your home.
                                </div>
                                <div id="national_estimate-btn" class="brxe-div btn is-no-icon" data-animi="up"
                                    data-delay="0.4" data-duration="0.6"
                                    data-interactions='[{"id":"arkdpe","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                    data-interaction-id="3d91a8">
                                    <div id="brxe-hnllfz" class="brxe-text-basic">
                                        Get a Free Estimate
                                    </div>
                                </div>
                                <div id="get-estimate-btn1" class="brxe-div btn is-no-icon bricks-lazy-hidden"
                                    data-animi="up" data-delay="0.4" data-duration="0.6">
                                    <div id="brxe-tfpafh" class="brxe-text-basic">
                                        Get a Free Estimate
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-hiicha" class="brxe-div image-wrapper absolute">
                                <img width="572" height="214"
                                    src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-lhbwaa"
                                    decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                            </div>
                        </div>
                        <div id="brxe-ombtav" class="brxe-block hero_image-wrapper">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-1005x1024.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-pkhzdz" loading="eager"
                                decoding="async"
                                srcset="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-1005x1024.jpg'); ?> 1005w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-294x300.jpg'); ?> 294w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1-768x783.jpg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-1.jpg'); ?> 1032w"
                                sizes="(max-width: 1005px) 100vw, 1005px">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-zocmqt" class="brxe-section section">
            <div id="brxe-puuyxk" class="brxe-container padding-global">
                <div id="brxe-pmsxct" class="brxe-block section-component">
                    <div id="brxe-kexobw" class="brxe-block">
                        <div id="brxe-huyhml" data-script-id="huyhml" class="brxe-tabs-nested">
                            <div id="brxe-iqsgji" class="brxe-block tab-menu">
                                <div id="brxe-ncvtwr" class="brxe-block" data-animi="up" data-duration="0.6">
                                    <h2 id="brxe-ipnkoa" class="brxe-heading heading-style-display text-color-green">
                                      <?php if ($location_name) {
                                        echo 'The Benefits of Insulating Your ' . $location_name . ' Home';
                                      } else {
                                        echo 'The Benefits of Insulating Your Home';
                                      } ?>
                                    </h2>
                                    <img width="164" height="54"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-wzoqat" decoding="async"
                                        loading="lazy" data-type="string" />
                                </div>
                                <div id="brxe-majicq" class="brxe-div tab-title brx-open">
                                    <img width="54" height="54"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/23.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-hjglkd" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-beywzx" class="brxe-text-basic tab-heading">
                                        Temperature Control All Year
                                    </div>
                                </div>
                                <div id="brxe-vgamkp" class="brxe-div tab-title">
                                    <img width="45" height="45"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/1.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-mdgovv" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-cggknr" class="brxe-text-basic tab-heading">
                                        Feel the Comfort
                                    </div>
                                </div>
                                <div id="brxe-mraazt" class="brxe-div tab-title">
                                    <img width="45" height="45"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-mczink" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-mbvthl" class="brxe-text-basic tab-heading">
                                        Save on Bills
                                    </div>
                                </div>
                                <div id="brxe-omfpbb" class="brxe-div tab-title">
                                    <img width="45" height="45"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-gsuyjt" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-yuzojk" class="brxe-text-basic tab-heading">
                                        Keep Moisture at Bay
                                    </div>
                                </div>
                                <div id="brxe-rrjvqf" class="brxe-div tab-title">
                                    <img width="45" height="45"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-tytkju" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-htxjrj" class="brxe-text-basic tab-heading">
                                        Boost Your Home’s Value
                                    </div>
                                </div>
                                <div id="brxe-ciyzkp" class="brxe-div tab-title">
                                    <img width="45" height="45"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-bewrvd" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-jcwytb" class="brxe-text-basic tab-heading">
                                        Go Green and Save
                                    </div>
                                </div>
                                <div id="brxe-bmzqkg" class="brxe-div tab-title">
                                    <img width="45" height="45"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                        class="brxe-image css-filter size-full" alt="" id="brxe-yeibea" decoding="async"
                                        loading="lazy" data-type="string" />
                                    <div id="brxe-zcrpna" class="brxe-text-basic tab-heading">
                                        Increase the life of your HVAC and Roof
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-gnkbou" class="brxe-block tab-content" data-animi="up" data-duration="0.6">
                                <div id="brxe-yhhvwg" class="brxe-block tab-pane brx-open">
                                    <div id="brxe-dieqkl" class="brxe-block">
                                        <div id="brxe-zvtzog" class="brxe-block">
                                            <img width="930" height="692"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-23.jpg'); ?>"
                                                class="brxe-image image-cover css-filter size-full" alt="" id="brxe-sxlyws"
                                                decoding="async" loading="lazy" data-type="string"
                                                sizes="(max-width: 930px) 100vw, 930px" srcset="
                        <?php echo home_url('/wp-content/uploads/2024/09/Frame-23.jpg'); ?>         930w,
                        <?php echo home_url('/wp-content/uploads/2024/09/Frame-23-300x223.jpg'); ?> 300w,
                        <?php echo home_url('/wp-content/uploads/2024/09/Frame-23-768x571.jpg'); ?> 768w
                      " />
                                        </div>
                                    </div>
                                    <div id="brxe-evhobr" class="brxe-block">
                                        <h4 id="brxe-prbqbi"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Temperature Control All Year<br />
                                        </h4>
                                        <div id="brxe-odacqy" class="brxe-text">
                                            <p>
                                                Proper batt insulation, spray foam, or blown in insulation
                                                keeps your home just the right temperature, making it
                                                easier to stay warm in the winter and cool in summer.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-lzcldg" class="brxe-block tab-pane bricks-lazy-hidden">
                                    <div id="brxe-yzkqfv" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-lbyipr" class="brxe-block bricks-lazy-hidden">
                                            <img width="930" height="692"
                                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20930%20692'%3E%3C/svg%3E"
                                                class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                alt="" id="brxe-bxnrje" decoding="async" loading="lazy"
                                                data-src="<?php echo home_url('/wp-content/uploads/2024/06/2.png'); ?>"
                                                data-type="string" data-sizes="(max-width: 930px) 100vw, 930px"
                                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/06/2.png'); ?> 930w, <?php echo home_url('/wp-content/uploads/2024/06/2-300x223.png'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/06/2-768x571.png'); ?> 768w" />
                                        </div>
                                    </div>
                                    <div id="brxe-yocgvh" class="brxe-block bricks-lazy-hidden">
                                        <h4 id="brxe-ozrinr"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Feel the Comfort
                                        </h4>
                                        <div id="brxe-oxzuqd" class="brxe-text">
                                            <p>
                                                With fresh insulation, including spray foam insulation,
                                                you’ll notice fewer drafts and a more consistent indoor
                                                climate, creating a cozy atmosphere all year round.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-vutpor" class="brxe-block tab-pane bricks-lazy-hidden">
                                    <div id="brxe-epgzvo" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-svbmif" class="brxe-block bricks-lazy-hidden">
                                            <img width="930" height="692"
                                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20930%20692'%3E%3C/svg%3E"
                                                class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                alt="" id="brxe-btmlka" decoding="async" loading="lazy"
                                                data-src="<?php echo home_url('/wp-content/uploads/2024/09/6.jpg'); ?>"
                                                data-type="string" data-sizes="(max-width: 930px) 100vw, 930px"
                                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/09/6.jpg'); ?> 930w, <?php echo home_url('/wp-content/uploads/2024/09/6-300x223.jpg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/09/6-768x571.jpg'); ?> 768w" />
                                        </div>
                                    </div>
                                    <div id="brxe-rvetlo" class="brxe-block bricks-lazy-hidden">
                                        <h4 id="brxe-epwddv"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Save on Bills
                                        </h4>
                                        <div id="brxe-ahgamh" class="brxe-text">
                                            <p>
                                                Upgrading your insulation can lead to noticeable saving on
                                                your energy bills by reducing the need for heating and
                                                cooling. On average, homeowners save 15% on energy costs
                                                with proper insulation.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-bdgfam" class="brxe-block tab-pane bricks-lazy-hidden">
                                    <div id="brxe-kfdrqg" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-rbkhat" class="brxe-block bricks-lazy-hidden">
                                            <img width="930" height="692"
                                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20930%20692'%3E%3C/svg%3E"
                                                class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                alt="" id="brxe-wszjzh" decoding="async" loading="lazy"
                                                data-src="<?php echo home_url('/wp-content/uploads/2024/09/5.jpg'); ?>"
                                                data-type="string" data-sizes="(max-width: 930px) 100vw, 930px"
                                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/09/5.jpg'); ?> 930w, <?php echo home_url('/wp-content/uploads/2024/09/5-300x223.jpg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/09/5-768x571.jpg'); ?> 768w" />
                                        </div>
                                    </div>
                                    <div id="brxe-apqczp" class="brxe-block bricks-lazy-hidden">
                                        <h4 id="brxe-qjpayg"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Keep Moisture at Bay
                                        </h4>
                                        <div id="brxe-brvigf" class="brxe-text">
                                            <p>
                                                New insulation helps manage moisture better, cutting down
                                                on the chances of mold and mildew forming in your home,
                                                especially in areas like the crawl space.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-hfmlwg" class="brxe-block tab-pane bricks-lazy-hidden">
                                    <div id="brxe-gxnvwp" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-uhrdub" class="brxe-block bricks-lazy-hidden">
                                            <img width="1080" height="1438"
                                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201080%201438'%3E%3C/svg%3E"
                                                class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                alt="" id="brxe-kaywzg" decoding="async" loading="lazy"
                                                data-src="<?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837.jpeg'); ?>"
                                                data-type="string" data-sizes="(max-width: 1080px) 100vw, 1080px"
                                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837.jpeg'); ?> 1080w, <?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837-225x300.jpeg'); ?> 225w, <?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837-769x1024.jpeg'); ?> 769w, <?php echo home_url('/wp-content/uploads/2024/06/9dfb3c7d07bf37d7aeb69c416c84d837-768x1023.jpeg'); ?> 768w" />
                                        </div>
                                    </div>
                                    <div id="brxe-videvv" class="brxe-block bricks-lazy-hidden">
                                        <h4 id="brxe-etqlgg"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Boost Your Home’s Value
                                        </h4>
                                        <div id="brxe-ynrjxx" class="brxe-text">
                                            <p>
                                                Better insulation, such as spray foam insulation or
                                                fiberglass batt insulation, not only makes your home more
                                                comfortable, but can also increase its value when it’s
                                                time to sell.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-kkpqwk" class="brxe-block tab-pane bricks-lazy-hidden">
                                    <div id="brxe-cfqlln" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-znyani" class="brxe-block bricks-lazy-hidden">
                                            <img width="2000" height="1333"
                                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%202000%201333'%3E%3C/svg%3E"
                                                class="brxe-image image-cover css-filter size-full bricks-lazy-hidden"
                                                alt="" id="brxe-ukfund" decoding="async" loading="lazy"
                                                data-src="<?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821.jpeg'); ?>"
                                                data-type="string" data-sizes="(max-width: 2000px) 100vw, 2000px"
                                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821.jpeg'); ?> 2000w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-300x200.jpeg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-1024x682.jpeg'); ?> 1024w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-768x512.jpeg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-1536x1024.jpeg'); ?> 1536w, <?php echo home_url('/wp-content/uploads/2024/06/19b988b8b44a3af565ed4bf4e5f3c821-600x400.jpeg'); ?> 600w" />
                                        </div>
                                    </div>
                                    <div id="brxe-qnmvhe" class="brxe-block bricks-lazy-hidden">
                                        <h4 id="brxe-jpsxpm"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Go Green and Save
                                        </h4>
                                        <div id="brxe-yghxni" class="brxe-text">
                                            <p>
                                                Upgrading your insulation is not only great for the environment, but it helps reduce energy waste, lowers monthly utility bills, and makes your home more comfortable year-round. Koala Insulation provides high-quality, eco-friendly insulation solutions that help you save while going green.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-sbwlih" class="brxe-block tab-pane bricks-lazy-hidden">
                                    <div id="brxe-lrhwzu" class="brxe-block bricks-lazy-hidden">
                                        <div id="brxe-dmesfi" class="brxe-block bricks-lazy-hidden">
                                            <img width="1024" height="822"
                                                src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201024%20822'%3E%3C/svg%3E"
                                                class="brxe-image image-cover css-filter size-large bricks-lazy-hidden"
                                                alt="" id="brxe-pfrgzl" decoding="async" loading="lazy"
                                                data-src="<?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-1024x822.jpg'); ?>"
                                                data-type="string" data-sizes="(max-width: 1024px) 100vw, 1024px"
                                                data-srcset="<?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-1024x822.jpg'); ?> 1024w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-300x241.jpg'); ?> 300w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-768x616.jpg'); ?> 768w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-1536x1233.jpg'); ?> 1536w, <?php echo home_url('/wp-content/uploads/2024/09/ed5fe07b-af75-4771-af02-8624f1f4ff21-2048x1644.jpg'); ?> 2048w" />
                                        </div>
                                    </div>
                                    <div id="brxe-ehbpwb" class="brxe-block bricks-lazy-hidden">
                                        <h4 id="brxe-vhbzee"
                                            class="brxe-heading heading-style-h4 text-allcaps font-weight-medium">
                                            Increase the life of your HVAC and Roof
                                        </h4>
                                        <div id="brxe-ushkpa" class="brxe-text">
                                            <p>
                                                Proper insulation takes the stress of your HVAC unit and
                                                reduces moisture in the home, improving the life of your
                                                HVAC and roof.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-kgjsns" class="brxe-section section">
            <div id="brxe-owdcag" class="brxe-container padding-global">
                <div id="brxe-kvbgcu" class="brxe-block section-component">
                    <div id="brxe-ofjvfk" class="brxe-block brx-grid hero-block-grid">
                        <div id="brxe-ergqvd" class="brxe-block hero_content-wrapper" data-animation="up"
                            data-duration="0.6">
                            <div id="brxe-jvmaaz" class="brxe-block">
                                <h1 id="brxe-ttdiar" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    Homeowner’s Incentives
                                </h1>
                                <div id="brxe-bnexob" class="brxe-text-basic text-size-regular text-color-mute"
                                    data-animi="up" data-duration="0.6" data-delay="0.2" style="
                  opacity: 1;
                  translate: none;
                  rotate: none;
                  scale: none;
                  transform: translate(0px, 0px);
                ">
                                    Homeowners can benefit from several regional and national
                                    incentives when reinsulating their homes. Here are some options
                                    to explore.
                                </div>
                                <a href="/homeowner-incentives" class="brxe-jvgelr brxe-div btn-secondary" data-animi="up"
                                    data-duration="0.6" data-delay="0.3">
                                    <div class="brxe-nahywj brxe-text-basic">Read More</div>
                                </a>
                            </div>
                            <div id="brxe-abcsgm" class="brxe-div image-wrapper absolute">
                                <img width="572" height="214"
                                    src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mylqtd"
                                    decoding="async" loading="lazy" data-type="string"
                                    sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                            </div>
                        </div>
                        <div id="brxe-yihatq" class="brxe-block hero_image-wrapper">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-suhdva" loading="eager"
                                decoding="async" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?> 1005w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-294x300.jpg'); ?>    294w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-768x783.jpg'); ?>    768w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2.jpg'); ?>           1032w
              " sizes="(max-width: 1005px) 100vw, 1005px" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div id="cta" class="brxe-template">
            <section id="brxe-agowsi" class="brxe-section section">
                <div id="brxe-hwktzs" class="brxe-block section-component">
                    <div id="brxe-evndrw" class="brxe-block">
                        <div id="brxe-tkgjbp" class="brxe-block">
                            <h2 id="brxe-xiplhg" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                data-animi="up" data-delay="0.2" data-duration="0.6">
                                Find Your Location
                            </h2>
                            <div id="brxe-brpmwn" class="brxe-text-basic heading-style-h5" data-animi="up"
                                data-duration="0.6" data-delay="0.3">
                                Ready to Improve Your Insulation?
                            </div>
                            <div id="brxe-hossae" class="brxe-text-basic text-size-regular text-weight-semibold"
                                data-animi="up" data-duration="0.6" data-delay="0.3">
                                Whether it's spray foam insulation, blown in insulation, or anything
                                in between, we're here to help.
                            </div>
                            <div id="brxe-jtuwkc" class="brxe-div location-container" data-animi="up" data-delay="0.4"
                                data-duration="0.6">
                                <div id="brxe-nnegcj" data-script-id="nnegcj" class="brxe-code">
                                    <input type="text" id="my-zipcode-input" class="top-zipcode-input"
                                        placeholder="Zip or Postal Code" />
                                </div>
                                <div id="brxe-gjcvwu" class="brxe-div btn is-cta find-location-btn">
                                    <div id="brxe-smmtik" class="brxe-div">
                                        <svg class="brxe-svg btn-icon" id="brxe-gscjbg" xmlns="http://www.w3.org/2000/svg"
                                            width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M8.65481 16.7633C8.67746 16.7764 8.69527 16.7865 8.70788 16.7936L8.72882 16.8053C8.89597 16.8971 9.10332 16.8964 9.27063 16.8056L9.29212 16.7936C9.30473 16.7865 9.32254 16.7764 9.34519 16.7633C9.39049 16.737 9.45523 16.6988 9.53663 16.6486C9.69935 16.5484 9.92906 16.4007 10.2035 16.2068C10.7513 15.8198 11.4823 15.2456 12.2149 14.4955C13.673 13.0026 15.1875 10.7596 15.1875 7.875C15.1875 4.45774 12.4173 1.6875 9 1.6875C5.58274 1.6875 2.8125 4.45774 2.8125 7.875C2.8125 10.7596 4.32699 13.0026 5.78509 14.4955C6.51769 15.2456 7.24868 15.8198 7.79654 16.2068C8.07094 16.4007 8.30065 16.5484 8.46337 16.6486C8.54477 16.6988 8.60951 16.737 8.65481 16.7633ZM9 10.125C10.2426 10.125 11.25 9.11764 11.25 7.875C11.25 6.63236 10.2426 5.625 9 5.625C7.75736 5.625 6.75 6.63236 6.75 7.875C6.75 9.11764 7.75736 10.125 9 10.125Z"
                                                fill="white"></path>
                                        </svg>
                                    </div>
                                    <div id="brxe-aymaue" class="brxe-text-basic">
                                        Find My Location
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="brxe-bshupm" class="brxe-div">
                            <img width="296" height="143"
                                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1.png'); ?>"
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-wgftug"
                                decoding="async" data-type="string" />
                        </div>
                        <div id="brxe-wowmun" class="brxe-div">
                            <img width="560" height="352"
                                src="<?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>"
                                class="brxe-image image-contain is-absolute css-filter size-full" alt="" id="brxe-odzvyf"
                                decoding="async" data-type="string" sizes="(max-width: 560px) 100vw, 560px" srcset="
          <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1.png'); ?>         560w,
          <?php echo home_url('/wp-content/uploads/2024/06/Vector-1-1-300x189.png'); ?> 300w
        " />
                        </div>
                        <div id="brxe-zojbjb" class="brxe-div image-wrapper absolute" data-animi="up" data-duration="0.6"
                            data-delay="1">
                            <img width="440" height="410"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>"
                                class="brxe-image image-contain css-filter size-full" alt="" id="brxe-mjlwdy"
                                decoding="async" data-type="string" sizes="(max-width: 440px) 100vw, 440px" srcset="
          <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1.png'); ?>         440w,
          <?php echo home_url('/wp-content/uploads/2024/08/koala-head-icon-1-300x280.png'); ?> 300w
        " />
                        </div>
                    </div>
                </div>
                <div id="brxe-zutkds" class="brxe-block cta-icon-wrapper" data-animi="scale" data-duration="0.6"
                    data-delay="0.4">
                    <svg class="brxe-svg" id="brxe-xnwvlc" xmlns="http://www.w3.org/2000/svg" width="62" height="62"
                        viewBox="0 0 62 62" fill="none">
                        <g clip-path="url(#clip0_6431_678)">
                            <path
                                d="M8.1312 25.4686C7.65217 25.6298 7.30018 26.0407 7.21456 26.5389C7.12893 27.037 7.32348 27.5419 7.7212 27.8538L19.6529 37.2105L32.4737 28.3017C33.0973 27.8683 33.9541 28.0226 34.3875 28.6462C34.8208 29.2698 34.6666 30.1267 34.0429 30.56L21.2221 39.4688L25.8298 53.914C25.9834 54.3955 26.3888 54.754 26.8855 54.8475C27.3822 54.9409 27.8901 54.7544 28.2082 54.3616C36.2575 44.4241 42.4134 33.2972 46.5768 21.5352C46.7244 21.118 46.6623 20.6553 46.4097 20.2918C46.1572 19.9284 45.7451 19.7087 45.3026 19.7016C32.8272 19.5016 20.252 21.3905 8.1312 25.4686Z"
                                fill="#95C93D"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_6431_678">
                                <rect width="44" height="44" fill="white"
                                    transform="translate(0.379581 25.4873) rotate(-34.7942)"></rect>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </section>
        </div>
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
        <div style="display: none;">
            <?php if ($location_wr): ?>
                <?php foreach ($location_wr as $location_wr_item): ?>
                    <?php
                    $location_wr_item_id = $location_wr_item->ID;
                    $location_wr_item_link = get_permalink($location_wr_item_id);
                    $location_wr_item_name = get_the_title($location_wr_item_id);
                    ?>
                    <a href="<?php echo $location_wr_item_link ?>"><?php echo $location_wr_item_name ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No location why reinsulate posts found.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php
}
?>