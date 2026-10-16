# Beanstalk migration for all staging locations

Deploy the complete theme change and this package together. The package now reads every target location from the staging database (published, private, draft, pending and scheduled); it does not copy Traverse City or replace the staging database. Local supports read-only preflight. Apply and rollback require WordPress environment type `staging`.

## Run order

1. Take a hosting backup of the staging database and uploads. Keep staging's existing ACF content and integrations.
2. Deploy the feature branch. Confirm Forms 12/13 and existing media references pass preflight.
3. Run `wp eval-file /absolute/private/package/path/migrate.php dry-run` from the staging WordPress root. Use the deployed package's actual path. The script checks every bundled image hash, required theme functions, forms and existing static media identities, then lists every affected location without writing to the database or Media Library.
4. After reviewing the dry run, run `wp eval-file /absolute/private/package/path/migrate.php apply /absolute/private/backup-directory/before-beanstalk.json`. Choose a new filename in an existing directory outside the public web root. The script refuses an existing backup filename and verifies the backup before changing data.
5. Purge staging caches and QA normal location URLs. No preview parameter is required after successful migration. Production remains excluded by the environment guard. Bricks builder access is retained.

## What apply does

- Imports 26 bundled compressed WebP images through WordPress's media upload functions, including titles and alt text from media-manifest.json. Decorative images intentionally have empty alt text. Existing Cloudflare media hooks can process these normal WordPress imports.
- Stores attachment IDs under `koala_beanstalk_location_media`. Native rendered content and the quote mascot resolve mapped image IDs through WordPress's attachment URL filters, retaining theme files as fallbacks. CSS decorative backgrounds remain theme assets.
- Reuses imports by a stable asset identity and hash; reruns do not duplicate them. Titles/alt text edited after import are retained on rerun.
- Generates missing native layouts separately from each location's own ACF data. Existing native Gutenberg layouts are preserved. Existing Bricks metadata, ACF fields, form feeds, lead entries, location statuses and global content parts remain unchanged.
- Saves previous post content and activation/media-map options in the private rollback JSON. Content writes are read back and verified. The `koala_beanstalk_locations_enabled` setting is enabled only after all writes succeed.

## Rollback and interrupted runs

Run `wp eval-file /absolute/private/package/path/migrate.php rollback /absolute/private/backup-directory/before-beanstalk.json`. It rejects backups from another site and refuses to overwrite location content changed after migration. It restores previous content and options. Imported media are retained to avoid deleting images shared with other content or Cloudflare.

If apply stops, keep its backup and error output. Successful media imports are recorded immediately and can be reused on another apply with a fresh backup filename. Do not delete imported media blindly. A full hosting backup remains the recovery path for media/offload changes.

## Required staging QA

Verify Cloudflare offload completion and actual image delivery; importing an attachment is not proof its asynchronous CDN upload has completed. Check all images, location copy, phone numbers, reviews/NAP, sidebar/popup attribution, mobile accordions, video lightboxes, header/footer and asset isolation. Test no real leads during visual QA. Confirm maps and tracking policy for the staging domain. Hosting/CDN caching is separate from the Local Nginx configuration.

This package has not been applied to staging or production. Automatic activation on production is intentionally unsupported.
