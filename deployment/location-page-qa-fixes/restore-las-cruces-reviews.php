<?php
/**
 * Restore the review feed verified in the saved pre-launch Las Cruces HTML.
 * Run with WP-CLI eval-file outside the document root. Default is read-only.
 */
function koala_qa_restore_las_cruces_reviews(bool $apply = false): array {
    if (parse_url(home_url('/'), PHP_URL_HOST) !== 'koalainsulation.com') {
        throw new RuntimeException('This repair is only for the US production database.');
    }
    $location = get_page_by_path('las-cruces', OBJECT, 'location');
    if (!$location || (int) $location->ID !== 83452 || $location->post_name !== 'las-cruces' || $location->post_type !== 'location') {
        throw new RuntimeException('Las Cruces identity does not match the verified US record.');
    }
    $feed = get_post(13780);
    if (!$feed || $feed->post_type !== 'grw_feed' || $feed->post_status !== 'publish') {
        throw new RuntimeException('The previously used shared review feed is unavailable.');
    }
    $before = (string) get_post_meta(83452, 'google_review_shortcode', true);
    $after = '[grw id=13780]';
    if ($before === $after) {
        return ['action' => 'unchanged', 'location_id' => 83452, 'value' => $after];
    }
    if (trim($before) !== '') {
        throw new RuntimeException('A review feed has already been configured; refusing to overwrite it.');
    }
    if ($apply) {
        update_post_meta(83452, 'google_review_shortcode', $after);
        if (get_post_meta(83452, 'google_review_shortcode', true) !== $after) {
            throw new RuntimeException('Saved review feed verification failed.');
        }
    }
    return ['action' => $apply ? 'updated' : 'would-update', 'location_id' => 83452, 'before' => $before, 'after' => $after];
}
if (defined('WP_CLI') && WP_CLI && isset($args)) {
    $mode = $args[0] ?? 'dry-run';
    if (!in_array($mode, ['dry-run', 'apply'], true)) {
        throw new RuntimeException('Use dry-run or apply.');
    }
    WP_CLI::log(wp_json_encode(koala_qa_restore_las_cruces_reviews($mode === 'apply'), JSON_PRETTY_PRINT));
}
