# koalainsulation-us

GridPane **Hybrid** deployment repository for the Koala Insulation (US) WordPress site.

## What "Hybrid" means

Only the paths tracked in this repo are deployed to the server, overwriting their
matching directories. Everything else — WordPress core, `wp-config.php`, uploads,
and any plugins/themes **not** in this repo — is managed the normal way through
wp-admin and is left untouched by deploys.

The `.gpconfig/hybrid` marker file is what tells GridPane to use hybrid deployment.

## Tracked in this repo

```
.gpconfig/                                   deployment config + hooks
wp-content/plugins/koala-location/           custom plugin
wp-content/plugins/koala-gravity-integration-main/  custom plugin (GIANT Creative)
wp-content/plugins/blog-sync/                custom plugin
wp-content/themes/bricks/                    custom site theme
```

## Deploy hooks (`.gpconfig/`)

| File                   | When            | Runs as          |
| ---------------------- | --------------- | ---------------- |
| `predeploy.sh`         | before deploy   | site system user |
| `predeploy-server.sh`  | before deploy   | root             |
| `postdeploy.sh`        | after deploy    | site system user |
| `postdeploy-server.sh` | after deploy    | root             |

`keep.releases` controls how many past releases GridPane retains (currently `4`).

## Not committed

WordPress core, `wp-config.php`, `wp-content/uploads`, third-party plugins/themes
managed via wp-admin, `vendor/` (dev-only Composer deps), local editor backups
(`*-bk<date>`), `.DS_Store`, and logs. See [.gitignore](.gitignore).
