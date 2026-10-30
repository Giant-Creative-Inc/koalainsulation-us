<?php
/**
 * Keep removal-page JSON-LD aligned with the ACF content rendered by Bricks.
 * The shared template already prints get_field('schema'); no template edit or
 * database rewrite is required. Stored schema remains editable in wp-admin.
 */
if (!defined('ABSPATH')) {
    exit;
}

function koala_removal_schema_text($value)
{
    if (!is_scalar($value)) {
        return '';
    }
    $value = preg_replace('~</(?:p|div|li|h[1-6])\s*>|<br\s*/?>~i', ' ', (string) $value);
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
}

/** Flatten document-level graphs, retaining nested business/offer data. */
function koala_removal_schema_roots($value)
{
    if (!is_array($value) || !$value) {
        return [];
    }
    if (isset($value['@graph']) && is_array($value['@graph'])) {
        return koala_removal_schema_roots($value['@graph']);
    }
    if (isset($value['@type'])) {
        unset($value['@context']);
        return [$value];
    }
    $roots = [];
    foreach ($value as $item) {
        if (is_array($item)) {
            $roots = array_merge($roots, koala_removal_schema_roots($item));
        }
    }
    return $roots;
}

/** Remove old FAQ/HowTo entities and references, including nested graphs. */
function koala_removal_schema_clean($value, $removed_ids, $phone, $heading, $description)
{
    if (!is_array($value)) {
        return $value;
    }
    $types = isset($value['@type']) ? (array) $value['@type'] : [];
    if (array_intersect(['FAQPage', 'HowTo'], $types)
        || (isset($value['@id']) && in_array($value['@id'], $removed_ids, true))) {
        return null;
    }
    if ($phone !== '' && array_intersect(['LocalBusiness', 'HomeAndConstructionBusiness'], $types)) {
        $value['telephone'] = $phone;
    }
    // Update only the primary page Service, not other services in its catalog.
    if (in_array('Service', $types, true) && isset($value['@id']) && preg_match('/#service$/', $value['@id'])) {
        $value['serviceType'] = 'Insulation Removal Services';
        if ($heading !== '') {
            $value['name'] = $heading;
        }
        if ($description !== '') {
            $value['description'] = $description;
        }
    }
    $is_list = array_keys($value) === range(0, count($value) - 1);
    foreach ($value as $key => $item) {
        if (is_array($item)) {
            $item = koala_removal_schema_clean($item, $removed_ids, $phone, $heading, $description);
            if ($item === null) {
                unset($value[$key]);
            } else {
                $value[$key] = $item;
            }
        }
    }
    return $is_list ? array_values($value) : $value;
}

/** Gather identities to remove so no WebPage/Service points at stale markup. */
function koala_removal_schema_removed_ids($value)
{
    $ids = [];
    if (!is_array($value)) {
        return $ids;
    }
    if (array_intersect(['FAQPage', 'HowTo'], (array) ($value['@type'] ?? [])) && isset($value['@id'])) {
        $ids[] = $value['@id'];
    }
    foreach ($value as $item) {
        if (is_array($item)) {
            $ids = array_merge($ids, koala_removal_schema_removed_ids($item));
        }
    }
    return $ids;
}

function koala_format_removal_schema($value, $post_id, $field)
{
    if (is_admin() || !is_numeric($post_id) || get_post_type((int) $post_id) !== 'location-service'
        || !has_term(['insulation-removal-services', 'insulation-removal-service'], 'service_category', (int) $post_id)) {
        return $value;
    }
    $post_id = (int) $post_id;
    $heading = koala_removal_schema_text(get_field('service_hero_titile', $post_id));
    $description = koala_removal_schema_text(get_field('service_hero_description', $post_id));
    $related = get_field('related_location', $post_id);
    $related = is_array($related) ? reset($related) : $related;
    $location_id = is_object($related) ? (int) ($related->ID ?? 0) : (int) $related;
    $phone = $location_id && get_post_type($location_id) === 'location'
        ? koala_removal_schema_text(get_field('location_phone_number', $location_id)) : '';

    $decoded = is_string($value) ? json_decode($value, true) : null;
    $roots = koala_removal_schema_roots($decoded);
    $roots = koala_removal_schema_clean($roots, koala_removal_schema_removed_ids($decoded), $phone, $heading, $description);
    $roots = is_array($roots) ? array_values($roots) : [];
    $url = '';
    $provider = '';
    $has_service = false;
    foreach ($roots as $root) {
        $types = (array) ($root['@type'] ?? []);
        if (in_array('Service', $types, true) && isset($root['@id']) && preg_match('/#service$/', $root['@id'])) {
            $has_service = true;
            $url = $root['url'] ?? preg_replace('/#service$/', '', $root['@id']);
        }
        if (array_intersect(['LocalBusiness', 'HomeAndConstructionBusiness'], $types) && !empty($root['@id'])) {
            $provider = $root['@id'];
        }
    }
    // Match the routed page, retaining home_url()'s Canadian /ca prefix.
    $location_slug = get_query_var('location_slug');
    $service_slug = get_query_var('service_slug');
    if (get_query_var('custom_location_service_redirect') && $location_slug && $service_slug) {
        $url = home_url('/' . rawurlencode($location_slug) . '/services/' . rawurlencode($service_slug));
    }
    if (!$url) {
        $url = get_permalink($post_id);
    }
    // A missing/invalid legacy field can still use known location data. Do not
    // invent address, opening hours, review scores, or other missing details.
    if ($provider === '' && $location_id && get_post_type($location_id) === 'location') {
        $location_name = koala_removal_schema_text(get_field('location_name', $location_id));
        if ($location_name !== '') {
            $location_url = get_permalink($location_id);
            $provider = $location_url . '#localbusiness';
            $business = ['@type' => 'LocalBusiness', '@id' => $provider, 'url' => $location_url,
                'name' => stripos($location_name, 'Koala Insulation') === 0 ? $location_name : 'Koala Insulation of ' . $location_name];
            if ($phone !== '') {
                $business['telephone'] = $phone;
            }
            $roots[] = $business;
        }
    }
    if (!$has_service) {
        $service = ['@type' => 'Service', '@id' => $url . '#service', 'url' => $url,
            'name' => $heading ?: 'Insulation Removal Services', 'serviceType' => 'Insulation Removal Services'];
        if ($description !== '') {
            $service['description'] = $description;
        }
        if ($provider !== '') {
            $service['provider'] = ['@id' => $provider];
        }
        $roots[] = $service;
    }
    $questions = [];
    $rows = get_field('faqs', $post_id);
    foreach (is_array($rows) ? $rows : [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $question = koala_removal_schema_text($row['faq_title'] ?? '');
        $answer = koala_removal_schema_text($row['faq_content'] ?? '');
        if ($question !== '' && $answer !== '') {
            $questions[] = ['@type' => 'Question', 'name' => $question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer]];
        }
    }
    if ($questions) {
        $roots[] = ['@type' => 'FAQPage', '@id' => $url . '#faq', 'url' => $url, 'mainEntity' => $questions];
    }
    // The template prints this inside a script tag: encode tag delimiters too.
    return wp_json_encode(['@context' => 'https://schema.org', '@graph' => $roots],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
add_filter('acf/format_value/name=schema', 'koala_format_removal_schema', 20, 3);
