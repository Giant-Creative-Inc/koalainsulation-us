# QA Checklist — Attribution Tracking & Lead-Capture Resilience (v0.7.0)

Covers the two workstreams added in `0.7.0`:
- **A** — attribution/tracking hidden fields (client-side capture)
- **B** — never-reject lead capture (URL → ZIP → default → flagged to n8n)

Substitute your real values for the placeholders: `{SITE}` (e.g. `https://koala-example.com`), `{location-slug}` (a real published location page, e.g. `/st-louis`), and `{field-id}` (a hidden field's Gravity Forms ID).

---

## 0. Setup & prerequisites

- [ ] **Deploy 0.7.0** and **purge all caches** — WP Rocket, Nginx FastCGI (GridPane), and Cloudflare. The form HTML is cached, so until it's purged the new `attribution.js` tag won't appear.
- [ ] Confirm the script loads: DevTools → **Network** → filter `attribution.js` → status `200`, URL ends `?ver=0.7.0`.
- [ ] On the quote form (GF editor) add a **Hidden** field for each tracking key you want to test; note each field ID.
- [ ] **Settings → Koala Gravity → Quote Form Fields**: map each tracking key (`gclid`, `gbraid`, `wbraid`, `fbclid`, `msclkid`, `landing_page`, `referrer`, `form_timestamp`, `service`, `cta_text`, `form_id`, and the `Utm*` keys) to its hidden field.
- [ ] Make sure GF's own "dynamic population" is **off** on those hidden fields (the script only fills empties, so a GF-populated value would mask a test).
- [ ] **Settings → Lead Routing**: set a **Default Location** and a **Notification Email**.
- [ ] For `service`/`cta_text`: add `<body data-kgi-service="attic-insulation">` (or `<meta name="kgi-service" content="…">`) on the relevant page, and give the CTA link/button `data-kgi-cta="Get a Free Quote"`.

### Inspection tools (keep DevTools open)
- **Hidden field value:** Elements tab → find `input[name="input_{field-id}"]` → read its `value`. Or Console: `document.querySelector('input[name="input_{field-id}"]').value`
- **First-touch cookie:** Application → Cookies → `kgi_attrib` (JSON).
- **CTA memory:** Application → Session Storage → `kgi_cta`.
- **Fresh first-touch test:** use an **Incognito** window, or delete the `kgi_attrib` cookie and hard-reload.
- **Entry result:** GF → Entries → open the entry → **Koala Location Routing** sidebar.
- **n8n payload:** open the n8n execution / webhook node input JSON.

---

## 1. Attribution field population (Workstream A)

### 1a. Last-touch — params on the form page URL
- [ ] Open `{SITE}/{location-slug}/?utm_source=google&utm_medium=cpc&gclid=TEST_GCLID_1`
- [ ] Confirm the `UtmSource`, `UtmMedium`, and `gclid` hidden inputs contain those exact values.
- [ ] Confirm `form_id` = the GF form ID and `form_timestamp` = an ISO timestamp.

### 1b. First-touch — land elsewhere, submit on a clean URL (the caching case)
- [ ] Incognito. Open a **non-form** page: `{SITE}/about/?utm_source=meta&gclid=FIRST_TOUCH_1`
- [ ] Confirm `kgi_attrib` cookie now holds `{ landing_page, referrer, ts, UtmSource:"meta", gclid:"FIRST_TOUCH_1" }`.
- [ ] Click a normal internal link to the form page on a **clean URL** (`{SITE}/{location-slug}/`, no query string).
- [ ] Confirm `gclid`/`UtmSource` fill from the cookie, and `landing_page` = the `/about/…` URL, `referrer` = whatever brought you to `/about/`.

### 1c. Precedence — current URL wins, cookie is fallback
- [ ] With the cookie from 1b still set, open the form page **with different params**: `{SITE}/{location-slug}/?utm_source=bing&gclid=SECOND_TOUCH`
- [ ] Confirm the fields show `SECOND_TOUCH`/`bing` (current URL / last touch), **not** the cookie values.
- [ ] Open the form page clean again → confirm it falls back to the cookie's first-touch values.

### 1d. Each click-ID type
Load the form page with each and confirm the matching hidden field fills:
- [ ] `gclid` → `…/?gclid=TEST_GCLID`
- [ ] `gbraid` → `…/?gbraid=TEST_GBRAID`
- [ ] `wbraid` → `…/?wbraid=TEST_WBRAID`
- [ ] `fbclid` → `…/?fbclid=TEST_FBCLID`
- [ ] `msclkid` → `…/?msclkid=TEST_MSCLKID`

### 1e. landing_page, referrer, form_timestamp, form_id
- [ ] `landing_page` = the first URL of the session (from cookie).
- [ ] `referrer` = `document.referrer` captured at first touch (empty if direct/typed).
- [ ] `form_timestamp` = ISO 8601 string, set at fill time.
- [ ] `form_id` = the GF form ID.

### 1f. service & cta_text (page context)
- [ ] With `<body data-kgi-service="attic-insulation">` set, confirm `service` = `attic-insulation`.
- [ ] Remove the body attribute, add `<meta name="kgi-service" content="crawl-space">` → confirm `service` = `crawl-space`.
- [ ] With neither set → confirm `service` is blank (no path-segment guessing by design).
- [ ] Click a CTA with `data-kgi-cta="Get a Free Quote"` that leads to the form → confirm `cta_text` = `Get a Free Quote`.
- [ ] On a page with `<body data-kgi-cta="Book Now">` and no click → confirm `cta_text` = `Book Now`.

### 1g. Cache-safety
- [ ] Hit the form page in two browsers with **different** `gclid` values → each shows its own value (no cross-contamination).
- [ ] View-source (or a curl of the cached page) of the clean form URL → confirm the tracking hidden inputs are **empty** in the served HTML (values are added by JS, never baked into cache).

### 1h. Multiple form contexts
- [ ] Repeat 1a–1b for an **Additional Quote Form** (its own mapped field IDs).
- [ ] Repeat for a **Fixed-Location Form**.
- [ ] Page with the form embedded **twice** (inline + popup): confirm both copies fill.
- [ ] Multi-page / AJAX form: advance a page and confirm fields still populate after re-render.

### 1i. dataLayer push (thank-you page)
- [ ] Submit an AJAX-enabled form → on the thank-you page run `window.dataLayer` in Console.
- [ ] Confirm the `quote_form_submission` event includes the non-PII tracking keys (`gclid`, `UtmSource`, `landing_page`, `service`, `cta_text`, `form_id`, …) and **no** PII (name/email/phone).

---

## 2. Lead capture & routing (Workstream B)

> Tip: to test the no-URL cases you need the quote form on a page whose path is **not** a location slug (homepage, `/get-a-quote/`, or a popup on a non-location page).

### 2a. Location page with matching ZIP
- [ ] Submit from a real location page `{SITE}/{location-slug}/` using a ZIP owned by that location.
- [ ] Sidebar: **Routing Source = url**, **ZIP Routing = original_location**, no "needs review" banner, no notification email.

### 2b. Location page with no matching ZIP
- [ ] Submit from a real location page using a ZIP that has no exact owner and no owning location within the configured radius.
- [ ] After the background job runs, the page-derived location is cleared. Sidebar: **Routing Source = unresolved**, **ZIP Routing = unresolved**, and the "Needs routing review" banner is shown.
- [ ] Confirm one notification email arrives with all submitted fields and an entry link. Confirm Post SMTP logged the `wp_mail()` call.

### 2c. Resolved by ZIP (no location in URL)
- [ ] Submit from a **non-location page**, entering a ZIP that a location owns (its `location_zipcode` / `additional_zipcodes`).
- [ ] Entry is **created** (never rejected). Sidebar: **Routing Source = zip**, routed to that location, no review flag.

### 2d. Default-location fallback
- [ ] Submit from a non-location page with a ZIP **no** location owns and that isn't near one (e.g. a far/invalid ZIP; temporarily blank the zipcodeapi key if the nearest-API keeps finding one).
- [ ] The submission initially uses the Default Location and sends one review email; after background ZIP routing, the location is cleared and **Routing Source = unresolved**.
- [ ] Confirm only one notification email is sent for the entry.

### 2e. Unresolved — no default configured
- [ ] Set **Default Location = None**. Repeat the unowned/out-of-radius ZIP test from 2d.
- [ ] Sidebar: **Routing Source = unresolved**, review banner, notification email arrives.
- [ ] Confirm the lead is **still sent to n8n** (see 3a) with `location_found = false`.

### 2f. Fixed-location edge
- [ ] Point a Fixed-Location Form at a location, then unpublish/trash that location, and submit.
- [ ] Confirm it falls back to the Default Location (or `unresolved` if none) rather than being lost.

### 2g. No lead is ever rejected
- [ ] Force an unresolvable submission (non-location page, garbage ZIP) → confirm the visitor is **not** shown a location error and an entry **is** saved every time.
- [ ] Confirm phone validation still rejects a malformed phone number (regression — that block is intentional).

---

## 3. n8n payload verification

Trigger a submission for each routing outcome and inspect the webhook payload in n8n.

### 3a. Routing flags present on every payload
- [ ] `location_found` — `true` for url/zip/fixed/default, `false` for unresolved.
- [ ] `location_source` — `url` / `zip` / `fixed` / `default` / `unresolved`.
- [ ] `needs_review` — `true` for default/unresolved, `false` otherwise.

### 3b. Stable tracking schema
- [ ] With a form that has **no** field mapped for, say, `gbraid`, confirm the payload still contains `"gbraid": ""` (empty, not missing). All 16 tracking keys should always be present.

### 3c. Values end-to-end
- [ ] A lead submitted via `?gclid=E2E_TEST&utm_source=google` shows `"gclid":"E2E_TEST"`, `"UtmSource":"google"` in the n8n payload.
- [ ] Location fields (`location_id`, `location_name`, API keys) are populated for a resolved lead and **empty** for an `unresolved` lead.

### 3d. n8n workflow branch (your side)
- [ ] Add/verify the branch: `location_found == false` (or `location_source == "unresolved"`) → **Slack alert**, and **skip** the ServiceMinder/Housecall push so an empty-key lead doesn't error.

---

## 4. Regression sanity
- [ ] An existing, normal location-page lead behaves exactly as before (source=url, sent to n8n, correct thank-you redirect).
- [ ] Existing mapped fields (`first_name`, `email`, `zip`, `Utm*`) are unchanged in the payload.
- [ ] Google Sheet webhook payload is unchanged (tracking-key "always present" applies to n8n only).

---

## Example URLs by tracking-key type

Replace `{SITE}/{location-slug}` with a real form page. Values are arbitrary test strings — you don't need real ad clicks.

| Scenario | Example URL |
|---|---|
| **Google Search (gclid)** | `{SITE}/{location-slug}/?utm_source=google&utm_medium=cpc&utm_campaign=brand_search&utm_term=attic%20insulation&utm_content=ad_a&gclid=Cj0KTEST_GCLID` |
| **Google PMax / iOS (gbraid)** | `{SITE}/{location-slug}/?utm_source=google&utm_medium=cpc&utm_campaign=pmax&gbraid=0AAAAATEST_GBRAID` |
| **Google web-to-app (wbraid)** | `{SITE}/{location-slug}/?utm_source=google&utm_medium=cpc&utm_campaign=pmax&wbraid=Cj0KTEST_WBRAID` |
| **Meta / Facebook (fbclid)** | `{SITE}/{location-slug}/?utm_source=facebook&utm_medium=paid_social&utm_campaign=spring_promo&fbclid=IwAR2TEST_FBCLID` |
| **Microsoft / Bing (msclkid)** | `{SITE}/{location-slug}/?utm_source=bing&utm_medium=cpc&utm_campaign=bing_brand&msclkid=abc123TEST_MSCLKID` |
| **Email / newsletter (UTMs only)** | `{SITE}/{location-slug}/?utm_source=newsletter&utm_medium=email&utm_campaign=august_offer` |
| **All keys at once (edge)** | `{SITE}/{location-slug}/?utm_source=google&utm_medium=cpc&utm_campaign=all&utm_term=insulation&utm_content=v1&gclid=G_T&gbraid=GB_T&wbraid=WB_T&fbclid=FB_T&msclkid=MS_T` |
| **First-touch (land off-form, then navigate clean)** | Land: `{SITE}/about/?utm_source=google&utm_medium=cpc&gclid=FIRST_TOUCH` → then open `{SITE}/{location-slug}/` with no query string |
| **Last-touch override** | With the first-touch cookie set, open `{SITE}/{location-slug}/?utm_source=bing&gclid=SECOND_TOUCH` and confirm the newer values win |
| **Direct / no params (defaults)** | `{SITE}/{location-slug}/` → tracking fields blank except `landing_page`, `form_id`, `form_timestamp`, and page-context `service`/`cta_text` |

### Routing test inputs (form field values, not URL)
| Outcome | How to trigger |
|---|---|
| `url` | Submit from a real `{location-slug}` page |
| `zip` | Submit from a non-location page with a ZIP a location **owns** |
| `default` | Non-location page + ZIP **no** location owns (default configured) |
| `unresolved` | Non-location page + unowned ZIP, **Default Location = None** |
