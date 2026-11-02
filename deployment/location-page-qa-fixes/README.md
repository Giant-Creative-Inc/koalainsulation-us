# Live location QA fixes

This PR restores native location footer destinations and prevents the LeadConnector shadow-DOM widget from covering the mobile quote bar or the open form. Other footer templates retain their existing links. Blog destinations are unchanged. Chat positioning uses the provider's current `.lc_text-widget` markup; recheck this integration after provider updates.

Las Cruces previously rendered shared feed `[grw id=13780]` in its Bricks layout. Its `google_review_shortcode` field is blank. The existing published feed and location ID 83452 were verified against production; the repair's live dry run returned `would-update`. This restores that existing shared feed, not a new claim of location-specific reviews. The script defaults to read-only, checks the US host and exact record/feed identity, refuses to replace any different configured feed, and is idempotent. No production changes have been applied by this PR.

## Deploy after approval

Upload these runtime files under `wp-content/themes/bricks/beanstalk/`:

- `footer.php`
- `assets/footer.css`
- `assets/location-sidebar.js`
- `assets/location-sidebar.css`
- `assets/location-critical.css`
- `assets/location-deferred.css`

Do not upload `tests/`, Composer/vendor tooling, or this deployment folder into the public theme. Put only `restore-las-cruces-reviews.php` in a private server folder outside `htdocs`, then run:

```sh
wp --allow-root --path=/var/www/koalainsulation.com/htdocs db export /var/www/koalainsulation.com/beanstalk-qa-fixes/before-reviews.sql
wp --allow-root --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-qa-fixes/restore-las-cruces-reviews.php dry-run
wp --allow-root --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-qa-fixes/restore-las-cruces-reviews.php apply
wp --allow-root --path=/var/www/koalainsulation.com/htdocs post meta get 83452 google_review_shortcode
```

Create the private folder before upload/export. Preserve the dry-run result with the backup. Apply changes only this one metadata value; it does not modify page content, forms, menus, or other locations. Do not rerun the activation migration.

Clear location HTML and Beanstalk asset caches. Verify canonical URLs as well as cache-busted URLs: Las Cruces review cards, North Texas mobile quote bar and open form, chat accessibility, desktop chat placement, and restored footer destinations. No lead or booking should be submitted without separate authorization. Remove the repair helper after verification; keep the database backup privately until release acceptance.

If reverting the review change, first confirm its current value is still exactly `[grw id=13780]`; if it differs, stop to preserve subsequent edits. Restore `google_review_shortcode` to the blank value recorded in the dry run. Revert runtime files through Git if needed, then clear caches and recheck.

## Validation

```sh
php tests/location-page-qa-fixes/reviews-test.php
php tests/location-page-qa-fixes/footer-test.php
```

The source Beanstalk module also runs `composer test` and `composer lint`. Browser regression `tests/location-page-qa-fixes/chat-clearance.js` runs with Playwright CLI `run-code --filename`, against the active Local `koalainsulation.local` mirror. It reproduces the provider's open shadow-root widget, verifies the mobile quote and form hit targets, checks chat remains clickable, and preserves desktop placement. No submissions occur. Production provider/cache behavior must be reverified after deployment.
