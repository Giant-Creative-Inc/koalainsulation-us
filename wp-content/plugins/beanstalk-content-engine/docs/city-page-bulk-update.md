# City Page bulk update

The Beanstalk Content Engine can migrate existing Koala City Pages from a CSV
export while keeping unselected pages on the legacy template.

## Safety model

- Dry-run validation is the default.
- Rows target an existing `resources-landing-pa` by exact `post_id`.
- The supplied `slug` must match the existing page, so the migration does not
  silently change a permalink.
- The existing post status, title, author, dates, and unrelated metadata remain
  unchanged.
- WordPress creates a revision before the block content is replaced.
- The existing `areas-served` taxonomy and `rl_related_location` relationship
  are validated and applied.
- Structured-data inputs are stored through the live City Page manifest.
- `_beanstalk_template=koala/city-page` is written last. Until that succeeds,
  the page remains on the legacy template.
- A failed row restores its previous content, taxonomy, and protected metadata.

## Required identity and schema columns

The CSV must include:

```text
post_id
slug
related_location_id
service_area_name
state_name
state_abbreviation
service_type
```

It must also include every required field in the live `koala/city-page`
manifest. Optional image columns may be omitted or left empty. A populated image
cell is JSON containing an existing WordPress Media Library attachment ID and
alt text:

```json
{"id":1234,"alt":"Koala installer adding attic insulation"}
```

## Commands

Validate every row without writing:

```bash
wp beanstalk city-pages update /absolute/path/city-pages.csv
```

Validate and update exactly one existing page:

```bash
wp beanstalk city-pages update /absolute/path/city-pages.csv --post-id=12345 --apply
```

After the single-page result is reviewed, apply the validated batch:

```bash
wp beanstalk city-pages update /absolute/path/city-pages.csv --apply
```

Publishing is not part of this command. Existing published pages stay published;
existing drafts stay drafts.

## Phase II SEO values

The CSV template includes optional `meta_title`, `meta_description`, and `canonical_url` columns. Supply optimized values alongside the original city identity and content columns. The importer saves these to Yoast SEO's existing title, description, and canonical metadata; it does not add duplicate head tags. Old CSV files continue to work. Blank or omitted SEO cells retain existing overrides and automatic defaults. The editor's SEO tab loads the saved Yoast overrides. Its fields take precedence over the Yoast panel when saving a field-managed city page; clearing a field in this tab restores the automatic default.

A canonical must be a complete HTTP/HTTPS URL without credentials or a fragment. It may point at a different preferred page; it does not change the imported page's slug, merge pages, or create a redirect. Values are validated before any writes and restored with the content if the import fails. Preview-only CSV validation makes no changes. Phase II should use the existing full city CSV with these columns populated, rather than an SEO-only file.
