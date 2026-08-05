# Koala Gravity Integration

Custom Gravity Forms integration for Koala Insulation quote forms. It resolves which franchise location a quote submission belongs to, routes the lead to that location's thank-you page, and sends the submission to n8n (with retries) for downstream processing.

## Requirements

- **Gravity Forms must be installed and active.** This is a hard requirement: the plugin will refuse to activate without it (`includes/dependencies.php`), and an admin notice appears if Gravity Forms is later deactivated while this plugin is still active.
- **WordPress 6.0+, PHP 8.0+** (see the plugin header in `koala-gravity-integration.php`).
- **A franchise location custom post type** (slug configurable, defaults to `location` — see Settings below), with each location post providing these ACF fields (read via `get_field()` in `includes/forms/payload-builder.php`):
  - `location_name`
  - `housecall_pro_api_key`
  - `location_serviceminder_api_key`
  - `location_serviceminder_id`

  This plugin does not register that post type or its fields — it assumes they already exist on the site.

## Settings

Configure everything under **Settings → Koala Gravity Integration** in wp-admin. Nothing is hardcoded in code — all of the following are stored as WordPress options and editable from this one screen.

### General

| Field | Description |
|---|---|
| **Quote Form** | The Gravity Form this plugin attaches to. All hooks (validation, submission handling, asset loading, confirmation redirect) are scoped to this form ID. |
| **Location Post Type** | The custom post type slug used for franchise locations. Defaults to `location`. |

### Thank You Page

| Field | Description |
|---|---|
| **Country** | `US`, `Canada`, or `Other`. Used when parsing *incoming* request URLs to know whether to expect a country-prefixed path (e.g. `/ca/{location-slug}`) before the location slug. Defaults to `US` (no prefix) if unset. Does **not** affect the outgoing thank-you redirect — see note below. |
| **Country Slug** | Only used when Country is `Other`, e.g. `mx`. Letters and dashes only. |
| **Thank You Page Slug** | The final URL segment of the thank-you redirect, e.g. `thank-you`. Letters and dashes only. |

After a successful submission, the visitor is redirected to `{home_url}/{location-slug}/{thank-you-slug}`. The redirect builder does **not** add a country prefix itself — `home_url()` already resolves to the correct country-specific base on this install, so the Country/Country Slug settings only matter for resolving *incoming* URLs (see `kgi_get_current_location_from_request()` in `includes/location-resolver.php`), not for building the outgoing redirect (`kgi_build_thank_you_url()` in `includes/forms/form-handler.php`).

### n8n Integration

| Field | Description |
|---|---|
| **Webhook URL** | The n8n webhook that quote submissions are POSTed to as JSON, once a location has been resolved. |

### Field Mapping

Two mapping tables, both populated dynamically from whichever form is selected as the Quote Form above:

- **Quote Form Fields** — maps payload keys sent to n8n (`first_name`, `email`, `zip`, UTM fields, consent flags, etc.) to fields on the quote form. Unmapped fields are simply omitted from the outbound payload.
- **Location Routing Fields** — maps three **hidden fields that must exist on the quote form** to `location_slug`, `location_id`, and `page_url`. These are populated automatically on page load with the resolved location's slug/ID and the current page URL, and are read back after submission to determine which location the lead belongs to. This is internal routing data, not sent to n8n directly.

If you ever rebuild the quote form in Gravity Forms and field IDs shift, re-map both tables here — no code changes needed.

### Additional Quote Forms

A second mechanism for a form that needs the *exact same* dynamic location resolution as the main Quote Form — the location is resolved per-request from whichever page it's on, not fixed — but must be a distinct Gravity Form. The primary use case is a duplicate of the quote form embedded elsewhere on the same page (e.g. inside a popup): Gravity Forms' own AJAX handling resolves everything by DOM ID (the iframe it submits to, the load handler, and the wrapper it writes a validation-failure re-render into), and embedding the *same* form twice on one page means all of that always resolves to the first copy, misdirecting validation errors from every other copy. Duplicating the form in Gravity Forms and mapping the duplicate here avoids the ID collision entirely — no theme or JS changes needed.

Each row in this repeater configures:

| Field | Description |
|---|---|
| **Gravity Form** | The duplicate form this row applies to. Must be different from the main Quote Form above. |
| **Location Routing Fields** | Same three hidden fields as the main Quote Form's Location Routing Fields (`location_slug`, `location_id`, `page_url`) — but scoped to this form's own field IDs, since it's a separate Gravity Form. |
| **Quote Form Fields** | Same payload keys as the main Quote Form Fields table, mapped via a dropdown scoped to whichever Gravity Form this row targets. |

Unlike Fixed-Location Forms below, an additional quote form gets the full main-form treatment: location validation blocks submission if unresolved, and the confirmation redirect resolves the location per-submission just like the main form.

### Fixed-Location Forms

