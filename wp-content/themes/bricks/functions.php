<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

// Redirect uppercase slugs to lowercase to prevent duplicate content.
add_action('template_redirect', function () {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path        = parse_url($request_uri, PHP_URL_PATH);
    if (!$path) return;

    $lowercase = strtolower($path);
    if ($path === $lowercase) return;

    $query = parse_url($request_uri, PHP_URL_QUERY);
    wp_redirect($lowercase . ($query ? '?' . $query : ''), 301);
    exit;
});

function koala_disable_wp_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url', '__return_false');
}
add_action('init', 'koala_disable_wp_emojis', 1);

// Add support for responsive embedded content (YouTube, Vimeo, etc.).
add_theme_support('responsive-embeds');
/*
add_action( 'template_redirect', function() {
    if ( is_page( 'terms-and-conditions' ) ) {
        wp_redirect( home_url( '/privacy-policy' ), 302 );
        exit;
    }
});
*/

/**
 * Define constants
 *
 * @since 1.0
 */
define('BRICKS_VERSION', '1.9.7.1');
define('BRICKS_NAME', 'Bricks');
define('BRICKS_TEMP_DIR', 'bricks-temp'); // Template import/export (JSON & ZIP)
define('BRICKS_PATH', trailingslashit(get_template_directory()));    // require_once files
define('BRICKS_PATH_ASSETS', trailingslashit(BRICKS_PATH . 'assets'));
define('BRICKS_URL', trailingslashit(get_template_directory_uri())); // WP enqueue files
define('BRICKS_URL_ASSETS', trailingslashit(BRICKS_URL . 'assets'));
define('BRICKS_REMOTE_URL', 'https://bricksbuilder.io/');
define('BRICKS_REMOTE_ACCOUNT', BRICKS_REMOTE_URL . 'account/');

define('BRICKS_BUILDER_PARAM', 'bricks');
define('BRICKS_BUILDER_IFRAME_PARAM', 'brickspreview');
define('BRICKS_DEFAULT_IMAGE_SIZE', 'large');

define('BRICKS_DB_PANEL_WIDTH', 'bricks_panel_width');
define('BRICKS_DB_BUILDER_SCALE_OFF', 'bricks_builder_scale_off');
define('BRICKS_DB_BUILDER_WIDTH_LOCKED', 'bricks_builder_width_locked');

define('BRICKS_DB_COLOR_PALETTE', 'bricks_color_palette');
define('BRICKS_DB_BREAKPOINTS', 'bricks_breakpoints');
define('BRICKS_DB_GLOBAL_SETTINGS', 'bricks_global_settings');
define('BRICKS_DB_GLOBAL_ELEMENTS', 'bricks_global_elements');
define('BRICKS_DB_GLOBAL_CLASSES', 'bricks_global_classes');
define('BRICKS_DB_GLOBAL_CLASSES_CATEGORIES', 'bricks_global_classes_categories');
define('BRICKS_DB_GLOBAL_CLASSES_LOCKED', 'bricks_global_classes_locked');
define('BRICKS_DB_PSEUDO_CLASSES', 'bricks_global_pseudo_classes');
define('BRICKS_DB_PINNED_ELEMENTS', 'bricks_pinned_elements');
define('BRICKS_DB_SIDEBARS', 'bricks_sidebars');
define('BRICKS_DB_THEME_STYLES', 'bricks_theme_styles');
define('BRICKS_DB_ADOBE_FONTS', 'bricks_adobe_fonts');

define('BRICKS_DB_EDITOR_MODE', '_bricks_editor_mode');
define('BRICKS_BREAKPOINTS_LAST_GENERATED', 'bricks_breakpoints_last_generated');

define('BRICKS_CSS_FILES_LAST_GENERATED', 'bricks_css_files_last_generated');
define('BRICKS_CSS_FILES_LAST_GENERATED_TIMESTAMP', 'bricks_css_files_last_generated_timestamp');
define('BRICKS_CSS_FILES_ADMIN_NOTICE', 'bricks_css_files_admin_notice');

define('BRICKS_CODE_SIGNATURES_LAST_GENERATED', 'bricks_code_signatures_last_generated');
define('BRICKS_CODE_SIGNATURES_LAST_GENERATED_TIMESTAMP', 'bricks_code_signatures_last_generated_timestamp');
define('BRICKS_CODE_SIGNATURES_ADMIN_NOTICE', 'bricks_code_signatures_admin_notice');

/**
 * Syntax since 1.2 (container element)
 *
 * Pre 1.2: '_bricks_page_{$content_type}'
 */
define('BRICKS_DB_PAGE_HEADER', '_bricks_page_header_2');
define('BRICKS_DB_PAGE_CONTENT', '_bricks_page_content_2');
define('BRICKS_DB_PAGE_FOOTER', '_bricks_page_footer_2');
define('BRICKS_DB_PAGE_SETTINGS', '_bricks_page_settings');

define('BRICKS_DB_REMOTE_TEMPLATES', 'bricks_remote_templates');
define('BRICKS_DB_TEMPLATE_SLUG', 'bricks_template');
define('BRICKS_DB_TEMPLATE_TAX_BUNDLE', 'template_bundle');
define('BRICKS_DB_TEMPLATE_TAX_TAG', 'template_tag');
define('BRICKS_DB_TEMPLATE_TYPE', '_bricks_template_type');
define('BRICKS_DB_TEMPLATE_SETTINGS', '_bricks_template_settings');

define('BRICKS_DB_CUSTOM_FONTS', 'bricks_fonts');
define('BRICKS_DB_CUSTOM_FONT_FACES', 'bricks_font_faces');
define('BRICKS_DB_CUSTOM_FONT_FACE_RULES', 'bricks_font_face_rules'); // @since 1.7.2

define('BRICKS_EXPORT_TEMPLATES', 'brick_export_templates');

define('BRICKS_ADMIN_PAGE_URL_LICENSE', admin_url('admin.php?page=bricks-license'));

define('BRICKS_AUTH_CHECK_INTERVAL', 30);

if (!defined('BRICKS_DEBUG ')) {
    define('BRICKS_DEBUG', false);
}

if (!defined('BRICKS_MAX_REVISIONS_TO_KEEP')) {
    define('BRICKS_MAX_REVISIONS_TO_KEEP', 100);
}

/**
 * Multisite constants
 *
 * @since 1.0
 */

// Global data: Color palette
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_COLOR_PALETTE')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_COLOR_PALETTE', false);
}

// Global data: Global classes
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES', false);
}

// Global data: Global classes categories
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES_CATEGORIES')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_CLASSES_CATEGORIES', false);
}

// Global data: Global elements
if (!defined('BRICKS_MULTISITE_USE_MAIN_SITE_GLOBAL_ELEMENTS')) {
    define('BRICKS_MULTISITE_USE_MAIN_SITE_GLOBAL_ELEMENTS', false);
}

/**
 * Use minified assets when SCRIPT_DEBUG is off
 *
 * @since 1.0
 */
if (BRICKS_DEBUG || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)) {
    define('BRICKS_ASSETS_SUFFIX', '');
} else {
    define('BRICKS_ASSETS_SUFFIX', '.min');
}

/**
 * Admin notice if PHP version is older than 5.4
 *
 * Required due to: array shorthand, array dereferencing etc.
 *
 * @since 1.0
 */
if (version_compare(PHP_VERSION, '5.4', '>=')) {
    require_once BRICKS_PATH . 'includes/init.php';
} else {
    add_action(
        'admin_notices',
        function () {
            // translators: %s: PHP version number
            $message = sprintf(esc_html__('Bricks requires PHP version %s+.', 'bricks'), '5.4');
            $html = sprintf('<div class="error">%s</div>', wpautop($message));
            echo wp_kses_post($html);
        }
    );
}

// Add GA4 event if Cloudflare APO cache is active
add_action('wp_head', function () {
    $cf_cache_status = isset($_SERVER['HTTP_CF_CACHE_STATUS']) ? $_SERVER['HTTP_CF_CACHE_STATUS'] : '';
    $cf_edge_cache = isset($_SERVER['HTTP_CF_EDGE_CACHE']) ? $_SERVER['HTTP_CF_EDGE_CACHE'] : '';

    // Check if Cloudflare cache HIT and APO is active for WordPress
    if (strtoupper($cf_cache_status) === 'HIT' && strpos($cf_edge_cache, 'platform=wordpress') !== false): ?>
        <script>
            gtag('event', 'cloudflare_cache_hit', {
                event_category: 'performance',
                event_label: 'APO active'
            });
        </script>
    <?php endif;
});


/**
 * Builder check
 *
 * NEVER EVER EVER DELETE THIS
 * DELETING THIS WILL ALLOW WP TO USE ITS CANONICAL REDIRECT LOGIC TO REDIRECT /atlanta to /atlanta-perimeter-north WHICH CONFLICTS WITH
 * THE REDIRECT WE HAVE IN PLACE TO REDIRECT /atlanta TO /south-atlanta 
 */
add_filter('redirect_canonical', function ($redirect_url) {
    if (strpos($_SERVER['REQUEST_URI'], '/atlanta') !== false) {
        return false;
    }
    return $redirect_url;
});
add_action('template_redirect', function () {
    if (untrailingslashit($_SERVER['REQUEST_URI']) === '/atlanta') {
        wp_redirect('https://koalainsulation.com/south-atlanta', 301);
        exit;
    }
});



/**
 * Builder check
 *
 * @since 1.0
 */
function bricks_is_builder()
{
    return (!is_admin() && isset($_GET[BRICKS_BUILDER_PARAM]));
}

function bricks_is_builder_iframe()
{
    return (bricks_is_builder() && isset($_GET[BRICKS_BUILDER_IFRAME_PARAM]));
}

function bricks_is_builder_main()
{
    return (bricks_is_builder() && !isset($_GET[BRICKS_BUILDER_IFRAME_PARAM]));
}

function bricks_is_frontend()
{
    return !bricks_is_builder();
}

/**
 * Is AJAX call check
 *
 * @since 1.0
 */
function bricks_is_ajax_call()
{
    return defined('DOING_AJAX') && DOING_AJAX;
}

/**
 * Is WP REST API call check
 *
 * @since 1.5
 */
function bricks_is_rest_call()
{
    return defined('REST_REQUEST') && REST_REQUEST;
}

/**
 * Is builder call (AJAX OR REST API)
 *
 * @since 1.5
 */
function bricks_is_builder_call()
{
    // Use PHP constant BRICKS_IS_BUILDER @since 1.5.5 to perform builder check logic only once
    if (!defined('BRICKS_IS_BUILDER')) {
        define('BRICKS_IS_BUILDER', \Bricks\Builder::is_builder_call());
    }

    return BRICKS_IS_BUILDER;
}


/**
 * Render dynamic data tags inside of a content string
 *
 * Example: Inside an executing Code element, custom plugin, etc.
 *
 * Academy: https://academy.bricksbuilder.io/article/function-bricks_render_dynamic_data/
 *
 * @since 1.5.5
 *
 * @param string $content The content (including dynamic data tags).
 * @param int    $post_id The post ID.
 * @param string $context text, image, link, etc.
 *
 * @return string
 */
function bricks_render_dynamic_data($content, $post_id = 0, $context = 'text')
{
    return \Bricks\Integrations\Dynamic_Data\Providers::render_content($content, $post_id, $context);
}

function get_custom_field_value_current_post($meta_key)
{
    global $post;
    return get_post_meta($post->ID, $meta_key, true);
}

function get_custom_field_value_from_a_post($postId, $meta_key)
{
    return get_post_meta($postId, $meta_key, true);
}

