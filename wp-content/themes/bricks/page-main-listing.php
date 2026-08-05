<?php
/* Template Name: Main Services Listing */
get_header();

// Get the current URL path
$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Split the URL into segments
$segments = explode('/', $current_url);

// Determine if the URL is location-specific or generic
if (count($segments) === 2 && $segments[1] === 'services') {
    $location_slug = $segments[0];
    show_location_page($location_slug);

} elseif (count($segments) === 1 && $segments[0] === 'services') {
    render_fallback_content();

} else {
    // Invalid or unexpected URL structure
    echo "Page not found.";
    get_template_part('404');
    exit;
}

get_footer();

function show_location_page($location_slug)
{
    // Get the location post by slug
    $location_post = get_page_by_path($location_slug, OBJECT, 'location');

    if (!$location_post) {
        echo "Location not found.";
        return;
    }

    // Get the location ID
    $location_id = $location_post->ID;

    $location_name = get_field('location_name', $location_id);

    $location_title = get_the_title($location_id);

    $services = get_field('location_service', $location_id);

    ?>
    <main id="brx-content">
       <!-- <section id="us-services-sub-nav" class="brxe-section section">
            <div id="brxe-dauwze" class="brxe-container">
                <div id="brxe-uqpvvo" class="brxe-block section-component">
                    <div id="brxe-yewkoa" class="brxe-block">
                        <?php if ($services): ?>
                            <?php foreach ($services as $service): ?>
                                <?php
                                $service_id = $service->ID;
                                $service_title = get_field('location_service_name', $service_id);
                                $service_link = get_permalink($service_id);
                                $service_icon = get_field('location_service_icon', $service_id);
                                $service_icon_url = is_array($service_icon) ? $service_icon['url'] : '';
                                ?>
                                <a id="<?php echo $service_id ?>" href="<?php echo $service_link ?>"
                                    class="brxe-hshkqg brxe-div service-link-tag" data-animi="up" data-duration="0.6">
                                    <?php if ($service_icon): ?>
                                        <div class="brxe-fjoqym brxe-block our-service-item-img-wrapper">
                                            <img src="<?php echo $service_icon_url; ?>" alt="<?php echo $service_title; ?>"
                                                class="brxe-rjuylc brxe-image image-cover">
                                        </div>
                                    <?php endif; ?>
                                    <p class="brxe-amgdod brxe-text-basic"><?php echo $service_title ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No services found for this location.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section> -->
        <section id="brxe-cvsffj" class="brxe-section section">
            <div id="brxe-dpgady" class="brxe-container padding-global">
                <div id="brxe-rbinfy" class="brxe-block padding-section-small">
                    <div id="brxe-icngvh" class="brxe-block section-component">
                        <div id="brxe-jkgpgn" class="brxe-block">
                            <div id="brxe-ohwozs" class="brxe-block" data-animi="up" data-duration="0.6">
                                <h1 id="brxe-jaukob" class="brxe-heading heading-style-h3 text-allcaps">Our <span
                                        class="heading-style-display-span">Services</span></h1>
                                <div id="brxe-xgybev" class="brxe-block"><svg class="brxe-svg" id="brxe-qecbao"
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
                            <div id="brxe-pqifbt" class="brxe-text-basic text-size-regular text-weight-semibold"
                                data-animi="up" data-duration="0.6" data-delay="0.4">
                                At Koala Insulation, we help make your home or business
                                comfortable year round—warm in the winter, cool in the summer, and
                                energy-efficient every day. Whether you want to improve comfort at
                                home or lower energy costs at work, we’ve got the right spray foam
                                insulation services and solutions for you.
                                <div><br /></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-fmbgxp" class="brxe-section section">
            <div id="brxe-fpionf" class="brxe-container padding-global">
                <div id="brxe-iohrru" class="brxe-block">
                    <div id="usa-our-services" class="brxe-block">
                        <h2 id="brxe-zkddcs" class="brxe-heading heading-style-h2 text-color-green">
                            <?php if ($location_name) {
                              echo '<span class="h2-span">Comprehensive Insulation Services in</span> ' . $location_name;
                            } else {
                              echo 'Comprehensive Insulation Services';
                            } ?>
                        </h2>
                        <?php if ($services): ?>
                            <?php foreach ($services as $service): ?>
                                <?php
                                $service_id = $service->ID;
                                $service_title = get_field('location_service_name', $service_id);
                                $service_description = get_field('location_service_short_description', $service_id);
                                $service_link = get_permalink($service_id);
                                $service_image = wp_get_attachment_image_url(get_field('location_service_image', $service_id), 'full');
                                $service_materials = get_field('location_materials', $service_id);
                                ?>
                                <div id="<?php echo $service_id ?>" class="brxe-dpsvxp brxe-block service-item" data-animi="up"
                                    data-duration="0.6" data-delay="0.3">
                                    <div class="brxe-tkywyn brxe-block">
                                        <?php if ($service_image): ?>
                                            <div class="brxe-mdcvgz brxe-block">
                                                <img width="930" height="692" src="<?php echo $service_image ?>"
                                                    class="brxe-pkeych brxe-image image-cover" alt="<?php echo $service_title ?>" />
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="brxe-lyxiss brxe-block">
                                        <div class="brxe-sesztd brxe-block">
                                            <h4 class="brxe-nstzwt brxe-heading heading-style-h4">
                                                <?php echo $service_title ?>
                                            </h4>
                                            <!-- <div class="brxe-pxiwcy brxe-text-basic text-size-regular">
                                                Keep your home cozy and dry with our spray foam insulation,
                                                sealing out drafts and moisture for better energy savings and
                                                enhanced comfort year round.
                                            </div> -->
                                            <?php if ($service_materials): ?>
                                                <div class="brxe-trgmug brxe-block">
                                                    <h6 class="brxe-qvsemt brxe-heading heading-style-h6">Made from</h6>
                                                    <div class="brxe-vxzhbb brxe-block">
                                                        <?php foreach ($service_materials as $service_material):
                                                        // echo "<pre style='display:none;'>";
                                                        //     print_r($service_material);
                                                        // echo "</pre>";
                                                            $material_id = $service_material->ID;
                                                            $material_name = get_the_title($material_id);
                                                            // $material_icon = $service_material['material_image']['url'];
                                                            $material_image_field = get_field('material_image', $material_id);
                                                            $material_icon = is_array($material_image_field) ? $material_image_field['url'] : '';
                                                            ?>
                                                            <div class="brxe-ulsjps brxe-div">
                                                                <div class="brxe-tasvdk brxe-div">
                                                                    <img width="110" height="115"
                                                                        src="<?php echo esc_url($material_icon); ?>"
                                                                        class="brxe-kcbzmv brxe-image image-cover css-filter size-large"
                                                                        alt="<?php echo esc_attr($material_name); ?>" decoding="async">
                                                                </div>
                                                                <div class="brxe-izswel brxe-text-basic text-size-regular">
                                                                    <?php echo esc_html($material_name); ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="brxe-scxbze brxe-text-basic text-size-regular text-color-mute">
                                                <?php echo $service_description ?>
                                            </div>
                                            <a href="<?php echo $service_link ?>" class="brxe-frnrdn brxe-div btn is-no-icon">
                                                <div class="brxe-evjxnp brxe-text-basic">Learn More</div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No services found for this location.</p>
                        <?php endif; ?>
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
}

