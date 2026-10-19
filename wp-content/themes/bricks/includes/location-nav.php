<?php
/**
 * Server-side location header/footer personalization.
 *
 * Resolve the current location from the request and render location-aware
 * header/footer navigation before the response reaches the browser.
 *
 * The footer phone text remains client-rendered follow-up work.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', 'koala_output_server_menu_state', 3);
function koala_output_server_menu_state()
{
    $location = koala_current_location_post();
    if (!$location) {
        return;
    }

    $data = get_location_data(array('slug' => $location->post_name));
    $has_blog = !is_wp_error($data) && !empty($data['blogPage']) && is_array($data['blogPage']);
    ?>
    <style id="koala-server-menu-state">
        #default-nav-box,
        #main-nav-services,
        #main-ft-services,
        #nav-location,
        #ft-location,
        #franchise-nav-link,
        #franchise-ft-link,
        #ft-national-partnerships,
        #brxe-vgmgfj {
            display: none !important;
            visibility: hidden !important;
        }

        #location-nav-box {
            display: flex !important;
            visibility: visible !important;
        }

        #custom-service {
            display: block !important;
            visibility: visible !important;
        }

        #custom-service-footer {
            display: flex !important;
            visibility: visible !important;
        }

        <?php if (!$has_blog) : ?>
        #blog-link-nav,
        #blog-link-footer {
            display: none !important;
            visibility: hidden !important;
        }
        <?php endif; ?>
    </style>
    <?php
}

/**
 * Normalize a location relationship value to a published location post.
 */
function koala_normalize_location_post($value)
{
    if (is_array($value)) {
        $value = reset($value);
    }

    if ($value instanceof WP_Post) {
        $location = $value;
    } elseif (is_numeric($value)) {
        $location = get_post((int) $value);
    } elseif (is_string($value) && $value !== '') {
        $location = get_page_by_path(sanitize_title($value), OBJECT, 'location');
    } else {
        return null;
    }

    if (!($location instanceof WP_Post) || $location->post_type !== 'location' || $location->post_status !== 'publish') {
        return null;
    }

    return $location;
}

/**
 * Resolve the location represented by the current request.
 *
 * Custom location routes expose location_slug. Shared location pages use the
 * first URL segment (for example /central-atlanta/why-koala). Relationship
 * metadata is retained as a final fallback for singular custom posts.
 */
function koala_current_location_post()
{
    static $resolved = false;
    static $location = null;

    if ($resolved) {
        return $location;
    }

    $resolved = true;

    $query_slug = sanitize_title((string) get_query_var('location_slug'));
    if ($query_slug !== '') {
        $location = koala_normalize_location_post($query_slug);
        if ($location) {
            return $location;
        }
    }

    if (is_singular('location')) {
        $location = koala_normalize_location_post(get_queried_object());
        if ($location) {
            return $location;
        }
    }

    $request_path = isset($_SERVER['REQUEST_URI'])
        ? (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH)
        : '';
    $segments = array_values(array_filter(explode('/', trim($request_path, '/'))));

    if (!empty($segments[0])) {
        $location = koala_normalize_location_post($segments[0]);
        if ($location) {
            return $location;
        }
    }

    $queried = get_queried_object();
    if ($queried instanceof WP_Post) {
        $relationship_keys = array(
            'related_location',
            'wk_related_location',
            'wr_related_location',
            'ho_related_location',
            'rl_related_location',
            'blog_related_location',
        );

        foreach ($relationship_keys as $meta_key) {
            $location = koala_normalize_location_post(get_post_meta($queried->ID, $meta_key, true));
            if ($location) {
                return $location;
            }
        }
    }

    return null;
}

/**
 * Location URL and services used by the header and footer shortcodes.
 */
function koala_current_location_services()
{
    $location = koala_current_location_post();

    if (!$location) {
        return array('base' => '', 'services' => array());
    }

    $data = get_location_data(array('slug' => $location->post_name));
    $services = (!is_wp_error($data) && !empty($data['services']) && is_array($data['services']))
        ? $data['services']
        : array();

    return array(
        'base'     => untrailingslashit(get_permalink($location->ID)),
        'services' => $services,
    );
}

