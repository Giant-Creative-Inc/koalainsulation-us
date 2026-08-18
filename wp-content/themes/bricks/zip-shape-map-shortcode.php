<?php
/**
 * Add your Google Maps JavaScript API key here.
 */
if (!defined('ZIP_SHAPE_MAPS_API_KEY')) {
    define('ZIP_SHAPE_MAPS_API_KEY', 'AIzaSyBOBCV9KYqqwo8CRYhBbHfjBp5Jea72XQk');
}

/**
 * Shortcode:
 *
 * [zip_shape_map]
 *
 * Optional:
 * [zip_shape_map post_id="123"]
 * [zip_shape_map height="500px" zoom="10"]
 * [zip_shape_map lat="43.190950" lng="-89.370207" zipcodes="53532,53571,53590"]
 */
add_shortcode('zip_shape_map', 'zip_shape_map_render_shortcode');

function zip_shape_map_render_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id'  => '',
        'lat'      => '',
        'lng'      => '',
        'zipcodes' => '',
        'height'   => '650px',
        'zoom'     => '10',
    ), $atts, 'zip_shape_map');

    $post_id = !empty($atts['post_id']) ? absint($atts['post_id']) : get_the_ID();

    if (!$post_id) {
        $post_id = get_queried_object_id();
    }

    $lat = $atts['lat'] !== ''
        ? $atts['lat']
        : get_post_meta($post_id, 'location_latitude', true);

    /**
     * This reads your current field spelling:
     * location_logitude
     */
    $lng = $atts['lng'] !== ''
        ? $atts['lng']
        : get_post_meta($post_id, 'location_logitude', true);

    /**
     * Fallback in case you later rename the field to location_longitude.
     */
    if ($lng === '') {
        $lng = get_post_meta($post_id, 'location_longitude', true);
    }

    $zipcodes_raw = $atts['zipcodes'] !== ''
        ? $atts['zipcodes']
        : get_post_meta($post_id, 'additional_zipcodes', true);

    $location_address = get_post_meta($post_id, 'location_address', true);
    $show_pin = zip_shape_map_address_has_street($location_address);

    // ACF true/false field. When enabled, hide the ZIP boundary outline.
    $hide_service_area = (bool) get_post_meta($post_id, 'hide_service_area', true);
    $show_boundary = !$hide_service_area;

    $lat = is_numeric($lat) ? (float) $lat : null;
    $lng = is_numeric($lng) ? (float) $lng : null;

    $zipcodes = zip_shape_map_parse_zipcodes($zipcodes_raw);

    // When the boundary is hidden the map only needs a center point,
    // so ZIP codes are no longer required.
    if ($lat === null || $lng === null || ($show_boundary && empty($zipcodes))) {
        return '<p>Map data is missing.</p>';
    }

    zip_shape_map_enqueue_assets();

    static $instance = 0;
    $instance++;

    $map_id = 'zip-shape-map-' . $instance;

    $height = preg_match('/^\d+(px|rem|em|vh|vw|%)$/', $atts['height'])
        ? $atts['height']
        : '650px';

    $zoom = absint($atts['zoom']);

    if (!$zoom) {
        $zoom = 10;
    }

    $config = array(
        'id'           => $map_id,
        'lat'          => $lat,
        'lng'          => $lng,
        'zipcodes'     => $zipcodes,
        'zoom'         => $zoom,
        'showPin'      => $show_pin,
        'showBoundary' => $show_boundary,
    );

    ob_start();
    ?>

    <div
        class="zip-shape-map-shell"
        style="position:relative;width:100%;height:<?php echo esc_attr($height); ?>;border-radius:12px;overflow:hidden;background:#9ec7ba;"
    >
        <div
            id="<?php echo esc_attr($map_id); ?>"
            class="zip-shape-map"
            style="width:100%;height:100%;"
            aria-label="Service area map"
        ></div>
        <button
            type="button"
            class="zip-shape-map-placeholder"
            data-zip-shape-map-load
            aria-controls="<?php echo esc_attr($map_id); ?>"
            style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:24px;border:0;background:#9ec7ba;color:#fff;font:inherit;font-weight:600;text-align:center;cursor:pointer;"
        >
            Load service area map
        </button>
    </div>

    <script>
        window.zipShapeMapConfigs = window.zipShapeMapConfigs || [];
        window.zipShapeMapConfigs.push(<?php echo wp_json_encode($config); ?>);
    </script>

    <?php
    return ob_get_clean();
}

