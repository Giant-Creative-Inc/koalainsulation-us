<?php
// Get the linked post from the relationship field
$linked_post = get_field('linked_resource');

if ($linked_post) {
    $redirect_url = get_permalink($linked_post[0]);
    wp_redirect($redirect_url, 301);
    exit;
} else {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    include get_query_template('404');
    exit;
}