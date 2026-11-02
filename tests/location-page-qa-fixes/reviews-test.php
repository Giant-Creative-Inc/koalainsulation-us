<?php
// Standalone WP-API test double: this test never connects to WordPress or a database.
$state = ['value' => '', 'writes' => 0, 'slug' => 'las-cruces', 'feed' => true];
function home_url($path = '') { return 'https://koalainsulation.com' . $path; }
function get_page_by_path($path, $output, $type) { global $state; return (object) ['ID' => 83452, 'post_name' => $state['slug'], 'post_type' => 'location']; }
function get_post($id) { global $state; return $state['feed'] ? (object) ['ID' => 13780, 'post_type' => 'grw_feed', 'post_status' => 'publish'] : null; }
function get_post_meta($id, $key, $single = false) { global $state; return $state['value']; }
function update_post_meta($id, $key, $value) { global $state; if ($id !== 83452 || $key !== 'google_review_shortcode') throw new RuntimeException('Unexpected target'); $state['writes']++; $state['value'] = $value; return true; }
define('OBJECT', 'OBJECT');
require dirname(__DIR__, 2) . '/deployment/location-page-qa-fixes/restore-las-cruces-reviews.php';
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
$r = koala_qa_restore_las_cruces_reviews();
check($state['writes'] === 0 && $r['action'] === 'would-update', 'Dry run must not write');
koala_qa_restore_las_cruces_reviews(true);
check($state['value'] === '[grw id=13780]' && $state['writes'] === 1, 'Apply must restore verified old feed');
koala_qa_restore_las_cruces_reviews(true);
check($state['writes'] === 1, 'Rerun must be a no-op');
$state['value'] = '[grw id=999]';
try { koala_qa_restore_las_cruces_reviews(true); throw new LogicException('Existing feed overwritten'); } catch (RuntimeException $e) { check(!($e instanceof LogicException), $e->getMessage()); }
$state['value'] = ''; $state['feed'] = false;
try { koala_qa_restore_las_cruces_reviews(true); throw new LogicException('Missing feed allowed'); } catch (RuntimeException $e) { check(!($e instanceof LogicException), $e->getMessage()); }
check($state['writes'] === 1, 'Guard failures must not write');
echo "Review repair: dry run, apply, idempotence and drift/feed guards passed.\n";
