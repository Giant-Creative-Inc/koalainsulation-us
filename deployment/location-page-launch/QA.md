# US launch implementation validation — November 2, 2026

## Completed locally

- Beanstalk `composer test`: 228 tests, 962 assertions, pass. Production activation and REST-location regression tests were observed failing before implementation and then passing.
- Beanstalk `composer lint`: pass. Modified runtime files are byte-identical in the source folder, active Local mirror and PR checkout. Development tooling remains in the source folder.
- `php tests/location-launch/activation-test.php`: 15 checks against the actual deployed PHP files. Deployment defaults to legacy; URL parameters cannot activate production; allowlisted IDs, builder exclusion, other-page isolation, Canada exclusion and lazy popup REST context pass.
- `php tests/location-launch/runner-test.php`: 14 migration guard checks pass.
- `php tests/location-launch/lifecycle-test.php`: 37 in-memory lifecycle checks pass. Covers a read-only plan, content/feed drift before writes, failed option persistence, apply without activation, read-only verify, delivery-gated activation, interrupted saves/imports, repeatable rollback, original option absence, preservation of SEO/routing metadata and unrelated media-map additions, attachment reuse, site/integrity checks and removal of Local URLs from generated patterns.
- Real Local WordPress dry runs passed for Traverse City (existing native layout preserved) and Boise (legacy/empty content generates a location-specific layout). The preflight read actual Gravity Forms 12/13, field identities, addon feeds, routing configuration and asset identities. Only private plan files were written; no apply/activate/rollback ran against a real database.
- Local browser smoke: Traverse City's header and Services dropdown, location headline/phone/reviews/community data, footer and sidebar Form 12 rendered. The quote popup loaded Form 13. No submission was sent.
- Local HTML asset check: zero legacy theme/Bricks-upload assets; 13 external Beanstalk assets; Gravity Forms assets retained.
- Existing routing checks: 109 backend ZIP-lookup assertions, five frontend ZIP-lookup tests and repeated form-asset configuration regression pass.
- New migration PHP syntax, package JSON and Git whitespace checks pass.

## Release checks still required before running production commands

- Real WordPress apply/verify/activate/rollback rehearsal on an isolated staging copy of current production. In-memory lifecycle tests are not that rehearsal.
- Fresh production dry run after the reviewed code is deployed, with human review of the 107-location proposal and exact content/image/option diffs. Production changes since this inventory must be reconciled.
- Every-location old/new SEO/content/schema and navigation parity, desktop/mobile interaction checks and relevant hosting/Cloudflare cache verification against the deployed release. Preserve current production metadata; do not copy staging's Chattanooga schema.
- Separately authorized synthetic form submissions, notification receipt and CRM/location routing verification. No lead-delivery claim is made by these tests.

No staging/production runtime, activation, content or database was changed by this PR preparation. The PR can be reviewed and merged manually; it does not automatically launch pages or execute the database migration.
