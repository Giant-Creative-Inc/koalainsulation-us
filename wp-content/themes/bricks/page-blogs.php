<?php
/* Template Name: Blogs Page */

// Get the current URL to extract the location slug
// $current_url = $_SERVER['REQUEST_URI'];
$current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// $location_slug = str_replace('/blog', '', $current_url); // Remove '/blogs' from the URL to get the location slug
$location_slug = trim(str_replace('/blog', '', $current_url), '/');

// Query the Location post by slug
$location = get_page_by_path($location_slug, OBJECT, 'location'); // Replace 'location' with your custom post type slug for location


if ($location) {
    // Get the location ID
    $location_id = $location->ID;

    // Query for "Location Blogs" posts related to the current location
    $args = array(
        'post_type' => 'location-blog', // The custom post type slug for "Location Blogs"
        'posts_per_page' => 1, // Just need one for meta
        'meta_query' => array(
            array(
                'key' => 'blog_related_location', // The ACF relationship field key in "Location Blogs"
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
        'post_type' => 'location-blog', 
        'posts_per_page' => 1, 
        'meta_query' => array(
            array(
                'key' => 'blog_related_location', 
                'value' => $location_id,
                'compare' => 'LIKE',
            ),
        ),
    ));

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();

            //Get related blogs
            $related_blogs = get_field('related_blogs');
            ?>
            <main id="brx-content">
                <section id="brxe-qsncig" class="brxe-section section">
                    <div id="brxe-hhujvy" class="brxe-container padding-global">
                        <div id="brxe-ixtyah" class="brxe-block">
                            <div id="brxe-neullm" class="brxe-block section-component">
                                <div id="brxe-eooglh" class="brxe-block">
                                    <div id="brxe-nkcsce" class="brxe-block">
                                        <h1 id="brxe-vruvdg" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                            data-animi="up" data-duration="0.6" data-delay="0.2">
                                            <?php the_title(); ?>
                                        </h1>
                                        <div id="brxe-hfttqe" class="brxe-text-basic text-size-regular text-color-mute"
                                            data-animi="up" data-duration="0.6" data-delay="0.3">
                                            Check out our latest blog posts for easy tips, advice, and
                                            insights on insulation and saving energy for your home.
                                        </div>
                                    </div>
                                    <div id="brxe-phwril" class="brxe-div" data-animi="up" data-duration="0.8">
                                        <div id="brxe-rfevns" data-script-id="rfevns" class="brxe-code">
                                            <div class="blog-filter-container">
                                                <input id="brxe-qbhwxm" class="brxe-filter-search filter-search" value=""
                                                    data-brx-filter="true" name="s" placeholder="Search" aria-label="Search"
                                                    type="search" autocomplete="off" spellcheck="false" />
                                                <!-- <ul id="brxe-jsqojz" class="brxe-filter-radio" data-brx-filter="true" data-mode="button">
                          <li>
                            <label class="brx-input-radio-option-empty depth-0"><input type="radio" name="category" value=""
                                checked="" /><span class="bricks-button">All</span></label>
                          </li>
                          <li>
                            <label class="depth-0"><input type="radio" name="category" value="articles" /><span
                                class="bricks-button">Articles</span></label>
                          </li>
                          <li>
                            <label class="depth-0"><input type="radio" name="category" value="news" /><span
                                class="bricks-button">News</span></label>
                          </li>
                          <li>
                            <label class="depth-0"><input type="radio" name="category" value="press" /><span
                                class="bricks-button">Press</span></label>
                          </li>
                        </ul> -->
                                            </div>
                                        </div>
                                        <?php
                                        // Retrieve all terms from the 'blog-category' taxonomy
                                        $blog_categories = get_terms([
                                            'taxonomy' => 'blog-locations-category',
                                            'hide_empty' => false, // Set to true to hide terms with no posts
                                        ]);
                                        if (!is_wp_error($blog_categories) && !empty($blog_categories)): ?>
                                            <ul id="brxe-jsqojz" class="brxe-filter-radio hide" data-brx-filter="true"
                                                data-mode="button">
                                                <!-- Add the "All" option -->
                                                <li>
                                                    <label class="brx-input-radio-option-empty depth-0">
                                                        <input type="radio" name="category" value="" checked="" />
                                                        <span class="bricks-button active">All</span>
                                                    </label>
                                                </li>
                                                <?php foreach ($blog_categories as $category): ?>
                                                    <li>
                                                        <label class="depth-0">
                                                            <input type="radio" name="category"
                                                                value="<?php echo esc_attr($category->slug); ?>" />
                                                            <span class="bricks-button"><?php echo esc_html($category->name); ?></span>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p>No categories found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div id="brxe-hakplf" class="brxe-block brx-grid">
                                    <?php if ($related_blogs): ?>
                                        <?php foreach ($related_blogs as $blog): ?>
                                            <?php
                                            $blog_id = $blog->ID;
                                            $blog_title = get_the_title($blog_id);
                                            $blog_date = get_field('date', $blog_id);
                                            $blog_link = get_permalink($blog_id);
                                            $blog_image = get_field('thumbnail_image', $blog_id);
                                            $blog_taxonomy_terms = get_the_terms($blog_id, 'blog-locations-category');
                                            $blog_taxonomy_names = $blog_taxonomy_terms && !is_wp_error($blog_taxonomy_terms)
                                                ? wp_list_pluck($blog_taxonomy_terms, 'name')
                                                : [];
                                            ?>
                                            <li class="brxe-bhgzqc brxe-div" data-animi="up" data-duration="0.6">
                                              <?php $blog_image_url = get_field('thumbnail_image', $blog_id);
                                                if ($blog_image_url) { ?>
                                                  <div class="brxe-pabbtz brxe-block"><?php
                                                    $img_id = attachment_url_to_postid($blog_image_url); // works if URL is in Media Library

                                                    if ($img_id) {
                                                        // Full WP markup with srcset, sizes, loading="lazy", width/height
                                                        echo wp_get_attachment_image(
                                                          $img_id,
                                                          'large',
                                                          false,
                                                          array(
                                                            'class' => 'brxe-ygbmyt brxe-image image-cover css-filter size-large',
                                                            'alt'   => esc_attr($blog_title),
                                                            'decoding' => 'async',
                                                          )
                                                        );
                                                    } else {
                                                        // Fallback if it’s not a library image (external URL, etc.)
                                                        ?>
                                                        <img
                                                          src="<?php echo esc_url($blog_image_url); ?>"
                                                          alt="<?php echo esc_attr($blog_title); ?>"
                                                          class="brxe-ygbmyt brxe-image image-cover css-filter size-large"
                                                          decoding="async"
                                                          width="600"
                                                          height="400"
                                                          loading="lazy"
                                                        />
                                                        <?php
                                                    } ?>
                                                  </div>
                                                <?php } ?>
                                                <div class="brxe-swwkfr brxe-block">
                                                    <div class="brxe-mhbrou brxe-block">
                                                        <div class="brxe-pojbhz brxe-text-basic text-size-regular">
                                                            <?php echo $blog_date; ?>
                                                        </div>
                                                        <h5 class="brxe-lhjnhb brxe-post-title heading-style-h5">
                                                            <a href="<?php echo $blog_link; ?>">
                                                                <?php echo $blog_title; ?></a>
                                                        </h5>
                                                    </div>
                                                    <a href="<?php echo $blog_link; ?>" class="brxe-fmfqwh brxe-div btn-secondary">
                                                        <div class="brxe-wbymrh brxe-text-basic">Read More</div>
                                                    </a>
                                                    <div class="brxe-ehypiw brxe-div tag bricks-lazy-hidden">
                                                        <div class="brxe-xuqski brxe-text-basic">
                                                            <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No blogs found for this location.</p>
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
            </main>
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const searchInput = document.getElementById("brxe-qbhwxm");
                    const categoryButtons = document.querySelectorAll('input[name="category"]');
                    const items = document.querySelectorAll("#brxe-hakplf .brxe-bhgzqc");

                    // Function to filter items
                    const filterItems = () => {
                        const searchTerm = searchInput.value.toLowerCase();
                        const selectedCategory = document.querySelector(
                            'input[name="category"]:checked'
                        ).value;

                        items.forEach((item) => {
                            const title = item
                                .querySelector(".brxe-lhjnhb")
                                .textContent.toLowerCase();
                            const category = item
                                .querySelector(".brxe-xuqski a")
                                .textContent.toLowerCase();

                            // Check if the item matches the search term and selected category
                            const matchesSearch = title.includes(searchTerm);
                            const matchesCategory =
                                selectedCategory === "" || category === selectedCategory;

                            // Show or hide the item based on the filter conditions
                            if (matchesSearch && matchesCategory) {
                                item.style.display = "flex";
                            } else {
                                item.style.display = "none";
                            }
                        });
                    };

                    // Function to update active class on radio buttons
                    const updateActiveClass = () => {
                        categoryButtons.forEach((button) => {
                            const parentLabel = button.closest("label");
                            if (button.checked) {
                                parentLabel.querySelector(".bricks-button").classList.add("active");
                            } else {
                                parentLabel
                                    .querySelector(".bricks-button")
                                    .classList.remove("active");
                            }
                        });
                    };

                    // Event listener for search input
                    searchInput.addEventListener("input", filterItems);

                    // Event listener for radio buttons
                    categoryButtons.forEach((button) => {
                        button.addEventListener("change", () => {
                            updateActiveClass();
                            filterItems();
                        });
                    });

                    // Initialize filtering and active class on page load
                    updateActiveClass();
                    filterItems();
                });
            </script>

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
    
    $blogs = get_posts(array(
        'post_type' => 'blog-article',
        'posts_per_page' => 9,
    ));

    $featured_blog = get_posts(array(
        'post_type' => 'blog-article',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'featured',
                'value' => true,
                'compare' => '=',
            ),
        ),
    ));

    $location_blog_pages = get_posts(array(
        'post_type' => 'location-blog',
        'posts_per_page' => -1,
    ));
    ?>
    <main id="brx-content">
        <section id="brxe-qsncig" class="brxe-section section">
            <div id="brxe-hhujvy" class="brxe-container padding-global">
                <div id="brxe-ixtyah" class="brxe-block">
                    <div id="brxe-neullm" class="brxe-block section-component">
                        <div id="brxe-eooglh" class="brxe-block">
                            <div id="brxe-nkcsce" class="brxe-block">
                                <h1 id="brxe-vruvdg" class="brxe-heading heading-style-h1 font-weight-bold text-allcaps"
                                    data-animi="up" data-duration="0.6" data-delay="0.2">
                                    Blog
                                </h1>
                                <div id="brxe-hfttqe" class="brxe-text-basic text-size-regular text-color-mute"
                                    data-animi="up" data-duration="0.6" data-delay="0.3">
                                    Check out our latest blog posts for easy tips, advice, and
                                    insights on insulation and saving energy for your home.
                                </div>
                            </div>
                            <div id="brxe-phwril" class="brxe-div" data-animi="up" data-duration="0.8">
                                <div id="brxe-rfevns" data-script-id="rfevns" class="brxe-code">
                                    <div class="blog-filter-container">
                                        <input id="brxe-qbhwxm" class="brxe-filter-search filter-search" value=""
                                            data-brx-filter="true" name="s" placeholder="Search" aria-label="Search"
                                            type="search" autocomplete="off" spellcheck="false" />
                                        <!-- <ul id="brxe-jsqojz" class="brxe-filter-radio" data-brx-filter="true" data-mode="button">
                      <li>
                        <label class="brx-input-radio-option-empty depth-0"><input type="radio" name="category" value=""
                            checked="" /><span class="bricks-button">All</span></label>
                      </li>
                      <li>
                        <label class="depth-0"><input type="radio" name="category" value="articles" /><span
                            class="bricks-button">Articles</span></label>
                      </li>
                      <li>
                        <label class="depth-0"><input type="radio" name="category" value="news" /><span
                            class="bricks-button">News</span></label>
                      </li>
                      <li>
                        <label class="depth-0"><input type="radio" name="category" value="press" /><span
                            class="bricks-button">Press</span></label>
                      </li>
                    </ul> -->
                                    </div>
                                </div>
                                <?php
                                // Retrieve all terms from the 'blog-category' taxonomy
                                $blog_categories = get_terms([
                                    'taxonomy' => 'category',
                                    'hide_empty' => false, // Set to true to hide terms with no posts
                                ]);
                                if (!is_wp_error($blog_categories) && !empty($blog_categories)): ?>
                                    <ul id="brxe-jsqojz" class="brxe-filter-radio hide" data-brx-filter="true"
                                        data-mode="button">
                                        <!-- Add the "All" option -->
                                        <li>
                                            <label class="brx-input-radio-option-empty depth-0">
                                                <input type="radio" name="category" value="" checked="" />
                                                <span class="bricks-button active">All</span>
                                            </label>
                                        </li>
                                        <?php foreach ($blog_categories as $category): ?>
                                            <li>
                                                <label class="depth-0">
                                                    <input type="radio" name="category"
                                                        value="<?php echo esc_attr($category->slug); ?>" />
                                                    <span class="bricks-button"><?php echo esc_html($category->name); ?></span>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No categories found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                        if (!empty($featured_blog)) {
                            $featured_blog_post = $featured_blog[0];
                            $featured_blog_id = $featured_blog_post->ID;
                            $featured_blog_title = get_the_title($featured_blog_id);
                            $featured_blog_short_description = get_field('short_description_', $featured_blog_id);
                            $featured_blog_date = get_field('date', $featured_blog_id);
                            $featured_blog_link = get_permalink($featured_blog_id);
                            $featured_blog_image = get_field('thumbnail_image', $featured_blog_id);
                            $featured_blog_taxonomy_terms = get_the_terms($featured_blog_id, 'category');
                            $featured_blog_taxonomy_names = $featured_blog_taxonomy_terms && !is_wp_error($featured_blog_taxonomy_terms)
                                ? wp_list_pluck($featured_blog_taxonomy_terms, 'name')
                                : [];
                            ?>
                            <div class="brxe-wewujg brxe-block brx-grid featured-blog-card" data-animi="up" data-duration="0.8"
                                data-delay="0.5">
                              <?php $blog_image_url = get_field('thumbnail_image', $featured_blog_id);
                                if ($blog_image_url) { ?>
                                  <div class="brxe-pabbtz brxe-block"><?php
                                    $img_id = attachment_url_to_postid($blog_image_url); // works if URL is in Media Library

                                    if ($img_id) {
                                        // Full WP markup with srcset, sizes, loading="lazy", width/height
                                        echo wp_get_attachment_image(
                                          $img_id,
                                          'large',
                                          false,
                                          array(
                                            'class' => 'brxe-ygbmyt brxe-image image-cover css-filter size-large',
                                            'alt'   => esc_attr($blog_title),
                                            'decoding' => 'async',
                                          )
                                        );
                                    } else {
                                        // Fallback if it’s not a library image (external URL, etc.)
                                        ?>
                                        <img
                                          src="<?php echo esc_url($blog_image_url); ?>"
                                          alt="<?php echo esc_attr($blog_title); ?>"
                                          class="brxe-ygbmyt brxe-image image-cover css-filter size-large"
                                          decoding="async"
                                        />
                                        <?php
                                    } ?>
                                  </div>
                                <?php } ?>
                                <div class="brxe-vtfaqn brxe-block">
                                    <?php if (!empty($featured_blog_taxonomy_names)): ?>
                                        <div class="brxe-vqnzml brxe-div tag">
                                            <div class="brxe-jexpqc brxe-text-basic">
                                                <a><?php echo implode(', ', $featured_blog_taxonomy_names); ?></a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="brxe-delgkr brxe-text-basic text-size-regular is-all-caps">
                                        <?php echo $featured_blog_date; ?>
                                    </div>
                                    <h2 class="brxe-taywuf brxe-post-title heading-style-h2 is-all-caps">
                                        <a href="<?php echo $featured_blog_link; ?>">
                                            <?php echo $featured_blog_title; ?>
                                        </a>
                                    </h2>
                                    <div class="brxe-pogncl brxe-text-basic text-size-regular">
                                        <?php echo $featured_blog_short_description ?>
                                    </div>
                                    <a href="<?php echo $featured_blog_link; ?>" class="brxe-pxpzgr brxe-div btn-secondary">
                                        <div class="brxe-lghmey brxe-text-basic">Read More</div>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div id="brxe-hakplf" class="brxe-block brx-grid">
                            <?php if ($blogs): ?>
                                <?php foreach ($blogs as $blog): ?>
                                    <?php
                                    $blog_id = $blog->ID;
                                    $blog_title = get_the_title($blog_id);
                                    $blog_date = get_field('date', $blog_id);
                                    $blog_link = get_permalink($blog_id);
                                    $blog_image = get_field('thumbnail_image', $blog_id);
                                    $blog_taxonomy_terms = get_the_terms($blog_id, 'category');
                                    $blog_taxonomy_names = $blog_taxonomy_terms && !is_wp_error($blog_taxonomy_terms)
                                        ? wp_list_pluck($blog_taxonomy_terms, 'name')
                                        : [];
                                    ?>
                                    <li class="brxe-bhgzqc brxe-div" data-animi="up" data-duration="0.6">
                                      <?php $blog_image_url = get_field('thumbnail_image', $blog_id);
                                        if ($blog_image_url) { ?>
                                          <div class="brxe-pabbtz brxe-block"><?php
                                            $img_id = attachment_url_to_postid($blog_image_url); // works if URL is in Media Library

                                            if ($img_id) {
                                                // Full WP markup with srcset, sizes, loading="lazy", width/height
                                                echo wp_get_attachment_image(
                                                  $img_id,
                                                  'large',
                                                  false,
                                                  array(
                                                    'class' => 'brxe-ygbmyt brxe-image image-cover css-filter size-large',
                                                    'alt'   => esc_attr($blog_title),
                                                    'decoding' => 'async',
                                                  )
                                                );
                                            } else {
                                                // Fallback if it’s not a library image (external URL, etc.)
                                                ?>
                                                <img
                                                  src="<?php echo esc_url($blog_image_url); ?>"
                                                  alt="<?php echo esc_attr($blog_title); ?>"
                                                  class="brxe-ygbmyt brxe-image image-cover css-filter size-large"
                                                  decoding="async"
                                                />
                                                <?php
                                            } ?>
                                          </div>
                                        <?php } ?>
                                        <div class="brxe-swwkfr brxe-block">
                                            <div class="brxe-mhbrou brxe-block">
                                                <div class="brxe-pojbhz brxe-text-basic text-size-regular">
                                                    <?php echo $blog_date; ?>
                                                </div>
                                                <h5 class="brxe-lhjnhb brxe-post-title heading-style-h5">
                                                    <a href="<?php echo $blog_link; ?>">
                                                        <?php echo $blog_title; ?></a>
                                                </h5>
                                            </div>
                                            <a href="<?php echo $blog_link; ?>" class="brxe-fmfqwh brxe-div btn-secondary">
                                                <div class="brxe-wbymrh brxe-text-basic">Read More</div>
                                            </a>
                                            <div class="brxe-ehypiw brxe-div tag bricks-lazy-hidden">
                                                <div class="brxe-xuqski brxe-text-basic">
                                                    <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No blogs found.</p>
                            <?php endif; ?>
                        </div>
                        <?php
                        if ($blogs) {
                            ?>
                            <div class="load-more-btn-wrapper">
                                <button id="load-more" class="load-more-btn">Load More</button>
                            </div>
                            <?php
                        }
                        ?>
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
            <?php if ($location_blog_pages): ?>
                <?php foreach ($location_blog_pages as $location_blog_page): ?>
                    <?php
                    $location_blog_page_id = $location_blog_page->ID;
                    $location_blog_page_link = get_permalink($location_blog_page_id);
                    $location_blog_page_name = get_the_title($location_blog_page_id);
                    ?>
                    <a href="<?php echo $location_blog_page_link ?>"><?php echo $location_blog_page_name ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No location blog page posts found.</p>
            <?php endif; ?>
        </div>
    </main>
    <script>
        jQuery(document).ready(function ($) {
            let ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
            let page = 2; // For pagination (Load More)
            let isSearching = false; // Flag to track if a search is active

            // Function to load blogs (initial, search, or load more)
            function loadBlogs(data, append = false) {
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        if (append) {
                            // Append for "Load More"
                            $('#brxe-hakplf').append(response);
                        } else {
                            // Replace for initial load or search
                            $('#brxe-hakplf').html(response);
                            page = 2; // Reset page number
                        }

                        // Hide "Load More" button during search or when no more blogs are available
                        if (isSearching || !response.trim() || response.trim().includes('No blogs found.')) {
                            $('#load-more').hide();
                        } else {
                            $('#load-more').show();
                        }
                    },
                    error: function () {
                        alert('Error loading blogs. Please try again.');
                    },
                });
            }

            // Load initial blogs on page load
            loadBlogs({ action: 'load_initial_blogs' });

            // Search blogs on input change
            $('#brxe-qbhwxm').on('keyup', function () {
                let searchQuery = $(this).val().trim();

                if (searchQuery === '') {
                    isSearching = false; // Not searching
                    loadBlogs({ action: 'load_initial_blogs' }); // Load initial blogs when input is cleared
                } else {
                    isSearching = true; // Searching
                    loadBlogs({ action: 'search_blogs', query: searchQuery });
                }
            });

            // Load more blogs on button click
            $('#load-more').on('click', function () {
                if (!isSearching) {
                    // Append results only when not searching
                    loadBlogs({ action: 'load_more_blogs', page: page }, true);
                    page++; // Increment page number
                }
            });
        });
    </script>
    <?php
}
?>