<?php
if (!defined('ABSPATH')) {
    exit;
}

$use_optimized_location_template = isset($_GET['koala_optimized_location'])
    && $_GET['koala_optimized_location'] === '1'
    && (
        (function_exists('koala_is_local_preview') && koala_is_local_preview())
        || current_user_can('manage_options')
    );

if (!$use_optimized_location_template) {
    require get_template_directory() . '/single.php';
    return;
}

if (
    (function_exists('bricks_is_builder') && bricks_is_builder())
    || isset($_GET[BRICKS_BUILDER_PARAM])
    || isset($_GET[BRICKS_BUILDER_IFRAME_PARAM])
) {
    require get_template_directory() . '/single.php';
    return;
}

the_post();

$post_id = get_the_ID();

$field = function ($key, $fallback = '') use ($post_id) {
    if (function_exists('get_field')) {
        $value = get_field($key, $post_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    $value = get_post_meta($post_id, $key, true);

    return ($value !== null && $value !== false && $value !== '') ? $value : $fallback;
};

$ids_from_field = function ($value) {
    if (!$value) {
        return [];
    }

    if (!is_array($value)) {
        $value = [$value];
    }

    $ids = [];
    foreach ($value as $item) {
        if ($item instanceof WP_Post) {
            $ids[] = $item->ID;
        } elseif (is_numeric($item)) {
            $ids[] = (int) $item;
        }
    }

    return array_values(array_filter(array_unique($ids)));
};

$plain = function ($value) {
    return trim(wp_strip_all_tags((string) $value));
};

$location_name = $plain($field('location_name', get_the_title()));
$location_state = $plain($field('location_state', ''));
$location_region = $plain($field('location_region', 'USA'));
$address = $plain($field('location_address', ''));
$phone = $plain($field('location_phone_number', ''));
$zip = $plain($field('location_zipcode', ''));
$lat = $plain($field('location_latitude', ''));
$lng = $plain($field('location_logitude', ''));
$hero_title = $plain($field('location_hero_title', $location_name . ' Insulation Installation Experts'));
$hero_description = $plain($field('location_hero_description', $field('_yoast_wpseo_metadesc', 'Professional insulation services from your local Koala Insulation team.')));
$services_title = $plain($field('services_title', 'Expert Insulation Installation in ' . $location_name));
$services_description = $plain($field('services_description', 'Explore insulation services available from your local Koala Insulation team.'));
$who_title = $plain($field('who_we_are_title', 'Why ' . $location_name . ' Homeowners Choose Koala Insulation'));
$who_description = $plain($field('who_we_are_description', 'Koala Insulation provides professional service, trusted guidance, and quality insulation installation.'));
$overview_title = $plain($field('location_overview_title', 'Koala Insulation of ' . $location_name));
$overview = $field('location_insulation_rich_text_1', $field('location_insulation_rich_text_2', ''));
$overview = $overview ? wp_kses_post(wpautop($overview)) : '';
$satisfaction_title = $plain($field('satisfaction_title', 'Where Service Meets Superior Insulation'));
$satisfaction_description = $plain($field('satisfaction_description', 'We make insulation upgrades simple, fast, and reliable.'));
$cities_title = $plain($field('local_cities_title', 'Expert Insulation Across ' . $location_name . ' and Nearby Communities'));
$cities_description = $plain($field('local_cities_description', 'Koala Insulation is proud to serve homeowners throughout the local area.'));
$promo_title = $plain($field('location_promos_title', 'Exclusive Local Offers'));
$promo_description = $plain($field('location_promos_description', ''));
$meta_description = $plain(get_post_meta($post_id, '_yoast_wpseo_metadesc', true));
$meta_description = $meta_description ?: $hero_description;
$page_title = $plain(get_post_meta($post_id, '_yoast_wpseo_title', true));
$page_title = $page_title ?: $hero_title . ' - Koala Insulation';

$hero_image = function_exists('koala_get_location_hero_image_data')
    ? koala_get_location_hero_image_data($post_id)
    : [
        'url' => content_url('uploads/2025/02/2-600x400.webp'),
        'alt' => 'Worker installing blown-in insulation in an attic.',
        'width' => 600,
        'height' => 400,
    ];

$service_ids = $ids_from_field($field('location_service', []));
$city_ids = $ids_from_field($field('local_cities', []));
$promo_ids = $ids_from_field($field('location_promos', []));

$address_parts = array_map('trim', explode(',', $address));
$street_address = $address_parts[0] ?? $address;
$address_locality = $address_parts[1] ?? $location_name;
$state_postal = $address_parts[2] ?? '';
$address_region = $location_state;
$postal_code = $zip;

if (preg_match('/([A-Z]{2})\s+([A-Z0-9 -]+)/i', $state_postal, $matches)) {
    $address_region = strtoupper($matches[1]);
    $postal_code = trim($matches[2]);
}

$schema_services = [];
foreach ($service_ids as $service_id) {
    $schema_services[] = [
        '@type' => 'Offer',
        'itemOffered' => [
            '@type' => 'Service',
            'name' => get_the_title($service_id),
            'url' => get_permalink($service_id),
        ],
    ];
}

$business_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'HomeAndConstructionBusiness',
    '@id' => get_permalink($post_id) . '#business',
    'name' => 'Koala Insulation of ' . $location_name,
    'url' => get_permalink($post_id),
    'telephone' => $phone,
    'image' => $hero_image['url'],
    'description' => $meta_description,
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $street_address,
        'addressLocality' => $address_locality,
        'addressRegion' => $address_region,
        'postalCode' => $postal_code,
        'addressCountry' => $location_region === 'Canada' ? 'CA' : 'US',
    ],
    'areaServed' => array_map(function ($city_id) {
        return [
            '@type' => 'City',
            'name' => get_the_title($city_id),
            'url' => get_permalink($city_id),
        ];
    }, array_slice($city_ids, 0, 20)),
    'makesOffer' => $schema_services,
];

