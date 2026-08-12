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

Configure everything under **Settings → Koala Gravity Integration** in wp-admin. The following values are stored as WordPress options and editable from this one screen; when Notification Email is blank, it uses the documented Koala marketing-team default.

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

- **Quote Form Fields** — maps payload keys sent to n8n (`first_name`, `email`, `zip`, UTM fields, consent flags, the attribution/tracking fields below, etc.) to fields on the quote form. Unmapped fields are simply omitted from the outbound payload.
- **Location Routing Fields** — maps three **hidden fields that must exist on the quote form** to `location_slug`, `location_id`, and `page_url`. These are populated automatically on page load with the resolved location's slug/ID and the current page URL, and are read back after submission to determine which location the lead belongs to. This is internal routing data, not sent to n8n directly.

If you ever rebuild the quote form in Gravity Forms and field IDs shift, re-map both tables here — no code changes needed.

### Attribution & Tracking Fields

The Quote Form Fields table also includes a set of attribution/tracking keys that are **populated client-side**, not typed by the visitor: the ad-platform click IDs (`gclid`, `gbraid`, `wbraid`, `fbclid`, `msclkid`), the five `Utm*` keys, `landing_page`, `referrer`, `form_timestamp`, `service`, and `cta_text`. To use them, add a **hidden field on the quote form** for each one you want and map it here, exactly like any other payload field — the values then flow to n8n and the Google Sheet automatically, and the non-PII ones are also pushed to the thank-you-page `dataLayer` (see `KGI_DATALAYER_FIELD_MAP_KEYS`).

`form_id` is **not** in this list and is **not mappable**: the form ID is intrinsic to every entry, so it's added to the n8n payload server-side (from the entry itself) and is always correct — no hidden field or mapping required.

To keep the n8n schema stable, **every** attribution/tracking key is always present in the n8n payload — a form with no field mapped for a given key sends it as an empty string (`""`) rather than omitting it — so n8n nodes can reference `gclid`, `landing_page`, `cta_text`, etc. without erroring on a missing key. (This applies to the n8n payload only; the Google Sheet payload still includes only mapped fields.)

Population is handled by `assets/js/attribution.js`, loaded on **every** page:

- **First-touch capture.** On the visitor's first page view, the script snapshots the UTMs, click IDs, `landing_page` (that first URL), `referrer`, and a timestamp into a first-party `kgi_attrib` cookie (~90 days, `SameSite=Lax`). This is why it loads site-wide — the landing page is usually not the page the form lives on.
- **Fill.** On a page with a mapped form, each hidden field is filled only if empty. For the UTMs and click IDs the **current page URL wins** (last touch) and the cookie is the fallback (first touch); `landing_page`/`referrer` always come from the cookie; `form_timestamp` is set at fill time.
- **Service / CTA from page context.** `service` reads `<body data-kgi-service="…">` then `<meta name="kgi-service" content="…">`. `cta_text` reads the `data-kgi-cta` value (or text) of a clicked element carrying that attribute — remembered across the navigation to the form — then falls back to `<body data-kgi-cta="…">`. Set these attributes on the relevant pages/CTAs in the theme; unset means the field is left blank.

**Why client-side:** the site serves forms from full-page caches (Nginx FastCGI, WP Rocket, Cloudflare), so injecting per-visitor values server-side at render would bake one visitor's `gclid`/UTM into the shared cached HTML. Running in the browser after the cached HTML loads gives each visitor their own values. This overlaps with the **HandL UTM Grabber** plugin — once these fields are verified in production, HandL can be retired for these forms (audit any `[handl_*]` shortcodes or other forms/reporting that rely on it first).

### Additional Quote Forms

A second mechanism for a form that needs the *exact same* dynamic location resolution as the main Quote Form — the location is resolved per-request from whichever page it's on, not fixed — but must be a distinct Gravity Form. The primary use case is a duplicate of the quote form embedded elsewhere on the same page (e.g. inside a popup): Gravity Forms' own AJAX handling resolves everything by DOM ID (the iframe it submits to, the load handler, and the wrapper it writes a validation-failure re-render into), and embedding the *same* form twice on one page means all of that always resolves to the first copy, misdirecting validation errors from every other copy. Duplicating the form in Gravity Forms and mapping the duplicate here avoids the ID collision entirely — no theme or JS changes needed.

Each row in this repeater configures:

| Field | Description |
|---|---|
| **Gravity Form** | The duplicate form this row applies to. Must be different from the main Quote Form above. |
| **Location Routing Fields** | Same three hidden fields as the main Quote Form's Location Routing Fields (`location_slug`, `location_id`, `page_url`) — but scoped to this form's own field IDs, since it's a separate Gravity Form. |
| **Quote Form Fields** | Same payload keys as the main Quote Form Fields table, mapped via a dropdown scoped to whichever Gravity Form this row targets. |

An additional quote form gets the full main-form treatment: the confirmation redirect resolves the location per-submission just like the main form, and it uses the same never-reject lead-capture path (see **Lead Routing** below) — a submission is never blocked for an unresolved location.

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

