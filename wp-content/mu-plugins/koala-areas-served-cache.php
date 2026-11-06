<?php
/**
 * Plugin Name: Koala Areas Served cache
 * Description: Refresh the US Areas Served page after a city page is published or unpublished.
 */

if (!defined('ABSPATH') || get_option('siteurl') !== 'https://koalainsulation.com') {
    return;
}

add_action('transition_post_status', 'koala_queue_areas_served_cache_refresh', 10, 3);

function koala_queue_areas_served_cache_refresh($new_status, $old_status, $post)
{
    if ($post->post_type !== 'resources-landing-pa'
        || ($new_status === 'publish') === ($old_status === 'publish')) {
        return;
    }

    $publishing = $new_status === 'publish';
    $previous_locations = !$publishing && has_term('areas-served', 'resources-page-type', $post)
        ? (array) get_post_meta($post->ID, 'rl_related_location', true)
        : array();

    // REST and editor saves can write taxonomy and relationship data after the status hook.
    add_action('shutdown', static function () use ($post, $publishing, $previous_locations) {
        if ((get_post_status($post->ID) === 'publish') !== $publishing) {
            return;
        }
        if ($publishing && !has_term('areas-served', 'resources-page-type', $post->ID)) {
            return;
        }

        $location_ids = $publishing
            ? (array) get_post_meta($post->ID, 'rl_related_location', true)
            : $previous_locations;

        foreach (array_unique(array_map('intval', $location_ids)) as $location_id) {
            $location = get_post($location_id);
            if (!$location || $location->post_type !== 'location' || $location->post_status !== 'publish') {
                continue;
            }

            // Each transition refreshes this one listing. Simultaneous
            // publishes must not both skip the purge after seeing each other.
            koala_purge_areas_served_cache('/' . $location->post_name . '/areas-served');
        }
    });
}

function koala_purge_areas_served_cache($path)
{
    $paths = array($path, $path . '/');
    if (function_exists('rocket_clean_files')) {
        rocket_clean_files(array_map('home_url', $paths));
    }

    foreach ($paths as $variant) {
        $request = curl_init('https://koalainsulation.com/purge' . $variant);
        curl_setopt_array($request, array(
            CURLOPT_RESOLVE => array('koalainsulation.com:443:127.0.0.1'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ));
        curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
        curl_close($request);
        if (!in_array($status, array(200, 412), true)) {
            error_log('Koala Areas Served origin purge failed for ' . $variant . ' (HTTP ' . $status . ')');
        }
    }

    try {
        $config = new Cloudflare\APO\Integration\DefaultConfig(file_get_contents(CLOUDFLARE_PLUGIN_DIR . 'config.json'));
        $logger = new Cloudflare\APO\Integration\DefaultLogger(false);
        $store = new Cloudflare\APO\WordPress\DataStore($logger);
        $integration = new Cloudflare\APO\Integration\DefaultIntegration($config, new Cloudflare\APO\WordPress\WordPressAPI($store), $store, $logger);
        $api = new Cloudflare\APO\WordPress\WordPressClientAPI($integration);
        $zone = $api->getZoneTag('koalainsulation.com');
        if (!$zone) {
            throw new RuntimeException('Cloudflare zone unavailable');
        }
        $response = $api->callAPI(new Cloudflare\APO\API\Request('POST', 'zones/' . $zone . '/purge_cache', array(), array(
            'prefixes' => array('koalainsulation.com' . $path),
        )));
        if (empty($response['success'])) {
            throw new RuntimeException('Cloudflare prefix purge failed');
        }
    } catch (Throwable $error) {
        error_log('Koala Areas Served CDN purge failed for ' . $path . ': ' . $error->getMessage());
    }
}
