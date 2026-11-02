# Location city-name display casing

The November 2 US inventory found 823 uppercase city-list entries across 44 of the 107 launched location pages (742 distinct uppercase titles). East Valley has six uppercase entries; Upper Cumberland has 46.

The native community-list renderer now title-cases only wholly uppercase labels. Existing mixed case stays unchanged. State abbreviations after commas and AFB are retained; Mc prefixes and apostrophes are handled. City ordering, hidden/expanded entries and Show More behavior are preserved. Original spelling and spacing are retained; this is not an official place-name spelling correction.

This changes display on native location community lists only. No database update or activation migration is needed. City records, city-page titles, URLs, schema, SEO fields and relationships are unchanged.

After approval, upload only:

`wp-content/themes/bricks/beanstalk/location-community.php`

Clear the affected location HTML caches, then verify normal live URLs for East Valley and Upper Cumberland and rescan the affected lists. Do not upload tests or deployment documentation to the public theme. If reverting, restore the previous runtime file and clear affected page caches.

Validation:

- Source module `composer test`: 250 tests, 1,013 assertions passed. The new formatter/rendering regressions failed first on the old output.
- Source module `composer lint`: passed.
- `php tests/location-city-casing/check.php`: passed.
- Applying the formatter in memory to the complete live inventory produced 823 label changes across 44 locations; mixed-case names were unchanged.
- Local East Valley and Upper Cumberland smoke checks at 1440/375 px passed: visible title-case labels, Show More, header/content/footer, dynamic location data, form presence, asset isolation and no horizontal overflow. No leads submitted.
