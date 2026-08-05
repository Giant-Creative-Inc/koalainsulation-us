<?php
/* Template Name: Homeowner Incentives */

// Get the current URL to extract the location slug
$current_url = $_SERVER['REQUEST_URI'];
$location_slug = str_replace('/homeowner-incentives', '', $current_url); // Remove '/homeowner-incentives' from the URL to get the location slug

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location'); // Replace 'location' with your custom post type slug for location

if ($location) {
    // Get the location ID
    $location_id = $location->ID;
    $location_title = get_the_title( $location->ID );

    // Query for "Homeowner Incentives" posts related to the current location
    $args = array(
        'post_type' => 'location-homeowner-i', // The custom post type slug for "Homeowner Incentives"
        'posts_per_page' => 1, // Just need one for meta
        'meta_query' => array(
            array(
                'key' => 'ho_related_location', // The ACF relationship field key in "Homeowner Incentives"
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
        'post_type' => 'location-homeowner-i',
        'posts_per_page' => -1, 
        'meta_query' => array(
            array(
                'key' => 'ho_related_location',
                'value' => $location_id,
                'compare' => 'LIKE',
            ),
        ),
    ));

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();

            // Get all Homeowner FAQs
            $all_faqs = get_posts(array(
                'post_type' => 'homeowner-incentives',
                'posts_per_page' => -1,
            ));
            ?>
            <main id="brx-content">
                <section id="brxe-yqjdcf" class="brxe-section section">
                    <div id="brxe-wzuukq" class="brxe-container padding-global">
                        <div id="brxe-xkhaib" class="brxe-block section-component">
                            <div id="brxe-avcngh" class="brxe-block brx-grid hero-block-grid">
                                <div id="brxe-rdapaz" class="brxe-block hero_content-wrapper">
                                    <h1 id="brxe-agmygl" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        <?php the_title(); ?>
                                    </h1>
                                    <?php /*
                                    <h2 id="brxe-kbkeen" class="brxe-heading text-size-large is-blue" data-animi="up"
                                        data-duration="0.6">
                                        Save More with Koala Insulation’s Homeowner Incentives
                                    </h2>
                                    */ ?>
                                    <div id="brxe-ibaxcu" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-delay="0.2" data-duration="0.6">
                                        <?php
                                        //$heroContent = get_field('ho_hero_description');
                                        //if ($heroContent) {
                                        //    echo $heroContent;
                                        //}


    $location_name = !empty($location_title)
        ? ' ' . esc_html($location_title) . ''
        : '';

    echo '
    <p>
        At Koala Insulation' . $location_name . ', our goal has always been simple: help homeowners create more comfortable, energy-efficient homes while reducing energy costs and saving money.
        Over the past few years, federal and state programs have made incentives available to help homeowners offset the cost of insulation upgrades.
    </p>

    <p>
        While some programs have ended like the Inflation Reduction Act, many homeowners can still file for past credits, and state-specific rebates may still be available.
    </p>

    <p>
        Learn which incentives were available, how to file if you qualify, and where to find state and local rebates.
    </p>';
                                        ?>
                                    </div>
                                    <div id="brxe-qpmjhd" class="brxe-div image-wrapper absolute">
                                        <img width="572" height="214"
                                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-nvassh"
                                            decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                                    </div>
                                </div>
                                <div id="brxe-sxjbsy" class="brxe-block hero_image-wrapper">
                                    <?php
                                    $heroImage = get_field('ho_hero_image');
                                    if ($heroImage):
                                        ?>
                                        <img width="1005" height="1024" src="<?php echo esc_url($heroImage); ?>"
                                            class="brxe-image image-cover css-filter size-large" alt=" " id="brxe-zkpceh"
                                            loading="eager" decoding="async" srcset="<?php echo esc_url($heroImage); ?>"
                                            sizes="(max-width: 1005px) 100vw, 1005px" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php /*
                <section id="brxe-ympotz" class="brxe-section section">
                    <div id="brxe-qaciyl" class="brxe-container padding-global">
                        <div id="brxe-dxkfza" class="brxe-block section-component">
                            <div id="brxe-mskujs" class="brxe-block grid-block">
                                <div id="brxe-anmfge" class="brxe-block">
                                    <h2 id="brxe-wnpadc" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        Inflation Reduction Act (IRA) – What Homeowners Should Know
                                    </h2>
                                    <div id="brxe-clbyvi" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        The Inflation Reduction Act (IRA), passed in 2022, expanded energy-efficiency incentives for homeowners who completed qualifying upgrades, including insulation. These incentives applied to projects completed during specific tax years and helped many homeowners reduce the overall cost of improving their homes.<br><br>

                                        Koala Insulation continues to share this information as a helpful resource for homeowners who are filing their taxes or exploring state-level rebate opportunities.

                                    </div>
                                    <a id="brxe-jleemb"
                                        href="<?php echo home_url('/wp-content/uploads/2024/09/IRA_At_A_Glance_Infographic_Jan23-1.pdf'); ?>"
                                        target="_blank" class="brxe-div btn"><svg class="brxe-svg btn-icon" id="brxe-eqmczb"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                                            fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10 18.5C14.4183 18.5 18 14.9183 18 10.5C18 6.08172 14.4183 2.5 10 2.5C5.58172 2.5 2 6.08172 2 10.5C2 14.9183 5.58172 18.5 10 18.5ZM10.75 7.25C10.75 6.83579 10.4142 6.5 10 6.5C9.58579 6.5 9.25 6.83579 9.25 7.25V11.8401L7.29959 9.73966C7.01774 9.43613 6.54319 9.41855 6.23966 9.70041C5.93613 9.98226 5.91855 10.4568 6.20041 10.7603L9.45041 14.2603C9.59231 14.4132 9.79145 14.5 10 14.5C10.2086 14.5 10.4077 14.4132 10.5496 14.2603L13.7996 10.7603C14.0814 10.4568 14.0639 9.98226 13.7603 9.70041C13.4568 9.41855 12.9823 9.43613 12.7004 9.73966L10.75 11.8401V7.25Z"
                                                fill="white"></path>
                                        </svg>
                                        <div id="brxe-iopscj" class="brxe-text-basic">Download</div>
                                    </a>
                                </div>
                                <div id="brxe-aoyrwh" class="brxe-block" data-animi="up" data-duration="0.6">
                                    <div id="brxe-suxlcg" class="brxe-block image-wrapper" data-animi="scale" data-duration="0.6"
                                        data-delay="0.2">
                                        <img width="709" height="824"
                                            src="<?php echo home_url('/wp-content/uploads/2024/08/infographic-image-1.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-glcjrb"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 709px) 100vw, 709px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/08/infographic-image-1.png'); ?>         709w,
                  <?php echo home_url('/wp-content/uploads/2024/08/infographic-image-1-258x300.png'); ?> 258w
                " />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                */ ?>
                <?php /*
                <section id="brxe-bikhjp" class="brxe-section section">
                    <div id="brxe-ogdbcd" class="brxe-container padding-global">
                        <div id="brxe-invsug" class="brxe-block section-component">
                            <div id="brxe-ogvway" class="brxe-block grid-block">
                                <div id="brxe-vcdhma" class="brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                    <img width="908" height="983"
                                        src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-qnohtp" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 908px) 100vw, 908px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?>         908w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-277x300.jpg'); ?> 277w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-768x831.jpg'); ?> 768w
              " />
                                </div>
                                <div id="brxe-ljxyoh" class="brxe-block">
                                    <h2 id="brxe-avgseq" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        Energy Efficiency Home Improvement Credit (Section 25C)
                                    </h2>
                                    <div id="brxe-uxwkch" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        Qualifying insulation work completed in the most recent tax year (2025). When it was available, the Energy Efficiency Home Improvement Credit (Section 25C) allowed homeowners to claim:

                                        <ul>
                                            <li><span class="is-blue">Up to 30% of the cost</span> of qualifying insulation materials and labor</li>
                                            <li><span class="is-blue">Up to $1,200 per year</span> as a federal tax credit</li>
                                        </ul>

                                        <h3 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">How to File If Your Insulation Was Installed in 2025</h3><br>

                                        If you had insulation installed within the last tax year, you may still be able to file for this credit when completing your federal tax return.<br><br>

                                        <h4 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">Koala Insulation can help by:</h4>

                                        <ul>
                                            <li>Providing clear invoices and project documentation</li>
                                            <li>Explaining which insulation upgrades previously qualified</li>
                                            <li>Walking you through what information is typically needed to file</li>
                                        </ul>

                                        <h4 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">To claim the credit, homeowners generally:</h4>

                                        <ul>
                                            <li>File IRS Form 5695 with their federal tax return</li>
                                            <li>Use their Koala Insulation invoice and project details</li>
                                            <li>Report eligible insulation costs under the Energy Efficiency Home Improvement Credit section</li>
                                        </ul>

                                        Final eligibility is determined by the IRS. A tax professional can help confirm whether your project qualifies.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                */ ?>
                <section id="brxe-sgsgoo" class="brxe-section section" style="padding-top: 80px;">
                    <div id="brxe-gbsciu" class="brxe-container padding-global">
                        <div id="brxe-bdlsje" class="brxe-block section-component">
                            <div id="brxe-dhuogc" class="brxe-block grid-block">
                                <div id="brxe-bbdvaq" class="brxe-block">
                                    <h2 id="brxe-qqmrki" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        HOMES Rebate Program (State-Administered)
                                    </h2>
                                    <div id="brxe-iusijy" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        The HOMES Rebate Program offered incentives to homeowners who reduced their home’s overall energy use through efficiency improvements, including insulation upgrades.
                                    </div>
                                    <div id="brxe-ipxpzz" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        <h4 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">Key things to know:</h4>

                                        <ul>
                                            <li>Rebates were based on <span class="is-blue">measured or modeled energy savings</span></li>
                                            <li>A <span class="is-blue">minimum 20% energy reduction</span> was typically required</li>
                                            <li>Programs were <span class="is-blue">run by individual states</span>, not the federal government</li>
                                        </ul>

                                        Because this program was administered at the state level, availability, timelines, and filing requirements varied widely.
                                    </div>
                                </div>
                                <div id="brxe-qaeive" class="brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                    <img width="908" height="861"
                                        src="<?php echo home_url('/wp-content/uploads/2025/02/2.webp'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-oubbpl" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 908px) 100vw, 908px" srcset="
                <?php echo home_url('/wp-content/uploads/2025/02/2.webp'); ?>         908w,
                <?php echo home_url('/wp-content/uploads/2025/02/2-300x284.webp'); ?> 300w,
                <?php echo home_url('/wp-content/uploads/2025/02/2-768x728.webp'); ?> 768w
              " />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-bikhjp" class="brxe-section section">
                    <div id="brxe-ogdbcd" class="brxe-container padding-global">
                        <div id="brxe-invsug" class="brxe-block section-component">
                            <div id="brxe-ogvway" class="brxe-block grid-block">
                                <div id="brxe-vcdhma" class="brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                    <img width="908" height="861"
                                        src="<?php echo home_url('/wp-content/uploads/2024/08/2-1.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-oubbpl" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 908px) 100vw, 908px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/2-1.jpg'); ?>         908w,
                <?php echo home_url('/wp-content/uploads/2024/08/2-1-300x284.jpg'); ?> 300w,
                <?php echo home_url('/wp-content/uploads/2024/08/2-1-768x728.jpg'); ?> 768w
              " />
                                </div>
                                <div id="brxe-ljxyoh" class="brxe-block">
                                    <h2 id="brxe-avgseq" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        High-Efficiency Electric Home Rebate Program
                                    </h2>
                                    <div id="brxe-uxwkch" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        This program provided additional incentives for energy-efficient home upgrades and was also administered by individual states. Insulation improvements often played an important role in qualifying projects.<br><br>

                                        Rebate amounts were based on:

                                        <ul>
                                            <li>Overall project cost</li>
                                            <li>Energy savings achieved</li>
                                            <li>State-specific program guidelines</li>
                                        </ul>

                                        Some states continue to offer rebates through updated or modified programs.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-nqhatl" class="brxe-section section">
                    <div id="brxe-uhevaf" class="brxe-container padding-global">
                        <div id="brxe-eantvb" class="brxe-block section-component">
                            <div id="brxe-nditvb" class="brxe-block">
                                <h2 id="brxe-foxfmj" class="brxe-heading heading-style-h2 text-weight-semibold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    How to Find State-Specific Rebates

                                </h2>
                                <div id="brxe-gbjtwb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    State and local rebates change frequently and may still be available even after federal programs end.<br><br>
                                    <h4>To find rebates in your area:</h4>
                                </div>
                                <div id="brxe-xpmqbz" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    1. Visit the <span class="is-800">Energy Star Rebate Finder</span> <br>
                                    2. Check your <span class="is-800">state energy office website</span> <br>         
                                    3. Review local utility company efficiency programs <br>
                                    4. Confirm eligibility requirements and available funding 
                                </div>
                                <div id="brxe-tdpkxg" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    Koala Insulation is always happy to point homeowners toward trusted resources, but final eligibility is determined by the program administrator.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-iadzzb" class="brxe-section section">
                    <div id="brxe-smufre" class="brxe-container padding-global">
                        <div id="brxe-ymltaq" class="brxe-block section-component">
                            <div id="brxe-scaahm" class="brxe-block">
                                <h2 id="brxe-qkerue" class="brxe-heading heading-style-h2 text-weight-semibold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    How Koala Insulation Supports You

                                </h2>
                                <div id="brxe-rkleub" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    While Koala Insulation does not file rebates or tax credits on your behalf, we support homeowners by:
                                </div>
                                <div id="brxe-unuecm" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    <ul>
                                        <li>Providing clear invoices and documentation</li>
                                        <li>Explaining which insulation upgrades previously qualified for incentives</li>
                                        <li>Helping you understand what paperwork may be needed</li>
                                        <li>Directing you to reliable federal and state resources</li>
                                    </ul><br>

                                    Our goal is to make the process clearer and less overwhelming.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-pusvcr" class="brxe-section section">
                    <div id="brxe-lqcwta" class="brxe-container padding-global">
                        <div id="brxe-pixlce" class="brxe-block section-component">
                            <div id="brxe-euwiym" class="brxe-block">
                                <h4 id="brxe-tvqnzc" class="brxe-heading heading-style-h4 font-weight-medium" data-animi="up"
                                    data-duration="0.6">
                                    Frequently Asked Questions
                                </h4>
                                <div class="acc-container4">
                                    <?php if ($all_faqs): ?>
                                        <?php foreach ($all_faqs as $faq): ?>
                                            <?php
                                            $faq_id = $faq->ID;
                                            $faq_title = get_the_title($faq_id);
                                            $faq_post = get_post($faq_id);
                                            $faq_content = $faq_post ? apply_filters('the_content', $faq_post->post_content) : '';
                                            ?>
                                            <div class="acc4">
                                                <div class="acc-head4 accordion-title-wrapper">
                                                    <div class="acc4-head-inner">
                                                        <h5 class="accordian-title">
                                                            <?php echo $faq_title ?>
                                                        </h5>
                                                    </div>
                                                    <div class="accordian-icon-wrapper">
                                                        <svg class="icon expanded acc-icon-min" xmlns="http://www.w3.org/2000/svg"
                                                            width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M15 12H9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                                                stroke="#043968" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                        <svg class="icon acc-icon-plus" xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M12 9V15M15 12H9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                                                stroke="#043968" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="acc-content4 accordion-content-wrapper">
                                                    <div class="accordian-text">
                                                        <?php echo $faq_content ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No faqs found for this location.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-eaeoov" class="brxe-section section">
                    <div id="brxe-scffln" class="brxe-container padding-global">
                        <div id="brxe-ofqwue" class="brxe-block section-component">
                            <h4 id="brxe-tyurqy" class="brxe-heading heading-style-h4 font-weight-medium" data-animi="up"
                                data-duration="0.6">
                                Resources
                            </h4>
                            <a id="brxe-hwlyfw" href="https://www.crfb.org/blogs/whats-inflation-reduction-act" target="_blank"
                                class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-awpsdj" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-kgvjxc" class="brxe-heading heading-style-h6 font-weight-medium">
                                        More on the Inflation Reduction Act
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-daosaf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-jnbgya" href="http://Energy.gov" target="_blank" class="brxe-block" data-animi="up"
                                data-duration="0.6" data-delay="0.4">
                                <div id="brxe-inwgik" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-tjdhnk" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Tax Credits for Homeowners from Energy.gov<br />
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-goxpud" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-snyvcg" href="https://www.energystar.gov/rebate-finder" target="_blank"
                                class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-cfpgdl" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-dbonca" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Energy Star Rebate Finder
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-ahdwft" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-yosncv" href="https://www.dsireusa.org/" target="_blank" class="brxe-block"
                                data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-mnssre" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-daafmz" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Tax Credits by State
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-ixfceg" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-bnccxk" href="https://homes.rewiringamerica.org/calculator" target="_blank"
                                class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-slykta" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-vvohye" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Calculate Your Inflation Reduction Act Savings<br />
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-jkmcxf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a>
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
    
    // Get all Homeowner FAQs
    $all_faqs = get_posts(array(
        'post_type' => 'homeowner-incentives',
        'posts_per_page' => -1,
    ));

    $location_ho = get_posts(array(
        'post_type' => 'location-homeowner-i',
        'posts_per_page' => -1,
    ));
    ?>
                <main id="brx-content">
                <section id="brxe-yqjdcf" class="brxe-section section">
                    <div id="brxe-wzuukq" class="brxe-container padding-global">
                        <div id="brxe-xkhaib" class="brxe-block section-component">
                            <div id="brxe-avcngh" class="brxe-block brx-grid hero-block-grid">
                                <div id="brxe-rdapaz" class="brxe-block hero_content-wrapper">
                                    <h1 id="brxe-agmygl" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        <?php the_title(); ?>
                                    </h1>
                                    <?php /*
                                    <h2 id="brxe-kbkeen" class="brxe-heading text-size-large is-blue" data-animi="up"
                                        data-duration="0.6">
                                        Save More with Koala Insulation’s Homeowner Incentives
                                    </h2>
                                    */ ?>
                                    <div id="brxe-ibaxcu" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-delay="0.2" data-duration="0.6">
                                        <?php
                                        //$heroContent = get_field('ho_hero_description');
                                        //if ($heroContent) {
                                        //    echo $heroContent;
                                        //}


    $location_name = !empty($location_title)
        ? ' ' . esc_html($location_title) . ''
        : '';

    echo '
    <p>
        At Koala Insulation' . $location_name . ', our goal has always been simple: help homeowners create more comfortable, energy-efficient homes while reducing energy costs and saving money.
        Over the past few years, federal and state programs have made incentives available to help homeowners offset the cost of insulation upgrades.
    </p>

    <p>
        While some programs have ended like the Inflation Reduction Act, many homeowners can still file for past credits, and state-specific rebates may still be available.
    </p>

    <p>
        Learn which incentives were available, how to file if you qualify, and where to find state and local rebates.
    </p>';
                                        ?>
                                    </div>
                                    <div id="brxe-qpmjhd" class="brxe-div image-wrapper absolute">
                                        <img width="572" height="214"
                                            src="<?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-nvassh"
                                            decoding="async" data-type="string" sizes="(max-width: 572px) 100vw, 572px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector.png'); ?>         572w,
                  <?php echo home_url('/wp-content/uploads/2024/06/Vector-300x112.png'); ?> 300w
                " />
                                    </div>
                                </div>
                        <div id="brxe-sxjbsy" class="brxe-block hero_image-wrapper">
                            <img width="1005" height="1024"
                                src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-131-2-1005x1024.jpg'); ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-zkpceh" loading="eager"
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
                <?php /*
                <section id="brxe-ympotz" class="brxe-section section">
                    <div id="brxe-qaciyl" class="brxe-container padding-global">
                        <div id="brxe-dxkfza" class="brxe-block section-component">
                            <div id="brxe-mskujs" class="brxe-block grid-block">
                                <div id="brxe-anmfge" class="brxe-block">
                                    <h2 id="brxe-wnpadc" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        Inflation Reduction Act (IRA) – What Homeowners Should Know
                                    </h2>
                                    <div id="brxe-clbyvi" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        The Inflation Reduction Act (IRA), passed in 2022, expanded energy-efficiency incentives for homeowners who completed qualifying upgrades, including insulation. These incentives applied to projects completed during specific tax years and helped many homeowners reduce the overall cost of improving their homes.<br><br>

                                        Koala Insulation continues to share this information as a helpful resource for homeowners who are filing their taxes or exploring state-level rebate opportunities.

                                    </div>
                                    <a id="brxe-jleemb"
                                        href="<?php echo home_url('/wp-content/uploads/2024/09/IRA_At_A_Glance_Infographic_Jan23-1.pdf'); ?>"
                                        target="_blank" class="brxe-div btn"><svg class="brxe-svg btn-icon" id="brxe-eqmczb"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                                            fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10 18.5C14.4183 18.5 18 14.9183 18 10.5C18 6.08172 14.4183 2.5 10 2.5C5.58172 2.5 2 6.08172 2 10.5C2 14.9183 5.58172 18.5 10 18.5ZM10.75 7.25C10.75 6.83579 10.4142 6.5 10 6.5C9.58579 6.5 9.25 6.83579 9.25 7.25V11.8401L7.29959 9.73966C7.01774 9.43613 6.54319 9.41855 6.23966 9.70041C5.93613 9.98226 5.91855 10.4568 6.20041 10.7603L9.45041 14.2603C9.59231 14.4132 9.79145 14.5 10 14.5C10.2086 14.5 10.4077 14.4132 10.5496 14.2603L13.7996 10.7603C14.0814 10.4568 14.0639 9.98226 13.7603 9.70041C13.4568 9.41855 12.9823 9.43613 12.7004 9.73966L10.75 11.8401V7.25Z"
                                                fill="white"></path>
                                        </svg>
                                        <div id="brxe-iopscj" class="brxe-text-basic">Download</div>
                                    </a>
                                </div>
                                <div id="brxe-aoyrwh" class="brxe-block" data-animi="up" data-duration="0.6">
                                    <div id="brxe-suxlcg" class="brxe-block image-wrapper" data-animi="scale" data-duration="0.6"
                                        data-delay="0.2">
                                        <img width="709" height="824"
                                            src="<?php echo home_url('/wp-content/uploads/2024/08/infographic-image-1.png'); ?>"
                                            class="brxe-image image-contain css-filter size-full" alt="" id="brxe-glcjrb"
                                            decoding="async" loading="lazy" data-type="string"
                                            sizes="(max-width: 709px) 100vw, 709px" srcset="
                  <?php echo home_url('/wp-content/uploads/2024/08/infographic-image-1.png'); ?>         709w,
                  <?php echo home_url('/wp-content/uploads/2024/08/infographic-image-1-258x300.png'); ?> 258w
                " />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                */ ?>
                <?php /*
                <section id="brxe-bikhjp" class="brxe-section section">
                    <div id="brxe-ogdbcd" class="brxe-container padding-global">
                        <div id="brxe-invsug" class="brxe-block section-component">
                            <div id="brxe-ogvway" class="brxe-block grid-block">
                                <div id="brxe-vcdhma" class="brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                    <img width="908" height="983"
                                        src="<?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-qnohtp" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 908px) 100vw, 908px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-22.jpg'); ?>         908w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-277x300.jpg'); ?> 277w,
                <?php echo home_url('/wp-content/uploads/2024/08/Frame-22-768x831.jpg'); ?> 768w
              " />
                                </div>
                                <div id="brxe-ljxyoh" class="brxe-block">
                                    <h2 id="brxe-avgseq" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        Energy Efficiency Home Improvement Credit (Section 25C)
                                    </h2>
                                    <div id="brxe-uxwkch" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        Qualifying insulation work completed in the most recent tax year (2025). When it was available, the Energy Efficiency Home Improvement Credit (Section 25C) allowed homeowners to claim:

                                        <ul>
                                            <li><span class="is-blue">Up to 30% of the cost</span> of qualifying insulation materials and labor</li>
                                            <li><span class="is-blue">Up to $1,200 per year</span> as a federal tax credit</li>
                                        </ul>

                                        <h3 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">How to File If Your Insulation Was Installed in 2025</h3><br>

                                        If you had insulation installed within the last tax year, you may still be able to file for this credit when completing your federal tax return.<br><br>

                                        <h4 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">Koala Insulation can help by:</h4>

                                        <ul>
                                            <li>Providing clear invoices and project documentation</li>
                                            <li>Explaining which insulation upgrades previously qualified</li>
                                            <li>Walking you through what information is typically needed to file</li>
                                        </ul>

                                        <h4 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">To claim the credit, homeowners generally:</h4>

                                        <ul>
                                            <li>File IRS Form 5695 with their federal tax return</li>
                                            <li>Use their Koala Insulation invoice and project details</li>
                                            <li>Report eligible insulation costs under the Energy Efficiency Home Improvement Credit section</li>
                                        </ul>

                                        Final eligibility is determined by the IRS. A tax professional can help confirm whether your project qualifies.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                */ ?>
                <section id="brxe-sgsgoo" class="brxe-section section" style="padding-top: 80px;">
                    <div id="brxe-gbsciu" class="brxe-container padding-global">
                        <div id="brxe-bdlsje" class="brxe-block section-component">
                            <div id="brxe-dhuogc" class="brxe-block grid-block">
                                <div id="brxe-bbdvaq" class="brxe-block">
                                    <h2 id="brxe-qqmrki" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        HOMES Rebate Program (State-Administered)
                                    </h2>
                                    <div id="brxe-iusijy" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        The HOMES Rebate Program offered incentives to homeowners who reduced their home’s overall energy use through efficiency improvements, including insulation upgrades.
                                    </div>
                                    <div id="brxe-ipxpzz" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        <h4 class="brxe-heading heading-style-h4 text-color-green font-weight-medium">Key things to know:</h4>

                                        <ul>
                                            <li>Rebates were based on <span class="is-blue">measured or modeled energy savings</span></li>
                                            <li>A <span class="is-blue">minimum 20% energy reduction</span> was typically required</li>
                                            <li>Programs were <span class="is-blue">run by individual states</span>, not the federal government</li>
                                        </ul>

                                        Because this program was administered at the state level, availability, timelines, and filing requirements varied widely.
                                    </div>
                                </div>
                                <div id="brxe-qaeive" class="brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                    <img width="908" height="861"
                                        src="<?php echo home_url('/wp-content/uploads/2025/02/2.webp'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-oubbpl" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 908px) 100vw, 908px" srcset="
                <?php echo home_url('/wp-content/uploads/2025/02/2.webp'); ?>         908w,
                <?php echo home_url('/wp-content/uploads/2025/02/2-300x284.webp'); ?> 300w,
                <?php echo home_url('/wp-content/uploads/2025/02/2-768x728.webp'); ?> 768w
              " />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-bikhjp" class="brxe-section section">
                    <div id="brxe-ogdbcd" class="brxe-container padding-global">
                        <div id="brxe-invsug" class="brxe-block section-component">
                            <div id="brxe-ogvway" class="brxe-block grid-block">
                                <div id="brxe-vcdhma" class="brxe-block image-wrapper" data-animi="up" data-duration="0.6">
                                    <img width="908" height="861"
                                        src="<?php echo home_url('/wp-content/uploads/2024/08/2-1.jpg'); ?>"
                                        class="brxe-image image-cover css-filter size-full" alt="" id="brxe-oubbpl" decoding="async"
                                        loading="lazy" data-type="string" sizes="(max-width: 908px) 100vw, 908px" srcset="
                <?php echo home_url('/wp-content/uploads/2024/08/2-1.jpg'); ?>         908w,
                <?php echo home_url('/wp-content/uploads/2024/08/2-1-300x284.jpg'); ?> 300w,
                <?php echo home_url('/wp-content/uploads/2024/08/2-1-768x728.jpg'); ?> 768w
              " />
                                </div>
                                <div id="brxe-ljxyoh" class="brxe-block">
                                    <h2 id="brxe-avgseq" class="brxe-heading heading-style-h2 font-weight-bold text-allcaps"
                                        data-animi="up" data-duration="0.6">
                                        High-Efficiency Electric Home Rebate Program
                                    </h2>
                                    <div id="brxe-uxwkch" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                        data-duration="0.6">
                                        This program provided additional incentives for energy-efficient home upgrades and was also administered by individual states. Insulation improvements often played an important role in qualifying projects.<br><br>

                                        Rebate amounts were based on:

                                        <ul>
                                            <li>Overall project cost</li>
                                            <li>Energy savings achieved</li>
                                            <li>State-specific program guidelines</li>
                                        </ul>

                                        Some states continue to offer rebates through updated or modified programs.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-nqhatl" class="brxe-section section">
                    <div id="brxe-uhevaf" class="brxe-container padding-global">
                        <div id="brxe-eantvb" class="brxe-block section-component">
                            <div id="brxe-nditvb" class="brxe-block">
                                <h2 id="brxe-foxfmj" class="brxe-heading heading-style-h2 text-weight-semibold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    How to Find State-Specific Rebates

                                </h2>
                                <div id="brxe-gbjtwb" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    State and local rebates change frequently and may still be available even after federal programs end.<br><br>
                                    <h4>To find rebates in your area:</h4>
                                </div>
                                <div id="brxe-xpmqbz" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    1. Visit the <span class="is-800">Energy Star Rebate Finder</span> <br>
                                    2. Check your <span class="is-800">state energy office website</span> <br>         
                                    3. Review local utility company efficiency programs <br>
                                    4. Confirm eligibility requirements and available funding 
                                </div>
                                <div id="brxe-tdpkxg" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    Koala Insulation is always happy to point homeowners toward trusted resources, but final eligibility is determined by the program administrator.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-iadzzb" class="brxe-section section">
                    <div id="brxe-smufre" class="brxe-container padding-global">
                        <div id="brxe-ymltaq" class="brxe-block section-component">
                            <div id="brxe-scaahm" class="brxe-block">
                                <h2 id="brxe-qkerue" class="brxe-heading heading-style-h2 text-weight-semibold text-allcaps"
                                    data-animi="up" data-duration="0.6">
                                    How Koala Insulation Supports You

                                </h2>
                                <div id="brxe-rkleub" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    While Koala Insulation does not file rebates or tax credits on your behalf, we support homeowners by:
                                </div>
                                <div id="brxe-unuecm" class="brxe-text-basic text-size-regular text-weight-semibold" data-animi="up"
                                    data-duration="0.6">
                                    <ul>
                                        <li>Providing clear invoices and documentation</li>
                                        <li>Explaining which insulation upgrades previously qualified for incentives</li>
                                        <li>Helping you understand what paperwork may be needed</li>
                                        <li>Directing you to reliable federal and state resources</li>
                                    </ul><br>

                                    Our goal is to make the process clearer and less overwhelming.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-pusvcr" class="brxe-section section">
                    <div id="brxe-lqcwta" class="brxe-container padding-global">
                        <div id="brxe-pixlce" class="brxe-block section-component">
                            <div id="brxe-euwiym" class="brxe-block">
                                <h4 id="brxe-tvqnzc" class="brxe-heading heading-style-h4 font-weight-medium" data-animi="up"
                                    data-duration="0.6">
                                    Frequently Asked Questions
                                </h4>
                                <div class="acc-container4">
                                    <?php if ($all_faqs): ?>
                                        <?php foreach ($all_faqs as $faq): ?>
                                            <?php
                                            $faq_id = $faq->ID;
                                            $faq_title = get_the_title($faq_id);
                                            $faq_post = get_post($faq_id);
                                            $faq_content = $faq_post ? apply_filters('the_content', $faq_post->post_content) : '';
                                            ?>
                                            <div class="acc4">
                                                <div class="acc-head4 accordion-title-wrapper">
                                                    <div class="acc4-head-inner">
                                                        <h5 class="accordian-title">
                                                            <?php echo $faq_title ?>
                                                        </h5>
                                                    </div>
                                                    <div class="accordian-icon-wrapper">
                                                        <svg class="icon expanded acc-icon-min" xmlns="http://www.w3.org/2000/svg"
                                                            width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M15 12H9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                                                stroke="#043968" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                        <svg class="icon acc-icon-plus" xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M12 9V15M15 12H9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                                                stroke="#043968" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="acc-content4 accordion-content-wrapper">
                                                    <div class="accordian-text">
                                                        <?php echo $faq_content ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No faqs found for this location.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-eaeoov" class="brxe-section section">
                    <div id="brxe-scffln" class="brxe-container padding-global">
                        <div id="brxe-ofqwue" class="brxe-block section-component">
                            <h4 id="brxe-tyurqy" class="brxe-heading heading-style-h4 font-weight-medium" data-animi="up"
                                data-duration="0.6">
                                Resources
                            </h4>
                            <a id="brxe-hwlyfw" href="https://www.crfb.org/blogs/whats-inflation-reduction-act" target="_blank"
                                class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-awpsdj" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-kgvjxc" class="brxe-heading heading-style-h6 font-weight-medium">
                                        More on the Inflation Reduction Act
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-daosaf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-jnbgya" href="http://Energy.gov" target="_blank" class="brxe-block" data-animi="up"
                                data-duration="0.6" data-delay="0.4">
                                <div id="brxe-inwgik" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-tjdhnk" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Tax Credits for Homeowners from Energy.gov<br />
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-goxpud" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-snyvcg" href="https://www.energystar.gov/rebate-finder" target="_blank"
                                class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-cfpgdl" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-dbonca" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Energy Star Rebate Finder
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-ahdwft" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-yosncv" href="https://www.dsireusa.org/" target="_blank" class="brxe-block"
                                data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-mnssre" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-daafmz" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Tax Credits by State
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-ixfceg" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a><a id="brxe-bnccxk" href="https://homes.rewiringamerica.org/calculator" target="_blank"
                                class="brxe-block" data-animi="up" data-duration="0.6" data-delay="0.4">
                                <div id="brxe-slykta" class="brxe-block title-item-acc accordion-title-wrapper">
                                    <h6 id="brxe-vvohye" class="brxe-heading heading-style-h6 font-weight-medium">
                                        Calculate Your Inflation Reduction Act Savings<br />
                                    </h6>
                                    <svg class="brxe-svg" id="brxe-jkmcxf" xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.25 12C4.25 11.5858 4.58579 11.25 5 11.25L18.1893 11.25L12.7197 5.78033C12.4268 5.48744 12.4268 5.01256 12.7197 4.71967C13.0126 4.42678 13.4874 4.42678 13.7803 4.71967L20.5303 11.4697C20.8232 11.7626 20.8232 12.2374 20.5303 12.5303L13.7803 19.2803C13.4874 19.5732 13.0126 19.5732 12.7197 19.2803C12.4268 18.9874 12.4268 18.5126 12.7197 18.2197L18.1893 12.75L5 12.75C4.58579 12.75 4.25 12.4142 4.25 12Z"
                                            fill="#0F172A"></path>
                                    </svg>
                                </div>
                            </a>
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
}
?>