function koala_current_location_resources()
{
    $location = koala_current_location_post();
    if (!$location) {
        return array();
    }

    $data = get_location_data(array('slug' => $location->post_name));
    if (is_wp_error($data) || empty($data['rlPage']) || !is_array($data['rlPage'])) {
        return array();
    }

    $base = untrailingslashit(get_permalink($location->ID));
    $links = array();
    foreach ($data['rlPage'] as $resource) {
        $terms = !empty($resource['terms']) && is_array($resource['terms']) ? $resource['terms'] : array();
        if (in_array('Areas served', $terms, true)) {
            continue;
        }

        $link = isset($resource['link']) ? $resource['link'] : '';
        $title = isset($resource['title']) ? $resource['title'] : '';
        $target = '';
        if (in_array('Meet the team', $terms, true)) {
            $title = 'Meet Koala Insulation of ' . get_the_title($location->ID);
            $link = $base . '/meet-the-team';
        } elseif (in_array('Recent Projects', $terms, true)) {
            $title = 'Recent Projects of ' . get_the_title($location->ID);
            $link = $base . '/recent-projects';
        } elseif (in_array('Careers', $terms, true)) {
            $title = 'Careers';
            $link = $base . '/careers';
            $target = '_blank';
        }

        if ($title !== '' && $link !== '') {
            $links[] = array('title' => $title, 'link' => $link, 'target' => $target);
        }
    }
    return $links;
}

function koala_location_has_resource_term($term_name)
{
    $location = koala_current_location_post();
    if (!$location) {
        return false;
    }

    $data = get_location_data(array('slug' => $location->post_name));
    if (is_wp_error($data) || empty($data['rlPage']) || !is_array($data['rlPage'])) {
        return false;
    }

    foreach ($data['rlPage'] as $resource) {
        if (!empty($resource['terms']) && is_array($resource['terms']) && in_array($term_name, $resource['terms'], true)) {
            return true;
        }
    }

    return false;
}

add_filter('bricks/frontend/render_data', 'koala_location_menu_links_server_side', 20, 3);
function koala_location_menu_links_server_side($content, $post, $area) {
    if (!in_array($area, array('header', 'footer'), true)) {
        return $content;
    }
    $location = koala_current_location_post();
    if (!$location) {
        return $content;
    }
    $base = untrailingslashit(get_permalink($location->ID));
    if (!$base) {
        return $content;
    }

    $map = array(
        'why-koala-link'             => $base . '/why-koala',
        'why-koala-link-footer'      => $base . '/why-koala',
        'why-reinsulate-link'        => $base . '/why-reinsulate',
        'why-reinsulate-link-footer' => $base . '/why-reinsulate',
        'homeowner-link'             => $base . '/homeowner-incentives',
        'homeowner-link-footer'      => $base . '/homeowner-incentives',
        'local-home-savings-btn'     => $base . '/homeowner-incentives',
        'faq-nav-link'               => $base . '/faq',
        'faq-ft-link'                => $base . '/faq',
        'testimonials-nav-link'      => $base . '/testimonials',
        'testimonials-ft-link'       => $base . '/testimonials',
        'blog-link-nav'              => $base . '/blog',
        'blog-link-footer'           => $base . '/blog',
        'nav-home'                   => $base,
        'ft-home'                    => $base,
        'ft-privacy-policy'          => $base . '/privacy-policy',
        'ft-terms-and-conditions'    => $base . '/terms-and-conditions',
        'ft-services'                => $base . '/services',
    );

    foreach ($map as $id => $url) {
        // The id attribute appears before href within the same tag in Bricks
        // output; rewrite only that element's href (first match per id).
        $content = preg_replace(
            '/(\bid="' . preg_quote($id, '/') . '"[^>]*?\shref=")[^"]*(")/',
            '${1}' . esc_url($url) . '${2}',
            $content,
            1
        );
    }

    if ($area === 'header') {
        $content = preg_replace(
            '/(<li\b[^>]*\bid="brxe-vgmgfj"[^>]*)(>)/i',
            '$1 style="display:none"$2',
            $content,
            1
        );
    }

    return $content;
}

