<?php
/**
 * Plugin Name: Koala Boulder mice post redirect
 * Description: Redirect the legacy Boulder spray foam and mice article URL.
 */

if (!defined('ABSPATH') || get_option('siteurl') !== 'https://koalainsulation.com') {
    return;
}

add_action('template_redirect', static function () {
    $request = wp_parse_url($_SERVER['REQUEST_URI'] ?? '');
    $slug = 'does-spray-foam-insulation-keep-mice-out-pros-cons-and-best-practic';
    $legacy = '/boulder/blog-detail/' . $slug;

    if (!in_array($request['path'] ?? '', array($legacy, $legacy . '/'), true)) {
        return;
    }

    $destination = home_url('/boulder/blog/' . $slug);
    if (!empty($request['query'])) {
        $destination .= '?' . $request['query'];
    }

    if (wp_safe_redirect($destination, 301, 'Koala Boulder legacy blog')) {
        exit;
    }
}, 0);