function zip_shape_map_address_has_street($address) {
    $address = trim(wp_strip_all_tags((string) $address));

    if ($address === '') {
        return false;
    }

    return (bool) preg_match(
        '/\b\d{1,6}\s+[A-Za-z0-9][A-Za-z0-9\s.\'#-]*\s+(street|st\.?|road|rd\.?|avenue|ave\.?|court|ct\.?|drive|dr\.?|lane|ln\.?|boulevard|blvd\.?|way|place|pl\.?|circle|cir\.?|terrace|ter\.?|trail|trl\.?|parkway|pkwy\.?|highway|hwy\.?|route|rte\.?|square|sq\.?|crescent|cres\.?|close|gate|gardens|grove|heights|landing|loop|mews|path|row|run|view|walk)\b/i',
        $address
    );
}

function zip_shape_map_parse_zipcodes($zipcodes_raw) {
    $parts = preg_split('/[\s,]+/', (string) $zipcodes_raw);

    $zipcodes = array();

    foreach ($parts as $zip) {
        $zip = preg_replace('/[^0-9]/', '', $zip);

        if (preg_match('/^\d{5}$/', $zip)) {
            $zipcodes[$zip] = $zip;
        }
    }

    return array_values($zipcodes);
}

function zip_shape_map_enqueue_assets() {
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;

    $loader_path = get_template_directory() . '/assets/js/custom/zip-shape-map.js';
    $loader_version = file_exists($loader_path) ? filemtime($loader_path) : null;

    wp_enqueue_script(
        'zip-shape-map-loader',
        get_template_directory_uri() . '/assets/js/custom/zip-shape-map.js',
        array(),
        $loader_version,
        true
    );

    wp_add_inline_script(
        'zip-shape-map-loader',
        'window.zipShapeMapRestUrl = ' . wp_json_encode(rest_url('zip-shape-map/v1/boundaries')) . ';' .
        'window.zipShapeMapApiKey = ' . wp_json_encode(ZIP_SHAPE_MAPS_API_KEY) . ';',
        'before'
    );
}

/**
 * REST endpoint for cached ZIP/ZCTA boundary data.
 */
add_action('rest_api_init', function () {
    register_rest_route('zip-shape-map/v1', '/boundaries', array(
        'methods'             => 'GET',
        'callback'            => 'zip_shape_map_get_boundaries',
        'permission_callback' => '__return_true',
    ));
});

function zip_shape_map_prepare_boundary_geojson($features) {
    $prepared = array();

    foreach ((array) $features as $feature) {
        if (empty($feature['geometry']['type']) || empty($feature['geometry']['coordinates'])) {
            continue;
        }

        $prepared[] = array(
            'type'       => 'Feature',
            'properties' => new stdClass(),
            'geometry'   => array(
                'type'        => $feature['geometry']['type'],
                'coordinates' => $feature['geometry']['coordinates'],
            ),
        );
    }

    return array(
        'type'     => 'FeatureCollection',
        'features' => $prepared,
    );
}

