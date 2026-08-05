<?php
function inject_canonical_in_head()
{
    // Get query vars
    $location = get_query_var('location_slug');
    $blog = get_query_var('blog_slug');

    // Construct the expected canonical URL based on your structure
    $canonical_url = home_url("/{$location}/blog/{$blog}");

    // Output the canonical tag
    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />';
}
add_action('wp_head', 'inject_canonical_in_head');

// Get WordPress header
get_header();

// Get query vars from URL (location and blog)
$location = get_query_var('location_slug'); // The location part of the URL
$blog = get_query_var('blog_slug');   // The blog part of the URL

// Query the 'blog-location' post type based on the blog slug
$args = array(
    'post_type' => 'blog-location',
    'name' => $blog,  // Query using the blog slug
    'posts_per_page' => 1,
);
$query = new WP_Query($args);

// Check if the post exists
if ($query->have_posts()):
    while ($query->have_posts()):
        $query->the_post();
        $title = get_the_title();
        $meta_description = get_field('short_description');
        $date = get_field('date');
        $detail_image = get_field('detail_image');
        $blog_id = get_the_ID();
        $blog_taxonomy_terms = get_the_terms($blog_id, 'blog-locations-category');
        $blog_taxonomy_names = $blog_taxonomy_terms && !is_wp_error($blog_taxonomy_terms)
            ? wp_list_pluck($blog_taxonomy_terms, 'name')
            : [];
        $related_blogs = get_posts(array(
            'post_type' => 'blog-location',
            'posts_per_page' => 3,
            'meta_query' => array(
                array(
                    'key' => 'related_location',
                    'value' => $location_id ?? null,
                    'compare' => 'LIKE',
                ),
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'blog-locations-category',
                    'field' => 'slug',
                    'terms' => $blog_taxonomy_names,
                ),
            ),
        ));
        ?>
        <main id="brx-content">
            <section id="brxe-qtnvyr" class="brxe-section section">
                <div id="brxe-vfxbmv" class="brxe-container padding-global">
                    <div id="brxe-xkhhqq" class="brxe-block section-component">
                        <div id="brxe-aylowy" class="brxe-block">
                            <div id="brxe-oetttv" class="brxe-block">
                                <div id="brxe-fnuccz" class="brxe-text-basic text-size-regular font-weight-semibold"
                                    data-animi="up" data-duration="0.8" data-delay="0.1">
                                    <?php echo $date; ?>
                                </div>
                                <h1 id="brxe-auakvt" class="brxe-post-title post-title" data-animi="up" data-delay="0.2"
                                    data-duration="0.8">
                                    <?php echo the_title(); ?>
                                </h1>
                                <span class="blog-breadcrumbs">
                                    <a href="<?php echo home_url("/{$location}/"); ?>">
                                        <?php echo esc_html(ucwords(str_replace(['-', '_'], ' ', $location))); ?>
                                    </a>
                                    &gt; 
                                    <a href="<?php echo home_url("/{$location}/blog"); ?>">Blog</a>
                                    &gt; 
                                    <?php the_title(); ?>
                                </span>
                                <?php if (!empty($blog_taxonomy_names)): ?>
                                    <div id="brxe-jdbrss" class="brxe-div tag" data-animi="up" data-delay="0.3" data-duration="0.8">
                                        <div id="brxe-mobink" class="brxe-text-basic text-size-regular font-weight-semibold">
                                            <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <img width="1024" height="768" src="<?php echo $detail_image; ?>"
                                class="brxe-image image-cover css-filter size-large" alt="" id="brxe-xfdqun" />
                        </div>
                        <div id="brxe-njlfxv" class="brxe-block" data-animi="up" data-duration="0.8" data-delay="0.5">
                            <div id="brxe-nvrpzd" class="brxe-text blog-rich-text">
                                <?php 
    $content = apply_filters('the_content', get_the_content());
    
    // Define the CTA HTML
    $cta_html = '
    <div class="post-cta-inline" style="margin: 40px 0; padding: 30px; background-color: #f8f9f8; border-radius: 8px; text-align: center; border: 1px solid #e0e0e0;">
        <h3 style="margin-bottom: 20px;">Ready to start your insulation project?</h3>
        <div class="brxe-div btn is-no-icon" 
             style="display: inline-block; cursor: pointer;"
             data-interactions=\'[{"id":"pigxzw","trigger":"click","action":"show","target":"popup","templateId":"4865"}]\' 
             data-interaction-id="9f6f9b">
            <div class="brxe-text-basic">Get a Free Estimate</div>
        </div>
    </div>';

    // Explode content by paragraph tags
    $paragraphs = explode('</p>', $content);
    
    foreach ($paragraphs as $index => $paragraph) {
        // Only echo the closing tag if it wasn't the last empty element
        if (trim($paragraph)) {
            echo $paragraph . '</p>';
        }

        // Inject CTA after the 3rd paragraph (index 2)
        if ($index === 2) {
            echo $cta_html;
        }
    }

    // If post is shorter than 3 paragraphs, ensure CTA still shows at the bottom
    if (count($paragraphs) < 4) {
        echo $cta_html;
    }
    ?>
                            </div>
                        </div>
                        <!-- Author Box -->
                        <?php
                         $author_id = get_the_author_meta('ID');
                            $author_img = get_field('author_image', 'user_' . $author_id);
                            $author_job = get_field('job_title', 'user_' . $author_id);
                            $author_loc = get_field('author_location_name', 'user_' . $author_id);
                            $author_bio = get_the_author_meta('description');

                            if ($author_img): // Only show if custom author data exists
                            ?>
                            <div class="author-card-container">
                                <div class="author-image-column">
                                    <div class="author-image-border">
                                        <img src="<?php echo esc_url($author_img['url']); ?>" alt="<?php echo get_the_author(); ?>">
                                    </div>
                                </div>
                                <div class="author-info-column">
                                    <!-- <div class="author-quote-icon">W</div> -->
                                    <div class="author-bio-text">
                                        <?php echo wpautop($author_bio); ?>
                                    </div>
                                    <div class="author-meta-footer">
                                        <p class="author-name"><?php the_author(); ?>,</p>
                                        <?php if($author_job): ?><p class="author-job"><?php echo esc_html($author_job); ?></p><?php endif; ?>
                                        <?php if($author_loc): ?><p class="author-location"><?php echo esc_html($author_loc); ?></p><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php if (!empty($related_blogs)): ?>
                <section id="brxe-hhlaev" class="brxe-section section">
                    <div id="brxe-cquptg" class="brxe-container padding-global">
                        <div id="brxe-ocjqys" class="brxe-block section-component">
                            <div id="brxe-lpybpq" class="brxe-block">
                                <div id="brxe-xrqnss" class="brxe-div" data-animi="up" data-duration="0.8">
                                    <h2 id="brxe-pcobev" class="brxe-heading heading-style-h2">
                                        Related
                                    </h2>
                                    <h2 id="brxe-dycqnp" class="brxe-heading heading-style-h2">
                                        <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                                    </h2>
                                </div>
                                <div id="brxe-ujgpmz" class="brxe-block brx-grid">
                                    <ul class="related-blogs-list">
                                        <?php foreach ($related_blogs as $blog): ?>
                                            <?php
                                            $blog_id = $blog->ID; // Corrected variable from $related_blog to $blog
                                            $blog_title = get_the_title($blog_id);
                                            $blog_date = get_field('date', $blog_id);
                                            $blog_link = get_permalink($blog_id);
                                            $blog_image = get_field('thumbnail_image', $blog_id);
                                            $blog_taxonomy_terms = get_the_terms($blog_id, 'blog-locations-category');
                                            $blog_taxonomy_names = $blog_taxonomy_terms && !is_wp_error($blog_taxonomy_terms)
                                                ? wp_list_pluck($blog_taxonomy_terms, 'name')
                                                : [];
                                            ?>
                                            <li class="brxe-rsltzq brxe-block list-item" data-animi="up" data-duration="0.6"
                                                data-delay="0.1">
                                                <div class="brxe-njcspf brxe-block">
                                                    <?php if (!empty($blog_taxonomy_names)): ?>
                                                        <div class="brxe-cpkaid brxe-div tag">
                                                            <div class="brxe-wmerpm brxe-text-basic">
                                                                <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($blog_image): ?>
                                                        <img width="936" height="857" src="<?php echo $blog_image; ?>"
                                                            class="brxe-mlzwhd brxe-image image-cover css-filter size-large"
                                                            alt="<?php echo esc_attr($blog_title); ?>" />
                                                    <?php endif; ?>
                                                </div>
                                                <div class="brxe-skdklv brxe-text-basic text-size-regular font-weight-semibold">
                                                    <?php echo $blog_date; ?>
                                                </div>
                                                <h1 class="brxe-fymnnd brxe-post-title heading-style-h6 font-weight-medium">
                                                    <a href="<?php echo esc_url($blog_link); ?>"><?php echo $blog_title; ?></a>
                                                </h1>
                                                <a href="<?php echo esc_url($blog_link); ?>" class="brxe-iunsti brxe-div btn-secondary">
                                                    <div class="brxe-abrxvm brxe-text-basic">Read More</div>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
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
    wp_reset_postdata();
else:
    render_fallback_content();
endif;

// Get WordPress footer
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