# City Page admin importer

Feature base: `Giant-Creative-Inc/koalainsulation-us` `main`.

Open **Tools → City Page Import** as an administrator. Download the header-only template, populate it using the Local Cities Pages spreadsheet format, then upload and preview. Review row errors, select valid rows and explicitly apply. Updates enable the Beanstalk City Page template while preserving publication status and post titles. Missing slugs create draft pages after the same content, image, location and schema checks pass.

- `title` supplies schema service-area name; `slug` identifies a new city or must exactly match one existing `resources-landing-pa` page assigned `areas-served`.
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

Draft creation extension: preview labels each row as an existing-page update or new draft. Apply rechecks that a new slug is still unused, validates create permissions, and creates a draft with the title, content, areas-served term, location relationship, schema and Beanstalk template. Any setup failure deletes the partial draft. Re-uploading a created slug resolves the draft as an update; rerunning a completed job row cannot create duplicates. New pages are never published automatically. Automated tests cover draft creation, preview without insertion, permission denial, content rejection, cleanup and slug collisions. No real draft creation was run during read-only Local QA.

Updated read-only Local preview on 2026-10-30: 132 valid rows comprising 121 new drafts and 11 existing-page updates; two errors remain (Denham Springs malformed slug, Chesterfield missing location). No apply request was made and no city content was created or updated.

City slugs are normalized with WordPress `sanitize_title()` before lookup, duplicate detection and creation. For example, `denham springs-la` becomes `denham-springs-la`. Preview and reports show the normalized slug. Empty sanitized slugs are rejected.

Use **Revalidate uploaded CSV** to rebuild a preview from the saved original rows without uploading again. It creates a fresh job identity, clears selections, re-runs normalization and validation, and preserves source row numbers. Administrator/nonce/job ownership checks and the shared request lock apply. Revalidation is blocked once any row was updated, created or interrupted, so it cannot erase write history or silently replay changes. The saved rows remain subject to the two-hour expiration.

Actual two-row Local apply pilot on 2026-10-30: Ho-Ho-Kus `86149` updated while retaining published status; Chesterfield `88322` created as a draft with related location `4931`. The other 132 rows were not applied. Database readback confirmed content, template activation, areas-served taxonomy, location relationship and schema values. Compared the full resources-post baseline: only `86149` changed and only `88322` was added. Backups and final readback are outside this checkout in `koala/backups/city-import-pilot-20261030/`.

The pilot uncovered older Local copies of CityPageContext, ContentBuilder and ContentValidator. These were backed up and synchronized to the feature branch versions. The first existing-page attempt rolled back its content exactly after the older context class treated unchanged ACF values as failure. The corrected existing row was retried successfully. The new draft and existing pilot were then rebuilt with the synchronized builder to preserve rich-text formatting and template attributes. Browser checks on both confirmed city H1, resolved placeholders, header/footer, forms, local franchise phone/reviews and no broken loaded images; no forms were submitted. The older ignored Local `city-page-context-contract.php` still expects unchanged ACF values and integer relationship IDs to fail; it is stale against main. The new tracked `php tests/city-import/context-test.php` covers the current intended behavior and passes alongside the other importer tests.

Publication behavior update: missing city pages now publish after all content, location, taxonomy, schema and template setup succeeds. Temporary draft insertion is an internal step; failed setup never leaves a partial published page. Creating users must have both create and publish permissions. Existing-page updates preserve their current publication status. This supersedes the earlier draft-only behavior described in historical QA notes. The completed Chesterfield Local pilot was also published at the user's request; the remaining 132 rows were not applied.

Existing-route matching correction: a CSV slug ending with its two-letter state abbreviation also checks the unsuffixed city slug. A fallback match must be an areas-served page linked to exactly the same franchise ID. The original slug is preserved and shown in the preview. Multiple candidates or both suffixed/unsuffixed URLs in the same franchise block the row. Creation re-resolves the original row during apply and refuses to create if an existing city has appeared or is now recognized. The Local pilot duplicate Chesterfield `88322` was backed up and moved to Trash; original `/gateway-west-metro-east-il/chesterfield` (`69221`) remains published and was not modified. Read-only Local normalization confirms CSV `chesterfield-mo` now selects update target `69221` with canonical slug `chesterfield`.