/**
 * Renders the fallback content.
 */
function render_fallback_content()
{//Get the main services
    $services = get_posts(array(
        'post_type' => 'service',
        'posts_per_page' => -1,
    ));

    $location_rl_pages = get_posts(array(
        'post_type' => 'resources-landing-pa',
        'posts_per_page' => -1,
    ));
    ?>
    <main id="brx-content">
        <section id="us-services-sub-nav" class="brxe-section section">
            <div id="brxe-dauwze" class="brxe-container">
                <div id="brxe-uqpvvo" class="brxe-block section-component">
                    <div id="brxe-yewkoa" class="brxe-block">
                        <?php if ($services): ?>
                            <?php foreach ($services as $service): ?>
                                <?php
                                $service_id = $service->ID;
                                $service_title = get_the_title($service_id);
                                $service_link = get_permalink($service_id);
                                $service_icon = get_field('service_icon', $service_id);
                                $service_icon_url = is_array($service_icon) ? $service_icon['url'] : '';
                                ?>
                                <a id="<?php echo $service_id ?>" href="<?php echo $service_link ?>"
                                    class="brxe-hshkqg brxe-div service-link-tag" data-animi="up" data-duration="0.6">
                                    <?php if ($service_icon): ?>
                                        <div class="brxe-fjoqym brxe-block our-service-item-img-wrapper">
                                            <img src="<?php echo $service_icon_url; ?>" alt="<?php echo $service_title; ?>"
                                                class="brxe-rjuylc brxe-image image-cover">
                                        </div>
                                    <?php endif; ?>
                                    <p class="brxe-amgdod brxe-text-basic"><?php echo $service_title ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No services found for this location.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-cvsffj" class="brxe-section section">
            <div id="brxe-dpgady" class="brxe-container padding-global">
                <div id="brxe-rbinfy" class="brxe-block padding-section-small">
                    <div id="brxe-icngvh" class="brxe-block section-component">
                        <div id="brxe-jkgpgn" class="brxe-block">
                            <div id="brxe-ohwozs" class="brxe-block" data-animi="up" data-duration="0.6">
                                <h1 id="brxe-jaukob" class="brxe-heading heading-style-h3 text-allcaps">Our <span
                                        class="heading-style-display-span">Services</span></h1>
                                <div id="brxe-xgybev" class="brxe-block"><svg class="brxe-svg" id="brxe-qecbao"
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
                            <div id="brxe-pqifbt" class="brxe-text-basic text-size-regular text-weight-semibold"
                                data-animi="up" data-duration="0.6" data-delay="0.4">
                                At Koala Insulation, we help make your home or business
                                comfortable year round—warm in the winter, cool in the summer, and
                                energy-efficient every day. Whether you want to improve comfort at
                                home or lower energy costs at work, we’ve got the right spray foam
                                insulation services and solutions for you.
                                <div><br /></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-fmbgxp" class="brxe-section section">
            <div id="brxe-fpionf" class="brxe-container padding-global">
                <div id="brxe-iohrru" class="brxe-block">
                    <div id="usa-our-services" class="brxe-block">
                        <?php if ($services): ?>
                            <?php foreach ($services as $service): ?>
                                <?php
                                $service_id = $service->ID;
                                $service_title = get_the_title($service_id);
                                $service_description = get_field('short_description_', $service_id);
                                $service_link = get_permalink($service_id);
                                $service_image = get_field('service_image', $service_id);
                                $service_image_url = is_array($service_image) ? $service_image['url'] : '';
                                $service_materials = get_field('materials', $service_id);
                                $service_sub_head = get_field('service_post_sub_header', $service_id);
                                ?>
                                <div id="<?php echo $service_id ?>" class="brxe-dpsvxp brxe-block service-item" data-animi="up"
                                    data-duration="0.6" data-delay="0.3">
                                    <div class="brxe-tkywyn brxe-block">
                                        <?php if ($service_image): ?>
                                            <div class="brxe-mdcvgz brxe-block">
                                                <img width="930" height="692" src="<?php echo $service_image_url ?>"
                                                    class="brxe-pkeych brxe-image image-cover" alt="<?php echo $service_title ?>" />
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="brxe-lyxiss brxe-block">
                                        <div class="brxe-sesztd brxe-block">
                                            <h4 class="brxe-nstzwt brxe-heading heading-style-h4">
                                                <?php echo $service_title ?>
                                            </h4>
                                            <div class="brxe-pxiwcy brxe-text-basic text-size-regular">
                                                <?php echo $service_sub_head ?>
                                            </div>
                                            <?php if ($service_materials): ?>
                                                <div class="brxe-trgmug brxe-block">
                                                    <h6 class="brxe-qvsemt brxe-heading heading-style-h6">Made from</h6>
                                                    <div class="brxe-vxzhbb brxe-block">
                                                        <?php foreach ($service_materials as $service_material):
                                                            $material_id = $service_material->ID;
                                                            $material_name = get_the_title($material_id);
                                                            $material_icon = $service_material['material_image']['url'];
                                                            ?>
                                                            <div class="brxe-ulsjps brxe-div">
                                                                <div class="brxe-tasvdk brxe-div">
                                                                    <img width="110" height="115"
                                                                        src="<?php echo esc_url($material_icon); ?>"
                                                                        class="brxe-kcbzmv brxe-image image-cover css-filter size-large"
                                                                        alt="<?php echo esc_attr($material_name); ?>" decoding="async">
                                                                </div>
                                                                <div class="brxe-izswel brxe-text-basic text-size-regular">
                                                                    <?php echo esc_html($material_name); ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="brxe-scxbze brxe-text-basic text-size-regular text-color-mute">
                                                <?php echo $service_description ?>
                                            </div>
                                            <a href="/locations" class="brxe-fdmakd brxe-div btn"><svg
                                                    class="brxe-zkgxge brxe-svg btn-icon" xmlns="http://www.w3.org/2000/svg"
                                                    width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M8.65481 16.7633C8.67746 16.7764 8.69527 16.7865 8.70788 16.7936L8.72882 16.8053C8.89597 16.8971 9.10332 16.8964 9.27063 16.8056L9.29212 16.7936C9.30473 16.7865 9.32254 16.7764 9.34519 16.7633C9.39049 16.737 9.45523 16.6988 9.53663 16.6486C9.69935 16.5484 9.92906 16.4007 10.2035 16.2068C10.7513 15.8198 11.4823 15.2456 12.2149 14.4955C13.673 13.0026 15.1875 10.7596 15.1875 7.875C15.1875 4.45774 12.4173 1.6875 9 1.6875C5.58274 1.6875 2.8125 4.45774 2.8125 7.875C2.8125 10.7596 4.32699 13.0026 5.78509 14.4955C6.51769 15.2456 7.24868 15.8198 7.79654 16.2068C8.07094 16.4007 8.30065 16.5484 8.46337 16.6486C8.54477 16.6988 8.60951 16.737 8.65481 16.7633ZM9 10.125C10.2426 10.125 11.25 9.11764 11.25 7.875C11.25 6.63236 10.2426 5.625 9 5.625C7.75736 5.625 6.75 6.63236 6.75 7.875C6.75 9.11764 7.75736 10.125 9 10.125Z"
                                                        fill="white"></path>
                                                </svg>
                                                <div class="brxe-pviyxk brxe-text-basic">
                                                    Find Your nearest Location
                                                </div>
                                            </a><a href="<?php echo $service_link ?>" class="brxe-frnrdn brxe-div btn is-no-icon">
                                                <div class="brxe-evjxnp brxe-text-basic">Learn More</div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No services found for this location.</p>
                        <?php endif; ?>
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
        <div style="display: none;">
            <?php if ($location_rl_pages): ?>
                <?php foreach ($location_rl_pages as $location_rl_page): ?>
                    <?php
                    $location_rl_page_id = $location_rl_page->ID;
                    $location_rl_page_link = get_permalink($location_rl_page_id);
                    $location_rl_page_name = get_the_title($location_rl_page_id);
                    ?>
                    <a href="<?php echo $location_rl_page_link ?>"><?php echo $location_rl_page_name ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No location resources landing page posts found.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php
}
?>