A second mechanism for forms that don't need dynamic location resolution at all — e.g. a dedicated Gravity Form embedded on a single franchise location's own landing page, where the location is always the same one and never needs to be inferred from the URL.

Each row in this repeater configures:

| Field | Description |
|---|---|
| **Gravity Form** | The form this row applies to. Must be different from the main Quote Form above. |
| **Location** | The one franchise location post this form always routes to. |
| **Page URL Field ID** | Optional hidden field that captures the current page URL for the Google Sheet log, same idea as the main form's Page URL routing field. Leave blank if not needed. |
| **Field Mapping** | Same payload keys as the main Quote Form Fields table, mapped via a dropdown scoped to whichever Gravity Form this row targets — changing the row's Gravity Form re-fetches that form's fields via AJAX and refreshes the dropdowns. |
| **Custom Fields** | Per-row "+ Add Custom Field" repeater for pushing extra payload keys not in the fixed list above — enter the key name (becomes the payload field name) and pick the Gravity Forms field to source it from. |

Everything downstream of location resolution — the n8n payload build, retry/backoff, the per-location Google Sheet webhook, and the Gravity Forms entry detail sidebar — is shared with the main Quote Form and requires no per-form code. Only these settings differ:

- No location validation blocks submission (there's nothing dynamic to validate — the location is fixed configuration, not user input). If the configured location is somehow missing or deleted, the entry is still accepted and marked `blocked_missing_location` in its entry meta, same as the main form's failure path, rather than showing the visitor an error.
- No `location_slug`/`location_id` hidden fields are needed or populated — only the optional page-URL field, if configured.
- The thank-you redirect still goes to `{home_url}/{location-slug}/{thank-you-slug}` using the Thank You Page settings above.

Add a new row, save, and the form starts working immediately — no code changes needed for the common case of "one more form for one more location."

**Note on `match_location_by_zip()` in the theme's `functions.php`:** this AJAX handler (used by `custom-service.js`'s zip locator) builds a location data array for the matched location but does not currently include the location's post ID or slug:

```php
$location_data = [
    'title' => ...,
    'address' => ...,
    'phone' => ...,
    'zipcode' => $main_zip,
    'website' => get_the_permalink($post),
    'key' => get_field('housecall_pro_api_key', $post->ID),
    'sm_key' => get_field('location_serviceminder_api_key', $post->ID),
    'additional_zipcodes' => $additional_zips_array
];
```

`custom-service.js` reads `locations.id`/`locations.slug` (and `loc.id`/`loc.slug` in the distance-fallback paths, which source from this same array) to populate the `input_1_18`/`input_1_17` routing fields. Without `id`/`slug` in this array, those fields get set to the literal string `"undefined"` instead of real values, which breaks location resolution for any submission routed through the zip locator. This needs `'id' => $post->ID` and `'slug' => $post->post_name` added to `$location_data` in the theme before the zip locator's routing-field population can work correctly.

**Note on the zip/postal code locator widget:** the LSM Bricks Theme's zip code locator (`custom-service.js`, not part of this plugin) populates these same hidden routing fields client-side when a location is matched outside of an actual location page (e.g. via the zip search popup rather than the page URL). That script hardcodes the field IDs (`input_1_6`, `input_1_17`, `input_1_18`, `input_1_19`) rather than reading the field map above, so if the quote form is ever rebuilt and field IDs shift, or the form ID changes, those hardcoded IDs in the theme must be updated to match — re-mapping the tables here alone is not enough. Likewise, if the zip/postal code search flow in the theme is ever reworked (new AJAX actions, changed response shape, new match/fallback paths), the corresponding population of `input_1_6/17/18/19` in `custom-service.js` will need to be updated too, since this plugin has no visibility into that script.

**Note on `all-pages.js`:** the theme's `all-pages.js` (also not part of this plugin) has a block in its location-data fetch handler that sets `key`/`keySm`/`url` from `data.hcpKey`/`data.smKey` on location pages:

```js
// if (data.hcpKey) {
//   document.getElementById("key").value = data.hcpKey;
//   document.querySelector('#key').value = data.hcpKey;
//   document.getElementById("url").value = data.url;
//   sessionStorage.setItem("location_key", data.hcpKey);
// } else {
//   sessionStorage.setItem("location_key", "");
// }

// if (data.smKey) {
//   document.getElementById("keySm").value = data.smKey;
//   document.querySelector('#keySm').value = data.smKey;
//   document.getElementById("url").value = data.url;
//   sessionStorage.setItem("location_sm_key", data.smKey);
// } else {
//   sessionStorage.setItem("location_sm_key", "");
// }
```

This must stay commented out. It writes to the same `key`/`keySm`/`url` fields that `custom-service.js`'s zip locator populates, and re-enabling it on a location page would run on every page load and overwrite those values out from under the zip-locator/routing-field flow described above. If this block is ever uncommented or restored, it needs to be re-commented (or reconciled with `custom-service.js`) — this plugin has no visibility into `all-pages.js` either.