function get_all_locations_data()
{
    $cache_key = 'koala_location_map_data_v2';
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $locations_data = [];

    // Query all location posts (you can modify post_type or filters as needed)
    $location_posts = get_posts([
        'post_type' => 'location',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);


    foreach ($location_posts as $location) {
        $url = get_permalink($location->ID);
        $title = get_field('location_name', $location->ID);
        $location_address = get_field('location_address', $location->ID);
        $lat = get_field('location_latitude', $location->ID);
        $long = get_field('location_logitude', $location->ID);
        $phone = get_field('location_phone_number', $location->ID);
        $field = get_field('location_area_serviced', $location->ID);
        $location_service = is_array($field)
            ? (is_object($field[0]) ? $field[0]->post_title : $field[0])
            : $field;


        // Positional rows keep the one client-side map payload compact:
        // [title, latitude, longitude, address, phone, URL, service area].
        $locations_data[] = [
            $title,
            $lat,
            $long,
            $location_address,
            $phone,
            $url ? $url : '',
            $location_service ? $location_service : '',
        ];
    }

    set_transient($cache_key, $locations_data, 12 * HOUR_IN_SECONDS);
    return $locations_data;
}
add_action('save_post_location', function () {
    delete_transient('koala_all_locations_data');
    delete_transient('koala_location_map_data_v2');
});

function enqueue_custom_scripts()
{
    global $post;
    $front_page = is_front_page();
    $faq_page = is_page('faq');
    $homeowner_incentives_page = is_page('homeowner-incentives');
    $service_page = is_page('services');
    $single_service_page = is_singular('location-service');
    // National (non-location) service singles, e.g. /services/commercial-insulation-services.
    // These render the photo slider (Bricks template "Services single page"), so they
    // need Swiper too — is_singular('location-service') above does NOT match them.
    $single_service_national = is_singular('service');
    $location_page = is_page('locations');
    $single_location_page = is_singular('location');
    $single_location_service = is_singular('location-service');
    $why_koala_page = ($post && $post->post_name === 'why-koala');
    $why_reinsulate = ($post && $post->post_name === 'why-reinsulate');

     wp_enqueue_style(
        'koala-custom-css', 
        get_stylesheet_directory_uri() . '/assets/css/custom.css', 
        array(), 
        filemtime(get_stylesheet_directory() . '/assets/css/custom.css') // Auto-updates version for cache busting
    );

    // Swiper library + slider initialisers are only needed where custom slider
    // markup renders: front page (services slider), single locations (partner
    // slider), single location-service / why-koala / why-reinsulate (photo &
    // before/after sliders). sliders.js was extracted from all-pages.js so the
    // ~300 lines of Swiper init no longer ship on every page.
    $needs_swiper = $front_page || $single_location_page || $why_koala_page || $why_reinsulate || $single_service_page || $single_service_national;
    if ($needs_swiper) {
        wp_enqueue_script(
            'swiper-bundle',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
            array(),
            null,
            true
        );
        $sliders_ver = filemtime(get_stylesheet_directory() . '/assets/js/custom/sliders.js');
        wp_enqueue_script(
            'koala-sliders',
            get_template_directory_uri() . '/assets/js/custom/sliders.js',
            array('jquery', 'swiper-bundle'),
            $sliders_ver,
            true
        );
    }

    // Review-count integration (reviews.js) only needs to run where the
    // #review-count target renders. Empirically that is single locations and
    // landing pages; the script self-terminates when the target is absent, but
    // gating keeps it off every other page. Extracted from all-pages.js.
    $needs_reviews = $single_location_page || is_singular('landing-pages');
    if ($needs_reviews) {
        $reviews_ver = filemtime(get_stylesheet_directory() . '/assets/js/custom/reviews.js');
        wp_enqueue_script(
            'koala-reviews',
            get_template_directory_uri() . '/assets/js/custom/reviews.js',
            array(),
            $reviews_ver,
            true
        );
    }

    // Register the script first
    // Only load Google Maps on pages that actually use it — keeps it off blog, FAQ, etc.
    // Single location pages use the service-area shortcode, which lazy-loads
    // Google Maps when its fixed-size placeholder approaches the viewport.
    $needs_maps = $front_page || $location_page || $single_service_page || $single_location_service;
    if ($needs_maps) {
        wp_register_script(
            'google-maps',
            'https://maps.googleapis.com/maps/api/js?key=AIzaSyBOBCV9KYqqwo8CRYhBbHfjBp5Jea72XQk',
            array(),
            null,
            true
        );
        wp_enqueue_script('google-maps');
    }


    if ($front_page) {
        wp_enqueue_script(
            'custom-map-init',
            get_template_directory_uri() . '/assets/js/custom/custom-map-init.js',
            array('google-maps', 'jquery'),
            null,
            true
        );
    }
    if ($faq_page || $homeowner_incentives_page) {
        wp_enqueue_script(
            'faq-accordion',
            get_template_directory_uri() . '/assets/js/custom/accordion.js',
            array('jquery'),
            null,
            true
        );
    }

    if ($location_page || $single_service_page) {
        if ( get_the_ID() !== '8449'){ 
        wp_enqueue_script(
            'location-page',
            get_template_directory_uri() . '/assets/js/custom/location-page.js',
            array('google-maps', 'jquery'),
            null,
            true
        );
        }
    }

    if ($location_page || $single_service_page) {
        wp_enqueue_script(
            'custom-service-script',
            get_template_directory_uri() . '/assets/js/custom/service-page.js',
            array(),
            null,
            true
        );
    }

    $all_pages_ver = filemtime(get_stylesheet_directory() . '/assets/js/custom/all-pages.js');
    wp_enqueue_script('all-pages-js', get_template_directory_uri() . '/assets/js/custom/all-pages.js', array('jquery'), $all_pages_ver, true);

    // popup.js: global Bricks popup helpers (?form= opener + CallRail re-swap for
    // the Estimate popup #4865). The popup renders site-wide via the header, and
    // both blocks self-guard, so this loads globally like all-pages.js. Extracted
    // from all-pages.js; pure vanilla (no jQuery dependency).
    $popup_ver = filemtime(get_stylesheet_directory() . '/assets/js/custom/popup.js');
    wp_enqueue_script('koala-popup', get_template_directory_uri() . '/assets/js/custom/popup.js', array(), $popup_ver, true);

    // custom-service-js handles the header ZIP lookup and estimate popup.
    $custom_service_ver = filemtime(get_stylesheet_directory() . '/assets/js/custom-service.js');
    wp_enqueue_script('custom-service-js', get_template_directory_uri() . '/assets/js/custom-service.js', array('jquery'), $custom_service_ver, true);

    $needs_service_scripts = $front_page || $location_page || $single_location_page || $single_service_page || $single_location_service;

    wp_localize_script('custom-service-js', 'ajaxData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'match_location_nonce' => wp_create_nonce('match_location'),
        'zip_code_in_radius_nonce' => wp_create_nonce('zip_code_in_radius_nonce'),
    ]);

    $koala_data = [
        'ajax_url' => admin_url('admin-ajax.php'),
        'is_location' => $location_page,
        'map_pin' => home_url() . '/wp-content/uploads/2024/09/map-pin.svg',
    ];

    if ($needs_maps) {
        $koala_data['locations'] = get_all_locations_data();
    }

    wp_localize_script('all-pages-js', 'koalaData', $koala_data);
    wp_enqueue_style('custom-service-css', get_template_directory_uri() . '/assets/css/custom-service.css');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


// Add async and defer attributes to the Google Maps script
function add_async_defer_to_google_maps($tag, $handle)
{
    if ($handle === 'google-maps') {
        return str_replace('<script ', '<script async defer ', $tag);
    }

    
    return $tag;
}
add_filter('script_loader_tag', 'add_async_defer_to_google_maps', 10, 2);

function display_location_services()
{
    ob_start(); // Start output buffering
    ?>

    <a id="custom-service" class="brxe-dropdown nav-dropdown">
        <div class="brx-submenu-toggle">
            <span>Services</span>
            <button aria-expanded="false" aria-label="Toggle dropdown">
                <svg class="" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path
                        d="M8.99998 9.87855L12.7123 6.16626L13.773 7.22692L8.99998 11.9999L4.22705 7.22692L5.28771 6.16626L8.99998 9.87855Z"
                        fill="white"></path>
                </svg>
            </button>
        </div>
        <ul id="custom-service-ul" class="brxe-div dropdown-content brx-dropdown-content">
            <!-- JavaScript will populate this list -->
        </ul>
    </a>
    <?php

    return ob_get_clean(); // Return the buffered content
}

add_shortcode('location_services', 'display_location_services');

function display_location_services_footer()
{
    ob_start(); // Start output buffering
    ?>

    <div id="custom-service-footer" class="brxe-div footer-links-wrapper">
        <!-- JavaScript will populate this content -->
    </div>
    <?php

    return ob_get_clean(); // Return the buffered content
}

add_shortcode('location_services_footer', 'display_location_services_footer');

function handle_estimate_form_submission()
{
    // Verify reCAPTCHA Enterprise
    $recaptcha_token = sanitize_text_field($_POST['recaptcha_token']);
    $recaptcha_project_id = 'virtual-equator-450216-g8';
    $recaptcha_api_key = 'AIzaSyCIQauwT_clQpb8smKc7Jkxs3176LCKGpc';

    $recaptcha_response = wp_remote_post("https://recaptchaenterprise.googleapis.com/v1/projects/$recaptcha_project_id/assessments?key=$recaptcha_api_key", [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'event' => [
                'token' => $recaptcha_token,
                'siteKey' => '6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt',
                'expectedAction' => 'submit'
            ]
        ])
    ]);

    if (is_wp_error($recaptcha_response)) {
        wp_send_json_error(['message' => 'reCAPTCHA request failed.']);
    }

    $recaptcha_body = json_decode(wp_remote_retrieve_body($recaptcha_response), true);

    if (
        empty($recaptcha_body['tokenProperties']['valid']) ||
        $recaptcha_body['riskAnalysis']['score'] < 0.5
    ) {
        wp_send_json_error(['message' => 'reCAPTCHA failed or suspicious activity detected.']);
    }

    // Sanitize and collect form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = koala_normalize_phone( sanitize_text_field($_POST['mobile_number']) );
    $address1 = sanitize_text_field($_POST['address1']);
    $address2 = sanitize_text_field($_POST['address2']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    $consent = sanitize_text_field($_POST['DoNotText']);
    $consent_email = sanitize_text_field($_POST['DoNotEmail']);
    $consent_sms_marketing = filter_var($_POST['cust_smsmarketingconsent'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $key = sanitize_text_field($_POST['key']);
    $utm_source   = sanitize_text_field($_POST['UtmSource'] ?? '');
    $utm_medium   = sanitize_text_field($_POST['UtmMedium'] ?? '');
    $utm_campaign = sanitize_text_field($_POST['UtmCampaign'] ?? '');

    // Prepare data for the first API call (customers endpoint)
    $data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'addresses' => [
            [
                "street" => $address1,
                "street_line_2" => $address2,
                "city" => $city,
                "state" => $state,
                "zip" => $zip,
            ],
        ],
        'DoNotText' => $consent,
        'DoNotEmail' => $consent_email,
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];

    // Prepare data for the the post type
    $post_type_data = [
        'ip_address' => '',
        'full_name' => $first_name . ' ' . $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'address' => $address1 . ' ' . $address2 . ', ' . $city . ' ' . $state . ' ' . $zip,
    ];

    // First API call
    $response = wp_remote_post('https://api.housecallpro.com/customers', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the form.']);
        wp_die();
    }

    $response_body = wp_remote_retrieve_body($response);
    $decoded_response = json_decode($response_body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        wp_die();
    }

    // Extract the customer ID from the first response
    $customer_id = $decoded_response['id'] ?? null;
    if (!$customer_id) {
        wp_send_json_error(['message' => 'Customer ID not returned from the first API call.']);
        wp_die();
    }

    // Prepare data for the second API call (leads endpoint)
    $lead_data = [
        'customer_id' => $customer_id,
        'address' => [
            "street" => $address1,
            "street_line_2" => $address2,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
        ],
        'lead_source' => "Website",
        'DoNotText' => $consent,
        'DoNotEmail' => $consent_email,
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];

    // Second API call
    $lead_response = wp_remote_post('https://api.housecallpro.com/leads', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($lead_data),
    ]);

    if (is_wp_error($lead_response)) {
        wp_send_json_error(['message' => 'There was an error submitting the lead to the second API endpoint.']);
        wp_die();
    }

    $lead_response_body = wp_remote_retrieve_body($lead_response);
    $decoded_lead_response = json_decode($lead_response_body, true);
    $status_code = wp_remote_retrieve_response_code($lead_response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(
            [
                'message' => 'Invalid JSON response received from the second API.',
                'raw_response' => $lead_response_body,
                'status_code' => $status_code
            ]
        );
        wp_die();
    }

    save_submission_to_cpt($post_type_data);

    // =================================================================
    // START: NEW CHIRP WEBHOOK LOGIC
    // =================================================================

    // The Page ID for the Kansas City page is 4815.
    $kansas_city_page_id = 4815; 
    $submitted_page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;

    // Check if the submission is from the target page
    if ($submitted_page_id === $kansas_city_page_id) {
        // Prepare the data array to send to our helper function
        $data_for_webhook = [
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'email'         => $email,
            'mobile_number' => $phone,
            'zip'           => $zip,
        ];
        send_kansas_city_submission_to_chiirp($data_for_webhook);
    }
    
    // =================================================================
    // END: NEW CHIRP WEBHOOK LOGIC
    // =================================================================

    // Return success response with both API responses
    wp_send_json_success([
        'message' => 'Form submitted successfully.',
        'customer_response' => $decoded_response,
        'lead_response' => $decoded_lead_response,
        'status_code' => $status_code,

    ]);

    wp_die(); // this is required to terminate immediately and return a proper response
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_submit_estimate_form', 'handle_estimate_form_submission');
add_action('wp_ajax_nopriv_submit_estimate_form', 'handle_estimate_form_submission');


function handle_estimate_sm_form_submission()
{
    // Check if the nonce is present and valid
    if (!isset($_POST['nonce'])) {
        wp_send_json_error(['message' => 'Invalid security token.']);
        wp_die(); // Required to stop execution and return a proper response
    }


    // Sanitize and collect form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = koala_normalize_phone( sanitize_text_field($_POST['mobile_number']) );
    $address1 = sanitize_text_field($_POST['address1']);
    $address2 = sanitize_text_field($_POST['address2']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    $consent = sanitize_text_field($_POST['DoNotText']);
    $consent_email = sanitize_text_field($_POST['DoNotEmail']);
    $consent_sms_marketing = filter_var($_POST['cust_smsmarketingconsent'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $serviceId = isset($_POST['service_id']) ? absint($_POST['service_id']) : 0;
    $key = sanitize_text_field($_POST['key']);
    $submitted_page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;
    $utm_source   = sanitize_text_field($_POST['UtmSource'] ?? '');
    $utm_medium   = sanitize_text_field($_POST['UtmMedium'] ?? '');
    $utm_campaign = sanitize_text_field($_POST['UtmCampaign'] ?? '');

    // Prepare data for the required payload structure
    // Conditionally build the data payload
    $target_page_ids = [66343, 66325];
    if (in_array($submitted_page_id, $target_page_ids)) {
        // --- NEW PAYLOAD for the specific page(s) ---
        $data = [
            "Matches" => [
                [
                    "Name" => $first_name . " " . $last_name,
                    "Phone" => $phone,
                    "Email" => $email,
                    "Address1" => $address1,
                    "Address2" => $address2,
                    "City" => $city,
                    "State" => $state,
                    "Zip" => $zip,
                    "LeadSource" => "",
                    "Category" => "Prospect", // Add category
                    "DoNotText" => $consent,
                    'DoNotEmail' => $consent_email,
                    "CustomFields" => [
                        ["Name" => "SMS Marketing Consent", "Value" => $consent_sms_marketing ? "True" : "False"]
                    ],
                    // "Notes" => [
                    //     ["Body" => "Lead submitted from website form (New Routing)."]
                    // ],
                    'UtmSource' => $utm_source,
                    'UtmMedium' => $utm_medium,
                    'UtmCampaign' => $utm_campaign,
                ]
            ],
            "DistributeLead" => true, // CRITICAL: Add distribution flag
            "ApiKey" => $key,
        ];
    } else {
        // --- ORIGINAL PAYLOAD for all other pages ---
        $data = [
            "Matches" => [
                [
                    "Name" => $first_name . " " . $last_name,
                    "Phone" => $phone,
                    "Email" => $email,
                    "Address1" => $address1,
                    "Address2" => $address2,
                    "City" => $city,
                    "State" => $state,
                    "Zip" => $zip,
                    "LeadSource" => "Website",
                    "DoNotText'" => $consent,
                    'DoNotEmail' => $consent_email,
                    "CustomFields" => [
                        ["Name" => "SMS Marketing Consent", "Value" => $consent_sms_marketing ? "True" : "False"]
                    ],
                    'UtmSource' => $utm_source,
                    'UtmMedium' => $utm_medium,
                    'UtmCampaign' => $utm_campaign,
                ]
            ],
            "ApiKey" => $key,
            "ResultCode" => "",
            "Message" => ""
        ];
    }

    $response = wp_remote_post('https://serviceminder.io/api/contacts/addupdate', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the form.']);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);
        //wp_send_json_success(['message' => 'Form submitted successfully.', 'response' => $response]);
        // if (json_last_error() === JSON_ERROR_NONE) {
        //     wp_send_json_success(['message' => 'Form submitted successfully.', 'response' => $decoded_response]);
        // }
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
            wp_die();
        }


        // Extract the customer ID from the first response
        $customer_id = $decoded_response['Matches'][0]['Id'] ?? null;

        if (!$customer_id) {
            wp_send_json_error(['message' => 'Customer ID not returned from the first API call.']);
            wp_die();
        }

        // Get today's date in MM/DD/YYYY format
        $search_date = date("m/d/Y");


        // Prepare data for the second API call (slot-search endpoint)
        $slots_search_data = [
            'ContactId' => $customer_id,
            'SearchDate' => $search_date,
            'SlotWindowDays' => 3,
            'ServiceId' => $serviceId,
            'ApiKey' => $key
        ];

        // Second API call
        $slots_search_response = wp_remote_post('https://serviceminder.com/api/appointments/slotsearch', [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($slots_search_data),
        ]);

        if (is_wp_error($slots_search_response)) {
            wp_send_json_error(['message' => 'There was an error submitting the lead to the second API endpoint.']);
            wp_die();
        }


        $slots_search_response_body = wp_remote_retrieve_body($slots_search_response);
        $decoded_slots_search_response = json_decode($slots_search_response_body, true);
        $status_code = wp_remote_retrieve_response_code($slots_search_response);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(
                [
                    'message' => 'Invalid JSON response received from the second API.',
                    'raw_response' => $slots_search_response_body,
                    'status_code' => $status_code
                ]
            );
            wp_die();
        }

        // =================================================================
    // START: NEW CHIRP WEBHOOK LOGIC
    // =================================================================

    // The Page ID for the Kansas City page is 4815.
    $kansas_city_page_id = 4815; 
    $submitted_page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;

    // Check if the submission is from the target page
    if ($submitted_page_id === $kansas_city_page_id) {
        // Prepare the data array to send to our helper function
        $data_for_webhook = [
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'email'         => $email,
            'mobile_number' => $phone,
            'zip'           => $zip,
        ];
        send_kansas_city_submission_to_chiirp($data_for_webhook);
    }
    
    // =================================================================
    // END: NEW CHIRP WEBHOOK LOGIC
    // =================================================================

        // Return success response with both API responses
        wp_send_json_success([
            'message' => 'Form submitted successfully.',
            'customer_response' => $decoded_response,
            'slots_response' => $decoded_slots_search_response,
            'status_code' => $status_code,

        ]);

    }

    wp_die(); // this is required to terminate immediately and return a proper response
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_submit_estimate_sm_form', 'handle_estimate_sm_form_submission');
add_action('wp_ajax_nopriv_submit_estimate_sm_form', 'handle_estimate_sm_form_submission');

function handle_both_submissions()
{
    // Verify reCAPTCHA Enterprise
    $recaptcha_token = sanitize_text_field($_POST['recaptcha_token']);
    $recaptcha_project_id = 'virtual-equator-450216-g8';
    $recaptcha_api_key = 'AIzaSyCIQauwT_clQpb8smKc7Jkxs3176LCKGpc';

    $recaptcha_response = wp_remote_post("https://recaptchaenterprise.googleapis.com/v1/projects/$recaptcha_project_id/assessments?key=$recaptcha_api_key", [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'event' => [
                'token' => $recaptcha_token,
                'siteKey' => '6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt',
                'expectedAction' => 'submit'
            ]
        ])
    ]);

    if (is_wp_error($recaptcha_response)) {
        wp_send_json_error(['message' => 'reCAPTCHA request failed.']);
    }

    $recaptcha_body = json_decode(wp_remote_retrieve_body($recaptcha_response), true);

    if (
        empty($recaptcha_body['tokenProperties']['valid']) ||
        $recaptcha_body['riskAnalysis']['score'] < 0.5
    ) {
        wp_send_json_error(['message' => 'reCAPTCHA failed or suspicious activity detected.']);
    }

    // Sanitize and collect form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = koala_normalize_phone( sanitize_text_field($_POST['mobile_number']) );
    $address1 = sanitize_text_field($_POST['address1']);
    $address2 = sanitize_text_field($_POST['address2']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    $consent = sanitize_text_field($_POST['DoNotText']);
    $consent_email = sanitize_text_field($_POST['DoNotEmail']);
    $consent_sms_marketing = filter_var($_POST['cust_smsmarketingconsent'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $key = sanitize_text_field($_POST['key']);
    $sm_key = sanitize_text_field($_POST['sm_key']);
    $utm_source   = sanitize_text_field($_POST['UtmSource'] ?? '');
    $utm_medium   = sanitize_text_field($_POST['UtmMedium'] ?? '');
    $utm_campaign = sanitize_text_field($_POST['UtmCampaign'] ?? '');

    // Prepare data for the first API call (customers endpoint)
    $data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'addresses' => [
            [
                "street" => $address1,
                "street_line_2" => $address2,
                "city" => $city,
                "state" => $state,
                "zip" => $zip,
            ],
        ],
        'DoNotText' => $consent,
        'DoNotEmail' => $consent_email,
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];

    // Prepare data for the the post type
    $post_type_data = [
        'ip_address' => '',
        'full_name' => $first_name . ' ' . $last_name,
        'email' => $email,
        'mobile_number' => $phone,
        'address' => $address1 . ' ' . $address2 . ', ' . $city . ' ' . $state . ' ' . $zip,
    ];

    // First API call
    $response = wp_remote_post('https://api.housecallpro.com/customers', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the form.']);
        wp_die();
    }

    $response_body = wp_remote_retrieve_body($response);
    $decoded_response = json_decode($response_body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        wp_die();
    }

    // Extract the customer ID from the first response
    $customer_id = $decoded_response['id'] ?? null;
    if (!$customer_id) {
        wp_send_json_error(['message' => 'Customer ID not returned from the first API call.']);
        wp_die();
    }

    // Prepare data for the second API call (leads endpoint)
    $lead_data = [
        'customer_id' => $customer_id,
        'address' => [
            "street" => $address1,
            "street_line_2" => $address2,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
        ],
        'lead_source' => "Website",
        'DoNotText' => $consent,
        'DoNotEmail' => $consent_email,
        'UtmSource' => $utm_source,
        'UtmMedium' => $utm_medium,
        'UtmCampaign' => $utm_campaign,
    ];

    // Second API call
    $lead_response = wp_remote_post('https://api.housecallpro.com/leads', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . $key,
        ],
        'body' => json_encode($lead_data),
    ]);

    if (is_wp_error($lead_response)) {
        wp_send_json_error(['message' => 'There was an error submitting the lead to the second API endpoint.']);
        wp_die();
    }

    $lead_response_body = wp_remote_retrieve_body($lead_response);
    $decoded_lead_response = json_decode($lead_response_body, true);
    $status_code = wp_remote_retrieve_response_code($lead_response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error([
            'message' => 'Invalid JSON response received from the second API.',
            'raw_response' => $lead_response_body,
            'status_code' => $status_code
        ]);
        wp_die();
    }

    $third_api_response = null;
    if ($status_code === 201) {
        $submitted_page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;

        // Prepare data for the required payload structure
        // Conditionally build the data payload
        $target_page_ids = [66343, 66325];
        if (in_array($submitted_page_id, $target_page_ids)) {
            // --- NEW PAYLOAD for the specific page(s) ---
            $third_api_data = [
                "Matches" => [
                    [
                        "Name" => $first_name . " " . $last_name,
                        "Phone" => $phone,
                        "Email" => $email,
                        "Address1" => $address1,
                        "Address2" => $address2,
                        "City" => $city,
                        "State" => $state,
                        "Zip" => $zip,
                        "LeadSource" => "",
                        "Category" => "Prospect", // Add category
                        "DoNotText" => $consent, 
                        'DoNotEmail' => $consent_email,
                        // "Notes" => [
                        //     ["Body" => "Lead submitted from website form (New Routing)."]
                        // ],
                        'UtmSource' => $utm_source,
                        'UtmMedium' => $utm_medium,
                        'UtmCampaign' => $utm_campaign,
                    ]
                ],
                "DistributeLead" => true, // CRITICAL: Add distribution flag
                "ApiKey" => $sm_key,
            ];
        } else {
            // --- ORIGINAL PAYLOAD for all other pages ---
            $third_api_data = [
                "Matches" => [
                    [
                        "Name" => $first_name . " " . $last_name,
                        "Phone" => $phone,
                        "Email" => $email,
                        "Address1" => $address1,
                        "Address2" => $address2,
                        "City" => $city,
                        "State" => $state,
                        "Zip" => $zip,
                        "LeadSource" => "Website",
                        "DoNotText'" => $consent,
                        'DoNotEmail' => $consent_email,
                        'UtmSource' => $utm_source,
                        'UtmMedium' => $utm_medium,
                        'UtmCampaign' => $utm_campaign,
                    ]   
                ],
                "ApiKey" => $sm_key,
                "ResultCode" => "",
                "Message" => ""
            ];
        }

        // Third API call
        $third_api_response = wp_remote_post('https://serviceminder.io/api/contacts/addupdate', [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($third_api_data),
        ]);
    }

    // Retrieve third API response body
    $third_api_response_body = $third_api_response ? wp_remote_retrieve_body($third_api_response) : null;
    $decoded_third_api_response = $third_api_response_body ? json_decode($third_api_response_body, true) : null;

    save_submission_to_cpt($post_type_data);

    // =================================================================
    // START: NEW CHIRP WEBHOOK LOGIC
    // =================================================================

    // The Page ID for the Kansas City page is 4815.
    $kansas_city_page_id = 4815; 
    $submitted_page_id = isset($_POST['page_id']) ? absint($_POST['page_id']) : 0;

    // Check if the submission is from the target page
    if ($submitted_page_id === $kansas_city_page_id) {
        // Prepare the data array to send to our helper function
        $data_for_webhook = [
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'email'         => $email,
            'mobile_number' => $phone,
            'zip'           => $zip,
        ];
        send_kansas_city_submission_to_chiirp($data_for_webhook);
    }
    
    // =================================================================
    // END: NEW CHIRP WEBHOOK LOGIC
    // =================================================================

    // Return success response with all API responses
    wp_send_json_success([
        'message' => 'Form submitted successfully.',
        'customer_response' => $decoded_response,
        'lead_response' => $decoded_lead_response,
        'third_api_response' => $decoded_third_api_response,
        'status_code' => $status_code,
    ]);

    wp_die();
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_handle_both_submissions', 'handle_both_submissions');
add_action('wp_ajax_nopriv_handle_both_submissions', 'handle_both_submissions');

function handle_get_slots_from_date()
{
    // Check if the nonce is present and valid
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_slots_from_date_nonce')) {
        wp_send_json_error(['message' => 'Invalid security token.']);
        wp_die(); // Required to stop execution and return a proper response
    }

    // Sanitize and collect form data
    $contactId = sanitize_text_field($_POST['contact_id']);
    $serviceId = sanitize_text_field($_POST['service_id']);
    $date = sanitize_text_field($_POST['selected_date']);
    $apiKey = sanitize_text_field($_POST['api_key']);

    // Prepare data for the first API call (customers endpoint)
    $data = [
        "ContactId" => $contactId,
        "ServiceId" => $serviceId,
        "SearchDate" => $date,
        "SlotWindowDays" => 2,
        "ApiKey" => $apiKey,
    ];

    $response = wp_remote_post('https://serviceminder.com/api/appointments/slotsearch', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the data.']);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success(['message' => 'Data submitted successfully.', 'response' => $decoded_response]);
        } else {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        }
    }

    wp_die(); // this is required to terminate immediately and return a proper response
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_get_slots_from_date', 'handle_get_slots_from_date');
add_action('wp_ajax_nopriv_get_slots_from_date', 'handle_get_slots_from_date');

function handle_create_a_booking()
{
    // Check if the nonce is present and valid
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create_a_booking_nonce')) {
        wp_send_json_error(['message' => 'Invalid security token.']);
        wp_die(); // Required to stop execution and return a proper response
    }

    // Sanitize and collect form data
    $contactId = sanitize_text_field($_POST['contact_id']);
    $serviceId = sanitize_text_field($_POST['service_id']);
    $dateTime = sanitize_text_field($_POST['data_time']);
    $dateTimeFormatted = sanitize_text_field($_POST['data_time_formatted']);
    $serviceAgentId = isset($_POST['service_agent_id']) ? absint($_POST['service_agent_id']) : 0;
    $serviceAgentName = sanitize_text_field($_POST['service_agent_name']);
    $driveTimeMinutes = isset($_POST['drive_time_minutes']) ? absint($_POST['drive_time_minutes']) : 0;
    $apiKey = sanitize_text_field($_POST['api_key']);

    // Prepare data for the first API call (customers endpoint)
    $data = [
        "ContactId" => $contactId,
        "ServiceId" => $serviceId,
        "Slots" => [
            [
                "ContactId" => $contactId,
                "DateTime" => $dateTime,
                "DateTimeFormatted" => $dateTimeFormatted,
                "ServiceAgentId" => $serviceAgentId,
                "ServiceAgentName" => $serviceAgentName,
                "DriveTimeMinutes" => $driveTimeMinutes
            ]
        ],
        "ApiKey" => $apiKey,
    ];

    $response = wp_remote_post('https://serviceminder.com/api/appointments/book', [
        'method' => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($data),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error submitting the data.']);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success(['message' => 'Data submitted successfully.', 'response' => $decoded_response]);
        } else {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        }
    }

    wp_die(); // this is required to terminate immediately and return a proper response
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_create_a_booking', 'handle_create_a_booking');
add_action('wp_ajax_nopriv_create_a_booking', 'handle_create_a_booking');

// Hook into wp_head to run the sessionStorage-based script in the head section
function inject_location_before_head_tag_script()
{
    // Retrieve the national GTM tag value from the post type "national-gtm-tag"
    $national_gtm_tag_value = '';
    $national_gtm_post = get_posts(array(
        'post_type' => 'national-gtm-tag',
        'name' => 'tag',
        'posts_per_page' => 1
    ));

    if (!empty($national_gtm_post)) {
        $national_gtm_tag_value = get_field('national_gtm_tag_script', $national_gtm_post[0]->ID); // Assuming ACF is used for the field
    }
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve the HTML string from sessionStorage
            var locationBeforeHeadTag = sessionStorage.getItem('location_before_head_tag');
            var locationBeforeHeadTagNational = <?php echo json_encode($national_gtm_tag_value); ?>;

            //console.log('locationBeforeHeadTag--', locationBeforeHeadTag);

            if (locationBeforeHeadTag && locationBeforeHeadTag !== "") {
                // Create a temporary container to hold the HTML string
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = locationBeforeHeadTag;
                //console.log('tempDiv--', tempDiv.innerHTML);

                // Append each node to the head tag
                Array.from(tempDiv.childNodes).forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        document.head.appendChild(node);
                    }
                });
            } else {
                //console.log('locationBeforeHeadTagNational--', locationBeforeHeadTagNational);
                // Create a temporary container to hold the HTML string
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = locationBeforeHeadTagNational;
                //console.log('tempDiv--', tempDiv.innerHTML);

                // Append each node to the head tag
                Array.from(tempDiv.childNodes).forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        document.head.appendChild(node);
                    }
                });
            }
        });
    </script>
    <?php
}
// add_action('wp_head', 'inject_location_before_head_tag_script');


