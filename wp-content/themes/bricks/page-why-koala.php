<?php
/* Template Name: Why Koala */

// Get the current URL path to extract the location slug
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_path = trim($current_path, '/'); // Remove leading/trailing slashes
$path_segments = explode('/', $current_path);

// The location slug should be the first segment before 'why-koala'
$location_slug = '';
if (count($path_segments) >= 2 && $path_segments[count($path_segments) - 1] === 'why-koala') {
    $location_slug = $path_segments[count($path_segments) - 2];
}

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location');

if ($location) {
    $location_id = $location->ID;

    // Query for "Why Koala" posts related to the current location
    $args = array(
        'post_type' => 'location-why-koala',
        'posts_per_page' => 1, // Just need one for meta
        'meta_query' => array(
            array(
                'key' => 'wk_related_location',
                'value' => $location_id,
                'compare' => 'LIKE',
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

    $niceJobId = get_field('location_nicejob_id', $location_id);
    $gr_shortCode = get_field('google_review_shortcode', $location_id);

    $query = new WP_Query(array(
        'post_type' => 'location-why-koala',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'wk_related_location',
                'value' => $location_id,
                'compare' => 'LIKE',
            ),
        ),
    ));

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();

            $related_services = get_field('wk_related_services');
            ?>
            <main id="brx-content">
                <section id="brxe-ldroab" class="brxe-section section">
                    <div id="brxe-iygybc" class="brxe-container padding-global">
                        <div id="brxe-stmvll" class="brxe-block section-component">
                            <div id="brxe-khgurm" class="brxe-block brx-grid hero-block-grid">
                                <div id="brxe-xidflz" class="brxe-block hero_content-wrapper" data-animation="up"
                                    data-duration="0.6">
                                    <div id="brxe-yxvpaf" class="brxe-block">
                                        <h1 id="brxe-tpieur" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                            data-animi="up" data-duration="0.6" data-delay="0.2">
                                            <?php the_title(); ?>
                                        </h1>
                                        <div id="brxe-shuocb" class="brxe-text-basic text-size-regular text-color-mute"
                                            data-animi="up" data-duration="0.6" data-delay="0.3">
                                            <?php
                                            $heroContent = get_field('wk_hero_description');
                                            if ($heroContent) {
                                                echo $heroContent;
                                            }
                                            ?>
                                        </div>
                                        <div id="national_estimate-btn" class="brxe-div btn is-no-icon" data-animi="up"
                                            data-delay="0.4" data-duration="0.6"
                                            data-interactions='[{"id":"arkdpe","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                            data-interaction-id="be5594">
                                            <div id="brxe-orhuzx" class="brxe-text-basic">
                                                Get a Free Estimate
                                            </div>
                                        </div>
                                        <div id="get-estimate-btn1" class="brxe-div btn is-no-icon bricks-lazy-hidden"
                                            data-animi="up" data-delay="0.4" data-duration="0.6">
                                            <div id="brxe-kggwlx" class="brxe-text-basic">
                                                Get a Free Estimate
                                            </div>
                                        </div>
                                    </div>
                                    <div id="brxe-bzmkie" class="brxe-div image-wrapper absolute">
                                        <img width="572" height="214"
                                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-dlvokk"
                                            decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                                    </div>
                                </div>
                                <div id="brxe-oqfivt" class="brxe-block hero_image-wrapper" data-animi="up" data-duration="0.6">
                                    <?php
                                    $heroImage = get_field('wk_hero_image');
                                    if ($heroImage) {
                                        ?>
                                        <img width="1005" height="1024" src="<?php echo esc_url($heroImage); ?>"
                                            class="brxe-image image-cover css-filter size-large" alt="Why Koala Hero Image"
                                            loading="eager" decoding="async" />
                                        <?php
                                    } else {
                                        ?>
                                        <img width="1005" height="1024"
                                            src="<?php echo home_url('/wp-content/uploads/2025/01/Handshake-scaled.jpg'); ?>"
                                            class="brxe-image image-cover css-filter size-large" alt="Why Koala Hero Image"
                                            id="brxe-ozcdgq" loading="eager" decoding="async" />
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-rviihv" class="brxe-section section">
                    <div id="brxe-syipwj" class="brxe-container padding-global">
                        <div id="brxe-byyqth" class="brxe-block section-component">
                            <div id="brxe-udeejo" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                <h2 id="brxe-naconx" class="brxe-heading heading-style-h3 is-all-caps">
                                    Only Solid Results With<br /><span class="heading-style-display-span">Koala Insulation</span>
                                </h2>
                                <div id="brxe-ramium" class="brxe-block bricks-lazy-hidden">
                                    <div id="brxe-ryzcil" class="brxe-div bricks-lazy-hidden">
                                        <img width="164" height="54"
                                            src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20164%2054'%3E%3C/svg%3E"
                                            class="brxe-image image-contain css-filter size-full bricks-lazy-hidden" alt=""
                                            id="brxe-bvojzt" decoding="async" loading="lazy"
                                            data-src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                            data-type="string" />
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-jrkyib" class="brxe-block brx-grid">
                                <div id="brxe-qccdup" class="brxe-block" data-animi="up" data-duration="0.6">
                                    <img width="1092" height="1436"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uakmgm" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 1092px) 100vw, 1092px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131.jpg'); ?>          1092w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-228x300.jpg'); ?>   228w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-779x1024.jpg'); ?>  779w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-768x1010.jpg'); ?>  768w
              " />
                                </div>
                                <div id="brxe-temrzp" class="brxe-block">
                                    <div id="brxe-pyrnnq" class="brxe-block" data-animi="up" data-duration="0.6">
                                        <h2 id="brxe-qnguvg" class="brxe-heading heading-style-h3 is-all-caps">
                                            Only Solid Results With<br /><span class="heading-style-display-span">Koala
                                                Insulation</span>
                                        </h2>
                                        <div id="brxe-ancqmj" class="brxe-block">
                                            <div id="brxe-ukjefa" class="brxe-div">
                                                <img width="164" height="54"
                                                    src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-tuueme"
                                                    decoding="async" loading="lazy" data-type="string" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="brxe-ytmyuv" class="brxe-text text-size-regular">
                                        <p>
                                            When you choose Koala Insulation, you’re choosing proven quality
                                            and results. Here’s how we deliver on our promises:
                                        </p>
                                    </div>
                                    <div class="brxe-div acc-container3">
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/122.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">
                                                        Exceptional Customer Service
                                                    </div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        We prioritize our customers with a focus on providing excellent
                                                        support and personalized service for every project. With over 13,000
                                                        positive reviews, we are dedicated to ensuring a customer-friendly,
                                                        stress-free and cost efficient experience for every customer.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Top Industry Expertise</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        With over 67,000 insulation jobs completed nationwide, we bring a
                                                        wealth of experience and skill. Our team uses the latest techniques to
                                                        ensure effective and reliable custom solutions to fit your needs.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/tiempo-rapido.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">
                                                        Quick and Efficient Service
                                                    </div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        We value your time and comfort, offering efficient services while
                                                        treating your home with care. We promise to leave your space in better
                                                        condition than we found it.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/w1.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Local Commitment</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        As a locally-owned business, we’re dedicated to serving our community
                                                        with top-quality services and a friendly, personal touch.
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
                <section id="brxe-jfodeq" class="brxe-section section">
                    <div id="brxe-xtitkc" class="brxe-container padding-global">
                        <div id="brxe-hyaoqb" class="brxe-block container-medium">
                            <div id="brxe-uiiuve" class="brxe-block section-component">
                                <div id="brxe-zfpkbx" class="brxe-block" data-animi="up" data-delay="0.1">
                                    <div id="brxe-zswths" class="brxe-block">
                                        <h2 id="brxe-oeolvl"
                                            class="brxe-heading heading-style-h2 font-weight-bold text-allcaps is-green"
                                            data-animi="up" data-duration="0.6">
                                            Your Satisfaction is Our Top Priority
                                        </h2>
                                        <div id="brxe-tmynax" class="testimonials-subtext brxe-text-basic" data-animi="up"
                                            data-duration="0.6">
                                            And don’t just take our word for it—hear from our happy clients
                                            about their experience with our professional insulation
                                            services.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="brxe-dchfxf" class="brxe-div nicejob-embed-constraints">
                            <?php
                            if ($niceJobId && $niceJobId !== "") {
                                echo '<div class="nj-stories" data-branding="bottom"></div>';
                            } else if ($gr_shortCode && $gr_shortCode !== "") {
                                echo do_shortcode($gr_shortCode);
                            } else {
                                echo '<div class="nj-stories" data-branding="bottom" data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392"></div>';
                            }
                            ?>
                        </div>
                </section>
                <section id="brxe-vrfjsw" class="brxe-section section">
                    <div id="brxe-fwftyf" class="brxe-container padding-global">
                        <div id="brxe-dupmaz" class="brxe-block section-component">
                            <div id="brxe-bhacmo" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                                <h3 id="brxe-ylrtmn" class="brxe-heading heading-style-h3 is-all-caps">
                                    Why<br /><span class="heading-style-display-span">Insulation?</span>
                                </h3>
                                <div id="brxe-mlofjl" class="brxe-block bricks-lazy-hidden">
                                    <div id="brxe-nbakcl" class="brxe-div bricks-lazy-hidden">
                                        <img width="164" height="54"
                                            src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20164%2054'%3E%3C/svg%3E"
                                            class="brxe-image image-contain css-filter size-full bricks-lazy-hidden" alt=""
                                            id="brxe-vpgusd" decoding="async" loading="lazy"
                                            data-src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                            data-type="string" />
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-qgrslx" class="brxe-block brx-grid">
                                <div id="brxe-sxouon" class="brxe-block" data-animi="up" data-duration="0.6">
                                    <img width="1092" height="1436"
                                        src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-11.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-full"
                                        alt="Koala Insulation estimator and a homeowner" id="brxe-qzbebf" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 1092px) 100vw, 1092px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11.jpg'); ?>          1092w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11-228x300.jpg'); ?>   228w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11-779x1024.jpg'); ?>  779w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11-768x1010.jpg'); ?>  768w
              " />
                                </div>
                                <div id="brxe-uulqsp" class="brxe-block">
                                    <div id="brxe-fnqmmt" class="brxe-block" data-animi="up" data-duration="0.6">
                                        <h3 id="brxe-fvmgxx" class="brxe-heading heading-style-h3 is-all-caps">
                                            Why<br /><span class="heading-style-display-span">Insulation?</span>
                                        </h3>
                                        <div id="brxe-uakpgs" class="brxe-block">
                                            <div id="brxe-xhgqau" class="brxe-div">
                                                <img width="164" height="54"
                                                    src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-ljenzk"
                                                    decoding="async" loading="lazy" data-type="string" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="brxe-ublttz" class="brxe-text text-size-regular">
                                        <p>
                                            Insulation does more than just save energy. Here’s why proper
                                            insulation is a smart choice for any home or business:
                                        </p>
                                    </div>
                                    <div class="brxe-div acc-container3">
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/spray-bottle_6453040-1.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Energy Savings</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        Customers often see up to a 30% reduction in energy bills* thanks to
                                                        our efficient insulation solutions like batt insulation and spray foam
                                                        insulation.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/22.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Enhanced Comfort</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        Our clients report a significant improvement in indoor comfort, with
                                                        more consistent temperatures and fewer drafts, especially with air
                                                        sealingand crawl space insulation.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/23.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Increased Property Value</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        Proper insulation not only cuts down on energy costs, but can also
                                                        increase your property’s resale value.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/24.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Long-Term Performance</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        We use high-quality materials and professional installation techniques
                                                        that ensure your insulation performs well for years.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/icons8-tools-100.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">
                                                        Quick and Efficient Service
                                                    </div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        Our streamlined process helps us complete projects efficiently,
                                                        minimizing disruption to your daily life.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brxe-block acc3">
                                            <div class="brxe-block acc-head3 accordion-title-wrapper">
                                                <div class="brxe-block acc3-head-inner">
                                                    <img width="54" height="55"
                                                        src="<?php echo home_url('/wp-content/uploads/2024/09/34.png'); ?>"
                                                        class="brxe-image css-filter size-full acc-head-image" alt=""
                                                        decoding="async" data-type="string" />
                                                    <div class="brxe-text-basic accordian-title">Satisfaction Guarantee</div>
                                                </div>
                                                <div class="brxe-div accordian-icon-wrapper">
                                                    <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                        class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                        data-type="string" />
                                                </div>
                                            </div>
                                            <div class="brxe-block acc-content3 accordion-content-wrapper">
                                                <div class="brxe-text accordian-text">
                                                    <p>
                                                        Most of our clients express high satisfaction with our services,
                                                        thanks to our commitment to delivering quality and care.
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
                <section id="brxe-tniiel" class="brxe-section section">
                    <div id="brxe-vgtnrg" class="brxe-container padding-global">
                        <div id="brxe-ghodiu" class="brxe-block section-component">
                            <div id="brxe-cdptlj" class="brxe-block brx-grid">
                                <div id="brxe-jysegi" class="brxe-block">
                                    <h2 id="brxe-vznqvk" class="brxe-heading heading-style-h2 is-all-caps font-weight-bold"
                                        data-animi="up" data-duration="0.6">
                                        Who we are
                                    </h2>
                                    <div id="brxe-vavixk" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        <?php
                                        $whoWeAreContent = get_field('wk_who_we_are_description');
                                        if ($whoWeAreContent) {
                                            echo $whoWeAreContent;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div id="brxe-jkwqvd" class="brxe-block video-link" data-animi="up" data-duration="0.6">
                                    <div id="brxe-hnxtto" data-script-id="hnxtto" class="brxe-video image-cover">
                                        <div allowfullscreen="" allow="autoplay"
                                            data-iframe-src="https://www.youtube.com/embed/CsvHGsgMjkQ?wmode=opaque&amp;rel=0&amp;enablejsapi=1"
                                            class="bricks-video-preview-image" style="
                  background-image: url(<?php echo home_url('/wp-content/uploads/2024/09/Frame-11-779x1024.jpg);'); ?>
                "></div>
                                        <div class="bricks-video-overlay"></div>
                                        <svg class="bricks-video-overlay-icon" xmlns="http://www.w3.org/2000/svg" width="118"
                                            height="119" viewBox="0 0 118 119" fill="none">
                                            <path
                                                d="M0 59.2013C0 26.6165 26.4152 0.201294 59 0.201294V0.201294C91.5848 0.201294 118 26.6165 118 59.2013V59.2013C118 91.7861 91.5848 118.201 59 118.201V118.201C26.4152 118.201 0 91.7861 0 59.2013V59.2013Z"
                                                fill="#95C93D"></path>
                                            <path
                                                d="M49.3202 41.3027C46.8231 39.7284 43.5703 41.523 43.5703 44.4749V73.9278C43.5703 76.8797 46.8231 78.6743 49.3202 77.1L72.6794 62.3736C75.0128 60.9024 75.0128 57.5002 72.6794 56.0291L49.3202 41.3027Z"
                                                fill="white"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-hbxtsm" class="brxe-section section">
                    <div id="brxe-dpljwj" class="brxe-block padding-section-medium">
                        <div id="brxe-qqrkha" class="brxe-container padding-global">
                            <div id="brxe-ubvxne" class="brxe-block section-component services-slider-main-component">
                                <div id="brxe-froqnu" class="brxe-block" data-animi="up" data-delay="0.1" data-duration="0.6">
                                    <div id="brxe-kzcdoy" class="brxe-block">
                                        <div id="brxe-omtxyw" class="brxe-block">
                                            <h2 id="brxe-npeswr" class="brxe-heading heading-style-h3">Our <span
                                                    class="heading-style-display-span">Services</span></h2>
                                            <div id="brxe-bqxvdl" class="brxe-block"><svg class="brxe-svg" id="brxe-tpwumw"
                                                    xmlns="http://www.w3.org/2000/svg" width="110" height="37" viewBox="0 0 110 37"
                                                    fill="none">
                                                    <path
                                                        d="M24.9561 20.0745C24.9561 20.0745 30.7303 18.1032 35.9878 13.5647C37.3632 12.3766 42.9989 9.27714 44.6454 8.54428C55.5866 3.66978 84.0276 -4.32755 109.323 4.16835C87.9251 3.03064 65.566 18.7212 65.566 18.7212C65.566 18.7212 44.6015 35.6227 31.2094 36.016C17.8173 36.4093 17.352 19.1593 0.551523 28.0689C9.28863 22.9289 20.0763 23.288 29.6478 22.3412C38.2614 21.4893 49.289 18.4872 54.5768 16.5638C60.0587 14.5716 64.6965 12.9029 64.6965 12.9029C67.9679 11.4888 73.8867 8.66878 73.8867 8.66878C73.8867 8.66878 48.8075 19.1844 24.9531 20.0757L24.9561 20.0745Z"
                                                        fill="url(#paint0_linear_6436_277)"></path>
                                                    <defs>
                                                        <linearGradient id="paint0_linear_6436_277" x1="105.758" y1="-4.57508"
                                                            x2="4.13631" y2="36.8572" gradientUnits="userSpaceOnUse">
                                                            <stop offset="0.36" stop-color="#95C93D"></stop>
                                                            <stop offset="1" stop-color="#73AADC"></stop>
                                                        </linearGradient>
                                                    </defs>
                                                </svg></div>
                                        </div>
                                        <div id="brxe-hgfybn" class="brxe-text-basic text-size-regular">Learn about all of the
                                            services we
                                            provide.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="brxe-xagxqc" class="brxe-block services-slider-main-component">
                            <div id="brxe-ymqomd" class="brxe-container padding-global">
                                <div id="brxe-vzjbuw" class="brxe-div" data-animi="up" data-delay="0.2" data-duration="0.6">
                                    <div id="brxe-uacxje" class="brxe-div slider-main_inner-wrapper">
                                        <div id="brxe-zwfzyo" class="brxe-div swiper">
                                            <div id="brxe-zxqbto" class="brxe-div swiper-wrapper">
                                                <?php if ($related_services): ?>
                                                    <?php foreach ($related_services as $service): ?>
                                                        <?php
                                                        $service_id = $service->ID;
                                                        $service_title = get_field('location_service_name', $service_id);
                                                        $service_description = get_field('location_service_short_description', $service_id);
                                                        $service_link = get_permalink($service_id);
                                                        $service_image = wp_get_attachment_image_url(get_field('location_service_image', $service_id), 'full');
                                                        //                             $service_image_url = is_array($service_image) ? $service_image['url'] : '';
                                                        ?>
                                                        <a href="<?php echo $service_link; ?>" class="brxe-whnuim brxe-block swiper-slide">
                                                            <div class="brxe-xkofdq brxe-block our-service-item-wrapper">
                                                                <div class="brxe-tdwsjw brxe-block">
                                                                    <div class="brxe-qvgcwa brxe-text-basic heading-style-h4">
                                                                        <?php echo $service_title; ?>
                                                                    </div>
                                                                    <div class="brxe-qvzryk brxe-text-basic text-size-regular">
                                                                        <?php echo $service_description; ?>
                                                                    </div>
                                                                    <div class="brxe-qxrocz brxe-div btn-secondary is-service-slider">
                                                                        <div class="brxe-nsxtyd brxe-text-basic">Learn More</div>
                                                                    </div>
                                                                </div>
                                                                <?php if ($service_image): ?>
                                                                    <div class="brxe-fjoqym brxe-block our-service-item-img-wrapper">
                                                                        <img src="<?php echo $service_image; ?>"
                                                                            alt="<?php echo $service_title; ?>"
                                                                            class="brxe-rjuylc brxe-image image-cover">
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </a>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p>No services found for this location.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-bstlex" class="brxe-div">
                                <div id="brxe-zzqszs" class="brxe-div swiper-prev bricks-lazy-hidden" tabindex="0" role="button"
                                    aria-label="Previous slide" aria-controls="brxe-zxqbto">
                                    <svg class="brxe-svg" id="brxe-mgqilz" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <mask id="mask0_6621_163" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                            width="24" height="24">
                                            <rect width="24" height="24" transform="matrix(-1 0 0 1 24 0)" fill="#D9D9D9"></rect>
                                        </mask>
                                        <g mask="url(#mask0_6621_163)">
                                            <path d="M7.825 13H20V11H7.825L13.425 5.4L12 4L4 12L12 20L13.425 18.6L7.825 13Z"
                                                fill="#043968"></path>
                                        </g>
                                    </svg>
                                </div>
                                <div id="brxe-fybita" class="brxe-div swiper-next" tabindex="0" role="button"
                                    aria-label="Next slide" aria-controls="brxe-zxqbto">
                                    <svg class="brxe-svg" id="brxe-chgebf" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none">
                                        <mask id="mask0_6621_238" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                            width="24" height="24">
                                            <rect width="24" height="24" fill="#D9D9D9"></rect>
                                        </mask>
                                        <g mask="url(#mask0_6621_238)">
                                            <path d="M16.175 13H4V11H16.175L10.575 5.4L12 4L20 12L12 20L10.575 18.6L16.175 13Z"
                                                fill="#191919">
                                            </path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                            <div id="brxe-wwfwrd" class="brxe-block"></div>
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

    //Get the main services
    $main_services = get_posts(array(
        'post_type' => 'service',
        'posts_per_page' => -1,
    ));

    $location_why_koala = get_posts(array(
        'post_type' => 'location-why-koala',
        'posts_per_page' => -1,
    ));
    ?>
    <main id="brx-content">
        <section id="brxe-ldroab" class="brxe-section section">
            <div id="brxe-iygybc" class="brxe-container padding-global">
                <div id="brxe-stmvll" class="brxe-block section-component">
                    <div id="brxe-khgurm" class="brxe-block brx-grid hero-block-grid">
                        <div id="brxe-xidflz" class="brxe-block hero_content-wrapper" data-animation="up"
                            data-duration="0.6">
                            <div id="brxe-yxvpaf" class="brxe-block">
                                <h1 id="brxe-tpieur" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6" data-delay="0.2">
                                    Why Koala Insulation
                                </h1>
                                <div id="brxe-shuocb" class="brxe-text-basic text-size-regular text-color-mute"
                                    data-animi="up" data-duration="0.6" data-delay="0.3">
                                    At Koala Insulation, we’re all about making your space more comfortable and efficient.
                                    We believe in
                                    doing things right, with a personal touch that sets us apart. From top-notch products
                                    like spray foam
                                    insulation to friendly service, we’re here to help you achieve the perfect insulation
                                    solution for your
                                    home or business.

                                    Discover why our customers love working with us and how we can make a difference for
                                    you!
                                </div>
                                <div id="national_estimate-btn" class="brxe-div btn is-no-icon" data-animi="up"
                                    data-delay="0.4" data-duration="0.6"
                                    data-interactions='[{"id":"arkdpe","trigger":"click","action":"show","target":"popup","templateId":"4865"}]'
                                    data-interaction-id="be5594">
                                    <div id="brxe-orhuzx" class="brxe-text-basic">
                                        Get a Free Estimate
                                    </div>
                                </div>
                                <div id="get-estimate-btn1" class="brxe-div btn is-no-icon bricks-lazy-hidden"
                                    data-animi="up" data-delay="0.4" data-duration="0.6">
                                    <div id="brxe-kggwlx" class="brxe-text-basic">
                                        Get a Free Estimate
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-bzmkie" class="brxe-div image-wrapper absolute">
                                <img width="572" height="214"
                                    src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                    class="brxe-image image-contain css-filter size-full" alt="" id="brxe-dlvokk"
                                    decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                            </div>
                            <div class="nj-badge"
                                data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392">
                            </div>
                        </div>
                        <div id="brxe-oqfivt" class="brxe-block hero_image-wrapper" data-animi="up" data-duration="0.6">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2025/01/Handshake-scaled.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="Why Koala Hero Image"
                                id="brxe-ozcdgq" loading="eager" decoding="async" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-rviihv" class="brxe-section section">
            <div id="brxe-syipwj" class="brxe-container padding-global">
                <div id="brxe-byyqth" class="brxe-block section-component">
                    <div id="brxe-udeejo" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                        <h3 id="brxe-naconx" class="brxe-heading heading-style-h3 is-all-caps">
                            Only Solid Results With
                        </h3>
                        <div id="brxe-ramium" class="brxe-block bricks-lazy-hidden">
                            <h3 id="brxe-edtuor" class="brxe-heading heading-style-display text-color-green">
                                Koala
                            </h3>
                            <div id="brxe-ryzcil" class="brxe-div bricks-lazy-hidden">
                                <img width="164" height="54"
                                    src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20164%2054'%3E%3C/svg%3E"
                                    class="brxe-image image-contain css-filter size-full bricks-lazy-hidden" alt=""
                                    id="brxe-bvojzt" decoding="async" loading="lazy"
                                    data-src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                    data-type="string" />
                            </div>
                        </div>
                    </div>
                    <div id="brxe-jrkyib" class="brxe-block brx-grid">
                        <div id="brxe-qccdup" class="brxe-block" data-animi="up" data-duration="0.6">
                            <img width="1092" height="1436"
                                src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-131.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-full" alt="" id="brxe-uakmgm" decoding="async"
                                loading="lazy" data-type="string" sizes="(max-width: 1092px) 100vw, 1092px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131.jpg'); ?>          1092w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-228x300.jpg'); ?>   228w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-779x1024.jpg'); ?>  779w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-131-768x1010.jpg'); ?>  768w
              " />
                        </div>
                        <div id="brxe-temrzp" class="brxe-block">
                            <div id="brxe-pyrnnq" class="brxe-block" data-animi="up" data-duration="0.6">
                                <h3 id="brxe-qnguvg" class="brxe-heading heading-style-h3 is-all-caps">
                                    Only Solid Results With
                                </h3>
                                <div id="brxe-ancqmj" class="brxe-block">
                                    <h3 id="brxe-eyhajc" class="brxe-heading heading-style-display text-color-green">
                                        Koala Insulation
                                    </h3>
                                    <div id="brxe-ukjefa" class="brxe-div">
                                        <img width="164" height="54"
                                            src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-tuueme"
                                            decoding="async" loading="lazy" data-type="string" />
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-ytmyuv" class="brxe-text text-size-regular">
                                <p>
                                    When you choose Koala Insulation, you’re choosing proven quality
                                    and results. Here’s how we deliver on our promises:
                                </p>
                            </div>
                            <div class="brxe-div acc-container3">
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/w2.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">
                                                #1 Insulation Company Nationwide
                                            </div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                As the #1 Insulation company nationwide, we pride ourselves on our
                                                exceptional reputation for quality work, our reliable team of licensed
                                                insulation experts, and lifetime-lasting customer service. We have a
                                                proven track record for delivering reliable and cost efficient
                                                insulation solutions that makes us a trusted choice for homeowners and
                                                businesses across the nation.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/122.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">
                                                Exceptional Customer Service
                                            </div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                We prioritize our customers with a focus on providing excellent
                                                support and personalized service for every project. With over 13,000
                                                positive reviews, we are dedicated to ensuring a customer-friendly,
                                                stress-free and cost efficient experience for every customer.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/2.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Top Industry Expertise</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                With over 67,000 insulation jobs completed nationwide, we bring a
                                                wealth of experience and skill. Our team uses the latest techniques to
                                                ensure effective and reliable custom solutions to fit your needs.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/tiempo-rapido.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">
                                                Quick and Efficient Service
                                            </div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                We value your time and comfort, offering efficient services while
                                                treating your home with care. We promise to leave your space in better
                                                condition than we found it.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/w1.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Local Commitment</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                As a locally-owned business, we’re dedicated to serving our community
                                                with top-quality services and a friendly, personal touch.
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
        <section id="brxe-jfodeq" class="brxe-section section">
            <div id="brxe-xtitkc" class="brxe-container padding-global">
                <div id="brxe-hyaoqb" class="brxe-block container-medium">
                    <div id="brxe-uiiuve" class="brxe-block section-component">
                        <div id="brxe-zfpkbx" class="brxe-block" data-animi="up" data-delay="0.1">
                            <div id="brxe-zswths" class="brxe-block">
                                <h2 id="brxe-oeolvl"
                                    class="brxe-heading heading-style-h2 font-weight-bold text-allcaps is-green"
                                    data-animi="up" data-duration="0.6">
                                    Your Satisfaction is Our Top Priority
                                </h2>
                                <div id="brxe-tmynax" class="testimonials-subtext brxe-text-basic" data-animi="up"
                                    data-duration="0.6">
                                    And don’t just take our word for it—hear from our happy clients
                                    about their experience with our professional insulation
                                    services.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nj-badge"
                    data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392">
                </div>
                <div id="brxe-dchfxf" class="brxe-div nicejob-embed-constraints">
                    <div class="nj-stories" data-branding="bottom"
                        data-source="5343641076236288,6069682734366720,6360224282181632,5857166636875776,5464982618112000,6180301227687936,5478677163278336,5376848448454656,6502259928596480,5788360505819136,5552839244382208,6144525232242688,5317999534276608,5771252565803008,5302672838623232,5795954379194368,5863204264083456,5132810422059008,6337240249139200,6112224302596096,5065990066405376,6649367102750720,5805497607520256,6166911908052992,6243193575702528,6043164410642432,4912272438984704,6015551902318592,4796275749027840,5254413281394688,5336099562192896,5516534754050048,6094148991451136,4688286394613760,5560370003968000,5473611873255424,4540512139214848,6197241828605952,5260062348541952,6091363329769472,5723836408922112,5664417027457024,4515013264408576,6359640479105024,5719381782298624,6597935092989952,6548585351479296,5571005320265728,6339767237607424,4542830743912448,5243577428344832,6192713626550272,4690597992988672,5734897471193088,4965425320820736,6300339693682688,5718249231089664,5433561228771328,6197155397632000,6164825363709952,5946693042569216,5679918312849408,4983666626789376,5649313048297472,4765327585443840,6019794881216512,5708212221509632,4764484473454592,5118694767460352,4809809653399552,4822080161054720,6528393355198464,4508743762182144,4944769023737856,6619382869655552,5306963775979520,5517095434190848,5909384913485824,6459478990651392">
                    </div>
                </div>
        </section>
        <section id="brxe-vrfjsw" class="brxe-section section">
            <div id="brxe-fwftyf" class="brxe-container padding-global">
                <div id="brxe-dupmaz" class="brxe-block section-component">
                    <div id="brxe-bhacmo" class="brxe-block bricks-lazy-hidden" data-animi="up" data-duration="0.6">
                        <h3 id="brxe-ylrtmn" class="brxe-heading heading-style-h3 is-all-caps">
                            Why
                        </h3>
                        <div id="brxe-mlofjl" class="brxe-block bricks-lazy-hidden">
                            <h3 id="brxe-eypcmb" class="brxe-heading heading-style-display text-color-green">
                                Insulation?
                            </h3>
                            <div id="brxe-nbakcl" class="brxe-div bricks-lazy-hidden">
                                <img width="164" height="54"
                                    src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20164%2054'%3E%3C/svg%3E"
                                    class="brxe-image image-contain css-filter size-full bricks-lazy-hidden" alt=""
                                    id="brxe-vpgusd" decoding="async" loading="lazy"
                                    data-src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                    data-type="string" />
                            </div>
                        </div>
                    </div>
                    <div id="brxe-qgrslx" class="brxe-block brx-grid">
                        <div id="brxe-sxouon" class="brxe-block" data-animi="up" data-duration="0.6">
                            <img width="1092" height="1436"
                                src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-11.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-full"
                                alt="Koala Insulation estimator and a homeowner" id="brxe-qzbebf" decoding="async"
                                loading="lazy" data-type="string" sizes="(max-width: 1092px) 100vw, 1092px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11.jpg'); ?>          1092w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11-228x300.jpg'); ?>   228w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11-779x1024.jpg'); ?>  779w,
                <?php echo home_url('/wp-content/uploads/2024/09/Frame-11-768x1010.jpg'); ?>  768w
              " />
                        </div>
                        <div id="brxe-uulqsp" class="brxe-block">
                            <div id="brxe-fnqmmt" class="brxe-block" data-animi="up" data-duration="0.6">
                                <h3 id="brxe-fvmgxx" class="brxe-heading heading-style-h3 is-all-caps">
                                    Why
                                </h3>
                                <div id="brxe-uakpgs" class="brxe-block">
                                    <h3 id="brxe-vfxiuq" class="brxe-heading heading-style-display text-color-green">
                                        Insulation?
                                    </h3>
                                    <div id="brxe-xhgqau" class="brxe-div">
                                        <img width="164" height="54"
                                            src="<?php echo home_url('/wp-content/uploads/2024/09/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-ljenzk"
                                            decoding="async" loading="lazy" data-type="string" />
                                    </div>
                                </div>
                            </div>
                            <div id="brxe-ublttz" class="brxe-text text-size-regular">
                                <p>
                                    Insulation does more than just save energy. Here’s why proper
                                    insulation is a smart choice for any home or business:
                                </p>
                            </div>
                            <div class="brxe-div acc-container3">
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/spray-bottle_6453040-1.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Energy Savings</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                Customers often see up to a 30% reduction in energy bills* thanks to
                                                our efficient insulation solutions like batt insulation and spray foam
                                                insulation.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/22.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Enhanced Comfort</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                Our clients report a significant improvement in indoor comfort, with
                                                more consistent temperatures and fewer drafts, especially with air
                                                sealingand crawl space insulation.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/23.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Increased Property Value</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                Proper insulation not only cuts down on energy costs, but can also
                                                increase your property’s resale value.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/24.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Long-Term Performance</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                We use high-quality materials and professional installation techniques
                                                that ensure your insulation performs well for years.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/icons8-tools-100.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">
                                                Quick and Efficient Service
                                            </div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                Our streamlined process helps us complete projects efficiently,
                                                minimizing disruption to your daily life.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="brxe-block acc3">
                                    <div class="brxe-block acc-head3 accordion-title-wrapper">
                                        <div class="brxe-block acc3-head-inner">
                                            <img width="54" height="55"
                                                src="<?php echo home_url('/wp-content/uploads/2024/09/34.png'); ?>"
                                                class="brxe-image css-filter size-full acc-head-image" alt=""
                                                decoding="async" data-type="string" />
                                            <div class="brxe-text-basic accordian-title">Satisfaction Guarantee</div>
                                        </div>
                                        <div class="brxe-div accordian-icon-wrapper">
                                            <img src="<?php echo home_url('/wp-content/uploads/2024/09/Frame-1.svg'); ?>"
                                                class="brxe-image icon-small css-filter size-full" alt="" decoding="async"
                                                data-type="string" />
                                        </div>
                                    </div>
                                    <div class="brxe-block acc-content3 accordion-content-wrapper">
                                        <div class="brxe-text accordian-text">
                                            <p>
                                                Most of our clients express high satisfaction with our services,
                                                thanks to our commitment to delivering quality and care.
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
        <section id="brxe-tniiel" class="brxe-section section">
            <div id="brxe-vgtnrg" class="brxe-container padding-global">
                <div id="brxe-ghodiu" class="brxe-block section-component">
                    <div id="brxe-cdptlj" class="brxe-block brx-grid">
                        <div id="brxe-jysegi" class="brxe-block">
                            <h2 id="brxe-vznqvk" class="brxe-heading heading-style-h2 is-all-caps font-weight-bold"
                                data-animi="up" data-duration="0.6">
                                Who we are
                            </h2>
                            <div id="brxe-vavixk" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6">
                                At Koala Insulation, we’re all about making your space more comfortable and efficient. We
                                believe in doing
                                things right, with a personal touch that sets us apart. From top-notch products like spray
                                foam insulation
                                to friendly service, we’re here to help you achieve the perfect insulation solution for your
                                home or
                                business. Discover why our customers love working with us and how we can make a difference
                                for you!
                            </div>
                        </div>
                        <div id="brxe-jkwqvd" class="brxe-block video-link" data-animi="up" data-duration="0.6">
                            <div id="brxe-hnxtto" data-script-id="hnxtto" class="brxe-video image-cover">
                                <div allowfullscreen="" allow="autoplay"
                                    data-iframe-src="https://www.youtube.com/embed/CsvHGsgMjkQ?wmode=opaque&amp;rel=0&amp;enablejsapi=1"
                                    class="bricks-video-preview-image" style="
                  background-image: url(<?php echo home_url('/wp-content/uploads/2024/09/Frame-11-779x1024.jpg);'); ?>
                "></div>
                                <div class="bricks-video-overlay"></div>
                                <svg class="bricks-video-overlay-icon" xmlns="http://www.w3.org/2000/svg" width="118"
                                    height="119" viewBox="0 0 118 119" fill="none">
                                    <path
                                        d="M0 59.2013C0 26.6165 26.4152 0.201294 59 0.201294V0.201294C91.5848 0.201294 118 26.6165 118 59.2013V59.2013C118 91.7861 91.5848 118.201 59 118.201V118.201C26.4152 118.201 0 91.7861 0 59.2013V59.2013Z"
                                        fill="#95C93D"></path>
                                    <path
                                        d="M49.3202 41.3027C46.8231 39.7284 43.5703 41.523 43.5703 44.4749V73.9278C43.5703 76.8797 46.8231 78.6743 49.3202 77.1L72.6794 62.3736C75.0128 60.9024 75.0128 57.5002 72.6794 56.0291L49.3202 41.3027Z"
                                        fill="white"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-hbxtsm" class="brxe-section section">
            <div id="brxe-dpljwj" class="brxe-block padding-section-medium">
                <div id="brxe-qqrkha" class="brxe-container padding-global">
                    <div id="brxe-ubvxne" class="brxe-block section-component services-slider-main-component">
                        <div id="brxe-froqnu" class="brxe-block" data-animi="up" data-delay="0.1" data-duration="0.6">
                            <div id="brxe-kzcdoy" class="brxe-block">
                                <div id="brxe-omtxyw" class="brxe-block">
                                    <h3 id="brxe-npeswr" class="brxe-heading heading-style-h3">Our</h3>
                                    <h3 id="brxe-gcfysc" class="brxe-heading heading-style-display">Services</h3>
                                    <div id="brxe-bqxvdl" class="brxe-block"><svg class="brxe-svg" id="brxe-tpwumw"
                                            xmlns="http://www.w3.org/2000/svg" width="110" height="37" viewBox="0 0 110 37"
                                            fill="none">
                                            <path
                                                d="M24.9561 20.0745C24.9561 20.0745 30.7303 18.1032 35.9878 13.5647C37.3632 12.3766 42.9989 9.27714 44.6454 8.54428C55.5866 3.66978 84.0276 -4.32755 109.323 4.16835C87.9251 3.03064 65.566 18.7212 65.566 18.7212C65.566 18.7212 44.6015 35.6227 31.2094 36.016C17.8173 36.4093 17.352 19.1593 0.551523 28.0689C9.28863 22.9289 20.0763 23.288 29.6478 22.3412C38.2614 21.4893 49.289 18.4872 54.5768 16.5638C60.0587 14.5716 64.6965 12.9029 64.6965 12.9029C67.9679 11.4888 73.8867 8.66878 73.8867 8.66878C73.8867 8.66878 48.8075 19.1844 24.9531 20.0757L24.9561 20.0745Z"
                                                fill="url(#paint0_linear_6436_277)"></path>
                                            <defs>
                                                <linearGradient id="paint0_linear_6436_277" x1="105.758" y1="-4.57508"
                                                    x2="4.13631" y2="36.8572" gradientUnits="userSpaceOnUse">
                                                    <stop offset="0.36" stop-color="#95C93D"></stop>
                                                    <stop offset="1" stop-color="#73AADC"></stop>
                                                </linearGradient>
                                            </defs>
                                        </svg></div>
                                </div>
                                <div id="brxe-hgfybn" class="brxe-text-basic text-size-regular">Learn about all of the
                                    services we
                                    provide.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="brxe-xagxqc" class="brxe-block services-slider-main-component">
                    <div id="brxe-ymqomd" class="brxe-container padding-global">
                        <div id="brxe-vzjbuw" class="brxe-div" data-animi="up" data-delay="0.2" data-duration="0.6">
                            <div id="brxe-uacxje" class="brxe-div slider-main_inner-wrapper">
                                <div id="brxe-zwfzyo" class="brxe-div swiper">
                                    <div id="brxe-zxqbto" class="brxe-div swiper-wrapper">
                                        <?php if ($main_services): ?>
                                            <?php foreach ($main_services as $service): ?>
                                                <?php
                                                $service_id = $service->ID;
                                                $service_title = get_the_title($service_id);
                                                $service_description = get_field('short_description_', $service_id);
                                                $service_link = get_permalink($service_id);
                                                $service_image = get_field('service_image', $service_id);
                                                $service_image_url = is_array($service_image) ? $service_image['url'] : '';
                                                ?>
                                                <a href="<?php echo $service_link; ?>" class="brxe-whnuim brxe-block swiper-slide">
                                                    <div class="brxe-xkofdq brxe-block our-service-item-wrapper">
                                                        <div class="brxe-tdwsjw brxe-block">
                                                            <div class="brxe-qvgcwa brxe-text-basic heading-style-h4">
                                                                <?php echo $service_title; ?>
                                                            </div>
                                                            <div class="brxe-qvzryk brxe-text-basic text-size-regular">
                                                                <?php echo $service_description; ?>
                                                            </div>
                                                            <div class="brxe-qxrocz brxe-div btn-secondary is-service-slider">
                                                                <div class="brxe-nsxtyd brxe-text-basic">Learn More</div>
                                                            </div>
                                                        </div>
                                                        <?php if ($service_image): ?>
                                                            <div class="brxe-fjoqym brxe-block our-service-item-img-wrapper">
                                                                <img src="<?php echo $service_image_url; ?>"
                                                                    alt="<?php echo $service_title; ?>"
                                                                    class="brxe-rjuylc brxe-image image-cover">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p>No services found for this location.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="brxe-bstlex" class="brxe-div">
                        <div id="brxe-zzqszs" class="brxe-div swiper-prev bricks-lazy-hidden" tabindex="0" role="button"
                            aria-label="Previous slide" aria-controls="brxe-zxqbto">
                            <svg class="brxe-svg" id="brxe-mgqilz" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <mask id="mask0_6621_163" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                    width="24" height="24">
                                    <rect width="24" height="24" transform="matrix(-1 0 0 1 24 0)" fill="#D9D9D9"></rect>
                                </mask>
                                <g mask="url(#mask0_6621_163)">
                                    <path d="M7.825 13H20V11H7.825L13.425 5.4L12 4L4 12L12 20L13.425 18.6L7.825 13Z"
                                        fill="#043968"></path>
                                </g>
                            </svg>
                        </div>
                        <div id="brxe-fybita" class="brxe-div swiper-next" tabindex="0" role="button"
                            aria-label="Next slide" aria-controls="brxe-zxqbto">
                            <svg class="brxe-svg" id="brxe-chgebf" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <mask id="mask0_6621_238" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                    width="24" height="24">
                                    <rect width="24" height="24" fill="#D9D9D9"></rect>
                                </mask>
                                <g mask="url(#mask0_6621_238)">
                                    <path d="M16.175 13H4V11H16.175L10.575 5.4L12 4L20 12L12 20L10.575 18.6L16.175 13Z"
                                        fill="#191919">
                                    </path>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div id="brxe-wwfwrd" class="brxe-block"></div>
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
            <?php if ($location_why_koala): ?>
                <?php foreach ($location_why_koala as $location_why_koala_item): ?>
                    <?php
                    $location_why_koala_item_id = $location_why_koala_item->ID;
                    $location_why_koala_item_link = get_permalink($location_why_koala_item_id);
                    $location_why_koala_item_name = get_the_title($location_why_koala_item_id);
                    ?>
                    <a href="<?php echo $location_why_koala_item_link ?>"><?php echo $location_why_koala_item_name ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No location why koala posts found.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php
}
?>