## City Pages in the WordPress editor

Use **Resources Landing Pages → Add City Page**. Enter the city as the WordPress title, select its published franchise, and enter state name and abbreviation. The Beanstalk City Page ACF group has all manifest content fields organized into City details, Hero, Residential Services, Commercial Services, Why Koala, Testimonials, FAQs and Final CTA tabs. New pages start with the active pattern's default copy and optional pattern images. Defaults are editable starter copy from the theme; location-specific pricing, rebates and climate statements should be reviewed for the new city.

The dedicated entry point enables field generation automatically. Existing enabled city pages load their current block copy, inline formatting, FAQs, images, related location and saved state/schema values. Field-managed pages use WordPress's classic title-and-ACF form, keeping the generated core blocks stored as `post_content`. Saving these fields regenerates the editorial blocks; use these fields for content changes. Other resource types keep their existing editor. The existing legacy ACF group is retained.

ACF validates required content, state inputs, images and published-location context before saving. On an explicit ACF save (after ACF persists its fields), the shared updater creates a revision and saves generated blocks, taxonomy, relationship, schema and template activation. New first-save drafts receive a sanitized title slug before generation. The WordPress Save Draft/Publish/Update controls determine publication status. Autosaves, revisions, disabled forms, unrelated post types and unauthorized requests are ignored. Generation failures show an admin notice; a newly enabled published page falls back to draft if setup fails. No schema or dynamic location data is embedded as static copy.

Additional tests:

```sh
php tests/city-import/editor-test.php
php tests/city-import/editor-validation-test.php
php tests/city-import/editor-save-test.php
```

Local browser QA on 2026-10-30: created `City Editor QA` draft `88324` using the actual editor, chose Gateway West & Metro East IL, Missouri/MO, and saved defaults. Changed hero heading and saved again; reloaded the form and preview to verify round-trip. Database readback showed 60 named content blocks, location `4931`, areas-served term, template activation, Missouri schema and matching field/block content. Preview showed the edited heading with resolved placeholders, the correct franchise header/phone/reviews, footer/form, and no broken loaded images. The QA page remains a draft and was not published. Existing Chesterfield `69221` was opened read-only to verify current context hydration; its content was not changed. Code is in the Content Engine plugin; no theme files were modified.

Taxonomy UI default: the city authoring screen now preselects `areas-served` in the Resources page types checklist before first save. It resolves the term by slug rather than a hardcoded ID, preserves existing selections, and applies only to field-managed City Pages. Ordinary resources and other taxonomies are unaffected. This changes the rendered checklist without writing taxonomy on a GET request; the existing save handler still assigns/verifies the taxonomy. `php tests/city-import/editor-taxonomy-test.php` covers selection preservation and scope. A fresh Local Add City Page screen was verified with Areas served checked before saving.

## Current-field Preview

The classic editor's Preview link now submits the current ACF fields to a nonce-protected preview action. Incomplete city details show a field-form notice instead of opening an empty legacy template. TinyMCE values are synchronized before preview. Valid draft previews save/generate the draft using the shared updater and activate the city template, location and taxonomy. Published/private/pending/scheduled cities use a 15-minute snapshot scoped to the editing user and post; their saved content and metadata are not updated. Request-local post, metadata, title and content filters expose that snapshot to the existing template and schema code. Snapshot responses are uncached and marked noindex/nofollow. A prepared-preview link remains available if the browser blocks the popup. Previously published resources without areas-served must first be saved as City Pages.

`php tests/city-import/editor-preview-test.php` checks immutable published post objects, metadata filter shape, per-post scope and per-user isolation. Local browser QA: an empty form displayed required city/title/state/location guidance; an unsaved QA draft heading appeared in the generated city preview; an unsaved published Chesterfield heading appeared only in the private preview. Database before/after comparisons confirmed unchanged published content, title, status, metadata (excluding the normal WordPress edit lock) and terms. Corrected previews showed the selected franchise header, footer, form and reviews, resolved placeholders and no broken loaded images. The screenshot draft `88325` was inspected but not filled or published. QA draft `88324` remains a draft. No theme files were modified.