// Hook into wp_footer to run the sessionStorage-based script in the before body tag
function inject_location_before_body_tag_script()
{
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve the HTML string from sessionStorage
            var locationBeforeBodyTag = sessionStorage.getItem('location_before_body_tag');
            //console.log('locationBeforeBodyTag--', locationBeforeBodyTag);

            if (locationBeforeBodyTag) {
                // Create a temporary container to hold the HTML string
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = locationBeforeBodyTag;
                //console.log('tempDiv--', tempDiv.innerHTML);

                // Append each node to the head tag
                Array.from(tempDiv.childNodes).forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        document.body.appendChild(node);
                    }
                });
            }
        });
    </script>
    <?php
}
// add_action('wp_footer', 'inject_location_before_body_tag_script');

// function koala_add_location_service_rewrite_rules($rules)
// {
//     $new_rules = array(
//         '([^/]+)/services/([^/]+)/?$' => 'index.php?location=$matches[1]&service=$matches[2]',
//     );

//     return $new_rules + $rules; // Add new rules at the top
// }
// add_filter('rewrite_rules_array', 'koala_add_location_service_rewrite_rules');

// function koala_add_blog_location_rewrite_rules($rules)
// {
//     $new_rules = array(
//         '([^/]+)/blog/([^/]+)/?$' => 'index.php?location=$matches[1]&blog=$matches[2]',
//     );

