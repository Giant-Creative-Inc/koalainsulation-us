# City Page admin importer

Feature base: `Giant-Creative-Inc/koalainsulation-us` `main`.

Open **Tools → City Page Import** as an administrator. Download the header-only template, populate it using the Local Cities Pages spreadsheet format, then upload and preview. Review row errors, select valid rows and explicitly apply. Updates enable the Beanstalk City Page template while preserving publication status and post titles. No new pages are created.

- `title` supplies schema service-area name; `slug` must exactly match one existing `resources-landing-pa` page assigned `areas-served`.
- `related_location_id` accepts a positive integer ID or an exact published `location` slug, optionally wrapped in `/`. The existing updater validates numeric IDs against this site's locations.
- State fields and the content manifest's required columns remain required. Missing reviews link label defaults to `Read more reviews`; schema service type defaults to `Insulation services`.
- Image cells accept existing attachment URLs or the updater's `{ "id": 123, "alt": "Description" }` JSON format. Production uploads URLs are also checked against the current site's uploads base URL. Images are never downloaded. Blank optional image fields use pattern defaults.
- Placeholder copy is preserved for the existing city template to render. CSV copy is content, not executable instructions.
- Rows with every city identity field blank are skipped. Source CSV record numbers are retained in reports, including skipped records. The 2026-10-30 source file has 134 city rows and 938 template-only rows. Chesterfield (record 73) lacks a franchise value; Denham Springs (record 48) has a space in its slug.
- Maximum file size is 10 MB and maximum nonblank city rows is 2,000. Preview/apply run five rows per AJAX request.
- The preview is stored for two hours, scoped to the uploading administrator and a unique job ID. Reload restores results; Continue preview resumes unfinished validation. Starting another upload replaces that administrator's preview.
- Only selected validated rows can apply, after all preview batches finish. Target IDs stay fixed; existing updater permissions and exact slug checks run again during apply.
- The shared atomic lock prevents concurrent admin import requests. Completed updates are not replayed. State is saved and read back before each write. Interrupted rows require manual inspection before reupload. A server crash may leave option `beanstalk_city_import_lock`; confirm no import request is running before removing that option with WP-CLI.
- A failed row does not roll back previously successful rows. Back up the database before applying; WordPress revisions alone do not restore all related metadata.
- Results download is a CSV with formula-prefix protection. Imported CSVs are held in private expiring transients, not placed in public uploads.

Run from repository root:

```sh
php tests/city-import/csv-test.php
php tests/city-import/job-test.php
php tests/city-import/admin-test.php
php tests/city-import/updater-test.php
```

These tests use WordPress stubs and never alter a database. They cover CSV shape and row numbering, ID and slug adaptation, image mapping, missing/ambiguous/non-city targets, edit permissions, duplicate rejection, preview/apply boundaries, replay prevention, nonce/admin gates, owner isolation, concurrent request rejection, and WordPress content unslashing.

Runtime QA: verify the screen and template download in Local WordPress, upload and preview the source CSV, inspect row errors and reload recovery. Applying real updates requires separate authorization. No city pages were changed during the feature's read-only Local preview.

Local verification on 2026-10-30: actual WordPress admin preview completed with 11 validated and 123 error rows. The errors comprise 121 unmatched existing city targets, one malformed slug (`denham springs-la`) and Chesterfield's missing location value. All referenced images resolved once the production-to-Local uploads host mapping was supported. Verified download of a 134-row results CSV, selection of exactly 11 valid rows with 123 disabled rows, and restoration of all 134 results after reload with no rows selected. No apply request was made. The existing admin page also logs an unrelated `wp-util.min.js` error (`_ is not defined`); import preview and report controls still completed successfully.
