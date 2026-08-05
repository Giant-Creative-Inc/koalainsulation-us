<?php
function inject_canonical_in_head()
{
    // Get query vars
    $location = get_query_var('location_slug');
    $service = get_query_var('service_slug');

    // Construct the expected canonical URL based on your structure
    $canonical_url = home_url("/{$location}/services/{$service}");

    // Output the canonical tag
    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />';
}
add_action('wp_head', 'inject_canonical_in_head');

// Get WordPress header
get_header();

// Get query vars from URL (location and service)
$location = get_query_var('location_slug'); // The location part of the URL
$service = get_query_var('service_slug');   // The service part of the URL

// Append location to service slug to create the new combined slug
$newServiceName = $service . '-' . $location; // e.g., "spray-foam-insulation-austin"

// Query the 'location-service' post type based on the combined slug
$args = array(
    'post_type' => 'location-service',
    'name' => $newServiceName,  // Query using the combined slug
    'posts_per_page' => 1,
);
$query = new WP_Query($args);

// Check if the post exists
if ($query->have_posts()):
    while ($query->have_posts()):
        $query->the_post();

        global $wp_query;
        $wp_query->post = $query->post;
        $wp_query->posts = [$query->post];
        $wp_query->queried_object = $query->post;
        $wp_query->queried_object_id = $query->post->ID;
        $wp_query->found_posts = 1;
        $wp_query->post_count = 1;
        $wp_query->is_single = true;
        $wp_query->is_page = false;
        $wp_query->is_singular = true;
        
        setup_postdata($query->post); // Ensures the_title(), the_content(), etc., work right    

        //get related benefits
        $benefits = get_field('location_benifits');

        // Get photos from the service related gallery
        $gallery = get_field('location_service_related_images');

        //get is air sealing condition
        $is_air_sealing = get_field('is_location_air_sealing');

        // Get related locations from the 'wk_related_location' field
        $related_locations = get_field('related_location', $post_id); // Replace with your ACF field key

        // Get custom field values for the location and service
        $location_post = get_post_meta(get_the_ID(), 'related_location', true);  // This should return an array of post IDs

        // Check if there is a related location
        if (!empty($location_post)) {
            // Assuming 'related_location' is a relationship field that stores post IDs, get the first related location
            $location_post_id = $location_post[0];  // Get the first related location ID

            $location_name = get_the_title($location_post_id);  // Get the title of the related location post

            $nicejobId = get_field('location_nicejob_id', $location_post_id);

            $gr_shortCode = get_field('google_review_shortcode', $location_post_id);

            //additional services
            $other_services = get_posts(array(
                'post_type' => 'location-service',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'related_location', // The ACF relationship field key in "Service Single"
                        'value' => $location_post_id, // The location ID from the URL
                        'compare' => 'LIKE', // Ensure it checks for the location ID in the relationship field
                    ),
                ),
                'post__not_in' => array(get_page_by_path($newServiceName, OBJECT, 'location-service')->ID),
            ));
        }
        ?>
        <main id="brx-content">
            <section id="brxe-ftgaok" class="brxe-section section">
                <div id="brxe-lcxbiz" class="brxe-container padding-global">
                    <div id="brxe-vsxius" class="brxe-block section-component">
                        <div id="brxe-fswwiw" class="brxe-block brx-grid hero-block-grid">
                            <div id="brxe-txszej" class="brxe-block hero_content-wrapper">
                                <div id="brxe-qawlhh" class="brxe-block">
                                    <span id="brxe-nxojuy" class="brxe-heading heading-style-h1 font-weight-bold is-all-caps"
                                        data-animi="up" data-duration="0.6">
                                        <?php
                                        $heroHeading = get_field('location_service_name');
                                        if ($heroHeading) {
                                            echo $heroHeading . ' in ' . get_field('location_name', $related_locations[0]->ID);
                                        }
                                        ?>
                                    </span>
                                    <div id="brxe-urabqa" class="brxe-text-basic text-size-regular text-color-mute"
                                        data-animi="up" data-duration="0.6" data-delay="0.2">
                                        <?php
                                        $heroContent = get_field('location_service_short_description');
                                        if ($heroContent) {
                                            echo $heroContent;
                                        }
                                        ?>
                                    </div>
                                    <div id="brxe-auhoyj" class="brxe-div btn is-no-icon" data-animi="up" data-duration="0.6"
                                        data-delay="0.3"
                                        data-interactions='[{"id":"tcimtz","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                        data-interaction-id="d87eef">
                                        <div id="brxe-aqyonk" class="brxe-text-basic">
                                            Get a Free Estimate
                                        </div>
                                    </div>
                                </div>
                                <div id="brxe-dmtimx" class="brxe-div image-wrapper absolute">
                                    <img width="572" height="214"
                                        src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                        class="brxe-image image-contain css-filter size-full" alt="" id="brxe-geagea"
                                        decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                                </div>
                            </div>
                            <div id="brxe-ecinhj" class="brxe-block hero_image-wrapper">
                                <?php
                                $heroImage = wp_get_attachment_image_url(get_field('location_service_image'), 'full');
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
            <section id="brxe-gzmfmi" class="brxe-section section">
                <div id="brxe-fiqmmk" class="brxe-container padding-global">
                    <div id="brxe-gmnhql" class="brxe-block section-component">
                        <div id="brxe-psxmaa" class="brxe-block">
                            <div id="brxe-cguboi" class="brxe-block" data-animi="up" data-duration="0.6">
                                <h2 id="brxe-zkddcs" class="brxe-heading heading-style-display text-color-green">
                                    <span class="h3-span">The Advantages of</span> <?php
                                    $heroHeading = get_field('location_service_name');
                                    if ($heroHeading) {
                                        echo $heroHeading;
                                    }
                                    ?> <span class="h3-span">for <?php echo get_field('location_name', $related_locations[0]->ID); ?> Homeowners</span>
                                </h2>
                                <div id="brxe-ezbhqi" class="brxe-div image-wrapper">
                                    <img width="164" height="54"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                        class="brxe-image image-contain css-filter size-full" alt="" id="brxe-actbwp"
                                        decoding="async" loading="lazy" data-type="string" />
                                </div>
                            </div>
                            <div id="brxe-odoarl" class="brxe-block">
                                <?php if ($benefits): ?>
                                    <?php foreach ($benefits as $benefit): ?>
                                        <?php
                                        $benefit_id = $benefit->ID;
                                        $benefit_title = get_the_title($benefit_id);
                                        $benefit_description = get_field('benefit_description_', $benefit_id);
                                        $benefit_icon = get_field('benefit_icon', $benefit_id);
                                        $benefit_icon_url = is_array($benefit_icon) ? $benefit_icon['url'] : '';
                                        ?>
                                        <div class="brxe-udhxrd brxe-block">
                                            <div class="brxe-sbmueo brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                                <img width="100" height="100" src="<?php echo $benefit_icon_url ?>"
                                                    class="brxe-paanej brxe-image image-contain css-filter size-large" alt=""
                                                    decoding="async" loading="lazy" data-type="string" />
                                            </div>
                                            <div id="brxe-yqmgld"
                                                class="brxe-text-basic heading-style-h5 text-color-green font-weight-bold"
                                                data-animi="up" data-duration="0.6">
                                                <?php echo $benefit_title ?>
                                            </div>
                                            <div id="brxe-qcpmzk" class="brxe-text-basic text-size-regular text-color-mute"
                                                data-animi="up" data-duration="0.6">
                                                <?php echo $benefit_description ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No benefits found for this service.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="brxe-gycuxr" class="brxe-section section">
                <div id="brxe-fxcwgc" class="brxe-container padding-global">
                    <div id="brxe-kbuxro" class="brxe-block section-component">
                        <div id="brxe-cxkqre" class="brxe-div">
                            <div id="brxe-ymngqx" class="brxe-text service-rich-text" data-animi="up" data-duration="0.8">
                                <?php
                                $content = get_the_content();
                                if ($content) {
                                    the_content();
                                }
                                ?>
                            </div>
                            <div class="service-detail-btn-wrapper">
                                <div id="brxe-auhoyj" class="brxe-div btn is-no-icon" data-animi="up" data-duration="0.6"
                                    data-delay="0.3"
                                    data-interactions='[{"id":"tcimtz","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                    data-interaction-id="d87eef">
                                    <div id="brxe-aqyonk" class="brxe-text-basic">
                                        Get a Free Estimate
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="brxe-zjfakb" class="brxe-section section">
                <div id="brxe-mmdskt" class="brxe-container padding-global">
                    <div id="brxe-rxpqmf" class="brxe-block">
                        <div id="brxe-remxif" class="brxe-block section-component">
                            <div id="brxe-lgilie" data-script-id="lgilie" class="brxe-tabs-nested steps-tab-wrapper">
                                <div id="brxe-duiljn" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                    <h3 id="brxe-btbghh" class="brxe-heading heading-style-h3">
                                        What to Expect
                                    </h3>
                                    <div id="brxe-awccpj" class="brxe-block bricks-lazy-hidden">
                                        <h3 id="brxe-jgnchc" class="brxe-heading heading-style-display">
                                            In 5 Easy <span class="display-span">Steps</span>
                                        </h3>
                                    </div>
                                </div>
                                <div id="brxe-abdylb" class="brxe-block tab-content" data-animi="up" data-duration="0.6">
                                    <div id="brxe-gepfhx" class="brxe-block steps-tab-pane tab-pane">
                                        <img width="723" height="719"
                                            src="<?php echo home_url('/wp-content/uploads/2025/02/1M0A2902-copy-scaled-1.webp'); ?>"
                                            class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uuyrjz"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 723px) 100vw, 723px" />
                                    </div>
                                    <div id="brxe-gyudqg" class="brxe-block steps-tab-pane tab-pane">
                                        <img width="723" height="719"
                                            src="<?php echo home_url('/wp-content/uploads/2024/10/2-Koala-Men-1024x576.jpg'); ?>"
                                            class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uuyrjz"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 723px) 100vw, 723px" />
                                    </div>
                                    <div id="brxe-vvayua" class="brxe-block steps-tab-pane tab-pane">
                                        <img width="723" height="719"
                                            src="<?php echo home_url('/wp-content/uploads/2024/10/Smiling-At-Brochure-scaled.jpg'); ?>"
                                            class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uuyrjz"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 723px) 100vw, 723px" />
                                    </div>
                                    <div id="brxe-yzcbvu" class="brxe-block steps-tab-pane tab-pane">
                                        <img width="723" height="719"
                                            src="<?php echo home_url('/wp-content/uploads/2024/10/IMG_0012-748x1024.jpg'); ?>"
                                            class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uuyrjz"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 723px) 100vw, 723px" />
                                    </div>
                                    <div id="brxe-crzvac" class="brxe-block steps-tab-pane tab-pane">
                                        <img width="723" height="719"
                                            src="<?php echo home_url('/wp-content/uploads/2024/10/BoostYourHomesValue.jpg'); ?>"
                                            class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uuyrjz"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 723px) 100vw, 723px" />
                                    </div>
                                </div>
                                <div id="brxe-ermljr" class="brxe-block steps-tab-menu tab-menu">
                                    <div id="brxe-xlzagw" class="brxe-block" data-animi="up" data-duration="0.6">
                                        <h3 id="brxe-rrjhag" class="brxe-heading heading-style-h3">
                                            What to Expect
                                        </h3>
                                        <div id="brxe-radvov" class="brxe-block">
                                            <h3 id="brxe-hvxpxt" class="brxe-heading heading-style-display">
                                                In 5 Easy <span class="display-span">Steps</span>
                                            </h3>
                                        </div>
                                    </div>
                                    <div id="brxe-rzuwab" class="brxe-block tab-menu-list" data-animi="up" data-duration="0.6"
                                        data-delay="0.2">
                                        <div id="brxe-ugbtxl" class="brxe-div steps-tab-title tab-title brx-open">
                                            <div id="brxe-bxkqbk" class="brxe-block step-tab-head active">
                                                <div id="brxe-bapnjj" class="brxe-text-basic accordian-title">
                                                    Schedule Your Free Evaluation
                                                </div>
                                            </div>
                                            <div id="brxe-znxyzr" class="brxe-block step-tab-content">
                                                <div id="brxe-lsgmxx" class="brxe-block">
                                                    <div id="brxe-qndiqv" class="brxe-text accordian-text">
                                                        <p>
                                                            Contact your nearest Koala Insulation Location, recognized as the #1
                                                            trusted insulation expert nationwide. We will discuss your needs and
                                                            schedule an appointment to provide a free evaluation.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-wyezkv" class="brxe-div steps-tab-title tab-title">
                                            <div id="brxe-yujemp" class="brxe-block step-tab-head">
                                                <div id="brxe-ieidyn" class="brxe-text-basic accordian-title">
                                                    Home Efficiency Assessment Estimate
                                                </div>
                                            </div>
                                            <div id="brxe-uofwvw" class="brxe-block step-tab-content">
                                                <div id="brxe-jdfkmg" class="brxe-block">
                                                    <div id="brxe-gjtvyt" class="brxe-text accordian-text">
                                                        <p>
                                                            One of our friendly insulation experts will perform a thorough home
                                                            efficiency assessment. Your insulation expert will then review the
                                                            assessment findings, the science behind these findings, and why any
                                                            changes are needed. Lastly, your insulation expert will provide the
                                                            best option that fits your home efficiency needs and your budget. 
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-ikuhyq" class="brxe-div steps-tab-title tab-title">
                                            <div id="brxe-lezkfj" class="brxe-block step-tab-head">
                                                <div id="brxe-muusye" class="brxe-text-basic accordian-title">
                                                    Schedule Your Insulation Service
                                                </div>
                                            </div>
                                            <div id="brxe-fxbgkh" class="brxe-block step-tab-content">
                                                <div id="brxe-ajqcuy" class="brxe-block">
                                                    <div id="brxe-tkhzzx" class="brxe-text accordian-text">
                                                        <p>
                                                            We will schedule your insulation service for the soonest
                                                            availability that fits your schedule because we understand that your
                                                            home insulation is a priority! 
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-utlftp" class="brxe-div steps-tab-title tab-title">
                                            <div id="brxe-awmkyg" class="brxe-block step-tab-head">
                                                <div id="brxe-nlcdtd" class="brxe-text-basic accordian-title">
                                                    Installation
                                                </div>
                                            </div>
                                            <div id="brxe-dijucr" class="brxe-block step-tab-content">
                                                <div id="brxe-tdhhtu" class="brxe-block">
                                                    <div id="brxe-xrojnh" class="brxe-text accordian-text">
                                                        <p>
                                                            Our team of highly skilled insulation technicians will
                                                            install your premium insulation solution in as little as two hours.
                                                            We treat your home like our own and take time to leave it cleaner
                                                            than it was before.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="brxe-tjyzux" class="brxe-div steps-tab-title tab-title">
                                            <div id="brxe-lshjou" class="brxe-block step-tab-head">
                                                <div id="brxe-esabef" class="brxe-text-basic accordian-title">
                                                    Enjoy Your Comfortable Efficient Home
                                                </div>
                                            </div>
                                            <div id="brxe-ibkiel" class="brxe-block step-tab-content">
                                                <div id="brxe-rsgalb" class="brxe-block">
                                                    <div id="brxe-ytddbm" class="brxe-text accordian-text">
                                                        <p>
                                                            Enjoy a more efficient home with lower energy bills and improved
                                                            comfort year-round. Enjoy your improved home stress free knowing
                                                            that Koala Insulation stands behind their work! Reach out to your
                                                            Koala Insulation team anytime for support. 
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="brxe-xkihub" class="brxe-section section">
                <div id="brxe-fixpyi" class="brxe-container padding-global">
                    <div id="brxe-fytmdo" class="brxe-block container-medium">
                        <div id="brxe-psboun" class="brxe-block section-component">
                            <div id="brxe-rntytu" class="brxe-block" data-animi="up" data-delay="0.1" data-duration="0.6">
                                <div id="brxe-edpedg" class="brxe-block">
                                    <h2 id="brxe-kvlfal"
                                        class="brxe-heading heading-style-h2 font-weight-bold text-allcaps is-green"
                                        data-animi="up" data-duration="0.6">
                                        Your Satisfaction is Our Top Priority
                                    </h2>
                                    <div id="brxe-ptszvq" class="testimonials-subtext brxe-text-basic" data-animi="up"
                                        data-duration="0.6">
                                        And don’t just take our word for it—hear from our happy clients
                                        about their experience with our professional insulation
                                        services.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="brxe-jkwxoy" class="brxe-div">
                        <?php
                        if ($nicejobId && $nicejobId !== "") {
                            echo '<div class="nj-badge">';
                        } else if ($nicejobId == "" && $gr_shortCode == "") {
                            echo '<div class="nj-badge" data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392"></div>';
                        } else {
                            echo '';
                        }
                        ?>
                    </div>
                    <div id="brxe-bagvju" class="brxe-div nicejob-embed-constraints">
                        <?php
                        if ($nicejobId && $nicejobId !== "") {
                            echo '<div class="nj-stories" data-branding="bottom"></div>';
                        } else if ($gr_shortCode && $gr_shortCode !== "") {
                            echo do_shortcode($gr_shortCode);
                        } else {
                            echo '<div class="nj-stories" data-branding="bottom" data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392"></div>';
                        }
                        ?>
                    </div>
                </div>
            </section>
            <?php
            if ($gallery && $is_air_sealing === false) {
                ?>
                <section id="brxe-vbjzxc" class="brxe-section section">
                    <div id="brxe-rowojv" class="brxe-container padding-global">
                        <div id="brxe-ehhfso" class="brxe-block padding-section-medium">
                            <div id="brxe-rhbubw" class="brxe-block section-component">
                                <div id="brxe-bdtfzu" class="brxe-block slider-photo-wrapper" data-animi="up" data-delay="0.1"
                                    data-duration="0.6">
                                    <div id="brxe-acbeii" class="brxe-div">
                                        <div id="brxe-iahnmp" class="brxe-div swiper is-slider-main-swiper">
                                            <div id="brxe-ggxsqx" class="brxe-div swiper-wrapper is-slider-main-wrapper">
                                                <?php foreach ($gallery as $gallery_item): ?>
                                                    <?php
                                                    $gallery_item_id = $gallery_item->ID;
                                                    $gallery_item_image = get_field('service_image', $gallery_item_id);
                                                    $gallery_item_image_url = is_array($gallery_item_image) ? $gallery_item_image['url'] : '';
                                                    ?>
                                                    <div class="brxe-voxtpg brxe-div swiper-slide is-slider-main-slide">
                                                        <div class="brxe-kooibg brxe-block">
                                                            <div class="brxe-rpcfvz brxe-block">
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
                                    <div id="brxe-mwmsnp" class="brxe-block">
                                        <div id="brxe-iesjjr" class="brxe-div" data-animi="up" data-duration="0.6">
                                            <div id="brxe-dbvrct" class="brxe-div swiper-prev" tabindex="0" role="button"
                                                aria-label="Previous slide" aria-controls="brxe-ggxsqx">
                                                <svg class="brxe-svg" id="brxe-hseqhn" xmlns="http://www.w3.org/2000/svg" width="24"
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
                                            <div id="brxe-hghvfh" class="brxe-div swiper-next" tabindex="0" role="button"
                                                aria-label="Next slide" aria-controls="brxe-ggxsqx">
                                                <svg class="brxe-svg" id="brxe-kvihnc" xmlns="http://www.w3.org/2000/svg" width="24"
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
                <?php
            }
            ?>
            <section id="brxe-uwetfq" class="brxe-section section">
                <div id="brxe-ytkgqn" class="brxe-container padding-global">
                    <div id="brxe-ygmlrg" class="brxe-block section-component">
                        <div id="brxe-bcxasi" class="brxe-block" data-animi="up" data-duration="0.6">
                            <h3 id="brxe-tdbzfb" class="brxe-heading heading-style-h3 text-allcaps">
                                Additional <span class="heading-style-display-span">Services</span>
                            </h3>
                            <div id="brxe-xhbmab" class="brxe-div image-wrapper">
                                <img width="164" height="54"
                                    src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-redtsh"
                                    decoding="async" loading="lazy" data-type="string" />
                            </div>
                        </div>
                        <div id="brxe-mtkdbi" class="brxe-block brx-grid">
                            <?php foreach ($other_services as $service): ?>
                                <?php
                                $service_id = $service->ID;
                                $service_title = get_field('location_service_name', $service_id);
                                $service_short_description = get_field('location_service_short_description', $service_id);
                                $service_image = get_field('location_services_detail_image', $service_id);
                                $service_link = get_permalink($service_id);
                                // If image is missing AND title contains "Blown In Attic Insulation" - complex if statement: work around of adding 1000 missing images to the post
                                if (
                                    (empty($service_image)) &&
                                    (!empty($service_title) && str_contains($service_title, 'Blown In Attic Insulation'))
                                  ) {
                                  // updates the image string
                                  $service_image = "https://koalainsulation.com/wp-content/uploads/2025/03/blowin2.png";
                                  $image_id = 49077; // Attachment ID from Media Library
                                  // update the post to the new image
                                  update_field('location_service_image', $image_id, $service_id);
                                  update_field('location_services_detail_image', $image_id, $service_id);
                                }
                                ?>
                                <a href="<?php echo $service_link ?>" aria-current="page" class="brxe-nsxiyn brxe-block"
                                    data-animi="up" data-duration="0.6" data-delay="0.2">
                                    <div class="brxe-hkyetg brxe-block">
                                        <div class="brxe-fqwrxt brxe-block">
                                            <img width="1005" height="1024" src="<?php echo $service_image; ?>"
                                                class="brxe-image image-cover css-filter size-large"
                                                alt="<?php echo $service_title ?>" id="brxe-zkapmc" decoding="async" loading="lazy"
                                                data-type="string" sizes="(max-width: 1005px) 100vw, 1005px" />
                                        </div>
                                        <div class="brxe-aaqvpm brxe-text-basic heading-style-h4">
                                            <?php echo $service_title ?>
                                        </div>
                                        <div class="brxe-fhuwhw brxe-text-basic text-size-regular text-color-mute">
                                            <?php echo $service_short_description ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
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
                                    Whether it's spray foam insulation, blown in insulation, or
                                    anything in between, we're here to help.
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
        </main>
        <?php
    endwhile;
else:
    render_fallback_content();
endif;

// // Get WordPress footer
get_footer();

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{
    ?>
    <h1>No Data</h1>
    <?php
}
?>