//     return $new_rules + $rules; // Add new rules at the top
// }
// add_filter('rewrite_rules_array', 'koala_add_blog_location_rewrite_rules');


// function add_custom_query_vars($vars)
// {
//     $vars[] = 'location';
//     $vars[] = 'service';
//     $vars[] = 'blog';
//     return $vars;
// }
// add_filter('query_vars', 'add_custom_query_vars');

// function koala_custom_template_redirect($template)
// {
//     if (get_query_var('location') && get_query_var('service')) {
//         // Load your custom template
//         return get_stylesheet_directory() . '/services-single.php';
//     }
//     return $template;
// }
// add_filter('template_include', 'koala_custom_template_redirect');

// function koala_custom_blog_template_redirect($template)
// {
//     if (get_query_var('location') && get_query_var('blog')) {
//         // Load your custom template
//         return get_stylesheet_directory() . '/blog-page-single.php';
//     }
//     return $template;
// }
// add_filter('template_include', 'koala_custom_blog_template_redirect');

function custom_location_service_rewrite_rules($rules)
{
    $new_rules = array(
        '([^/]+)/services/([^/]+)/?$' => 'index.php?custom_location_service_redirect=1&location_slug=$matches[1]&service_slug=$matches[2]',
    );
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'custom_location_service_rewrite_rules');

function custom_location_service_query_vars($vars)
{
    $vars[] = 'custom_location_service_redirect';
    $vars[] = 'location_slug';
    $vars[] = 'service_slug';
    return $vars;
}
add_filter('query_vars', 'custom_location_service_query_vars');

function custom_location_service_template()
{
    if (get_query_var('custom_location_service_redirect')) {
        $location_slug = get_query_var('location_slug');
        $service_slug = get_query_var('service_slug');

        // Generate the expected post slug: "spray-foam-insulation-austin"
        $expected_slug = $service_slug . '-' . $location_slug;

        // Query the post by post_name (slug)
        $args = array(
            'post_type' => 'location-service', // Ensure you're querying the correct post type
            'name' => $expected_slug,     // Query by post_name (slug)
            'posts_per_page' => 1,
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            // We have found a matching post
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                // Debug: confirm term exists
                $term = get_term_by('slug', 'blown-in-insulation-services', 'service_category');

                // Get all term slugs on the post (more reliable than get_the_terms in loops)
                $term_slugs = wp_get_post_terms($post_id, 'service_category', array('fields' => 'slugs'));

                $map = my_location_service_category_template_map();

                // Pick the first matching template ID from the map
                $template_id = 0;
                foreach ($term_slugs as $slug) {
                    if (!empty($map[$slug])) {
                        $template_id = (int) $map[$slug];
                        break;
                    }
                }

                // Get the custom field value for the related location (this is a relationship field)
                $location_post = get_post_meta(get_the_ID(), 'related_location', true); // This should return an array of post IDs

                if (!empty($location_post)) {
                    // Assuming 'related_location' is a relationship field that stores post IDs, get the first related location ID
                    $location_post_id = $location_post[0];  // Get the first related location ID
                    $location_name = get_the_title($location_post_id);  // Get the title of the related location post
                } else {
                    $location_name = 'No Location Available'; // Fallback in case no location is found
                }

                // Get the service name (make sure you replace 'service_name' with the actual custom field key)
                $service_name = get_post_meta(get_the_ID(), 'service_name', true);

                if (!$service_name) {
                    $service_name = 'No Service Name Available'; // Fallback if no service name is found
                }

                // Include the custom template for displaying the post
                if ($template_id > 0) {
                  get_header();

                  echo '<main id="brx-content">';

                    if (class_exists('\Bricks\Templates')) {
                      echo (new \Bricks\Templates())->render_shortcode(array('id' => $template_id));
                    } else {
                      echo do_shortcode('[bricks_template id="' . $template_id . '"]');
                    }
                  echo '</main>';

                  get_footer();
                } else {
                  include get_template_directory() . '/services-single.php';
                }

                exit;

            }
        } else {
            // Redirect to 404 if no matching post found
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            include(get_template_directory() . '/404.php');
            exit;
        }
    }
}
add_action('template_redirect', 'custom_location_service_template');
/**
 * Map service_category term slug => Bricks template ID
 * Return 0 (or null) to use the PHP template.
 */
function my_location_service_category_template_map() {
    $map = array(
        'spray-foam-insulation-services' => 79684
    );

    // Let you override in a child theme or plugin
    return apply_filters('my_location_service_category_template_map', $map);
}


function custom_location_blog_rewrite_rules($rules)
{
    $new_rules = array(
        '([^/]+)/blog/([^/]+)/?$' => 'index.php?custom_location_blog_redirect=1&location_slug=$matches[1]&blog_slug=$matches[2]',
    );
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'custom_location_blog_rewrite_rules');

function custom_location_blog_query_vars($vars)
{
    $vars[] = 'custom_location_blog_redirect';
    $vars[] = 'location_slug';
    $vars[] = 'blog_slug';
    return $vars;
}
add_filter('query_vars', 'custom_location_blog_query_vars');

function custom_location_blog_template()
{
    if (get_query_var('custom_location_blog_redirect')) {
        $location_slug = get_query_var('location_slug');
        $blog_slug = get_query_var('blog_slug');

        // Query the post by post_name (slug)
        $args = array(
            'post_type' => 'blog-location', // Your custom post type
            'name' => $blog_slug,      // Query by the blog slug
            'posts_per_page' => 1,                // Only 1 post needed
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            // We have found a matching post
            while ($query->have_posts()):
                $query->the_post();

                // Get the custom field value for the related location (relationship field)
                $location_post = get_post_meta(get_the_ID(), 'related_location', true); // This should return an array of post IDs

                // Get the location name (title of the related location post)
                if (!empty($location_post)) {
                    // Assuming 'related_location' stores post IDs, get the first related location ID
                    $location_post_id = $location_post[0]; // Get the first related location ID
                    $location_name = get_the_title($location_post_id); // Get the title of the related location post
                } else {
                    $location_name = 'No Location Available'; // Fallback if no location found
                }

                // Get the blog post title (post name)
                $blog_name = get_the_title(); // This will get the blog post's title

                // If no blog name, fallback
                if (!$blog_name) {
                    $blog_name = 'No Blog Available'; // Fallback text if no blog name found
                }

                // Include the custom template for displaying the post
                include(get_template_directory() . '/blog-page-single.php');
                exit;

            endwhile;
        } else {
            // Redirect to 404 if no matching post found
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            include(get_template_directory() . '/404.php');
            exit;
        }
    }
}
add_action('template_redirect', 'custom_location_blog_template');

add_action('template_redirect', function () {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Match /{location}/landing-pages/{page-slug}
    if (!preg_match('#^([^/]+)/landing-pages/([^/]+)/?$#', $uri, $matches)) {
        return;
    }

    $location_slug     = sanitize_title($matches[1]);
    $landing_page_slug = sanitize_title($matches[2]);

    // Validate location exists
    $location_obj = get_page_by_path($location_slug, OBJECT, 'location');
    if (!$location_obj) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        include(get_template_directory() . '/404.php');
        exit;
    }

    // Find landing page post by slug
    $query = new WP_Query(array(
        'post_type'      => 'landing-pages',
        'name'           => $landing_page_slug,
        'posts_per_page' => 1,
    ));

    if (!$query->have_posts()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        include(get_template_directory() . '/404.php');
        exit;
    }

    $query->the_post();
    $post_id = get_the_ID();

    // Validate the post's related_location matches the URL location
    $related     = get_post_meta($post_id, 'related_location', true);
    $related_ids = is_array($related) ? $related : (is_numeric($related) ? [(int) $related] : []);

    $location_match = false;
    foreach ($related_ids as $rid) {
        $loc = get_post($rid);
        if ($loc && $loc->post_name === $location_slug) {
            $location_match = true;
            break;
        }
    }

    if (!$location_match) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        include(get_template_directory() . '/404.php');
        exit;
    }

    // Set query context
    global $wp_query, $post;
    $current_post = get_post($post_id);
    $wp_query->is_404           = false;
    $wp_query->is_singular      = true;
    $wp_query->is_page          = true;
    $wp_query->queried_object    = $current_post;
    $wp_query->queried_object_id = $post_id;
    $wp_query->posts             = [$current_post];
    $wp_query->post              = $current_post;
    status_header(200);

    // Set query vars so nav can read location context
    set_query_var('location_slug', $location_slug);
    set_query_var('landing_page_slug', $landing_page_slug);

    // Inject canonical URL into <head>
    add_action('wp_head', function () use ($location_slug, $landing_page_slug) {
        $canonical_url = home_url("/{$location_slug}/landing-pages/{$landing_page_slug}");
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />';
    });

    $template_id = my_landing_page_template_id_us();

    if ($template_id > 0) {
        // Set $post to the location so get_header() inherits the location's
        // page_id, HCP key, SM key etc. — same as location pages.
        $post = $location_obj;
        setup_postdata($location_obj);

        get_header();

        // Swap back to the landing page post so Bricks reads the correct ACF fields.
        $post = $current_post;
        setup_postdata($current_post);

        echo '<main id="brx-content">';
        if (class_exists('\Bricks\Templates')) {
            echo (new \Bricks\Templates())->render_shortcode(array('id' => $template_id));
        } else {
            echo do_shortcode('[bricks_template id="' . $template_id . '"]');
        }
        echo '</main>';
        get_footer();
    } else {
        include(get_template_directory() . '/landing-page-single.php');
    }

    exit;
});

function my_landing_page_template_id_us() {
    return apply_filters('my_landing_page_template_id_us', 82051);
}

add_filter('post_type_link', function ($url, $post) {
    if ($post->post_type !== 'landing-pages') {
        return $url;
    }

    $related     = get_post_meta($post->ID, 'related_location', true);
    $related_ids = is_array($related) ? $related : (is_numeric($related) ? [(int) $related] : []);

    if (empty($related_ids)) {
        return $url;
    }

    $location = get_post($related_ids[0]);
    if (!$location) {
        return $url;
    }

    return home_url('/' . $location->post_name . '/landing-pages/' . $post->post_name . '/');
}, 10, 2);

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/location-data/(?P<slug>[a-zA-Z0-9-]+)', [
        'methods' => 'GET',
        'callback' => 'get_location_data',
        'permission_callback' => '__return_true', // Make sure it's publicly accessible
    ]);
});

function get_location_data($data)
{
    $slug = $data['slug'];

    $cache_key = 'koala_location_data_' . $slug;
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    // Query for the location post using the slug
    $location_post = get_posts(args: [
        'post_type' => 'location',
        'name' => $slug,
        'post_status' => 'publish',
        'numberposts' => 1,
    ]);

    if (empty($location_post)) {
        return new WP_Error('no_location', 'Location not found', ['status' => 404]);
    }

    // Extract the required data (e.g., post title)
    $location_data = [
        'name' => get_post_meta($location_post[0]->ID, 'location_name', true),
        'state' => get_post_meta($location_post[0]->ID, 'location_state', true),
        'address' => get_post_meta($location_post[0]->ID, 'location_address', true),
        'url' => get_permalink($location_post[0]->ID),
        'phone1' => get_post_meta($location_post[0]->ID, 'location_phone_number', true),
        'phone2' => get_post_meta($location_post[0]->ID, 'location_phone_number2', true),
        'nicejobId' => get_post_meta($location_post[0]->ID, 'location_nicejob_id', true),
        'hcpKey' => get_post_meta($location_post[0]->ID, 'housecall_pro_api_key', true),
        'smKey' => get_post_meta($location_post[0]->ID, 'location_serviceminder_api_key', true),
        'grShortcode' => get_post_meta($location_post[0]->ID, 'google_review_shortcode', true),
        'fbLink' => get_post_meta($location_post[0]->ID, 'location_facebook_link', true),
        'instaLink' => get_post_meta($location_post[0]->ID, 'location_instagram_link', true),
        'linkedinLink' => get_post_meta($location_post[0]->ID, 'location_linkedin_link', true),
        'ytLink' => get_post_meta($location_post[0]->ID, 'location_youtube_link', true),
        'scriptInHead' => get_post_meta($location_post[0]->ID, 'script_in_head_tag', true),
        //         'scriptBeforeHead' => get_post_meta($location_post[0]->ID, 'location_before_head_tag_script', true),
        'scriptInBody' => get_post_meta($location_post[0]->ID, 'script_in_body_tag', true),
        'services' => [],
        'wkPage' => [],
        'wrPage' => [],
        'hoPage' => [],
        'rlPage' => [],
        'blogPage' => [],
        'navText' => get_post_meta($location_post[0]->ID, 'location_nav_top_text', true),
        'navTopLink' => get_post_meta($location_post[0]->ID, 'location_nav_top_link', true),
        'faqs' => [],
        'rocNumber' => get_post_meta($location_post[0]->ID, 'roc_number', true),
    ];

    // Fetch the custom field for services (location_service) as an array of post IDs
    $custom_location_services_arr = get_post_meta($location_post[0]->ID, 'location_service', true);

    if (is_array($custom_location_services_arr)) {
        foreach ($custom_location_services_arr as $custom_location_services_arr_item) {
            $title = get_post_meta($custom_location_services_arr_item, 'location_service_name', true);
            $link = get_permalink($custom_location_services_arr_item);

            // Create an associative array (object in JSON)
            $serviceObject = [
                'title' => $title,
                'link' => $link
            ];

            $location_data['services'][] = $serviceObject; // Append to the array
        }
    }

    // Process related pages (wkPage, wrPage, hoPage, rlPage, blogPage) similarly:
    $related_pages = [
        'wkPage' => 'related_wk_page',
        'wrPage' => 'related_wr_page',
        'hoPage' => 'related_ho_page',
        'blogPage' => 'related_blog_page'
    ];

    foreach ($related_pages as $key => $meta_key) {
        $custom_location_arr = get_post_meta($location_post[0]->ID, $meta_key, true);
        if (is_array($custom_location_arr)) {
            foreach ($custom_location_arr as $custom_location_item) {
                $link = get_permalink($custom_location_item);

                $serviceObject = ['link' => $link];
                $location_data[$key][] = $serviceObject; // Append to the respective array
            }
        }
    }

    // Process related FAQ pages
    $custom_location_faqs_arr = get_post_meta($location_post[0]->ID, 'related_faqs', true);
    if (is_array($custom_location_faqs_arr)) {
        foreach ($custom_location_faqs_arr as $custom_location_faqs_arr_item) {
            $title = get_custom_field_value_from_a_post($custom_location_faqs_arr_item, 'faq_name');
            $content = get_custom_field_value_from_a_post($custom_location_faqs_arr_item, 'faq_content');

            $faqObject = [
                'title' => $title,
                'content' => $content
            ];

            $location_data['faqs'][] = $faqObject; // Append to the array
        }
    }

    $custom_location_rl_arr = get_post_meta($location_post[0]->ID, 'related_rl_page', true);
    if (is_array($custom_location_rl_arr)) {
        foreach ($custom_location_rl_arr as $custom_location_rl_arr_item) {
            // Get the terms for the specified taxonomy
            $terms = wp_get_post_terms($custom_location_rl_arr_item, 'resources-page-type');
            $term_slugs = [];

            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    $term_slugs[] = $term->slug; // Get term slug
                }
            }

            // Fetch post details without skipping 'areas-served'
            $title = get_the_title($custom_location_rl_arr_item);
            $link = get_permalink($custom_location_rl_arr_item);

            $term_names = array_map(function ($term) {
                return $term->name;
            }, $terms);

            // Create an associative array (object in JSON)
            $rlObject = [
                'title' => $title,
                'link' => $link,
                'terms' => $term_names,
            ];

            $location_data['rlPage'][] = $rlObject; // Append to the array
        }
    }

    set_transient($cache_key, $location_data, 12 * HOUR_IN_SECONDS);
    return $location_data;
}
add_action('save_post_location', function ($post_id) {
    $slug = get_post_field('post_name', $post_id);
    if ($slug) {
        delete_transient('koala_location_data_' . $slug);
    }
});

