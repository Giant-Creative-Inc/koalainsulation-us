<?php
/**
 * Plugin Name: Koala Boulder FAQ sitemap
 * Description: Include the routed Boulder FAQ page in the US Yoast page sitemap.
 */

if (!defined('ABSPATH') || get_option('siteurl') !== 'https://koalainsulation.com') {
    return;
}

add_filter('wpseo_sitemap_post_type_first_links', static function ($links, $post_type) {
    if ($post_type === 'page' && get_page_by_path('boulder', OBJECT, 'location')?->post_status === 'publish') {
        $links[] = array('loc' => home_url('/boulder/faq'));
    }

    return $links;
}, 10, 2);
