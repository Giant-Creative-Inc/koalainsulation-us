<?php
/**
 * Plugin Name: Koala Location Plugin
 * Description: Koala location plugin
 */

function koala_register_location_posttype()
{
    register_post_type('location', array('labels' => array('name' => 'Locations'), 'public' => true, 'rewrite' => 'affiliate', 'show_in_rest' => true));
}
add_action('init', 'koala_register_location_posttype');

function koala_location_remove_slug($post_link, $post)
{
    if ($post->post_type == "location" && $post->post_status == "publish") {

        // Get the Location region field value
        $location_region = get_post_meta($post->ID, 'location_region', true);

        // Check if Location region is Canada
        if ($location_region === 'Canada') {
            // Modify the post link for Canada
            $post_link = str_replace("/" . $post->post_type . '/', '/ca/', $post_link);
        } else {
            // Default modification for other locations
            $post_link = str_replace("/" . $post->post_type . '/', '/', $post_link);
        }
    }

    return $post_link;
}
add_filter('post_type_link', 'koala_location_remove_slug', 10, 2);


function koala_post_types_parse($query)
{

    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);

    if (count($uri_segments) == 3 && $query->is_main_query()) {

        if (!empty($query->query['attachment'])) {

            $args = array(
                'public' => true,
                '_builtin' => false
            );

            $post_types = get_post_types($args);
            // We don't want 'local_cities' being parsed so we are removing it from the list of post types
            unset($post_types['local_cities']);
            $post_types['page'] = 'page';
            $post_types['post'] = 'post';
            $query->set('post_type', $post_types);
        }

    } else {
        if (!$query->is_main_query() || 2 != count($query->query) || !isset($query->query['page'])) {
            return;
        }
        if (!empty($query->query['name'])) {
            $args = array(
                'public' => true,
                '_builtin' => false
            );

            $post_types = get_post_types($args);
            // We don't want 'local_cities' being parsed so we are removing it from the list of post types
            unset($post_types['local_cities']);
            $post_types['page'] = 'page';
            $post_types['post'] = 'post';

            $query->set('post_type', $post_types);
        }
    }

}
add_action('pre_get_posts', 'koala_post_types_parse');

//================
function koala_register_services_posttype()
{
    register_post_type('service', array(
        'labels' => array('name' => 'Services'),
        'public' => true,
        'rewrite' => array(
            'slug' => 'services',
        ),
        'show_in_rest' => true
    ));
}
add_action('init', 'koala_register_services_posttype');

function koala_register_location_services_posttype()
{
    register_post_type('location-service', array('labels' => array('name' => 'Location Services'), 'public' => true, 'rewrite' => array('slug' => 'affiliate'), 'show_in_rest' => true));
}
add_action('init', 'koala_register_location_services_posttype');

function register_custom_post_type_blog()
{
    register_post_type('blog-article', array(
        'labels' => array(
            'name' => 'Blog Articles'
        ),
        'public' => true,
        'rewrite' => array('slug' => 'blog'), // Permalink remains as 'blog'
    ));
}
add_action('init', 'register_custom_post_type_blog');

function koala_register_blog_location_posttype()
{
    register_post_type('blog-location', array('labels' => array('name' => 'Blog locations'), 'public' => true, 'rewrite' => 'affiliate', 'show_in_rest' => true));
}
add_action('init', 'koala_register_blog_location_posttype');

function koala_register_location_rl_posttype()
{
    register_post_type('resources-landing-pa', array('labels' => array('name' => 'Resources Landing Pages'), 'public' => true, 'rewrite' => 'affiliate', 'show_in_rest' => true));
}
add_action('init', 'koala_register_location_rl_posttype');

function koala_register_location_blog_posttype()
{
    register_post_type('location-blog', array('labels' => array('name' => 'Location Blogs'), 'public' => true, 'rewrite' => 'affiliate', 'show_in_rest' => true));
}
add_action('init', 'koala_register_location_blog_posttype');

function koala_location_service_remove_slug($post_link, $post)
{
    if ($post->post_type == "location-service" && $post->post_status == "publish") {
        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'related_location', true);
        $related_location_id = (is_array($related_location_ids) && !empty($related_location_ids)) ? $related_location_ids[0] : null;

        if ($related_location_id) {
            // Get the slug of the related location post
            $related_location_post = get_post($related_location_id);

            if ($related_location_post) {
                $related_location_slug = $related_location_post->post_name;

                //Get the custom field 'location_service_name' for the service name
                $service_name = get_post_meta($post->ID, 'location_service_name', true);
                $service_is_canada = get_post_meta($post->ID, 'is_canada', true);

                // Fallback to post_name if 'location_service_name' is empty
                if (empty($service_name)) {
                    $service_name = $post->post_name;
                }

                // Determine the correct URL structure based on is_canada field
                if ($service_is_canada == 1) {
                    $post_link = home_url('/ca/' . $related_location_slug . '/services/' . sanitize_title($service_name));
                } else {
                    $post_link = home_url('/' . $related_location_slug . '/services/' . sanitize_title($service_name));
                }
            }
        }
    }

    return $post_link;
}

add_filter('post_type_link', 'koala_location_service_remove_slug', 10, 2);

