# U.S. staging service rollout

This release covers national and location pages for spray foam, blown-in attic insulation, and insulation removal. It targets the staging branch. The new renderer requires home host `staging.koalainsulation.com`, WordPress environment type `staging`, and the `koala_beanstalk_services_enabled` option. Local retains automatic activation.

## WordPress and media preflight

1. Back up staging's database and uploads. Confirm `wp option get home` and `wp eval 'echo wp_get_environment_type();'` on the deployment target.
2. Compare the three national service posts by slug and the location service posts by location and service slug. Preserve staging ACF data and apply only the reviewed Local content or metadata that differs. Compare national block content, FAQ image associations, testimonial fallback, and process image overrides. Use attachment paths to resolve staging IDs. Also verify the editorial FAQ attachment references (87720, 87721 and 79734 in Local) and the national reviews shortcode (13780 in Local) against staging before activation.
3. Verify the staging quote forms 12 and 13, location relationships, and the national ZIP locator popup. Keep staging feeds and credentials. Use a controlled test destination before submitting a form.
4. Transfer required media into staging. Verify Cloudflare Images returns working files, and place the compressed upload variants alongside the matching staging originals using their relative uploads paths. The service renderer selects a same-name JPEG/PNG WebP variant only if the file is available on disk.

## Activate together and validate

After the staging branch is deployed and the content and media checks pass, run `wp option update koala_beanstalk_services_enabled 1` on staging. This enables the three supported service types at their normal national and location URLs in one step. Clear page and CDN caches, then check the three national routes and location examples including Boulder and Atlanta. Verify service copy, media, quote and ZIP flows, videos, and existing map pages. Confirm an anonymous cache hit and run three comparable mobile Lighthouse audits on Boulder.

For code rollback, run `wp option delete koala_beanstalk_services_enabled` and purge staging caches. Restore any changed posts or metadata from the preflight backup if needed. Media imports and staging content changes are separate from the code deployment.