function zip_shape_map_dissolve_boundary_geojson($features) {
    $geometries = array();

    foreach ((array) $features as $feature) {
        $geometry = $feature['geometry'] ?? array();
        $type = $geometry['type'] ?? '';
        $coordinates = $geometry['coordinates'] ?? array();

        if ($type === 'Polygon' && !empty($coordinates)) {
            $geometries[] = array('rings' => $coordinates);
        } elseif ($type === 'MultiPolygon') {
            foreach ($coordinates as $polygon) {
                if (!empty($polygon)) {
                    $geometries[] = array('rings' => $polygon);
                }
            }
        }
    }

    if (count($geometries) < 2) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    $response = wp_remote_post(
        'https://sampleserver6.arcgisonline.com/arcgis/rest/services/Utilities/Geometry/GeometryServer/union',
        array(
            'timeout' => 30,
            'body'    => array(
                'f'          => 'json',
                'sr'         => '4326',
                'geometries' => wp_json_encode(array(
                    'geometryType' => 'esriGeometryPolygon',
                    'geometries'   => $geometries,
                )),
            ),
        )
    );

    if (is_wp_error($response)) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    $union = json_decode(wp_remote_retrieve_body($response), true);
    $rings = $union['geometry']['rings'] ?? array();

    if (empty($rings)) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    $polygons = array();

    foreach ($rings as $ring) {
        if (zip_shape_map_ring_area($ring) < 0) {
            $polygons[] = array($ring);
        }
    }

    foreach ($rings as $ring) {
        if (zip_shape_map_ring_area($ring) < 0) {
            continue;
        }

        $assigned = false;

        foreach ($polygons as &$polygon) {
            if (zip_shape_map_point_in_ring($ring[0], $polygon[0])) {
                $polygon[] = $ring;
                $assigned = true;
                break;
            }
        }
        unset($polygon);

        if (!$assigned) {
            $polygons[] = array(array_reverse($ring));
        }
    }

    if (empty($polygons)) {
        return zip_shape_map_prepare_boundary_geojson($features);
    }

    return array(
        'type'     => 'FeatureCollection',
        'features' => array(array(
            'type'       => 'Feature',
            'properties' => new stdClass(),
            'geometry'   => array(
                'type'        => 'MultiPolygon',
                'coordinates' => $polygons,
            ),
        )),
    );
}

function zip_shape_map_ring_area($ring) {
    $area = 0.0;
    $count = count($ring);

    for ($index = 0; $index < $count; $index++) {
        $next = ($index + 1) % $count;
        $area += ($ring[$index][0] * $ring[$next][1]) - ($ring[$next][0] * $ring[$index][1]);
    }

    return $area / 2;
}

function zip_shape_map_point_in_ring($point, $ring) {
    $inside = false;
    $count = count($ring);

    for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
        $xi = $ring[$i][0];
        $yi = $ring[$i][1];
        $xj = $ring[$j][0];
        $yj = $ring[$j][1];

        $intersects = (($yi > $point[1]) !== ($yj > $point[1]))
            && ($point[0] < (($xj - $xi) * ($point[1] - $yi) / (($yj - $yi) ?: PHP_FLOAT_EPSILON)) + $xi);

        if ($intersects) {
            $inside = !$inside;
        }
    }

    return $inside;
}

function zip_shape_map_get_boundaries(WP_REST_Request $request) {
    $zipcodes_raw = $request->get_param('zips');
    $zipcodes = zip_shape_map_parse_zipcodes($zipcodes_raw);

    if (empty($zipcodes)) {
        return new WP_Error(
            'missing_zipcodes',
            'No ZIP codes provided.',
            array('status' => 400)
        );
    }

    sort($zipcodes);

    $cache_key = 'zip_shape_v4_' . md5(implode(',', $zipcodes));
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        return rest_ensure_response($cached);
    }

    $where = 'GEOID IN (' . implode(',', array_map(function ($zip) {
        return "'" . $zip . "'";
    }, $zipcodes)) . ')';

    $url = add_query_arg(array(
        'f'              => 'geojson',
        'where'          => $where,
        'outFields'      => 'GEOID,BASENAME',
        'returnGeometry' => 'true',
        'outSR'          => '4326',
    ), 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/tigerWMS_Census2020/MapServer/84/query');

    $response = wp_remote_get($url, array(
        'timeout' => 20,
    ));

    if (is_wp_error($response)) {
        return new WP_Error(
            'boundary_request_failed',
            'Could not load ZIP boundaries.',
            array('status' => 500)
        );
    }

    $body = wp_remote_retrieve_body($response);
    $geojson = json_decode($body, true);

    if (!is_array($geojson) || empty($geojson['features'])) {
        return new WP_Error(
            'empty_boundaries',
            'No ZIP boundaries found.',
            array('status' => 404)
        );
    }

    $prepared_geojson = zip_shape_map_dissolve_boundary_geojson($geojson['features']);

    if (empty($prepared_geojson['features'])) {
        return new WP_Error(
            'empty_boundaries',
            'No usable ZIP boundaries found.',
            array('status' => 404)
        );
    }

    set_transient($cache_key, $prepared_geojson, 30 * DAY_IN_SECONDS);

    return rest_ensure_response($prepared_geojson);
}
