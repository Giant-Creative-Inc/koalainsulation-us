<?php
/*
Plugin Name: Sync Blog-Location to Location-Blogs
Description: Automatically sync blog posts from 'blog-location' to 'location-blog' based on matching relationship fields.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('admin_menu', 'sync_blogs_menu');

function sync_blogs_menu() {
    add_menu_page(
        'Sync Blogs',
        'Sync Blogs',
        'manage_options',
        'sync-blogs',
        'sync_blogs_page'
    );
}

function sync_blogs_page() {
    if (isset($_POST['run_sync'])) {
        sync_blogs_from_blog_location_to_location_blog();
    }

    echo '<div class="wrap">';
    echo '<h1>Sync Blogs from Blog-Location to Location-Blogs</h1>';
    echo '<form method="post">';
    echo '<input type="submit" name="run_sync" value="Run Sync" class="button-primary">';
    echo '</form>';
    echo '</div>';
}

function sync_blogs_from_blog_location_to_location_blog()
{
    // Fetch all blog-location posts
    $blog_posts = get_posts([
        'post_type' => 'blog-location',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    if (empty($blog_posts)) {
        echo '<div class="notice notice-warning"><p>No posts found in blog-location.</p></div>';
        return;
    }

    foreach ($blog_posts as $blog_post) {
        // Get the related_location field (unserialize if needed)
        $related_location = get_post_meta($blog_post->ID, 'related_location', true);

        if (empty($related_location)) {
            continue;
        }

        // Unserialize the related_location if it’s serialized
        if (is_serialized($related_location)) {
            $related_location = maybe_unserialize($related_location);
        }

        // Ensure it's an array and get the first ID (assuming a single location relationship)
        $related_location_id = is_array($related_location) ? reset($related_location) : $related_location;

        // Find location-blog posts with a matching blog_related_location
        $location_posts = get_posts([
            'post_type' => 'location-blog',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => [
                [
                    'key' => 'blog_related_location',
                    'value' => '"' . $related_location_id . '"', // Match serialized value
                    'compare' => 'LIKE',
                ],
            ],
        ]);

        foreach ($location_posts as $location_post) {
            // Get the related_blogs field (unserialize if needed)
            $related_blogs = get_post_meta($location_post->ID, 'related_blogs', true);

            if (is_serialized($related_blogs)) {
                $related_blogs = maybe_unserialize($related_blogs);
            }

            if (!is_array($related_blogs)) {
                $related_blogs = [];
            }

            // Add the blog post ID if not already present
            if (!in_array($blog_post->ID, $related_blogs)) {
                $related_blogs[] = $blog_post->ID;
                update_post_meta($location_post->ID, 'related_blogs', $related_blogs);
            }
        }
    }

    echo '<div class="notice notice-success"><p>Sync complete!</p></div>';
}