function display_location_resources()
{
    $location = koala_current_location_post();
    if (!$location) {
        return '';
    }

    $base = untrailingslashit(get_permalink($location->ID));
    $resources = koala_current_location_resources();
    $links = array(
        array('title' => 'Testimonials', 'link' => $base . '/testimonials', 'target' => ''),
    );

    if (koala_location_has_resource_term('Areas served')) {
        $links[] = array('title' => 'Areas Served', 'link' => $base . '/areas-served', 'target' => '');
    }

    $links[] = array('title' => 'Blog', 'link' => $base . '/blog', 'target' => '');
    $links[] = array('title' => 'Homeowner Incentives', 'link' => $base . '/homeowner-incentives', 'target' => '');
    $links = array_merge($links, $resources);
    $links[] = array('title' => 'FAQ', 'link' => $base . '/faq', 'target' => '');

    ob_start();
    ?>
    <div id="custom-resources" class="brxe-dropdown nav-dropdown location-menu-dropdown">
        <div class="brx-submenu-toggle">
            <label class="location-menu-summary" for="custom-resources-toggle">
                <span>Resources</span>
                <span class="location-menu-chevron" aria-hidden="true">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M8.99998 9.87855L12.7123 6.16626L13.773 7.22692L8.99998 11.9999L4.22705 7.22692L5.28771 6.16626L8.99998 9.87855Z" fill="white"></path>
                </svg>
                </span>
            </label>
        </div>
        <input id="custom-resources-toggle" class="location-menu-toggle" type="checkbox" aria-label="Toggle Resources dropdown">
        <ul id="custom-resources-ul" class="brxe-div dropdown-content brx-dropdown-content">
            <?php foreach ($links as $link) : ?>
                <li class="menu-item">
                    <a class="brxe-text-link nav-nested-link" href="<?php echo esc_url($link['link']); ?>"<?php echo $link['target'] === '_blank' ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html($link['title']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('location_resources', 'display_location_resources');

function display_location_services()
{
    if (!koala_current_location_post()) {
        return '';
    }

    $context  = koala_current_location_services();
    $base     = $context['base'];
    $services = $context['services'];

    ob_start();
    ?>
    <div id="custom-service" class="brxe-dropdown nav-dropdown location-menu-dropdown">
        <div class="brx-submenu-toggle">
            <?php if ($base) : ?>
                <a href="<?php echo esc_url($base . '/services'); ?>">Services</a>
            <?php else : ?>
                <span>Services</span>
            <?php endif; ?>
            <label class="location-menu-summary location-menu-chevron" for="custom-service-toggle" aria-label="Toggle Services dropdown">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M8.99998 9.87855L12.7123 6.16626L13.773 7.22692L8.99998 11.9999L4.22705 7.22692L5.28771 6.16626L8.99998 9.87855Z" fill="white"></path>
                </svg>
            </label>
        </div>
        <input id="custom-service-toggle" class="location-menu-toggle" type="checkbox" aria-label="Toggle Services dropdown">
        <ul id="custom-service-ul" class="brxe-div dropdown-content brx-dropdown-content">
            <?php foreach ($services as $service) : ?>
                <li class="menu-item">
                    <div class="brxe-xvkivb brxe-div">
                        <a class="brxe-yttxdw brxe-text-link nav-nested-link" href="<?php echo esc_url($service['link']); ?>"><?php echo esc_html($service['title']); ?></a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('location_services', 'display_location_services');

function display_location_services_footer()
{
    if (!koala_current_location_post()) {
        return '';
    }

    $services = koala_current_location_services()['services'];

    ob_start();
    ?>
    <div id="custom-service-footer" class="brxe-div footer-links-wrapper">
        <?php foreach ($services as $service) : ?>
            <div class="brxe-sawnmd brxe-div">
                <a class="brxe-znrqhw brxe-text-basic footer-inner-link" href="<?php echo esc_url($service['link']); ?>"><?php echo esc_html($service['title']); ?></a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('location_services_footer', 'display_location_services_footer');