if ($lat && $lng) {
    $business_schema['geo'] = [
        '@type' => 'GeoCoordinates',
        'latitude' => $lat,
        'longitude' => $lng,
    ];
}

$custom_schema_nodes = [];
$schema_raw = $field('schema', '');
if ($schema_raw) {
    $schema_data = json_decode((string) $schema_raw, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($schema_data)) {
        if (!empty($schema_data['@graph']) && is_array($schema_data['@graph'])) {
            $custom_schema_nodes = $schema_data['@graph'];
        } elseif (array_keys($schema_data) === range(0, count($schema_data) - 1)) {
            $custom_schema_nodes = $schema_data;
        } else {
            $custom_schema_nodes = [$schema_data];
        }

        $custom_schema_nodes = array_values(array_filter(array_map(function ($node) {
            if (!is_array($node)) {
                return null;
            }

            unset($node['@context']);

            return $node;
        }, $custom_schema_nodes)));
    }
}

$location_schema_nodes = $custom_schema_nodes ?: [$business_schema];
$yoast_available = has_action('wpseo_head');

$breadcrumb_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => home_url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Locations',
            'item' => home_url('/locations/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $location_name,
            'item' => get_permalink($post_id),
        ],
    ],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ($yoast_available) : ?>
<?php
    remove_filter('wpseo_json_ld_output', '__return_false');

    add_filter('wpseo_opengraph_image', function ($image) use ($hero_image) {
        return $image ?: $hero_image['url'];
    }, 20);

    add_filter('wpseo_twitter_image', function ($image) use ($hero_image) {
        return $image ?: $hero_image['url'];
    }, 20);

    add_filter('wpseo_schema_graph', function ($graph) use ($location_schema_nodes) {
        foreach ($location_schema_nodes as $node) {
            if (is_array($node)) {
                $graph[] = $node;
            }
        }

        return $graph;
    }, 20);

    ob_start();
    do_action('wpseo_head');
    $yoast_head = ob_get_clean();
    echo $yoast_head;

    if (
        strpos($yoast_head, 'rel="canonical"') === false
        && strpos($yoast_head, "rel='canonical'") === false
    ) {
        $yoast_og_url = '';
        if (preg_match('/<meta[^>]+property=["\']og:url["\'][^>]+content=["\']([^"\']+)["\']/i', $yoast_head, $matches)) {
            $yoast_og_url = html_entity_decode($matches[1], ENT_QUOTES, get_bloginfo('charset'));
        }

        $canonical_url = $plain(get_post_meta($post_id, '_yoast_wpseo_canonical', true)) ?: ($yoast_og_url ?: get_permalink($post_id));
        $canonical_url = apply_filters('wpseo_canonical', $canonical_url, null);

        if ($canonical_url) {
            printf('<link rel="canonical" href="%s">', esc_url($canonical_url));
        }
    }
