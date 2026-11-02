# US Beanstalk production migration

This package is manual WP-CLI tooling. Uploading it does not run it. No deployment hook runs it. Merge/deploy and database execution are separate decisions. Canada is explicitly rejected.

## Prerequisites

- Review PR #8 and deploy its complete theme/runtime before generating the dry run. The new production renderer stays disabled until activation is explicitly saved, and only IDs in the migration allowlist can use it.
- Rehearse apply/verify/activate/rollback on an isolated staging copy first. The runner accepts exactly US production, US staging, or US Local with matching environment types. It does not change the environment type.
- Review `locations.proposed.json`: 107 proposed locations from the November 2 inventory. Savannah, Central Connecticut, Testing, Efficiency and Comfort are deliberately rejected. Reconcile the list with current production before execution. The migration does not change redirects or any excluded page.
- Review SEO, rendered content and navigation parity on staging. Existing Chattanooga schema must remain the production version; inherited Baldwin County heading cleanup is a separate editorial decision. The migration never imports staging SEO/review metadata.
- Forms 12 and 13 and all expected fields must exist. The three legacy attachments referenced by the renderer must match their actual file identities. Preflight aborts instead of substituting another image. All 26 design assets must match the manifest hashes.
- Have hosting database/uploads backups and a restore path. Keep plans, journals and SQL exports private; they contain original page content. They do not export lead entries or form credentials.

## Upload via SFTP/FTP

Upload `migrate.php`, `migration.php`, `dependencies.json`, `media-manifest.json` and the reviewed `locations.proposed.json` together to:

```
/var/www/koalainsulation.com/beanstalk-launch-20261102/
```

That is outside `/var/www/koalainsulation.com/htdocs`. Use a new dated folder for each release. The package contains no design image binaries: those ship with the theme PR. Prefer SFTP where available. Run these commands as the site system user. Add `--allow-root` to `wp` only if your SSH session is root, as in the existing server maintenance workflow.

## Commands (after deployment and release approval)

Create the private upload folder before transferring the files:

```bash
mkdir -m 700 /var/www/koalainsulation.com/beanstalk-launch-20261102
```

Take a fresh database export in addition to the hosting backup:

```bash
wp --path=/var/www/koalainsulation.com/htdocs db export /var/www/koalainsulation.com/beanstalk-launch-20261102/before.sql
chmod 600 /var/www/koalainsulation.com/beanstalk-launch-20261102/before.sql
```

Create the read-only dry run (writes only the private plan file):

```bash
wp --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-launch-20261102/migrate.php dry-run /var/www/koalainsulation.com/beanstalk-launch-20261102/locations.proposed.json /var/www/koalainsulation.com/beanstalk-launch-20261102/plan.json
```

Review `plan.json` privately. Each post contains its slug/ID, original and proposed content and drift hashes. The plan also lists each managed image and whether it will be imported or reused. Options retain their original absent/value state. Review the actual content, not only the counts. Do not edit a generated plan: integrity verification rejects edits. Change the allowlist and generate a new plan filename if scope changes.

Prepare the database content and media; the immutable rollback journal is saved before the first database write. This does **not** enable the new pages:

```bash
wp --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-launch-20261102/migrate.php apply /var/www/koalainsulation.com/beanstalk-launch-20261102/plan.json /var/www/koalainsulation.com/beanstalk-launch-20261102/journal.json
```

Verify the exact saved content, unchanged location metadata/form/feed/routing configuration, attachment identities and actual design image HTTP delivery:

```bash
wp --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-launch-20261102/migrate.php verify /var/www/koalainsulation.com/beanstalk-launch-20261102/journal.json
```

Activate only after successful verification and release QA:

```bash
wp --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-launch-20261102/migrate.php activate /var/www/koalainsulation.com/beanstalk-launch-20261102/journal.json
```

Activation repeats verification, including image delivery, then enables only the reviewed location IDs. Purge the relevant WP Rocket/hosting/Cloudflare HTML caches through the site's existing cache controls. `wp cache flush` alone does not establish that hosting/CDN HTML is fresh. Check normal public URLs and response cache headers; verify old and new page types. Separately authorized synthetic submissions must prove correct CRM/notification receipt; neither verify nor activate submits a form.

## On failure / rollback

Apply failures leave activation disabled; keep the journal and complete output. Do not rerun apply with the same journal filename and do not set the option manually. Roll back the partial run, then generate a fresh reviewed plan. Imported images are retained and recognized on the next plan, so successful imports are reused rather than duplicated. An interrupted upload before WordPress assigns its identity can leave an unused attachment for later manual review; it is not deleted automatically.

```bash
wp --path=/var/www/koalainsulation.com/htdocs eval-file /var/www/koalainsulation.com/beanstalk-launch-20261102/migrate.php rollback /var/www/koalainsulation.com/beanstalk-launch-20261102/journal.json
```

Rollback disables the renderer first. It validates every target before restoring page content, refuses to overwrite later content edits or changed managed media mappings, and restores migration-owned option values/absence. Unrelated media-map additions and all current lead entries remain intact. Design attachments remain to avoid breaking shared references. If drift blocks restoration, the renderer stays disabled; resolve the conflicting records explicitly. Purge all relevant HTML caches after rollback as well.

Do not restore the whole database routinely: that could discard new leads and unrelated edits. Full disaster recovery requires a plan to preserve all writes made since the backup.

## Tests and evidence

From the repository root:

```bash
php tests/location-launch/activation-test.php
php tests/location-launch/runner-test.php
php tests/location-launch/lifecycle-test.php
```

The in-memory adapter tests plan/apply/verify/activation/rollback, interrupted saves/imports, data/feed drift, integrity and cross-site rejection, option restoration, media reuse and preservation of unrelated mappings. It does not replace a real WordPress staging rehearsal or external CRM delivery tests. Beanstalk PHPUnit and lint remain in the source development directory per repository policy.