add_action('wp_ajax_load_initial_blogs', 'load_initial_blogs_callback');
add_action('wp_ajax_nopriv_load_initial_blogs', 'load_initial_blogs_callback');

function load_initial_blogs_callback()
{
    load_blogs_query(array('posts_per_page' => 9, 'paged' => 1));
    wp_die();
}

add_action('wp_ajax_search_blogs', 'search_blogs_callback');
add_action('wp_ajax_nopriv_search_blogs', 'search_blogs_callback');

function search_blogs_callback()
{
    $search_query = sanitize_text_field($_POST['query']);
    load_blogs_query(array(
        //     'posts_per_page' => 9,
        'paged' => 1,
        's' => $search_query,
        'search_columns' => array('post_title'),
    ));
    wp_die();
}

add_action('wp_ajax_load_more_blogs', 'load_more_blogs_callback');
add_action('wp_ajax_nopriv_load_more_blogs', 'load_more_blogs_callback');

function load_more_blogs_callback()
{
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 2;
    load_blogs_query(array(
        'posts_per_page' => 9,
        'paged' => $paged,
    ));
    wp_die();
}

function load_blogs_query($args)
{
    $defaults = array(
        'post_type' => 'blog-article', // Replace with your post type
    );
    $args = wp_parse_args($args, $defaults);
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $blog_id = get_the_ID();
            $blog_title = get_the_title($blog_id);
            $blog_date = get_field('date', $blog_id); // ACF field for date
            $blog_link = get_permalink($blog_id);
            $blog_image = get_field('thumbnail_image', $blog_id); // ACF field for thumbnail
            $blog_taxonomy_terms = get_the_terms($blog_id, 'category');
            $blog_taxonomy_names = $blog_taxonomy_terms && !is_wp_error($blog_taxonomy_terms)
                ? wp_list_pluck($blog_taxonomy_terms, 'name')
                : [];

            ?>
            <li class="brxe-bhgzqc brxe-div" data-animi="up" data-duration="0.6">
                <?php if ($blog_image): ?>
                    <div class="brxe-pabbtz brxe-block">
                        <img width="593" height="465" src="<?php echo esc_url($blog_image); ?>"
                            class="brxe-ygbmyt brxe-image image-cover css-filter size-large"
                            alt="<?php echo esc_attr($blog_title); ?>" />
                    </div>
                <?php endif; ?>
                <div class="brxe-swwkfr brxe-block">
                    <div class="brxe-mhbrou brxe-block">
                        <div class="brxe-pojbhz brxe-text-basic text-size-regular">
                            <?php echo esc_html($blog_date); ?>
                        </div>
                        <h5 class="brxe-lhjnhb brxe-post-title heading-style-h5">
                            <a href="<?php echo esc_url($blog_link); ?>">
                                <?php echo esc_html($blog_title); ?>
                            </a>
                        </h5>
                    </div>
                    <a href="<?php echo esc_url($blog_link); ?>" class="brxe-fmfqwh brxe-div btn-secondary">
                        <div class="brxe-wbymrh brxe-text-basic">Read More</div>
                    </a>
                    <div class="brxe-ehypiw brxe-div tag bricks-lazy-hidden">
                        <div class="brxe-xuqski brxe-text-basic">
                            <a><?php echo implode(', ', $blog_taxonomy_names); ?></a>
                        </div>
                    </div>
                </div>
            </li>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No blogs found.</p>';
    }
}


add_action('init', function () {
    $post_type = 'blog'; // Replace with the slug of the post type you want to delete posts from

    // Fetch all posts of the specified post type
    $posts = get_posts([
        'post_type' => $post_type,
        'posts_per_page' => -1, // Get all posts
        'post_status' => 'any', // Include drafts, published, trashed, etc.
    ]);

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true); // Force delete the post without sending it to trash
    }
});

function handle_get_zip_codes_in_radius()
{
    // Check if the nonce is present and valid
//     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_zip_codes_in_radius_nonce')) {
//         wp_send_json_error(['message' => 'Invalid security token.']);
//         wp_die(); // Required to stop execution and return a proper response
//     }

    // Sanitize and collect form data
    $zipCode = sanitize_text_field($_POST['zip_code']);
    $radius = sanitize_text_field($_POST['radius']);
    $apiKey = sanitize_text_field($_POST['api_key']);

    // Prepare the URL for the GET request
    $url = "https://www.zipcodeapi.com/rest/{$apiKey}/radius.json/{$zipCode}/{$radius}/mile";

    // Make the GET request
    $response = wp_remote_get($url, [
        'method' => 'GET',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    // Handle the response
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error fetching the ZIP codes.']);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success(['message' => 'ZIP codes fetched successfully.', 'response' => $decoded_response]);
        } else {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        }
    }

    wp_die(); // Required to terminate immediately and return a proper response
}

// Register the AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_get_zip_codes_in_radius', 'handle_get_zip_codes_in_radius');
add_action('wp_ajax_nopriv_get_zip_codes_in_radius', 'handle_get_zip_codes_in_radius');

function handle_get_zip_codes_distance_in_miles()
{
    // Get the input ZIP code
    $input_zip = isset($_POST['input_zip']) ? sanitize_text_field($_POST['input_zip']) : '';
    $nearby_zips_raw = isset($_POST['nearby_zips']) ? $_POST['nearby_zips'] : '';

    // Decode JSON string received from JavaScript
    $nearby_zips = json_decode(stripslashes($nearby_zips_raw), true);

    if (empty($input_zip) || empty($nearby_zips) || !is_array($nearby_zips)) {
        wp_send_json_error(['message' => 'Missing or invalid input ZIP or nearby ZIPs.']);
    }

    $api_key = "KscuTRFvJFCvE0IoDIp1XMtJqYOb3zAGqQuQLr2fouXcaCyHlBcKshJihTn4iBII"; // Ideally, store this securely
    $base_url = "https://www.zipcodeapi.com/rest/$api_key/distance.json";
    $distances = [];

    foreach ($nearby_zips as $zip) {
        $zip = sanitize_text_field($zip);
        $api_url = "$base_url/$input_zip/$zip/mile";

        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            error_log("ZIP API error: " . $response->get_error_message()); // Log errors
            continue;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['distance'])) {
            $distances[] = ['zip' => $zip, 'distance' => $data['distance']];
        }
    }

    // Sort ZIP codes by distance (ascending order)
    usort($distances, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    wp_send_json_success($distances);
}

// // Register AJAX actions
add_action('wp_ajax_get_zip_codes_distance_in_miles', 'handle_get_zip_codes_distance_in_miles');
add_action('wp_ajax_nopriv_get_zip_codes_distance_in_miles', 'handle_get_zip_codes_distance_in_miles');

function remove_trailing_slash_on_pages($string, $type)
{
    if (in_array($type, array('single', 'page'))) {
        return untrailingslashit($string);
    }
    return $string;
}
add_filter('user_trailingslashit', 'remove_trailing_slash_on_pages', 10, 2);

add_filter('wpseo_canonical', function ($canonical) {
    return untrailingslashit($canonical);
});

function redirect_trailing_slash($redirect_url, $requested_url)
{ // Only redirect if the requested URL ends with a slash (and is not the homepage)
    if (!is_front_page() && substr($requested_url, -1) === '/') {
        return untrailingslashit($requested_url);
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'redirect_trailing_slash', 10, 2);

function custom_body_code()
{
    // Only show if NOT in admin AND NOT in Bricks Editor
//    if ( ! is_admin() && ! isset($_GET['bricks']) ) {
//     echo '<div id="loader-wrapper">
//         <dotlottie-player
//             src="' . esc_url( get_stylesheet_directory_uri() . '/assets/images/Loader.lottie' ) . '"
//             autoplay
//             loop
//             style="width: 320px; margin: auto;">
//         </dotlottie-player>
//     </div>';
// }

if ( ! is_admin() && ! isset($_GET['bricks']) ) {
    echo '<div id="loader-wrapper">
        <video
            src="' . esc_url( get_stylesheet_directory_uri() . '/assets/images/Loader.webm' ) . '"
            autoplay
            loop
            muted
            playsinline
            preload="auto"
            style="margin:auto;display:block;">
        </video>
    </div>';
}


}
add_action('wp_footer', 'custom_body_code');

/**code for valid zipcode API start**/
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/valid-zipcode/', [
        'methods' => 'POST',
        'callback' => 'custom_valid_zipcode_api',
        'permission_callback' => '__return_true', // Optional: makes it public
    ]);
});

function custom_valid_zipcode_api($request)
{

    // Sanitize and collect form data
    $zipCode = sanitize_text_field($_POST['zip_code']);
    $radius = sanitize_text_field($_POST['radius']);
    $apiKey = sanitize_text_field($_POST['api_key']);

    /**Adding validations start**/
    // Initialize errors
    $errors = [];

    // Validate zip code: 5-digit numeric
    if (empty($zipCode)) {
        $errors[] = 'Invalid zip code';
    }

    // Validate radius: must be a positive number (e.g. between 1 and 100)
    if (empty($radius) || !is_numeric($radius) || $radius <= 0 || $radius > 50) {
        $errors[] = 'Invalid radius. It must be a number between 1 and 50.';
    }


    // Validate API key: must not be empty
    if (empty($apiKey)) {
        $errors[] = 'API key is required.';
    }



    // If there are validation errors, return them as a response
    if (!empty($errors)) {
        return new WP_REST_Response([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ], 400);
    }
    /**Adding validations end**/

    if (isCanadianPostalCode($zipCode)) {

        $url = "https://www.zipcodeapi.com/rest/v2/CA/{$apiKey}/radius.json/{$zipCode}/{$radius}/mile";

    } else {

        // Prepare the URL for the GET request
        $url = "https://www.zipcodeapi.com/rest/{$apiKey}/radius.json/{$zipCode}/{$radius}/mile";

    }



    // Make the GET request
    $response = wp_remote_get($url, [
        'method' => 'GET',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    // Handle the response
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'There was an error fetching the ZIP codes.']);
    } else {

        $response_body = wp_remote_retrieve_body($response);

        $decoded_response = json_decode($response_body, true);

        if (isCanadianPostalCode($zipCode)) {

            $decoded_response_zipcode = $decoded_response['postal_codes'];

        } else {

            $decoded_response_zipcode = $decoded_response['zip_codes'];
        }


        /**code to fetch location phone number start**/
        foreach ($decoded_response_zipcode as &$item) {



            if (isset($item['postal_code'])) {

                $item['zip_code'] = strtoupper(str_replace(' ', '', $item['postal_code']));
                unset($item['postal_code']);

                $item['state'] = $item['province'];
                unset($item['province']);

            }

            $zip = $item['zip_code'];

            // Query the post with matching location_zip_code
            $query = new WP_Query([
                'post_type' => 'location',
                'posts_per_page' => 1,
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => 'location_zipcode',
                        'value' => $zip,
                        'compare' => '='
                    ],
                    [
                        'key' => 'additional_zipcodes',
                        'value' => ',' . $zip . ',',
                        'compare' => 'LIKE'
                    ],
                    [
                        'key' => 'additional_zipcodes',
                        'value' => $zip . ',',
                        'compare' => 'LIKE'
                    ],
                    [
                        'key' => 'additional_zipcodes',
                        'value' => ',' . $zip,
                        'compare' => 'LIKE'
                    ],
                    [
                        'key' => 'additional_zipcodes',
                        'value' => $zip,
                        'compare' => '='
                    ]
                ],
                'fields' => 'ids',
            ]);


            //additional_zipcodes

            if (!empty($query->posts)) {
                $post_id = $query->posts[0];
                $phone_number = get_post_meta($post_id, 'location_phone_number', true);
                $item['phone_number'] = $phone_number ?: '';
            } else {
                $item['phone_number'] = ''; // or 'Not Found'
            }



        }

        if (isCanadianPostalCode($zipCode)) {
            unset($decoded_response['postal_codes']);
        }

        $decoded_response['zip_codes'] = $decoded_response_zipcode;

        /**code to fetch location phone number end**/

        if (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success(['message' => 'ZIP codes fetched successfully.', 'response' => $decoded_response]);
        } else {
            wp_send_json_error(['message' => 'Invalid JSON response received from the API.', 'raw_response' => $response_body]);
        }
    }

    wp_die(); // Required to terminate immediately and return a proper response

}
/**code for valid zipcode API end**/
/**Check whether postal code is candian start**/
if (!function_exists('isCanadianPostalCode')) {
    function isCanadianPostalCode($postalCode)
    {
        return preg_match('/^[A-Z]\d[A-Z][ ]?\d[A-Z]\d$/i', $postalCode);
    }
}
/**Check whether postal code is candian end**/

