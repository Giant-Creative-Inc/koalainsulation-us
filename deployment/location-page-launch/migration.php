<?php
/** Production migration library. Invoked only by the WP-CLI entry point. */
final class Koala_Location_Migration {
    const OPTIONS = ['koala_beanstalk_locations_enabled','koala_beanstalk_location_ids','koala_beanstalk_location_media'];
    const EXCLUDED = ['savannah','central-connecticut','testing','efficiency','comfort'];

    public static function site_allowed($home, $environment) {
        return ($home === 'https://koalainsulation.com' && $environment === 'production')
            || ($home === 'https://staging.koalainsulation.com' && $environment === 'staging')
            || ($home === 'https://koalainsulation.local' && $environment === 'local');
    }
    public static function validate_slugs($slugs) {
        if (!$slugs || !is_array($slugs)) throw new RuntimeException('Location allowlist is empty.');
        if (count($slugs) !== count(array_unique($slugs))) throw new RuntimeException('Location allowlist contains duplicate slugs.');
        foreach ($slugs as $slug) {
            if (!is_string($slug) || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug)) throw new RuntimeException('Invalid location slug.');
            if (in_array($slug,self::EXCLUDED,true)) throw new RuntimeException('Location is excluded: '.$slug);
        }
        return array_values($slugs);
    }
    public static function private_path($path, $webroot) {
        if (!str_starts_with($path, DIRECTORY_SEPARATOR)) throw new RuntimeException('Use an absolute private path.');
        $parent = realpath(dirname($path)); $root = realpath($webroot);
        if (!$parent || !$root || $parent === $root || str_starts_with($parent, $root.DIRECTORY_SEPARATOR)) throw new RuntimeException('Path must be outside the public web root in an existing directory.');
        if (is_link($path)) throw new RuntimeException('Symlinks are not permitted for plan/journal files.');
        return $parent.DIRECTORY_SEPARATOR.basename($path);
    }
    public static function write_new($path, $data) {
        if (file_exists($path)) throw new RuntimeException('File already exists: '.$path);
        $old = umask(0077); $f = fopen($path,'x'); umask($old);
        if (!$f) throw new RuntimeException('Cannot exclusively create '.$path);
        $json = json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
        $ok = fwrite($f,$json) === strlen($json); fflush($f); fclose($f);
        if (!$ok || file_get_contents($path)!==$json) throw new RuntimeException('Incomplete private file: '.$path);
    }
    public static function assert_snapshot($expected, $current) {
        if ($expected !== $current) throw new RuntimeException('Production data drift: create and review a fresh dry run.');
    }
    private static function hash($data) { return hash('sha256',serialize($data)); }
    public static function options() {
        $out=[];
        foreach(self::OPTIONS as $key) {
            $sentinel = new stdClass(); $value = get_option($key,$sentinel);
            $out[$key] = ['exists'=>$value !== $sentinel,'value'=>$value !== $sentinel ? $value : null];
        }
        return $out;
    }
    public static function snapshot($post) {
        return ['id'=>(int)$post->ID,'slug'=>$post->post_name,'status'=>$post->post_status,'type'=>$post->post_type,
            'title'=>$post->post_title,'modified'=>$post->post_modified_gmt,'content'=>hash('sha256',$post->post_content),
            'meta'=>self::hash(get_post_meta($post->ID))];
    }
    public static function integrations() {
        if (!class_exists('GFAPI')) throw new RuntimeException('Gravity Forms is required.');
        $forms=[];
        $dependencies=json_decode(file_get_contents(__DIR__.'/dependencies.json'),true,512,JSON_THROW_ON_ERROR);
        foreach($dependencies['media'] as $reference) {
            if (get_post_meta($reference['source_id'],'_wp_attached_file',true)!==$reference['file']) throw new RuntimeException('Legacy attachment identity mismatch: '.$reference['source_id']);
        }
        foreach($dependencies['forms'] as $expected) {
            $form=GFAPI::get_form($expected['id']);
            $fields=$form ? array_map(static fn($field)=>['id'=>$field->id,'type'=>$field->type,'label'=>$field->label],$form['fields']) : [];
            if (!$form || $form['title']!==$expected['title'] || $fields!==$expected['fields']) throw new RuntimeException('Form identity/fields mismatch: '.$expected['id']);
        }
        foreach([12,13] as $id) {
            $form=GFAPI::get_form($id);
            if (!$form || empty($form['is_active'])) throw new RuntimeException('Required active form missing: '.$id);
            $forms[$id]=$form;
        }
        // Hash full form configuration (including notifications) without exporting destinations or credentials.
        global $wpdb;
        $routing=$wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'kgi\\_%' ORDER BY option_name", ARRAY_A);
        $feeds=GFAPI::get_feeds(null,[12,13],null,null);
        if (is_wp_error($feeds)) throw new RuntimeException('Cannot read Gravity Forms feed configuration.');
        return self::hash([$forms,$feeds,$routing]);
    }
    public static function fingerprint() {
        $root=get_template_directory().'/beanstalk'; $files=[];
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS)) as $file) {
            $relative=substr($file->getPathname(),strlen($root)+1);
            if (!$file->isFile() || preg_match('~^(tests|vendor|tools)/|(^|/)[.]|composer|phpunit|phpcs|TESTING~',$relative)) continue;
            $files[$relative]=hash_file('sha256',$file->getPathname());
        }
        ksort($files);
        return self::hash([$files,hash_file('sha256',__FILE__),hash_file('sha256',__DIR__.'/media-manifest.json'),hash_file('sha256',__DIR__.'/dependencies.json')]);
    }
    public static function content($post) {
        if (str_contains($post->post_content,'"name":"koala-location-hero"')) return $post->post_content;
        $content=koala_beanstalk_location_hero_content($post->ID);
        $content.="\n<!-- wp:koala/location-partners /-->\n<!-- wp:koala/location-services-section /-->\n<!-- wp:koala/location-overview /-->\n";
        $content.=koala_beanstalk_location_quote_banner_content();
        foreach(['benefits','energy','expect'] as $name) $content.=koala_beanstalk_location_feature_content($name);
        $content.=koala_beanstalk_location_financing_content();
        foreach(['reviews','communities','nap','offers','education','final-cta'] as $name) $content.=koala_beanstalk_location_community_content($name);
        // Pattern sources contain Local asset URLs. Store same-site root-relative URLs, as the staging migration does.
        $content=str_replace(['https://koalainsulation.local/','http://koalainsulation.local/','https:\\/\\/koalainsulation.local\\/','http:\\/\\/koalainsulation.local\\/'],['/','/','\\/','\\/'],$content);
        if (preg_match('~(?:https?:)?(?://|\\\\/\\\\/)koalainsulation[.]local~',$content)) throw new RuntimeException('Local URL in generated content: '.$post->post_name);
        return $content;
    }
    public static function media() {
        $manifest=json_decode(file_get_contents(__DIR__.'/media-manifest.json'),true,512,JSON_THROW_ON_ERROR);
        foreach($manifest as &$asset) {
            $file=get_template_directory().'/beanstalk/assets/'.$asset['file'];
            if (!is_file($file) || hash_file('sha256',$file)!==$asset['sha256']) throw new RuntimeException('Design asset mismatch: '.$asset['file']);
            $ids=get_posts(['post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>2,'fields'=>'ids','meta_key'=>'_koala_beanstalk_asset','meta_value'=>'beanstalk-location:'.$asset['file']]);
            if (count($ids)>1) throw new RuntimeException('Duplicate media identity: '.$asset['file']);
            $asset['existing_id']=$ids ? (int)$ids[0] : 0;
            if ($ids && get_post_meta($ids[0],'_koala_beanstalk_sha256',true)!==$asset['sha256']) throw new RuntimeException('Managed media hash mismatch: '.$asset['file']);
        }
        return $manifest;
    }
    public static function plan($slugs) {
        self::validate_slugs($slugs);
        if (get_option('koala_beanstalk_locations_enabled',false)) throw new RuntimeException('Disable the previous rollout before planning a new one.');
        $plan=['version'=>1,'site'=>rtrim(home_url(),'/'),'environment'=>wp_get_environment_type(),'created'=>gmdate('c'),
            'code'=>self::fingerprint(),'integrations'=>self::integrations(),'options'=>self::options(),'posts'=>[],'media'=>self::media()];
        foreach($slugs as $slug) {
            $post=get_page_by_path($slug,OBJECT,'location');
            if (!$post || $post->post_status!=='publish') throw new RuntimeException('Published location not found: '.$slug);
            $after=self::content($post);
            $plan['posts'][]=['id'=>(int)$post->ID,'slug'=>$slug,'before'=>$post->post_content,'after'=>$after,
                'snapshot'=>self::snapshot($post),'after_sha256'=>hash('sha256',$after)];
        }
        $plan['digest']=self::hash($plan);
        return $plan;
    }
    public static function load($path) {
        self::private_path($path,ABSPATH);
        $data=json_decode(file_get_contents($path),true,512,JSON_THROW_ON_ERROR);
        if (($data['version']??null)!==1 || ($data['site']??null)!==rtrim(home_url(),'/') || ($data['environment']??null)!==wp_get_environment_type()) throw new RuntimeException('Plan/journal belongs to a different site or environment.');
        $copy=$data; unset($copy['digest']);
        if (($data['digest']??'')!==self::hash($copy)) throw new RuntimeException('Plan/journal integrity check failed.');
        return $data;
    }
    private static function save_option($key,$value) {
        update_option($key,$value,false);
        $missing=new stdClass();$actual=get_option($key,$missing);
        if ($actual===$missing || $actual!=$value) throw new RuntimeException('Option did not persist: '.$key);
    }
    public static function apply($plan,$journal) {
        self::private_path($journal,ABSPATH);
        self::assert_snapshot($plan['code'],self::fingerprint());
        self::assert_snapshot($plan['integrations'],self::integrations());
        self::assert_snapshot($plan['options'],self::options());
        self::assert_snapshot($plan['media'],self::media());
        foreach($plan['posts'] as $row) self::assert_snapshot($row['snapshot'],self::snapshot(get_post($row['id'])));
        // The immutable plan is also the rollback journal; it is saved before the first database write.
        self::write_new($journal,$plan);
        self::save_option('koala_beanstalk_locations_enabled',false);
        require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
        $map=get_option('koala_beanstalk_location_media',[]);
        foreach($plan['media'] as $asset) {
            $id=$asset['existing_id'];
            if (!$id) {
                $temp=wp_tempnam(basename($asset['file']));
                if (!$temp || !copy(get_template_directory().'/beanstalk/assets/'.$asset['file'],$temp)) throw new RuntimeException('Cannot prepare media.');
                $id=media_handle_sideload(['name'=>'koala-'.str_replace('/','-',$asset['file']),'tmp_name'=>$temp],0,$asset['title'],['post_title'=>$asset['title']]);
                if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
                update_post_meta($id,'_koala_beanstalk_asset','beanstalk-location:'.$asset['file']);
                update_post_meta($id,'_koala_beanstalk_sha256',$asset['sha256']);
                update_post_meta($id,'_wp_attachment_image_alt',$asset['alt']);
            }
            $map[$asset['file']]=(int)$id;
            self::save_option('koala_beanstalk_location_media',$map);
            WP_CLI::log('Media '.$id.': '.wp_get_attachment_url($id));
        }
        foreach($plan['posts'] as $row) {
            self::assert_snapshot($row['snapshot'],self::snapshot(get_post($row['id'])));
            if ($row['before']!==$row['after']) {
                $result=wp_update_post(wp_slash(['ID'=>$row['id'],'post_content'=>$row['after']]),true);
                if (is_wp_error($result)) throw new RuntimeException($result->get_error_message());
            }
            if (get_post_field('post_content',$row['id'])!==$row['after']) throw new RuntimeException('Content save mismatch: '.$row['slug']);
            self::assert_snapshot($row['snapshot']['meta'],self::hash(get_post_meta($row['id'])));
            WP_CLI::log('Prepared '.$row['slug']);
        }
        self::save_option('koala_beanstalk_location_ids',array_column($plan['posts'],'id'));
        self::verify($plan,false);
    }
    public static function verify($plan,$delivery=true) {
        self::assert_snapshot($plan['code'],self::fingerprint());
        self::assert_snapshot($plan['integrations'],self::integrations());
        foreach($plan['posts'] as $row) {
            $post=get_post($row['id']);
            if (!$post || $post->post_type!=='location' || $post->post_status!=='publish' || $post->post_name!==$row['slug'] || $post->post_title!==$row['snapshot']['title'] || hash('sha256',$post->post_content)!==$row['after_sha256']) throw new RuntimeException('Migrated location differs: '.$row['slug']);
            self::assert_snapshot($row['snapshot']['meta'],self::hash(get_post_meta($row['id'])));
        }
        self::assert_snapshot(array_column($plan['posts'],'id'),get_option('koala_beanstalk_location_ids',[]));
        $map=get_option('koala_beanstalk_location_media',[]);
        foreach($plan['media'] as $asset) {
            $id=$map[$asset['file']]??0;
            if (!$id || get_post_meta($id,'_koala_beanstalk_asset',true)!=='beanstalk-location:'.$asset['file'] || get_post_meta($id,'_koala_beanstalk_sha256',true)!==$asset['sha256']) throw new RuntimeException('Media mapping differs: '.$asset['file']);
            if ($delivery) {
                $url=wp_get_attachment_url($id);$response=$url ? wp_remote_get($url,['timeout'=>30,'limit_response_size'=>1024]) : false;
                $type=$response && !is_wp_error($response) ? wp_remote_retrieve_header($response,'content-type') : '';
                if (!$response || is_wp_error($response) || wp_remote_retrieve_response_code($response)!==200 || !str_starts_with($type,'image/')) throw new RuntimeException('Image delivery failed: '.$asset['file']);
                WP_CLI::log('Image delivered: '.$url);
            }
        }
    }
    public static function activate($plan) {
        self::verify($plan,true);
        self::save_option('koala_beanstalk_locations_enabled',true);
    }
    public static function rollback($plan) {
        // Disable first even if drift prevents restoration. Unrelated edits and new leads are never overwritten.
        self::save_option('koala_beanstalk_locations_enabled',false);
        $ids=get_option('koala_beanstalk_location_ids',[]);
        $before=$plan['options']['koala_beanstalk_location_ids'];
        if ($ids!==array_column($plan['posts'],'id') && $ids!==($before['exists'] ? $before['value'] : [])) throw new RuntimeException('Activation allowlist drift; stopped with renderer disabled.');
        foreach($plan['posts'] as $row) {
            $post=get_post($row['id']);
            if (!$post || $post->post_name!==$row['slug'] || $post->post_type!=='location' || !in_array($post->post_content,[$row['before'],$row['after']],true)) throw new RuntimeException('Content drift; stopped with renderer disabled: '.$row['slug']);
        }
        $mediaBefore=$plan['options']['koala_beanstalk_location_media'];
        $map=get_option('koala_beanstalk_location_media',[]);
        $original=$mediaBefore['exists'] ? $mediaBefore['value'] : [];
        foreach($plan['media'] as $asset) {
            $key=$asset['file'];$id=$map[$key]??null;
            if ($id!==null && $id!==($original[$key]??null)
                && (get_post_meta($id,'_koala_beanstalk_asset',true)!=='beanstalk-location:'.$key || get_post_meta($id,'_koala_beanstalk_sha256',true)!==$asset['sha256'])) throw new RuntimeException('Media mapping drift; stopped with renderer disabled: '.$key);
            if (array_key_exists($key,$original)) $map[$key]=$original[$key]; else unset($map[$key]);
        }
        foreach($plan['posts'] as $row) {
            if (get_post_field('post_content',$row['id'])===$row['before']) continue;
            $result=wp_update_post(wp_slash(['ID'=>$row['id'],'post_content'=>$row['before']]),true);
            if (is_wp_error($result) || get_post_field('post_content',$row['id'])!==$row['before']) throw new RuntimeException('Rollback save failed: '.$row['slug']);
        }
        foreach($plan['options'] as $key=>$state) {
            if ($key==='koala_beanstalk_locations_enabled' || $key==='koala_beanstalk_location_media') continue;
            if ($state['exists']) self::save_option($key,$state['value']); else { delete_option($key); if (get_option($key,null)!==null) throw new RuntimeException('Option removal failed: '.$key); }
        }
        if ($map || $mediaBefore['exists']) self::save_option('koala_beanstalk_location_media',$map); else delete_option('koala_beanstalk_location_media');
        $state=$plan['options']['koala_beanstalk_locations_enabled'];
        if ($state['exists']) self::save_option('koala_beanstalk_locations_enabled',$state['value']); else delete_option('koala_beanstalk_locations_enabled');
    }
}
