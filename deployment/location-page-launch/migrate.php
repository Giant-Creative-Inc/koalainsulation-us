<?php
/** Run only with wp eval-file; uploading this package never executes it. */
if (!defined('WP_CLI') || !WP_CLI) {
    http_response_code(403);
    exit('WP-CLI only.');
}
require_once __DIR__.'/migration.php';
try {
    if (!Koala_Location_Migration::site_allowed(rtrim(home_url(),'/'),wp_get_environment_type())) throw new RuntimeException('Unexpected site/environment; refusing migration.');
    $mode=$args[0]??'';
    if (!in_array($mode,['dry-run','apply','verify','activate','rollback'],true)) throw new RuntimeException('Expected dry-run, apply, verify, activate or rollback.');
    $lockPath=sys_get_temp_dir().'/koala-beanstalk-'.hash('sha256',home_url()).'.lock';
    if (is_link($lockPath)) throw new RuntimeException('Invalid migration lock.');
    $old=umask(0077);$lock=fopen($lockPath,'c');umask($old);
    if (!$lock || !flock($lock,LOCK_EX|LOCK_NB)) throw new RuntimeException('Another migration command is running.');
    if ($mode==='dry-run') {
        if (count($args)!==3) throw new RuntimeException('dry-run requires an allowlist JSON path and a NEW plan JSON path.');
        $allow=Koala_Location_Migration::private_path($args[1],ABSPATH);
        $out=Koala_Location_Migration::private_path($args[2],ABSPATH);
        if (file_exists($out)) throw new RuntimeException('Plan output already exists.');
        $slugs=json_decode(file_get_contents($allow),true,512,JSON_THROW_ON_ERROR);
        $plan=Koala_Location_Migration::plan($slugs);
        Koala_Location_Migration::write_new($out,$plan);
        foreach($plan['posts'] as $row) WP_CLI::log($row['id'].' '.$row['slug'].' '.$row['snapshot']['content'].' -> '.$row['after_sha256']);
        WP_CLI::success('Dry run saved to '.$out.'. No database/media writes. Review before/after content and scope before apply.');
    } else {
        if (count($args)!==($mode==='apply'?3:2)) throw new RuntimeException('Expected '.$mode.' '.($mode==='apply'?'PLAN.json NEW-JOURNAL.json':'JOURNAL.json'));
        $plan=Koala_Location_Migration::load($args[1]);
        if ($mode==='apply') {
            Koala_Location_Migration::apply($plan,$args[2]);
            WP_CLI::success('Content/media prepared. Renderer remains disabled. Run verify and review QA before activate.');
        } elseif ($mode==='verify') {
            Koala_Location_Migration::verify($plan,true);
            WP_CLI::success('Content, preserved metadata/integration configuration and design image delivery verified. This does not test lead delivery or public page caches.');
        } elseif ($mode==='activate') {
            Koala_Location_Migration::activate($plan);
            WP_CLI::success('Only the migration allowlist is enabled. Purge caches and verify public URLs, navigation, SEO and authorized form delivery.');
        } else {
            Koala_Location_Migration::rollback($plan);
            WP_CLI::success('Original page content and migration options restored. Imported media retained. Purge caches and verify the legacy pages.');
        }
    }
} catch (Throwable $error) {
    WP_CLI::error($error->getMessage());
}
