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