## How It Works

1. **Page load** — `includes/forms/field-population.php` resolves the current location from the URL (via `includes/location-resolver.php`, using the Country setting to know whether to expect a country-prefixed URL, and the Location Post Type setting to know which CPT to query) and writes it into the form's hidden routing fields. `includes/forms/assets.php` localizes that same resolved location, plus a small set of marketing/attribution field IDs, to the frontend script for the dataLayer push described below.
2. **Validation** — `includes/forms/form-handler.php` blocks submission if no location can be resolved, and validates phone field formatting.
3. **Submission** — on `gform_after_submission`, the location is re-resolved from the posted entry (`kgi_get_location_from_entry()`), stored as entry meta, and a background job is queued via WP-Cron.
4. **Background job** — `includes/jobs/background-jobs.php` builds the n8n payload (form fields + the location's ACF data) and POSTs it to the configured webhook, retrying up to `KGI_MAX_RETRIES` times with backoff on failure. This reads the location back from entry meta (`kgi_get_location_from_entry_meta()`), since by the time WP-Cron runs the submission request has long since ended.
5. **Confirmation/redirect** — on `gform_confirmation`, the location is **independently re-resolved from the entry's posted field value** (`kgi_get_location_from_entry()`, not from entry meta) and the visitor is redirected to `{home_url}/{location-slug}/{thank-you-slug}`. This intentionally avoids depending on the meta written in step 3 — `gform_after_submission` is not guaranteed to have completed by the time `gform_confirmation` fires within the same request, and reading meta here previously caused the redirect to silently fall back to a generic thank-you URL with no location slug.
6. **DataLayer push** — `assets/js/form-validation.js` listens for Gravity Forms' `gform_confirmation_loaded` JS event (fires just before the AJAX-submitted form navigates to its redirect confirmation) and pushes `{ event: 'quote_form_submission', locationName, locationId, ...marketing fields }` to `window.dataLayer`. Only non-PII fields are included — see `KGI_DATALAYER_FIELD_MAP_KEYS` in `includes/forms/assets.php`. This only fires for AJAX-enabled form submissions; a non-AJAX submission redirects entirely server-side with no opportunity for this JS to run.
7. **Entry detail sidebar** — `includes/admin/entry-details.php` shows resolution/submission status, retry count, and any error message directly on the Gravity Forms entry screen, so a stuck or failed submission is visible without checking logs.

Steps 1–5 and 7 above run identically for every **Additional Quote Form** (see Settings above), not just the main Quote Form — `kgi_get_all_quote_form_ids()` in `includes/constants.php` returns the full list, and every hook in steps 1, 2, 3, and 5 is registered once per form ID in that list, calling the exact same functions. The only difference is which field map each form's own field IDs resolve through, via `kgi_get_location_field_id_for_form()` and `kgi_get_field_map_for_form()` in `includes/constants.php`.

Steps 1–3, 5, and 7 above have a *different* parallel path for **Fixed-Location Forms** (see Settings above): `kgi_handle_fixed_location_form_submission()` and `kgi_redirect_after_fixed_location_form_submission()` in `includes/forms/form-handler.php` do the same job as their main-form counterparts, but resolve the location from static config (`kgi_resolve_fixed_location()` in `includes/location-resolver.php`) instead of the request URL. Steps 4 and 6 — the background job and payload builders — are entirely shared, reading whichever field map applies to the submitting form via `kgi_get_field_map_for_form()` in `includes/constants.php`.

## File Structure

```
koala-gravity-integration.php       Plugin bootstrap, activation hook
includes/
  constants.php                     Version, paths, config-reading helpers
  dependencies.php                  Gravity Forms presence check + activation guard
  bootstrap.php                     Loads modules and registers hooks (after GF check)
  logger.php                        Debug logging (WP_DEBUG-gated)
  location-resolver.php             URL/entry → location CPT post resolution
  admin/
    settings.php                    Settings page (all options above)
    entry-details.php                Entry detail sidebar panel
    notices.php                     "Gravity Forms required" admin notice
  forms/
    assets.php                      Frontend CSS/JS enqueue, dataLayer field localization
    field-population.php            Hidden field injection on page load
    form-handler.php                Validation, submission handling, thank-you redirect
    payload-builder.php             Builds the n8n payload from entry + location
  jobs/
    background-jobs.php             WP-Cron queue, retry/backoff, n8n delivery
assets/
  css/quote-form.css                 Quote form styling
  js/form-validation.js              Phone number masking + dataLayer push on confirmation
```

## Coding Standards

This plugin targets the `WordPress`, `WordPress-Extra`, and `WordPress-Docs` PHPCS rulesets. Run before committing:

```
composer install   # first time only, installs phpcs + WPCS into vendor/
composer lint       # checks all three rulesets
composer lint:fix   # auto-fixes what phpcbf can safely fix, then re-run lint
```