/**Code for solving cache issue for location single page start**/
add_filter('rocket_cache_reject_uri', function ($urls) {
    $locations = get_posts([
        'post_type' => 'location',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    foreach ($locations as $post) {
        $urls[] = str_replace(home_url(), '', get_permalink($post));
    }

    return $urls;
});
/**Code for solving cache issue for location single page end**/

add_action('pre_get_posts', 'force_yoast_to_see_location_service_post');
function force_yoast_to_see_location_service_post($query)
{
    // Only affect the main frontend query
    if (!is_admin() && $query->is_main_query()) {
        $location = get_query_var('location_slug');
        $service = get_query_var('service_slug');

        if ($location && $service) {
            $slug = $service . '-' . $location;
            $post = get_page_by_path($slug, OBJECT, 'location-service');

            if ($post) {
                // Force WP to treat this like a single post view
                $query->set('post_type', 'location-service');
                $query->set('p', $post->ID); // force it to load this post
                $query->is_single = true;
                $query->is_singular = true;
                $query->is_page = false;
            }
        }
    }
}

add_action('pre_get_posts', 'force_yoast_to_see_blog_location_post');
function force_yoast_to_see_blog_location_post($query)
{
    // Only on the frontend main query
    if (!is_admin() && $query->is_main_query()) {
        $location = get_query_var('location_slug');
        $blog = get_query_var('blog_slug');

        if ($location && $blog) {
            $post = get_page_by_path($blog, OBJECT, 'blog-location');

            if ($post) {
                $query->set('post_type', 'blog-location');
                $query->set('p', $post->ID); // Load by ID
                $query->is_single = true;
                $query->is_singular = true;
                $query->is_page = false;
            }
        }
    }
}

/*

// Add custom scripts to <head>
add_action('wp_head', function () {
    if (is_singular('location')) {
        $post_id = get_the_ID();
        $script_head = get_field('script_in_head_tag', $post_id);

        if ($script_head) {
            echo $script_head;
            return; // Exit early to skip fallback
        }
    }

    // Fallback GTM + GA head scripts
    ?>
    <!-- Google Tag Manager -->
    <script>
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-KSNRRFL8');
    </script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RFK3ZB6M00"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-RFK3ZB6M00');
      gtag('config', 'G-9RQMD52K78');
    </script>
    <?php
});

// Add noscript iframe + custom <body> scripts
add_action('wp_body_open', function () {
    if (is_singular('location')) {
        $post_id = get_the_ID();
        $script_body = get_field('script_in_body_tag', $post_id);

        if ($script_body) {
            echo $script_body;
            return; // Exit early to skip fallback
        }
    }

    // Fallback GTM noscript
    ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
      <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KSNRRFL8"
      height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <?php
});
*/

add_action('wp_ajax_match_location_by_zip', 'match_location_by_zip');
add_action('wp_ajax_nopriv_match_location_by_zip', 'match_location_by_zip');

function match_location_by_zip()
{
    check_ajax_referer('match_location', 'nonce');
    $zip = sanitize_text_field($_POST['zip_code']);

    $args = [
        'post_type' => 'location',
        'posts_per_page' => -1,
    ];

    $query = new WP_Query($args);

    $all_locations = [];

    foreach ($query->posts as $post) {
        $main_zip = get_field('location_zipcode', $post->ID);
        $additional_zips = get_field('additional_zipcodes', $post->ID);
        $additional_zips = is_array($additional_zips) ? $additional_zips : explode(',', $additional_zips);
        $all_zips = array_map('trim', array_merge([$main_zip], $additional_zips));

        $location_data = [
            'title' => html_entity_decode(get_the_title($post), ENT_QUOTES, 'UTF-8'),
            'address' => get_field('location_address', $post->ID),
            'phone' => get_field('location_phone_number', $post->ID),
            'zipcode' => $main_zip,
            'website' => get_the_permalink($post),
            'key' => get_field('housecall_pro_api_key', $post->ID),
            'sm_key' => get_field('location_serviceminder_api_key', $post->ID),
            'additional_zipcodes' => $additional_zips,
            'id'   => $post->ID,
            'slug' => $post->post_name,
        ];

        // If zip matches any of the known zips, return immediately
        if (in_array($zip, $all_zips)) {
            wp_send_json_success([
                'matched' => true,
                'location' => $location_data,
            ]);
        }

        $all_locations[] = $location_data;
    }

    // No match found – send all locations as fallback
    wp_send_json_success([
        'matched' => false,
        'locations' => $all_locations,
    ]);
}

// 1) Register the CPT
add_action('init', function () {
    register_post_type('form_submission', [
        'label' => 'Form Submissions',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => ['title'],
        'menu_icon' => 'dashicons-feedback',
        'capability_type' => 'post',
    ]);
});

// 2) Helper to save any array of data + IP
function save_submission_to_cpt($data)
{
    // get real user IP
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    // include IP in data
    $data['ip_address'] = $ip;

    // build title: IP – First Last or fallback with timestamp
    if (!empty($data['full_name'])) {
        $title = $ip . ' – ' . sanitize_text_field($data['full_name']);
    } else {
        $title = $ip . ' – Submission ' . date('Y-m-d H:i:s');
    }

    // insert the post
    $post_id = wp_insert_post([
        'post_type' => 'form_submission',
        'post_title' => wp_strip_all_tags($title),
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // save each value as meta
    foreach ($data as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    return $post_id;
}

function output_custom_or_default_gtm_head()
{
    // Output per-location GTM via PHP on location pages and all sub-pages so the
    // tag lands in <head> on initial load, not JS-injected after page load.
    // Uses first URL segment to find the location — same logic as all-pages.js.
    $custom_head_script = '';

    if (is_singular('location')) {
        $custom_head_script = get_field('script_in_head_tag');
    } else {
        $path          = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
        $first_segment = sanitize_title(strtok($path, '/'));
        if ($first_segment) {
            $location_ids = get_posts([
                'post_type'      => 'location',
                'name'           => $first_segment,
                'posts_per_page' => 1,
                'no_found_rows'  => true,
                'fields'         => 'ids',
            ]);
            if ($location_ids) {
                $custom_head_script = get_field('script_in_head_tag', $location_ids[0]);
            }
        }
    }

    // Keep noscript fallbacks in the original HTML. JavaScript-dependent
    // markup is queued below and injected only after visitor interaction.
    $has_custom_head_script = !empty($custom_head_script);
    $custom_head_noscript = '';
    if ($custom_head_script) {
        $custom_head_script = preg_replace_callback(
            '/<noscript\b[^>]*>.*?<\/noscript>/is',
            function ($matches) use (&$custom_head_noscript) {
                $custom_head_noscript .= $matches[0];
                return '';
            },
            $custom_head_script
        );
        echo $custom_head_noscript;
    }

    ?>
    <script type="text/javascript">
        // Tell all-pages.js whether PHP already output the location head script
        // so it skips re-injecting it from the REST API response.
        window.koalaHeadScriptOutput = <?php echo $has_custom_head_script ? 'true' : 'false'; ?>;
        window.koalaBodyScriptOutput = false;
        window.koalaLocationHeadMarkup = <?php echo wp_json_encode($custom_head_script ?: ''); ?>;
        window.koalaLocationBodyMarkup = '';
        window.koalaInteractionScriptsLoaded = false;

        // Insert arbitrary saved markup while recreating script elements so
        // they execute. External scripts retain document order.
        window.koalaInjectLocationMarkup = async function(markup, target) {
            if (!markup || !target) return;

            var template = document.createElement('template');
            template.innerHTML = markup;
            var fragment = template.content.cloneNode(true);
            var scripts = Array.prototype.slice.call(fragment.querySelectorAll('script'));
            var queuedScripts = [];

            scripts.forEach(function(oldScript) {
                var marker = document.createComment('koala-location-script');
                oldScript.parentNode.replaceChild(marker, oldScript);
                queuedScripts.push({ source: oldScript, marker: marker });
            });

            target.appendChild(fragment);

            for (var i = 0; i < queuedScripts.length; i++) {
                var item = queuedScripts[i];
                var script = document.createElement('script');

                Array.prototype.slice.call(item.source.attributes).forEach(function(attribute) {
                    script.setAttribute(attribute.name, attribute.value);
                });

                if (item.source.src) {
                    // Preserve explicit async scripts; otherwise execute saved
                    // external and inline scripts in their original order.
                    if (!item.source.hasAttribute('async')) {
                        script.async = false;
                    }

                    await new Promise(function(resolve) {
                        script.addEventListener('load', resolve, { once: true });
                        script.addEventListener('error', resolve, { once: true });
                        item.marker.parentNode.replaceChild(script, item.marker);
                    });
                } else {
                    script.textContent = item.source.textContent;
                    item.marker.parentNode.replaceChild(script, item.marker);
                }
            }
        };

        // --- Delay GTM, reCAPTCHA and Hotjar until user interaction ---
        var scriptsLoaded = false;

        async function loadThirdPartyScripts() {
            if (scriptsLoaded) return;
            scriptsLoaded = true;
            window.koalaInteractionScriptsLoaded = true;

            await window.koalaInjectLocationMarkup(
                window.koalaLocationHeadMarkup,
                document.head
            );
            await window.koalaInjectLocationMarkup(
                window.koalaLocationBodyMarkup,
                document.body
            );

            (function doLoad() {

            // --- 1. reCAPTCHA Enterprise loading disabled ---
            // var rc = document.createElement('script');
            // rc.src = "https://www.google.com/recaptcha/enterprise.js?render=6LeM0ysrAAAAAKIwt8W-CTQS6KZNq5Mh0NlEhHKt";
            // rc.async = true;
            // document.head.appendChild(rc);

            // --- 2. Load Hotjar ---
            (function(h, o, t, j, a, r) {
                h.hj = h.hj || function() { (h.hj.q = h.hj.q || []).push(arguments) };
                h._hjSettings = { hjid: 6387685, hjsv: 6 };
                a = o.getElementsByTagName('head')[0];
                r = o.createElement('script'); r.async = 1;
                r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
                a.appendChild(r);
            })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');

            // --- 3. Load national GTM ---
            if (!document.querySelector('script[src*="id=GTM-KSNRRFL8"]')) {
                (function(w, d, s, l, i) {
                    w[l] = w[l] || []; w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
                    var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true; j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer', 'GTM-KSNRRFL8');
            }

            })();
        }

        ['click', 'keydown', 'touchstart', 'wheel'].forEach(function(ev) {
            window.addEventListener(ev, loadThirdPartyScripts, { once: true, passive: true });
        });

        // Load immediately on thank-you URLs and in GTM Preview / Tag Assistant
        // mode so conversion tags do not depend on a subsequent interaction.
        var isThankYouUrl = window.location.href.toLowerCase().indexOf('thank-you') !== -1;
        if (isThankYouUrl || /[?&]gtm_debug=/.test(window.location.search)) {
            loadThirdPartyScripts();
        }

        // Force-load CallRail swap.js at DOMContentLoaded if something (e.g. Nitropack
        // render-blocking optimisation) has prevented the static tag from executing.
        // Reads the src from the tag already in the page so no URL is hardcoded here.
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof CallTrk !== 'undefined') return;
            var crTag = document.querySelector('script[src*="cdn.callrail.com"]');
            if (!crTag) return;
            var s = document.createElement('script');
            s.src = crTag.getAttribute('src');
            document.head.appendChild(s);
        });

    </script>
    <?php
}
add_action('wp_head', 'output_custom_or_default_gtm_head', 1);

add_action('wp_head', 'koala_output_location_schema', 2);
function koala_output_location_schema()
{
    if (is_admin() || ! is_singular('location')) {
        return;
    }

    $schema_raw = get_field('schema');
    if (empty($schema_raw)) {
        return;
    }

    $schema_data = json_decode($schema_raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return;
    }

    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <?php
}

add_action('wp_head', 'koala_output_location_service_schema', 2);
function koala_output_location_service_schema()
{
    if (is_admin()) {
        return;
    }

    // Only run on custom location-service URL pages
    if (!get_query_var('custom_location_service_redirect')) {
        return;
    }

    $location = get_query_var('location_slug');
    $service  = get_query_var('service_slug');

    if (empty($location) || empty($service)) {
        return;
    }

    $combined_slug = $service . '-' . $location;

    $query = new WP_Query([
        'post_type'      => 'location-service',
        'name'           => $combined_slug,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    if (empty($query->posts)) {
        return;
    }

    $post_id = $query->posts[0];

    // Skip posts that have a mapped Bricks template — those handle their own schema inline
    $term_slugs = wp_get_post_terms($post_id, 'service_category', ['fields' => 'slugs']);
    $map = my_location_service_category_template_map();
    foreach ($term_slugs as $slug) {
        if (!empty($map[$slug])) {
            return;
        }
    }

    $schema_raw = get_field('schema', $post_id);
    if (empty($schema_raw)) {
        return;
    }

    // Replace location name placeholder if present in the schema
    $related_location = get_post_meta($post_id, 'related_location', true);
    $location_name = !empty($related_location) ? get_the_title($related_location[0]) : '';
    $schema = str_replace('{acf_related_location_name}', $location_name, $schema_raw);

    ?>
    <script type="application/ld+json">
    <?php echo $schema; ?>
    </script>
    <?php
}

add_action('wp_head', 'koala_output_location_blog_schema', 2);
function koala_output_location_blog_schema()
{
    if (is_admin() || !is_singular('location-blog')) {
        return;
    }

    $post_id = get_the_ID();
    $schema_raw = get_field('schema', $post_id);
    if (empty($schema_raw)) {
        return;
    }

    $related_location = get_post_meta($post_id, 'related_location', true);
    $location_name = !empty($related_location) ? get_the_title($related_location[0]) : '';
    $schema = str_replace('{acf_related_location_name}', $location_name, $schema_raw);

    ?>
    <script type="application/ld+json">
    <?php echo $schema; ?>
    </script>
    <?php
}

add_action('wp_head', 'koala_output_resources_landing_schema', 2);
function koala_output_resources_landing_schema()
{
    if (is_admin() || !is_singular('resources-landing-pa')) {
        return;
    }

    $post_id = get_the_ID();
    $schema_raw = get_field('schema', $post_id);
    if (empty($schema_raw)) {
        return;
    }

    $related_location = get_post_meta($post_id, 'rl_related_location', true);
    $location_name = !empty($related_location) ? get_the_title($related_location[0]) : '';
    $schema = str_replace('{acf_related_location_name}', $location_name, $schema_raw);

    ?>
    <script type="application/ld+json">
    <?php echo $schema; ?>
    </script>
    <?php
}

add_action('wp_head', 'koala_output_service_schema', 2);
function koala_output_service_schema()
{
    if (is_admin() || !is_singular('service')) {
        return;
    }

    $schema_raw = get_field('schema');
    if (empty($schema_raw)) {
        return;
    }

    $schema_data = json_decode($schema_raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return;
    }

    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <?php
}

add_action('wp_head', 'koala_output_blog_location_schema', 2);
function koala_output_blog_location_schema()
{
    if (is_admin()) {
        return;
    }

    if (!get_query_var('custom_location_blog_redirect')) {
        return;
    }

    $blog_slug = get_query_var('blog_slug');
    if (empty($blog_slug)) {
        return;
    }

    $query = new WP_Query([
        'post_type'      => 'blog-location',
        'name'           => $blog_slug,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    if (empty($query->posts)) {
        return;
    }

    $post_id = $query->posts[0];

    $schema_raw = get_field('schema', $post_id);
    if (empty($schema_raw)) {
        return;
    }

    $related_location = get_post_meta($post_id, 'related_location', true);
    $location_name = !empty($related_location) ? get_the_title($related_location[0]) : '';
    $schema = str_replace('{acf_related_location_name}', $location_name, $schema_raw);

    ?>
    <script type="application/ld+json">
    <?php echo $schema; ?>
    </script>
    <?php
}

// Fix additional_zipcodes in WP REST API responses for the location post type.
// ACF's REST layer can type-coerce the comma-separated string into a malformed
// number for locations with many zip codes. We override it with the raw meta value
// split into a clean array of strings.
add_filter('rest_prepare_location', function ($response, $post, $request) {
    $data = $response->get_data();
    if (isset($data['acf']['additional_zipcodes'])) {
        $raw = get_post_meta($post->ID, 'additional_zipcodes', true);
        $data['acf']['additional_zipcodes'] = array_values(
            array_filter(array_map('trim', explode(',', (string) $raw)))
        );
        $response->set_data($data);
    }
    return $response;
}, 10, 3);

add_action('wp_head', 'koala_output_homepage_schema', 2);
function koala_output_homepage_schema()
{
    if (is_admin() || get_queried_object_id() !== 13) {
        return;
    }

    $schema_raw = get_field('schema', 13);
    if (empty($schema_raw)) {
        return;
    }

    $schema_data = json_decode($schema_raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return;
    }

    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <?php
}

// SEO vendor manages all schema manually — suppress Yoast's automatic JSON-LD output
add_filter('wpseo_json_ld_output', '__return_false');

add_action('wp_head', function () {
    echo '<style id="koala-critical-init">#footer-us,#usa-slider-why-koala,#usa-our-services,#us-services-sub-nav,#location-nav-box,#custom-service,#custom-service-footer,#main-page-widget,#local-page-widget,#main-page-stories-widget,#local-page-stories-widget,#cta,#cta-alt,#cta-alt2,#cta-quote,#national-nav-quote,#national_estimate-btn,#get-estimate-btn1,#get-estimate-btn2,#areas-served-navlink{display:none}</style>';
}, 2);

function output_custom_or_default_gtm_body()
{
    $custom_body_script = '';

    if (is_singular('location')) {
        $custom_body_script = get_field('script_in_body_tag');
    } else {
        $path          = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
        $first_segment = sanitize_title(strtok($path, '/'));
        if ($first_segment) {
            $location_ids = get_posts([
                'post_type'      => 'location',
                'name'           => $first_segment,
                'posts_per_page' => 1,
                'no_found_rows'  => true,
                'fields'         => 'ids',
            ]);
            if ($location_ids) {
                $custom_body_script = get_field('script_in_body_tag', $location_ids[0]);
            }
        }
    }

    // A noscript fallback must remain in the original HTML because it cannot
    // be inserted by the interaction loader when JavaScript is disabled.
    $has_custom_body_script = !empty($custom_body_script);
    $custom_body_noscript = '';
    if ($custom_body_script) {
        $custom_body_script = preg_replace_callback(
            '/<noscript\b[^>]*>.*?<\/noscript>/is',
            function ($matches) use (&$custom_body_noscript) {
                $custom_body_noscript .= $matches[0];
                return '';
            },
            $custom_body_script
        );
        echo $custom_body_noscript;
    }
    ?>
    <script>
        window.koalaBodyScriptOutput = <?php echo $has_custom_body_script ? 'true' : 'false'; ?>;
        window.koalaLocationBodyMarkup = <?php echo wp_json_encode($custom_body_script ?: ''); ?>;

        // Normally interaction cannot happen before wp_body_open, but handle
        // that edge case so the body payload is never left queued indefinitely.
        if (
            window.koalaInteractionScriptsLoaded &&
            window.koalaLocationBodyMarkup &&
            window.koalaInjectLocationMarkup
        ) {
            window.koalaInjectLocationMarkup(
                window.koalaLocationBodyMarkup,
                document.body
            );
        }
    </script>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe loading="lazy" src="https://www.googletagmanager.com/ns.html?id=GTM-KSNRRFL8" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
    </noscript>
    <?php
}

if (function_exists('wp_body_open')) {
    add_action('wp_body_open', 'output_custom_or_default_gtm_body');
} else {
    add_action('wp_footer', 'output_custom_or_default_gtm_body');
}

add_action('wp_head', function () {
    ?>
    <style>
        .text-size-regular.brxe-text-basic p {
            font-size: 18px;
            line-height: 27px;
        }

        @media (max-width: 478px) {
            #brxe-trohhj {
                background-image: linear-gradient(#8bc34a, #8bc34a);
            }
        }
    </style>
    <?php
}, 1);

// LCP image preload: NitroPack automatically detects and preloads the correct hero image
// using its CDN-rewritten URL — a static PHP preload here would point to the wrong URL.


// Put this in functions.php or a code-snippets plugin
if ( ! function_exists('format_phone_digits') ) {
    function format_phone_digits( $phone ) {
        if ( empty( $phone ) ) return '';
        $digits = preg_replace('/\D+/', '', $phone);
        if ( $digits === '' ) return '';
        return '+' . $digits;
    }
}

// Strip leading US country code "1" from submitted phone numbers before
// sending to ServiceMinder / HCP. Handles typed or pasted numbers like
// 15551234567, 1-555-123-4567, +1 (555) 123-4567, etc.
function koala_normalize_phone( $phone ) {
    if ( empty( $phone ) ) return '';
    $digits = preg_replace( '/\D+/', '', $phone );
    if ( strlen( $digits ) === 11 && $digits[0] === '1' ) {
        $digits = substr( $digits, 1 );
    }
    if ( strlen( $digits ) !== 10 ) return $phone; // unrecognized format — pass through unchanged
    return '(' . substr( $digits, 0, 3 ) . ') ' . substr( $digits, 3, 3 ) . '-' . substr( $digits, 6, 4 );
}

function tel_acf_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'field'   => 'location_phone_number', // default field name
            'post_id' => '',                       // 'option' for ACF options page or a numeric post ID
            'link'    => '0',                      // set to 1 to output "tel:+..."
            'fallback'=> '',                       // optional fallback string if field empty
        ),
        $atts,
        'tel_acf'
    );

    $field = sanitize_text_field( $atts['field'] );
    $post_id = $atts['post_id'];

    if ( $post_id === 'option' || $post_id === 'options' ) {
        $value = get_field( $field, 'option' );
    } elseif ( $post_id !== '' ) {
        $value = get_field( $field, $post_id );
    } else {
        $value = get_field( $field ); // current post context
    }

    if ( ! $value ) {
        return $atts['fallback'];
    }

    $tel = format_phone_digits( $value );
    if ( $atts['link'] === '1' || strtolower( $atts['link'] ) === 'true' ) {
        return 'tel:' . $tel;
    }
    return $tel;
}
add_shortcode( 'tel_acf', 'tel_acf_shortcode' );


/**
 * Sends customer data to the Chirp webhook for the Kansas City location.
 *
 * @param array $form_data The sanitized form data from the submission.
 */
function send_kansas_city_submission_to_chiirp($form_data) {
    // The webhook URL provided by the client.
    $webhook_url = 'https://app.chiirp.com/integrations/webhook/clients/5445/6d3500144bba147ff6f8c587ce699d4569a4e3db';

    /**
     * !!! IMPORTANT !!!
     * This is a placeholder payload based on your instructions.
     * from the Chirp documentation or the client.
     */
    $chiirp_payload = [
        'first_name' => $form_data['first_name'],
        'last_name'  => $form_data['last_name'],
        'email'      => $form_data['email'],
        'phone'      => $form_data['mobile_number'],
        'zip_code'   => $form_data['zip'],
        'source'     => 'Kansas City Website Form'
    ];

    // Send the data to the webhook using a non-blocking request.
    wp_remote_post($webhook_url, [
        'method'      => 'POST',
        'headers'     => ['Content-Type' => 'application/json'],
        'body'        => json_encode($chiirp_payload),
        'blocking'    => false, // Makes the request non-blocking so it doesn't slow down the user.
    ]);
}

add_action('wp_head', function () {

    // Only run on the front end
    if (is_admin()) {
        return;
    }

    // Get the current request URI
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Check if "hickory" exists anywhere in the URL
    if (stripos($request_uri, 'hickory') === false) {
        return;
    }
    ?>
    <div id="rwl-neighborhood"></div>
    <script type="text/javascript">
    (function(){
        var d = document, t = 'script',
            o = d.createElement(t),
            s = d.getElementsByTagName(t)[0];

        o.src = 'https://app.realworklabs.com/static/plugin/loader.js';

        window.addEventListener('rwlPluginReady', function () {
            window.rwlPlugin.init('https://app.realworklabs.com', 'LD1VI6o7rvH9b78t');
        }, false);

        s.parentNode.insertBefore(o, s);
    }());
    </script>
    <?php
});

function koala_add_schema_post_8449() {

    if ( ! is_singular() || get_queried_object_id() !== 8449 ) {
        return;
    }

    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "LocalBusiness",
          "@id": "https://koalainsulation.com/atlanta-perimeter-north/#localbusiness",
          "name": "Koala Insulation of Atlanta Perimeter North",
          "url": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation",
          "logo": "https://koalainsulation.com/wp-content/uploads/2021/01/koala-logo.png",
          "image": "https://koalainsulation.com/wp-content/uploads/2021/01/spray-foam-insulation.jpg",
          "telephone": "(404) 994-1287",
          "email": "atlantaperimeternorth@koalainsulation.com",
          "priceRange": "$$",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "120 Forrest Lake Dr NW",
            "addressLocality": "Atlanta",
            "addressRegion": "GA",
            "postalCode": "30327",
            "addressCountry": "US"
          },
          "areaServed": {
            "@type": "Place",
            "name": "Atlanta Perimeter North"
          },
          "sameAs": [
            "https://www.youtube.com/@koalainsulation"
          ]
        },
        {
          "@type": "Service",
          "@id": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation/#service",
          "serviceType": "Spray Foam Insulation",
          "name": "Spray Foam Insulation Services",
          "provider": {
            "@id": "https://koalainsulation.com/atlanta-perimeter-north/#localbusiness"
          },
          "areaServed": {
            "@type": "Place",
            "name": "Atlanta Perimeter North"
          },
          "description": "Koala Insulation provides expert spray foam insulation in Atlanta, enhancing energy efficiency and ensuring proper installation for lasting performance in homes and businesses.",
          "offers": {
            "@type": "Offer",
            "url": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation",
            "priceCurrency": "USD",
            "price": "Contact for Quote",
            "availability": "https://schema.org/InStock"
          }
        },
        {
          "@type": "WebPage",
          "@id": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation/#webpage",
          "url": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation",
          "name": "Top Spray Foam Insulation in Atlanta for Energy Efficiency Solutions",
          "description": "Koala Insulation provides expert spray foam insulation in Atlanta, enhancing energy efficiency and ensuring proper installation for lasting performance in homes and businesses.",
          "isPartOf": {
            "@type": "WebSite",
            "@id": "https://koalainsulation.com/atlanta-perimeter-north/#website",
            "url": "https://koalainsulation.com/atlanta-perimeter-north",
            "name": "Koala Insulation of Atlanta Perimeter North"
          },
          "about": {
            "@id": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation/#service"
          },
          "primaryImageOfPage": {
            "@type": "ImageObject",
            "url": "https://koalainsulation.com/wp-content/uploads/2021/01/spray-foam-insulation.jpg"
          },
          "inLanguage": "en-US"
        },
        {
          "@type": "BreadcrumbList",
          "@id": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation/#breadcrumb",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Home",
              "item": "https://koalainsulation.com/atlanta-perimeter-north"
            },
            {
              "@type": "ListItem",
              "position": 2,
              "name": "Services",
              "item": "https://koalainsulation.com/atlanta-perimeter-north/services"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "Spray Foam Insulation",
              "item": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation"
            }
          ]
        },
        {
          "@type": "FAQPage",
          "@id": "https://koalainsulation.com/atlanta-perimeter-north/services/spray-foam-insulation/#faq",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "Is spray foam right for my attic in Atlanta?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Spray foam insulation can be an excellent choice for attics by addressing moisture and surface preparation. When applied by certified professionals, it creates a dry, comfortable, and energy-efficient environment that provides lasting comfort and reduced energy costs."
              }
            },
            {
              "@type": "Question",
              "name": "How much does spray foam insulation cost in Atlanta?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Costs vary based on square footage and project complexity. Generally, small attics range from $1,000 to $2,000, medium attics from $2,000 to $5,000, and large attics from $4,000 to $8,000. Contact us for a free personalized quote."
              }
            },
            {
              "@type": "Question",
              "name": "How long does spray foam insulation last?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "One of the greatest benefits of spray foam is its longevity. Unlike some materials that settle or degrade over time, spray foam is designed to last the lifetime of the building. It maintains its shape and R-value, providing a permanent solution for air sealing and thermal protection."
              }
            },
            {
              "@type": "Question",
              "name": "Can spray foam improve the air quality in my home?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes. By creating an airtight seal, spray foam prevents outdoor pollutants, allergens, and dust from entering your home. It also helps manage humidity levels, which reduces the risk of mold and mildew growth, leading to a healthier indoor environment."
              }
            },
            {
              "@type": "Question",
              "name": "Does spray foam insulation help with soundproofing?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Spray foam is excellent for sound attenuation. Open-cell spray foam, in particular, is highly effective at absorbing sound waves, which helps reduce noise transfer between rooms or from the outside, creating a quieter living or working space."
              }
            },
            {
              "@type": "Question",
              "name": "Is spray foam insulation environmentally friendly?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Modern spray foam products are increasingly eco-friendly. Many are made with rapidly renewable materials and have low Global Warming Potential (GWP). Additionally, by significantly reducing energy consumption for heating and cooling, spray foam lowers the overall carbon footprint of your property."
              }
            },
            {
              "@type": "Question",
              "name": "How long does the installation process take?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Most residential spray foam projects can be completed in one to two days. The exact timeline depends on the size of the area being insulated and the amount of preparation or removal of old insulation required. We strive to minimize disruption to your daily routine."
              }
            },
            {
              "@type": "Question",
              "name": "Will I need to leave my home during installation?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "For safety reasons, we typically recommend that residents and pets vacate the premises during the application and for a specified curing period (usually 24 hours). This ensures no one is exposed to the fumes or particulates during the chemical reaction process."
              }
            },
            {
              "@type": "Question",
              "name": "What is the difference between open-cell and closed-cell spray foam?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Open-cell foam is lighter, more flexible, and great for soundproofing. Closed-cell foam is much denser, provides a higher R-value per inch, and acts as a vapor barrier. Our experts can recommend the best option based on your specific needs and budget."
              }
            },
            {
              "@type": "Question",
              "name": "Does spray foam provide a fire barrier?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Many spray foam products include fire-retardant additives. While it is not 'fireproof,' it can help slow the spread of a fire. Depending on local building codes, an additional thermal or ignition barrier may be required over the foam in certain areas like attics or crawl spaces."
              }
            },
            {
              "@type": "Question",
              "name": "Can spray foam be applied in cold weather?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes, but it requires specialized 'cold weather' formulations and equipment to ensure the chemicals react and bond correctly. Our technicians are trained to adjust for seasonal conditions to ensure a high-quality installation year-round in Atlanta."
              }
            },
            {
              "@type": "Question",
              "name": "Where should spray foam be installed in a home?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "We recommend spray foam for attics (to seal leaks), basements and crawl spaces (to act as a vapor barrier), exterior walls (for moisture protection), and even floors. It is highly versatile for any irregular gaps in a building's envelope."
              }
            },
            {
              "@type": "Question",
              "name": "Can I spray foam insulation myself?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "While DIY kits exist, it is not a recommended project. Professional installers have the training, equipment, and ventilation controls needed for safety and compliance. Poor DIY installation can lead to moisture problems or off-ratio foam."
              }
            }
          ]
        }
      ]
    }
    </script>
    <?php
}
add_action( 'wp_head', 'koala_add_schema_post_8449', 20 );