function koala_blog_location_remove_slug($post_link, $post)
{
    if ($post->post_type == "blog-location" && $post->post_status == "publish") {
        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'related_location', true);
        $related_location_id = (is_array($related_location_ids) && !empty($related_location_ids)) ? $related_location_ids[0] : null;

        if ($related_location_id) {
            // Get the slug of the related location post
            $related_location_post = get_post($related_location_id);

            if ($related_location_post) {
                $related_location_slug = $related_location_post->post_name;
                $post_link = home_url('/' . $related_location_slug . '/blog/' . $post->post_name);
            }
        }
    }

    return $post_link;
}

add_filter('post_type_link', 'koala_blog_location_remove_slug', 10, 2);

// function koala_location_resource_remove_slug($post_link, $post)
// {
//     if ($post->post_type == "resources-landing-pa" && $post->post_status == "publish") {
//         // Get the related location ID from the custom field
//         $related_location_ids = get_post_meta($post->ID, 'rl_related_location', true);
//         $related_location_id = $related_location_ids[0];

//         if ($related_location_id) {
//             // Get the slug of the related location post
//             $related_location_post = get_post($related_location_id);
//             $related_location_slug = $related_location_post->post_name;

//             // Replace the post type slug with the related location slug
//             $post_link = str_replace("/" . $post->post_type . '/', '/' . $related_location_slug . '/', $post_link);
//         }
//     }

//     return $post_link;
// }

// add_filter('post_type_link', 'koala_location_resource_remove_slug', 10, 2);
function koala_location_resource_modify_slug($post_link, $post)
{
    if ($post->post_type == "resources-landing-pa" && $post->post_status == "publish") {
        // Get the assigned terms from the taxonomy 'resources-page-type'
        $terms = get_the_terms($post->ID, 'resources-page-type');

        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'rl_related_location', true);

        if ($terms && !is_wp_error($terms) && is_array($related_location_ids) && !empty($related_location_ids)) {
            $taxonomy_slug = $terms[0]->slug;
            $related_location_id = $related_location_ids[0];

            // Get the related location post slug
            $related_location_post = get_post($related_location_id);
            $related_location_slug = $related_location_post ? $related_location_post->post_name : '';

            if ($related_location_slug) {
                // If the taxonomy is 'meet-the-team' or 'recent-projects', remove the post slug
                if (in_array($taxonomy_slug, ['meet-the-team', 'recent-projects'])) {
                    $post_link = home_url("/{$related_location_slug}/{$taxonomy_slug}");
                } else {
                    $post_link = home_url("/{$related_location_slug}/{$post->post_name}");
                }
            }
        }
    }

    return rtrim($post_link, '/'); // Remove trailing slash
}
add_filter('post_type_link', 'koala_location_resource_modify_slug', 10, 2);


function koala_blogs_urls($post_link, $post)
{
    if ($post->post_type == "location-blog" && $post->post_status == "publish") {
        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'blog_related_location', true);
        $related_location_id = (is_array($related_location_ids) && !empty($related_location_ids)) ? $related_location_ids[0] : null;

        if ($related_location_id) {
            // Get the slug of the related location post
            $related_location_post = get_post($related_location_id);

            if ($related_location_post) {
                $related_location_slug = $related_location_post->post_name;
                $post_link = home_url('/' . $related_location_slug . '/blog');
            }
        }
    }

    return $post_link;
}

add_filter('post_type_link', 'koala_blogs_urls', 10, 2);

function koala_why_koala_urls($post_link, $post)
{
    if ($post->post_type == "location-why-koala" && $post->post_status == "publish") {
        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'wk_related_location', true);
        $related_location_id = (is_array($related_location_ids) && !empty($related_location_ids)) ? $related_location_ids[0] : null;

        if ($related_location_id) {
            // Get the slug of the related location post
            $related_location_post = get_post($related_location_id);

            if ($related_location_post) {
                $related_location_slug = $related_location_post->post_name;
                $post_link = home_url('/' . $related_location_slug . '/why-koala');
            }
        }
    }

    return $post_link;
}

add_filter('post_type_link', 'koala_why_koala_urls', 10, 2);

function koala_why_reinsulate_urls($post_link, $post)
{
    if ($post->post_type == "location-why-reinsul" && $post->post_status == "publish") {
        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'wr_related_location', true);
        $related_location_id = (is_array($related_location_ids) && !empty($related_location_ids)) ? $related_location_ids[0] : null;

        if ($related_location_id) {
            // Get the slug of the related location post
            $related_location_post = get_post($related_location_id);

            if ($related_location_post) {
                $related_location_slug = $related_location_post->post_name;
                $post_link = home_url('/' . $related_location_slug . '/why-reinsulate');
            }
        }
    }

    return $post_link;
}

add_filter('post_type_link', 'koala_why_reinsulate_urls', 10, 2);

function koala_homeowner_urls($post_link, $post)
{
    if ($post->post_type == "location-homeowner-i" && $post->post_status == "publish") {
        // Get the related location ID from the custom field
        $related_location_ids = get_post_meta($post->ID, 'ho_related_location', true);
        $related_location_id = (is_array($related_location_ids) && !empty($related_location_ids)) ? $related_location_ids[0] : null;

        if ($related_location_id) {
            // Get the slug of the related location post
            $related_location_post = get_post($related_location_id);

            if ($related_location_post) {
                $related_location_slug = $related_location_post->post_name;
                $post_link = home_url('/' . $related_location_slug . '/homeowner-incentives');
            }
        }
    }

    return $post_link;
}

add_filter('post_type_link', 'koala_homeowner_urls', 10, 2);
?>