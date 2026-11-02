<?php
// Standalone footer rendering regression; no WordPress/database connection.
define('ABSPATH', '/');
$preview = true;
function koala_beanstalk_sidebar_preview() { global $preview; return $preview; }
function wp_parse_args($args, $defaults) { return array_merge($defaults, $args); }
function esc_url($s) { return htmlspecialchars($s, ENT_QUOTES); }
function esc_html($s) { return htmlspecialchars($s, ENT_QUOTES); }
function esc_attr($s) { return htmlspecialchars($s, ENT_QUOTES); }
function wp_date($f) { return '2026'; }
function home_url($p = '') { return 'https://koala.test' . $p; }
function koala_beanstalk_get_navigation_data() { return ['base'=>'https://koala.test/traverse-city','name'=>'Traverse City','services'=>[],'phone'=>'','address'=>'']; }
function render_footer() { ob_start(); include dirname(__DIR__, 2) . '/wp-content/themes/bricks/beanstalk/footer.php'; return ob_get_clean(); }
$html = render_footer();
foreach (['https://koala.test/locations/','https://www.facebook.com/KoalaInsulationHQ/','https://www.instagram.com/koalainsulation/','https://www.linkedin.com/company/koalainsulation','https://www.youtube.com/channel/UCEYUbnu82sHwLIRzsY6qtPQ','https://insulationinstitute.org/news-industry-issues/news-and-industry-issues/press-release/','https://www.insulate.org/homeowners-frequently-asked-questions/','https://www.energy.gov/sites/prod/files/2013/11/f5/insulation_guide.pdf'] as $url) {
    if (!str_contains($html, 'href="'.$url.'"')) throw new RuntimeException('Missing footer destination: '.$url);
}
$preview = false; $html = render_footer();
foreach (['KoalaInsulationHQ','homeowners-frequently-asked-questions','https://koala.test/locations/'] as $url) {
    if (str_contains($html, $url)) throw new RuntimeException('Other-template footer changed: '.$url);
}
echo "Footer parity and non-location isolation passed.\n";