function koala_add_schema_post_47412() {

    if ( ! is_singular() || get_queried_object_id() !== 47412 ) {
        return;
    }

    ?>
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://koalainsulation.com/#organization",
      "name": "Koala Insulation",
      "url": "https://koalainsulation.com/",
      "logo": {
        "@type": "ImageObject",
        "url": "https://koalainsulation.com/wp-content/uploads/2022/03/koala-logo.png"
      },
      "sameAs": [
        "https://www.facebook.com/koalainsulation",
        "https://www.linkedin.com/company/koalainsulation",
        "https://www.instagram.com/koalainsulation"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "https://koalainsulation.com/#website",
      "url": "https://koalainsulation.com/",
      "name": "Koala Insulation",
      "publisher": {
        "@id": "https://koalainsulation.com/#organization"
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "https://koalainsulation.com/blog/thinking-of-starting-an-insulation-business-in-2024-heres-what-you-need-to-know/#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "https://koalainsulation.com/"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Blog",
          "item": "https://koalainsulation.com/blog/"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "Thinking of Starting an Insulation Business in 2026? Here's What You Need to Know",
          "item": "https://koalainsulation.com/blog/thinking-of-starting-an-insulation-business-in-2024-heres-what-you-need-to-know"
        }
      ]
    },
    {
      "@type": "BlogPosting",
      "@id": "https://koalainsulation.com/blog/thinking-of-starting-an-insulation-business-in-2024-heres-what-you-need-to-know/#blogposting",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://koalainsulation.com/blog/thinking-of-starting-an-insulation-business-in-2024-heres-what-you-need-to-know"
      },
      "headline": "Thinking of Starting an Insulation Business in 2026? Here's What You Need to Know",
      "description": "A detailed guide covering startup costs, equipment requirements, profitability, licensing, and franchise opportunities for entrepreneurs starting an insulation business.",
      "image": {
        "@type": "ImageObject",
        "url": "https://koalainsulation.com/wp-content/uploads/2024/01/insulation-business.jpg"
      },
      "author": {
        "@type": "Organization",
        "name": "Koala Insulation"
      },
      "publisher": {
        "@id": "https://koalainsulation.com/#organization"
      },
      "datePublished": "2024-01-01",
      "dateModified": "2026-03-03",
      "articleSection": "Business",
      "keywords": [
        "how to start an insulation business",
        "insulation business startup costs",
        "spray foam business",
        "insulation franchise opportunities",
        "is insulation business profitable"
      ],
      "wordCount": "1800",
      "speakable": {
        "@type": "SpeakableSpecification",
        "cssSelector": [
          "h1",
          "h2",
          "p"
        ]
      },
      "about": [
        {
          "@type": "Thing",
          "name": "Insulation Business Startup"
        },
        {
          "@type": "Thing",
          "name": "Spray Foam Insulation"
        },
        {
          "@type": "Thing",
          "name": "Home Services Franchise"
        }
      ],
      "isPartOf": {
        "@id": "https://koalainsulation.com/#website"
      }
    },
    {
      "@type": "FAQPage",
      "@id": "https://koalainsulation.com/blog/thinking-of-starting-an-insulation-business-in-2024-heres-what-you-need-to-know/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Is starting an insulation business profitable?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Insulation businesses often report gross margins between 30% and 50%, with strong profitability driven by spray foam services, retrofit demand, and energy efficiency incentives."
          }
        },
        {
          "@type": "Question",
          "name": "How much does it cost to start an insulation business?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Startup costs typically range from $120,000 to $250,000 depending on equipment, spray foam rigs, vehicles, licensing, insurance, and working capital."
          }
        },
        {
          "@type": "Question",
          "name": "What equipment is needed for a spray foam insulation company?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Essential equipment includes a spray foam proportioner machine, heated hose system, spray guns, air compressor, insulation blowing machine, and safety gear."
          }
        },
        {
          "@type": "Question",
          "name": "Should I start independently or join an insulation franchise?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Franchise models provide structured training, vendor relationships, marketing systems, and operational support, which can help new business owners reach profitability faster."
          }
        }
      ]
    }
  ]
}
    </script>
    <?php
}
add_action( 'wp_head', 'koala_add_schema_post_47412', 20 );

