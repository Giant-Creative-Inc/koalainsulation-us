<?php
/** Isolated regression tests for the ACF schema filter used by the Bricks template. */
define('ABSPATH', __DIR__);
$GLOBALS['admin'] = false;
$GLOBALS['types'] = [101 => 'location-service', 201 => 'location', 999 => 'bricks_template'];
$GLOBALS['terms'] = [101 => ['insulation-removal-services']];
$GLOBALS['fields'] = [101 => [
    'related_location' => [(object) ['ID' => 201]],
    'service_hero_titile' => 'Professional Insulation Removal in London, ON',
    'service_hero_description' => '<p>Clear out old insulation &amp; prepare your attic.</p>',
    'faqs' => [
        ['faq_title' => 'Can you remove damp insulation?', 'faq_content' => '<p>Yes, we remove <strong>damp insulation</strong>.</p>'],
        ['faq_title' => 'Is a quote free?', 'faq_content' => '<p>Yes &amp; we explain the cost.</p>'],
        ['faq_title' => 'Empty answer', 'faq_content' => ''],
    ],
], 201 => ['location_phone_number' => '(519) 555-0123']];
function is_admin() { return $GLOBALS['admin']; }
function get_post_type($id) { return $GLOBALS['types'][$id] ?? null; }
function has_term($terms, $taxonomy, $id) { return (bool) array_intersect((array) $terms, $GLOBALS['terms'][$id] ?? []); }
function get_field($name, $id = null) { return $GLOBALS['fields'][$id][$name] ?? false; }
function get_post($id) { return isset($GLOBALS['types'][$id]) ? (object) ['ID' => $id, 'post_type' => $GLOBALS['types'][$id]] : null; }
function get_permalink($id) { return 'https://staging.example/ca/?post_type=location-service&p=' . $id; }
function get_query_var($key) { return $GLOBALS['query'][$key] ?? ''; }
function home_url($path = '') { return 'https://staging.example/ca' . $path; }
function wp_strip_all_tags($text) { return strip_tags($text); }
function wp_json_encode($value, $options = 0) { return json_encode($value, $options); }
function add_filter($tag, $callback, $priority, $args) { $GLOBALS['hooks'][$tag] = [$callback, $priority, $args]; }
function check($ok, $label) { if (!$ok) { throw new RuntimeException($label); } echo "PASS $label\n"; }
function nodes($value) {
    $out = [];
    if (!is_array($value)) { return $out; }
    if (isset($value['@type'])) { $out[] = $value; }
    foreach ($value as $item) { if (is_array($item)) { $out = array_merge($out, nodes($item)); } }
    return $out;
}
function typed($value, $type) { return array_values(array_filter(nodes($value), fn($n) => in_array($type, (array) $n['@type'], true))); }
$runtime = __DIR__ . '/../wp-content/themes/bricks/includes/koala-removal-schema.php';
if (file_exists($runtime)) { require $runtime; }
function format_schema($value, $id = 101) { return function_exists('koala_format_removal_schema') ? koala_format_removal_schema($value, $id, []) : $value; }
$legacy = json_encode([
    ['@context' => 'https://schema.org', '@type' => 'LocalBusiness', '@id' => 'https://staging.example/ca/london#localbusiness', 'telephone' => 'OLD', 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'CA']],
    ['@context' => 'https://schema.org', '@graph' => [
        ['@type' => 'Service', '@id' => 'https://staging.example/ca/london/services/insulation-removal-services#service', 'name' => 'Old service', 'provider' => ['@id' => 'https://staging.example/ca/london#localbusiness']],
        ['@type' => 'FAQPage', '@id' => '#oldfaq', 'mainEntity' => [['@type' => 'Question', 'name' => 'Generic old question']]],
        ['@type' => 'HowTo', '@id' => '#oldhowto', 'name' => 'Book a quote'],
    ]],
]);
$actual = json_decode(format_schema($legacy), true);
$faq = typed($actual, 'FAQPage');
check(count($faq) === 1 && count($faq[0]['mainEntity']) === 2 && $faq[0]['mainEntity'][0]['name'] === 'Can you remove damp insulation?', 'schema FAQs come from visible ACF rows and omit incomplete rows');
check($faq[0]['mainEntity'][1]['acceptedAnswer']['text'] === 'Yes & we explain the cost.', 'FAQ text decodes entities and strips markup');
check(count(typed($actual, 'HowTo')) === 0 && !str_contains(json_encode($actual), '#oldfaq'), 'stale FAQ and unrelated booking HowTo nodes are removed');
check(typed($actual, 'LocalBusiness')[0]['telephone'] === '(519) 555-0123', 'business phone follows linked location record');
check(typed($actual, 'LocalBusiness')[0]['address']['addressCountry'] === 'CA', 'existing Canadian address data is preserved');
check(typed($actual, 'Service')[0]['name'] === 'Professional Insulation Removal in London, ON', 'primary Service uses the visible page heading');
check(typed($actual, 'Service')[0]['description'] === 'Clear out old insulation & prepare your attic.', 'primary Service uses the visible page description');
check(typed($actual, 'Service')[0]['provider']['@id'] === 'https://staging.example/ca/london#localbusiness', 'connected provider identifiers remain intact');
check(format_schema($legacy, 999) === $legacy, 'template and other post types remain unchanged');
$GLOBALS['terms'][101] = ['spray-foam-insulation-services'];
check(format_schema($legacy) === $legacy, 'other service categories remain unchanged');
$GLOBALS['terms'][101] = ['insulation-removal-service'];
check(count(typed(json_decode(format_schema($legacy), true), 'FAQPage')[0]['mainEntity']) === 2, 'Canadian singular removal taxonomy is supported');
$GLOBALS['admin'] = true;
check(format_schema($legacy) === $legacy, 'admin reads preserve the saved schema field');
$GLOBALS['admin'] = false;
foreach ([[201], 201, (object) ['ID' => 201]] as $relationship) {
    $GLOBALS['fields'][101]['related_location'] = $relationship;
    check(typed(json_decode(format_schema($legacy), true), 'LocalBusiness')[0]['telephone'] === '(519) 555-0123', 'relationship ID and object return formats are supported');
}
foreach (['', '{broken JSON', '<script>{}</script>'] as $bad) {
    $built = json_decode(format_schema($bad), true);
    check(json_last_error() === JSON_ERROR_NONE && count(typed($built, 'Service')) === 1 && count(typed($built, 'FAQPage')) === 1, 'empty or invalid legacy schema produces valid content-derived schema');
}
$GLOBALS['query'] = ['custom_location_service_redirect' => 1, 'location_slug' => 'london', 'service_slug' => 'insulation-removal-services'];
check(typed(json_decode(format_schema(''), true), 'Service')[0]['url'] === 'https://staging.example/ca/london/services/insulation-removal-services', 'Canadian custom route retains the ca prefix');
$GLOBALS['fields'][201]['location_name'] = 'London';
$fallback = json_decode(format_schema(''), true);
check(typed($fallback, 'LocalBusiness')[0]['name'] === 'Koala Insulation of London' && !isset(typed($fallback, 'LocalBusiness')[0]['address']), 'missing legacy business uses actual location data without inventing an address');
$GLOBALS['fields'][101]['faqs'][0]['faq_content'] = '&lt;/script&gt; &amp; safe text';
check(!str_contains(format_schema($legacy), '</script>'), 'schema text cannot close the JSON-LD script element');
$GLOBALS['fields'][101]['faqs'] = [];
check(count(typed(json_decode(format_schema($legacy), true), 'FAQPage')) === 0, 'pages with no visible FAQs do not emit FAQPage');
check(isset($GLOBALS['hooks']['acf/format_value/name=schema']), 'runtime registers the field-specific ACF filter');
echo "All insulation removal schema tests passed.\n";
