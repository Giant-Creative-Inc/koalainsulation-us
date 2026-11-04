<?php
// Run: php tests/location-accessibility/check.php /path/to/wordpress/wp-includes/html-api
// Uses the real WordPress HTML parser without connecting to a database.
define('ABSPATH', '/');
$hooks = [];
function add_action(...$args) {}
function apply_filters($hook, $value, ...$args) { return $value; }
function add_filter(...$args) { global $hooks; $hooks[$args[0]] = $args; }
function esc_attr($value) { return htmlspecialchars($value, ENT_QUOTES); }
function koala_beanstalk_sidebar_preview() { global $native; return $native; }
$native = true;
$api = $argv[1] ?? '';
require_once dirname($api) . '/utf8.php';
require_once dirname($api) . '/kses.php';
foreach (['attribute-token', 'span', 'text-replacement', 'decoder', 'doctype-info', 'tag-processor'] as $class) {
    require_once $api . '/class-wp-html-' . $class . '.php';
}
require $argv[2] ?? dirname(__DIR__, 2) . '/wp-content/themes/bricks/beanstalk/location-page.php';
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
check(function_exists('koala_beanstalk_location_widget_accessibility'), 'Widget accessibility repair must exist');
check(($hooks['do_shortcode_tag'][3] ?? null) === 1, 'Shortcode hook must pass only the output');
$markup = '<div class="zip-shape-map" aria-label="Service area map"></div><button class="rpi-ltgt grw-prev"></button><button class="rpi-ltgt grw-next"></button>';
$result = koala_beanstalk_location_widget_accessibility($markup);
foreach (['role="region"', 'aria-label="Previous reviews"', 'aria-label="Next reviews"'] as $expected) {
    check(str_contains($result, $expected), 'Missing accessible widget semantics: ' . $expected);
}
check(koala_beanstalk_location_widget_accessibility($result) === $result, 'Repair must be idempotent');
$existing = '<div class="zip-shape-map" role="region" aria-label="Our territory"></div><button class="grw-prev" aria-label="Earlier feedback"></button><button class="grw-next" aria-labelledby="next-label"></button><button>Submit</button>';
check(koala_beanstalk_location_widget_accessibility($existing) === $existing, 'Existing semantics must be preserved');
$native = false;
check(koala_beanstalk_location_widget_accessibility($markup) === $markup, 'Non-native pages must be unchanged');
echo "Map role, review names, existing semantics, idempotence and page isolation passed.\n";