add_action('init', function() {
    if (isset($_GET['secure_csv']) && $_GET['secure_csv'] === 'reviews') {
        
        // 1. REPLICATE TEMPLATE LOGIC: Get the slug from the Referer (since it's an AJAX fetch)
        $referer = wp_get_referer();
        if (!$referer) { $referer = $_SERVER['HTTP_REFERER']; }
        
        // Extract the location slug (e.g., from /location-name/recent-projects)
        $path = parse_url($referer, PHP_URL_PATH);
        $location_slug = str_replace('/recent-projects', '', $path);
        $location_slug = trim($location_slug, '/');

        // 2. Query the Location post just like your template does
        $location = get_page_by_path($location_slug, OBJECT, 'location');

        if ($location) {
            // 3. Query the "Recent Projects" post related to this location
            $related_project = get_posts(array(
                'post_type' => 'resources-landing-pa',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'rl_related_location',
                        'value' => $location->ID,
                        'compare' => 'LIKE',
                    ),
                ),
            ));

            if (!empty($related_project)) {
                // 4. Get the CSV from the specific project found
                $csv_field = get_field('rl_nicejob_csv', $related_project[0]->ID);
                $file_id = is_array($csv_field) ? $csv_field['ID'] : attachment_url_to_postid($csv_field);
                $file_path = get_attached_file($file_id);

                if ($file_path && file_exists($file_path)) {
                    if (ob_get_length()) ob_clean(); 
                    header('Content-Type: text/csv; charset=UTF-8');
                    header('Access-Control-Allow-Origin: *'); 
                    header('X-Robots-Tag: noindex, nofollow');
                    readfile($file_path);
                    exit;
                }
            }
        }
        
        // If we reach here, show a helpful error for debugging
        wp_die("CSV Bridge Error: Could not find related project for slug: " . $location_slug);
    }
}, 20);


/**
 * Filter: Bricks Dynamic Tag Replacer (Native Meta Version)
 * * This function searches for the literal string '{acf_related_location_name}' 
 * within content rendered by Bricks and replaces it with actual post metadata.
 * By using get_post_meta(), we bypass ACF's extra processing for better performance.
 *
 * @param  string $content The raw content being processed by Bricks or WordPress.
 * @return string          The sanitized content with the placeholder replaced.
 */
function my_custom_bricks_placeholder_replace( $content ) {
    
    // 1. Define the placeholder text we are searching for
    $placeholder = '{acf_related_location_name}';

    // 2. Perform a quick check to see if the placeholder exists in this string
    // This prevents unnecessary database calls on elements that don't need it.
    if ( is_string( $content ) && strpos( $content, $placeholder ) !== false ) {
        
        /**
         * 3. Retrieve Data using Native WordPress Meta
         * We use get_the_ID() to target the current post.
         * Change 'related_location_name' to your exact meta key/field slug.
         */
        $meta_key   = 'related_location_name'; 
        $real_value = get_post_meta( get_the_ID(), $meta_key, true );

        // 4. Swap the placeholder for the real value if it exists
        if ( ! empty( $real_value ) ) {
            $content = str_replace( $placeholder, $real_value, $content );
        } else {
            /**
             * 5. Cleanup
             * If the field is empty, we remove the placeholder entirely
             * to avoid showing raw curly brackets to the user.
             */
            $content = str_replace( $placeholder, '', $content );
        }
    }

    return $content;
}

/**
 * Hook into Bricks standard render process.
 * This covers Heading, Basic Text, and other standard elements.
 */
add_filter( 'bricks/frontend/render_data', 'my_custom_bricks_placeholder_replace', 10, 1 );

/**
 * Hook into Bricks Dynamic Data parser.
 * This is crucial if your placeholder is nested inside another dynamic tag.
 */
add_filter( 'bricks/dynamic_data/render_content', 'my_custom_bricks_placeholder_replace', 10, 1 );

/**
 * Hook into standard WordPress content filter.
 * This ensures the replacement happens inside the "Post Content" (Gutenberg) area.
 */
add_filter( 'the_content', 'my_custom_bricks_placeholder_replace', 20 );


//For Canoncial URL from Yoast SEO, the URL is not correct due to complicated redirection method used
add_filter('wpseo_canonical', 'fix_blog_location_canonical');
function fix_blog_location_canonical($canonical) {
    if (is_singular('blog-location') && !is_preview()) {
        return get_permalink();
    }
    return $canonical;
}


// Remove Gutenberg Block Library CSS from loading on the frontend
function smart_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );        // WordPress core
    wp_dequeue_style( 'wp-block-library-theme' );  // WordPress core theme options
    wp_dequeue_style( 'wc-block-style' );          // WooCommerce blocks
    wp_dequeue_style( 'global-styles' );           // WP 5.9+ Inline styles
}
add_action( 'wp_enqueue_scripts', 'smart_remove_wp_block_library_css', 100 );


/**
 * Defer non-critical CSS and JS to solve "Render Blocking" issues
 * Protected from breaking the Bricks Builder interface
 */
function koala_defer_scripts($tag, $handle, $src) {
    if (is_admin()) {
        return $tag; // Don't modify admin scripts
    }

    // If the Bricks builder interface is loading, do not defer ANY scripts
    if (function_exists('bricks_is_builder') && bricks_is_builder()) {
        return $tag;
    }

    // List of script handles from your audit to DEFER
    $defer_scripts = [
        'all-pages-js',
        'koala-sliders',
        'koala-reviews',
        'koala-popup',
        'custom-service-js',
        'custom-map-init',
        'location-page',
        'faq-accordion',
        'custom-service-script',
        'js-cookie',
        'bricks-js-cookie',
        'bricks-fontfaceobserver',
        'handl-utm-grabber',
        'wpda_rest_api',
        'wp-polyfill',
        'underscore',
        'backbone',
        'wp-api',
        'api-request',
        'smush-lazy-load',
        'bricks-filters',
    ];

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'koala_defer_scripts', 10, 3);

/**
 * Load Icon CSS Asynchronously
 */
function koala_async_css($html, $handle, $href, $media) {
    $async_handles = [
        'bricks-ionicons',
        'bricks-font-awesome-6',
        'bricks-animate',
        'bricks-tooltips',
        'bricks-photoswipe',
        'bricks-google-fonts',
        'wpda_public_css',
        'cookieblocker-css',
        'koala-custom-css',
        'custom-service-css',
        'custom-blog-template',
        'custom-blog-template-css',
        'custom-service-template',
        'custom-service-template-css',
    ];
    $async_url_patterns = [
        'custom-blog-template.css',
        'custom-service-template.css',
        'cookieblocker',
    ];

    $should_async = in_array($handle, $async_handles);
    if (!$should_async) {
        foreach ($async_url_patterns as $pattern) {
            if (strpos($href, $pattern) !== false) {
                $should_async = true;
                break;
            }
        }
    }

    if ($should_async) {
        return '<link rel="stylesheet" href="' . esc_url($href) . '" media="print" onload="this.media=\'all\'; this.onload=null;">';
    }
    return $html;
}
add_filter('style_loader_tag', 'koala_async_css', 10, 4);


// Remove jQuery Migrate (reduces 1 blocking request)
function remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, ['jquery-migrate']);
        }
    }
}
add_action('wp_default_scripts', 'remove_jquery_migrate');

/**
 * Add a dedicated 48x48 favicon tag for Google Search/Ads.
 *
 * WordPress Site Icons do not always output a 48x48 favicon,
 * even when the original Site Icon is large enough.
 *
 * Google prefers a favicon that is at least 48x48.
 * This filter adds an explicit 48x48 favicon reference
 * using the existing WordPress Site Icon.
 */
add_filter('site_icon_meta_tags', function($meta_tags) {

    // Generate the Site Icon URL at 48x48 size.
    $favicon_48 = get_site_icon_url(48);

    // Only add the tag if WordPress successfully generated the image.
    if ($favicon_48) {

        // Add the favicon link tag to the existing Site Icon meta tags.
        $meta_tags[] = sprintf(
            '<link rel="icon" type="image/png" sizes="48x48" href="%s" />',
            esc_url($favicon_48)
        );
    }

    // Return the updated list of favicon/meta tags.
    return $meta_tags;

});

require_once get_stylesheet_directory() . '/zip-shape-map-shortcode.php';

// Remove broken/empty preconnect and dns-prefetch hints (e.g. href="http://")
add_filter('wp_resource_hints', function($urls, $relation_type) {
    if ($relation_type === 'preconnect' || $relation_type === 'dns-prefetch') {
        $urls = array_values(array_filter($urls, function($url) {
            $href = is_array($url) ? ($url['href'] ?? '') : $url;
            return !empty($href) && $href !== 'http://' && $href !== 'https://';
        }));
    }
    return $urls;
}, 10, 2);

/**
 * Redirect URLs with trailing slashes that result in 404s to the non-trailing slash version.
 */
add_action('template_redirect', function() {
    if (is_404() && substr($_SERVER['REQUEST_URI'], -1) === '/') {
        $redirect = rtrim($_SERVER['REQUEST_URI'], '/');
        if (!empty($_SERVER['QUERY_STRING'])) {
            $redirect .= '?' . $_SERVER['QUERY_STRING'];
        }
        wp_redirect(home_url($redirect), 301);
        exit;
    }
});

// Location "tax banner" (top-nav promo bar) show/hide + content. See includes/tax-banner.php.
require_once BRICKS_PATH . 'includes/tax-banner.php';