?>
<?php else : ?>
<meta name="robots" content="index, follow, max-image-preview:large">
<title><?php echo esc_html($page_title); ?></title>
<meta name="description" content="<?php echo esc_attr($meta_description); ?>">
<link rel="canonical" href="<?php echo esc_url(get_permalink($post_id)); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($meta_description); ?>">
<meta property="og:url" content="<?php echo esc_url(get_permalink($post_id)); ?>">
<meta property="og:image" content="<?php echo esc_url($hero_image['url']); ?>">
<meta name="twitter:card" content="summary_large_image">
<?php foreach ($location_schema_nodes as $schema_node) : ?>
<script type="application/ld+json"><?php echo wp_json_encode(array_merge(['@context' => 'https://schema.org'], $schema_node), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<?php endforeach; ?>
<script type="application/ld+json"><?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<?php endif; ?>
<?php wp_site_icon(); ?>
<link rel="preload" as="image" href="<?php echo esc_url($hero_image['url']); ?>">
<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/koala-location-optimized.css'); ?>">
</head>
<body <?php body_class('koala-location-optimized'); ?>>
<header>
    <div class="koala-topbar">
        <div class="koala-shell">
            <div><span class="koala-location-label"><?php echo esc_html($location_name . ($location_state ? ', ' . $location_state : '')); ?></span> <a href="<?php echo esc_url(home_url('/locations/')); ?>">View All Locations</a></div>
            <?php if ($phone) : ?><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a><?php endif; ?>
        </div>
    </div>
    <nav class="koala-nav" aria-label="Main navigation">
        <div class="koala-shell">
            <a class="koala-logo" href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url(content_url('uploads/2024/06/Logo.png')); ?>" width="115" height="80" alt="Koala Insulation">
            </a>
            <div class="koala-nav-links">
                <a href="#services">Services</a>
                <a href="#why-koala">Why Koala</a>
                <a href="#areas">Areas Served</a>
                <a href="#contact">Contact</a>
                <a class="koala-button" href="#quote-form">Get a Free Quote</a>
            </div>
        </div>
    </nav>
</header>

<main id="main-content">
    <section class="koala-hero">
        <div class="koala-shell koala-hero-grid">
            <div>
                <h1><?php echo esc_html($hero_title); ?></h1>
                <p class="koala-lede"><?php echo esc_html($hero_description); ?></p>
                <a class="koala-button secondary" href="#quote-form">Get a Free Quote</a>
            </div>
            <div>
                <?php if (!empty($hero_image['id'])) : ?>
                    <?php echo wp_get_attachment_image((int) $hero_image['id'], 'large', false, [
                        'alt' => $hero_image['alt'],
                        'fetchpriority' => 'high',
                        'loading' => 'eager',
                        'decoding' => 'async',
                    ]); ?>
                <?php else : ?>
                    <img src="<?php echo esc_url($hero_image['url']); ?>" alt="<?php echo esc_attr($hero_image['alt']); ?>" width="<?php echo esc_attr($hero_image['width']); ?>" height="<?php echo esc_attr($hero_image['height']); ?>" fetchpriority="high" loading="eager" decoding="async">
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="koala-section white" id="services">
        <div class="koala-shell">
            <p class="koala-eyebrow">Services</p>
            <div class="koala-text">
                <h2><?php echo esc_html($services_title); ?></h2>
                <p><?php echo esc_html($services_description); ?></p>
            </div>
            <?php if ($service_ids) : ?>
                <div class="koala-grid">
                    <?php foreach ($service_ids as $service_id) : ?>
                        <article class="koala-card">
                            <h3><?php echo esc_html(get_the_title($service_id)); ?></h3>
                            <?php $excerpt = get_the_excerpt($service_id); ?>
                            <?php if ($excerpt) : ?><p><?php echo esc_html(wp_trim_words($excerpt, 24)); ?></p><?php endif; ?>
                            <a href="<?php echo esc_url(get_permalink($service_id)); ?>">Learn more</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="koala-section" id="why-koala">
        <div class="koala-shell koala-two-col">
            <div>
                <p class="koala-eyebrow">Why Koala</p>
                <h2><?php echo esc_html($who_title); ?></h2>
            </div>
            <p><?php echo esc_html($who_description); ?></p>
        </div>
    </section>

    <?php if ($overview) : ?>
        <section class="koala-section white">
            <div class="koala-shell">
                <p class="koala-eyebrow">Local Insulation Experts</p>
                <h2><?php echo esc_html($overview_title); ?></h2>
                <div class="koala-rich-text koala-text"><?php echo $overview; ?></div>
            </div>
        </section>
    <?php endif; ?>

    <section class="koala-section blue">
        <div class="koala-shell koala-two-col">
            <div>
                <p class="koala-eyebrow">Service Commitment</p>
                <h2><?php echo esc_html($satisfaction_title); ?></h2>
            </div>
            <p><?php echo esc_html($satisfaction_description); ?></p>
        </div>
    </section>

    <section class="koala-section white" id="areas">
        <div class="koala-shell">
            <p class="koala-eyebrow">Areas Served</p>
            <div class="koala-text">
                <h2><?php echo esc_html($cities_title); ?></h2>
                <p><?php echo esc_html($cities_description); ?></p>
            </div>
            <?php if ($city_ids) : ?>
                <ul class="koala-list">
                    <?php foreach ($city_ids as $city_id) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($city_id)); ?>"><?php echo esc_html(get_the_title($city_id)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($promo_ids) : ?>
        <section class="koala-section">
            <div class="koala-shell">
                <p class="koala-eyebrow">Local Offers</p>
                <h2><?php echo esc_html($promo_title); ?></h2>
                <?php if ($promo_description) : ?><p class="koala-text"><?php echo esc_html($promo_description); ?></p><?php endif; ?>
                <div class="koala-grid">
                    <?php foreach ($promo_ids as $promo_id) : ?>
                        <?php
                        $promo_name = $plain(get_post_meta($promo_id, 'promotion_name', true)) ?: get_the_title($promo_id);
                        $promo_copy = $plain(get_post_meta($promo_id, 'promotion_short_description', true));
                        $promo_button = $plain(get_post_meta($promo_id, 'promotion_button_text', true)) ?: 'Claim this offer';
                        ?>
                        <article class="koala-card">
                            <h3><?php echo esc_html($promo_name); ?></h3>
                            <?php if ($promo_copy) : ?><p><?php echo esc_html($promo_copy); ?></p><?php endif; ?>
                            <a href="#quote-form"><?php echo esc_html($promo_button); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="koala-section white" id="contact">
        <div class="koala-shell koala-contact-grid">
            <div class="koala-contact-card">
                <p class="koala-eyebrow">Contact</p>
                <h2>Koala Insulation of <?php echo esc_html($location_name); ?></h2>
                <?php if ($address) : ?><p><strong>Address</strong><br><address><?php echo esc_html($address); ?></address></p><?php endif; ?>
                <?php if ($phone) : ?><p><strong>Phone</strong><br><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></p><?php endif; ?>
            </div>
            <form class="koala-form" id="quote-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="koala_location_quote_request">
                <input type="hidden" name="location_id" value="<?php echo esc_attr($post_id); ?>">
                <?php wp_nonce_field('koala_location_quote_request', 'koala_quote_nonce'); ?>
                <h2>Get a Free Quote</h2>
                <?php if (isset($_GET['quote']) && $_GET['quote'] === 'thanks') : ?>
                    <p><strong>Thanks. Your request has been received.</strong></p>
                <?php endif; ?>
                <div class="koala-field-row">
                    <label>First Name <input name="first_name" autocomplete="given-name" required></label>
                    <label>Last Name <input name="last_name" autocomplete="family-name"></label>
                </div>
                <label>Email <input type="email" name="email" autocomplete="email" required></label>
                <label>Phone <input type="tel" name="phone" autocomplete="tel" required></label>
                <label>Zip Code <input name="zip" autocomplete="postal-code" value="<?php echo esc_attr($zip); ?>" required></label>
                <label>Project Notes <textarea name="message"></textarea></label>
                <p class="koala-small">By submitting this form, you agree that Koala Insulation may contact you about your request.</p>
                <button class="koala-submit" type="submit">Request Quote</button>
            </form>
        </div>
    </section>
</main>

<footer class="koala-footer">
    <div class="koala-shell">
        <div><strong>Koala Insulation</strong><br><?php echo esc_html($location_name); ?></div>
        <nav class="koala-footer-nav" aria-label="Footer navigation">
            <a href="<?php echo esc_url(home_url('/services/')); ?>">Services</a>
            <a href="<?php echo esc_url(home_url('/why-koala/')); ?>">Why Koala</a>
            <a href="<?php echo esc_url(home_url('/locations/')); ?>">Locations</a>
            <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a>
        </nav>
    </div>
</footer>
</body>
</html>