- No location validation blocks submission (there's nothing dynamic to validate — the location is fixed configuration, not user input). If the configured location is somehow missing or deleted, the entry is still accepted and falls back to the **Default Location** (see **Lead Routing** below), flagged for review and notified, rather than showing the visitor an error.
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

### Lead Routing

A submission is **never rejected** for a missing location — that was a direct source of lost leads (the entry was discarded before it was saved, and the submitted ZIP was never consulted). Instead, at submission the location is resolved by the best signal available and the entry is always captured:

1. **URL / hidden field** — the location resolved from the page URL or the posted `location_id`, as before. Entry meta `kgi_location_source = url`.
2. **Exact ZIP owner** — if that fails, the submitted ZIP/postal code is looked up in the ownership index (in-memory, no API call) and its owning location is used. `kgi_location_source = zip`.
3. **Default Location** — if that also fails, the lead is routed to the configured overflow location so it still reaches n8n. `kgi_location_source = default`, the entry is flagged (`kgi_needs_review`), and a notification email is sent. During background ZIP routing, a submitted ZIP with no exact owner and no owner within the configured radius is unassigned instead of retaining the page/default location; its source becomes `unresolved` and the review email is sent.
4. **Unresolved** — if no location resolves and no default is configured, the entry is still saved, flagged (`kgi_location_source = unresolved`, `kgi_needs_review`), a notification is sent, **and it is still sent to n8n** with an empty location payload and a `location_found = false` flag (see below) so the n8n workflow can alert on it — e.g. post a Slack message — instead of the lead being dropped.

| Field | Description |
|---|---|
| **Default Location** | The overflow franchise location for leads whose location can't be resolved from the page or ZIP. Leave as *None* to send such leads to n8n as "no location found" (flagged) rather than to a franchise. |
| **Notification Email** | Where the Gravity Forms-style "needs routing review" email is sent when the default/unresolved fallback is used. Defaults to `marketingteam@koalainsulation.com` if blank, with `erin@giantcreative.ca` BCC'd. Gravity Forms renders its standard `{all_fields}` and `{entry_url}` merge tags, then WordPress `wp_mail()` sends the HTML so SMTP plugins can process and log it. |

Every lead sent to n8n carries three routing flags in its payload so the workflow can branch (e.g. alert vs. CRM push):

| Payload field | Value |
|---|---|
| `location_found` | `true` when a real location was resolved (URL, ZIP, fixed config, or default); `false` when none was found. |
| `location_source` | `url`, `zip`, `fixed`, `default`, or `unresolved` — how the location was determined. |
| `needs_review` | `true` for `default`/`unresolved` leads (routed to a fallback or none), `false` for a confident match. |

The routing source and a "needs routing review" banner are shown on the Gravity Forms entry detail sidebar. The background job's nearest-location ZIP API refinement (below) still runs on top of whatever is resolved here, so even a defaulted lead can be reassigned to a closer owner before it's sent.

### Resending a lead to n8n

An already-processed entry can be re-sent — e.g. after correcting a field mapping — from the Gravity Forms admin, without WP-CLI:

- **Single entry:** a **Resend to n8n** button in the *Koala Location Routing* panel on the entry detail screen.
- **Multiple entries:** a **Resend to n8n** bulk action on the Entries list.

Both reset the entry's status guard and re-run `kgi_process_quote_entry_job()` (`includes/admin/resend.php`), rebuilding the payload with the current mapping and re-POSTing it. Restricted to users who can edit entries. **Note:** a resend re-runs the *whole* workflow, so if n8n forwards to a CRM it can create a duplicate contact/job, and it re-fires the per-location Google Sheet webhook — pause the downstream step first if duplicates would be a problem.

### ZIP/postal-code ownership routing

Immediately before the background job builds the n8n payload, the submitted mapped `zip` value is normalized and checked against the Location CPT's `location_zipcode` and comma-separated `additional_zipcodes` ACF fields. If another published location owns that exact normalized value, that location supplies `location_id`, `location_name`, `housecall_pro_api_key`, `location_serviceminder_api_key`, and `location_serviceminder_id` to n8n. The submitted entry values and `page_url` remain unchanged. An empty, unmapped, or unowned ZIP/postal code falls back to the form's original location without flagging the outbound lead.

The ownership index is cached for one day, pre-warmed on this plugin version's first load, and rebuilt after ACF saves a Location CPT. This keeps normal lead routing to a cached lookup. The work is not performed during Gravity Forms validation, submission handling, or confirmation generation.

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

1. **Page load** — `includes/forms/field-population.php` resolves the current location from the URL (via `includes/location-resolver.php`, using the Country setting to know whether to expect a country-prefixed URL, and the Location Post Type setting to know which CPT to query) and writes it into the form's hidden routing fields. `includes/forms/assets.php` localizes that same resolved location, plus the marketing/attribution field IDs, to the frontend scripts — for the dataLayer push described below and for `assets/js/attribution.js`, which fills the attribution/tracking hidden fields client-side (see **Attribution & Tracking Fields** above).
2. **Validation** — `includes/forms/form-handler.php` validates phone field formatting. It does **not** block on location: an unresolved location is logged only, so the lead is captured and routed downstream rather than rejected.
3. **Submission** — on `gform_after_submission`, the location is resolved from the posted entry (`kgi_get_location_from_entry()`) → exact ZIP owner (`kgi_resolve_location_by_zip_exact()`) → the configured default location (see **Lead Routing** above), stored as entry meta with a `kgi_location_source`, and a background job is queued via WP-Cron. A default/unresolved fallback also flags the entry and emails staff.
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
    resend.php                       "Resend to n8n" button + entries-list bulk action
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
  js/attribution.js                  First-touch attribution capture + hidden-field population
```

## Coding Standards

This plugin targets the `WordPress`, `WordPress-Extra`, and `WordPress-Docs` PHPCS rulesets. Run before committing:

```
composer install   # first time only, installs phpcs + WPCS into vendor/
composer lint       # checks all three rulesets
composer lint:fix   # auto-fixes what phpcbf can safely fix, then re-run lint
```
