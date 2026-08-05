<?php
/* Template Name: Faq */
get_header();

// Get the current URL path
$current_url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Split the URL into segments
$segments = explode('/', $current_url);

// Determine if the URL is location-specific or generic
if (count($segments) === 2 && $segments[1] === 'faq') {
    $location_slug = $segments[0];
    show_location_page($location_slug);

} elseif (count($segments) === 1 && $segments[0] === 'faq') {
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

    $faqs = get_posts(array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC', // ASC for ascending order
    ));

    //related faqs
    $related_faqs = get_field('related_faqs', $location_id);

    ?>
    <main id="brx-content">
        <section id="brxe-umxkye" class="brxe-section section">
            <div id="brxe-dsmjwa" class="brxe-container padding-global">
                <div id="brxe-klnhbl" class="brxe-block section-component">
                    <div id="brxe-jecuvl" class="brxe-block">
                        <div id="brxe-kletwo" class="brxe-block">
                            <h1 id="brxe-axjtyz" class="brxe-heading heading-style-h1 font-weight-bold" data-animi="up"
                                data-duration="0.6">
                                  <?php if ($location_name) {
                                    echo 'What ' . $location_name . ' Residents Should Know About Insulation';
                                  } else {
                                    echo 'FAQ';
                                  } ?>
                            </h1>
                            <div id="brxe-tbktgq" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6" data-delay="0.2">
                                Got questions about insulation or our services? We’ve got answers!
                                Our FAQs section is here to help you understand more about what we
                                do and how we can make your home more comfortable and
                                energy-efficient. If you still have questions, feel free to reach
                                out to us—we’re always happy to assist!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-fxklxc" class="brxe-section section">
            <div id="brxe-bkgkcb" class="brxe-container padding-global">
                <div id="brxe-xlwzwc" class="brxe-block section-component">
                    <div id="brxe-lwolww" class="brxe-block">
                        <div class="acc-container4">
                            <?php if ($faqs): ?>
                                <?php foreach ($faqs as $faq): ?>
                                    <?php
                                    $faq_id = $faq->ID;
                                    $faq_title = get_the_title($faq_id);
                                    $faq_answer = get_field('faq_content', $faq_id);
                                    // If ACF is empty, fallback to the post_content
                                    if (!empty($faq_answer)) {
                                        $faq_content = wpautop($faq_answer);
                                    } else {
                                        $faq_content = apply_filters('the_content', get_post_field('post_content', $faq_id));
                                    }
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
                                <p>No faqs found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        if ($related_faqs) {
            ?>
            <div class="brxe-block">
                <section id="brxe-ociftp" class="brxe-section section">
                    <div id="brxe-hpeibw" class="brxe-container padding-global">
                        <div id="brxe-lflwxr" class="brxe-block section-component">
                            <div id="brxe-ejabqy" class="brxe-block">
                                <div id="brxe-eleiix" class="brxe-block">
                                    <h1 id="brxe-xsoqwb" class="brxe-heading heading-style-h1 font-weight-bold" data-animi="up"
                                        data-duration="0.6">
                                        <?php echo $location_title ?> FAQs
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="brxe-vpnexh" class="brxe-section section">
                    <div id="brxe-amaumn" class="brxe-container padding-global">
                        <div id="brxe-yzmkct" class="brxe-block section-component">
                            <div id="brxe-hkgvwb" class="brxe-block">
                                <div class="acc-container4">
                                    <?php if ($related_faqs): ?>
                                        <?php foreach ($related_faqs as $faq): ?>
                                            <?php
                                            $faq_id = $faq->ID;
                                            $faq_title = get_the_title($faq_id);
                                            $faq_answer = get_field('faq_content', $faq_id);
                                            // If ACF is empty, fallback to the post_content
                                            if (!empty($faq_answer)) {
                                                $faq_content = wpautop($faq_answer);
                                            } else {
                                                $faq_content = apply_filters('the_content', get_post_field('post_content', $faq_id));
                                            }
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
            </div>
            <?php
        } else {
            echo '';
        }
        ?>
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
{
    //get faqs
    $faqs = get_posts(array(
        'post_type' => 'faq',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC', // ASC for ascending order
    ));

    $location_services = get_posts(array(
        'post_type' => 'location-service',
        'posts_per_page' => -1,
    ));
    ?>
    <main id="brx-content">
        <section id="brxe-umxkye" class="brxe-section section">
            <div id="brxe-dsmjwa" class="brxe-container padding-global">
                <div id="brxe-klnhbl" class="brxe-block section-component">
                    <div id="brxe-jecuvl" class="brxe-block">
                        <div id="brxe-kletwo" class="brxe-block">
                            <h1 id="brxe-axjtyz" class="brxe-heading heading-style-h1 font-weight-bold" data-animi="up"
                                data-duration="0.6">
                                Insulation FAQS: Your Insulation Questions, Answered
                            </h1>
                            <div id="brxe-tbktgq" class="brxe-text-basic text-size-regular text-color-mute" data-animi="up"
                                data-duration="0.6" data-delay="0.2">
                                Got questions about insulation or our services? We’ve got answers!
                                Our FAQs section is here to help you understand more about what we
                                do and how we can make your home more comfortable and
                                energy-efficient. If you still have questions, feel free to reach
                                out to us—we’re always happy to assist!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="brxe-fxklxc" class="brxe-section section">
            <div id="brxe-bkgkcb" class="brxe-container padding-global">
                <div id="brxe-xlwzwc" class="brxe-block section-component">
                    <div id="brxe-lwolww" class="brxe-block">
                      <h2 id="brxe-ipnkoa" class="brxe-heading heading-style-h2 text-color-green">Frequently Asked Questions About Insulation</h2>
                        <div class="acc-container4">
                            <?php if ($faqs): ?>
                                <?php foreach ($faqs as $faq): ?>
                                    <?php
                                    $faq_id = $faq->ID;
                                    $faq_title = get_the_title($faq_id);
                                    $faq_answer = get_field('faq_content', $faq_id);
                                    // If ACF is empty, fallback to the post_content
                                    if (!empty($faq_answer)) {
                                        $faq_content = wpautop($faq_answer);
                                    } else {
                                        $faq_content = apply_filters('the_content', get_post_field('post_content', $faq_id));
                                    }
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
                                <p>No faqs found.</p>
                            <?php endif; ?>
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
            <?php if ($location_services): ?>
                <?php foreach ($location_services as $location_service): ?>
                    <?php
                    $location_service_id = $location_service->ID;
                    $location_service_link = get_permalink($location_service_id);
                    $location_service_name = get_the_title($location_service_id);
                    ?>
                    <a href="<?php echo $location_service_link ?>"><?php echo $location_service_name ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No location service posts found.</p>
            <?php endif; ?>
        </div>
    </main>
    <?php
}
?>