<?php
/** Run with wp eval-file migrate.php [dry-run|apply|rollback] [backup-file]. */
$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run','apply','rollback'], true)) WP_CLI::error('Expected dry-run, apply, or rollback.');
$local = wp_parse_url(home_url(), PHP_URL_HOST) === 'koalainsulation.local';
if (wp_get_environment_type() !== 'staging' && !($local && $mode === 'dry-run')) WP_CLI::error('Database writes are restricted to WP_ENVIRONMENT_TYPE=staging.');
foreach (['koala_beanstalk_location_hero_content','koala_beanstalk_location_feature_content','koala_beanstalk_location_community_content','koala_beanstalk_location_media_urls'] as $function) if (!function_exists($function)) WP_CLI::error('Deploy the complete Beanstalk theme first: missing '.$function);
$backupFile = $args[1] ?? '';
if ($mode === 'rollback') {
 if (!$backupFile || !is_readable($backupFile)) WP_CLI::error('Provide the backup JSON path.');
 $backup = json_decode(file_get_contents($backupFile), true, 512, JSON_THROW_ON_ERROR);
 if (($backup['site'] ?? '') !== home_url()) WP_CLI::error('Backup belongs to a different site.');
 foreach ($backup['posts'] as $row) {
  $current = get_post_field('post_content',$row['ID']);
  if ($current !== $row['before'] && hash('sha256',$current) !== $row['after_sha256']) WP_CLI::error('Location '.$row['ID'].' changed since migration; rollback stopped before any writes.');
 }
 update_option('koala_beanstalk_locations_enabled',false);
 foreach ($backup['posts'] as $row) {
  $result=wp_update_post(wp_slash(['ID'=>$row['ID'],'post_content'=>$row['before']]),true);
  if(is_wp_error($result) || get_post_field('post_content',$row['ID'])!==$row['before']) WP_CLI::error('Rollback failed for '.$row['ID'].'; activation remains disabled.');
 }
 foreach($backup['options'] as $key=>$value) $value===null ? delete_option($key) : update_option($key,$value);
 WP_CLI::success('Previous content and settings restored. Imported media retained to avoid deleting shared/CDN images.');return;
}
$manifest=json_decode(file_get_contents(__DIR__.'/content-manifest.json'),true,512,JSON_THROW_ON_ERROR);
$media=json_decode(file_get_contents(__DIR__.'/media-manifest.json'),true,512,JSON_THROW_ON_ERROR);
$root=get_template_directory().'/beanstalk/assets/';$errors=[];
foreach($media as $asset){$file=$root.$asset['file'];if(!is_readable($file)||hash_file('sha256',$file)!==$asset['sha256'])$errors[]='Missing or changed asset '.$asset['file'];}
foreach($manifest['media'] as $reference)if(get_post_meta($reference['source_id'],'_wp_attached_file',true)!==$reference['file'])$errors[]='Existing Bricks media ID '.$reference['source_id'].' must match '.$reference['file'];
if(!class_exists('GFAPI'))WP_CLI::error('Gravity Forms is required.');
foreach($manifest['forms'] as $expected){
 $form=GFAPI::get_form($expected['id']);
 if(!$form||$form['title']!==$expected['title']){$errors[]='Form identity mismatch '.$expected['id'];continue;}
 $fields=array_map(static fn($f)=>['id'=>$f->id,'type'=>$f->type,'label'=>$f->label],$form['fields']);
 if($fields!==$expected['fields'])$errors[]='Form fields mismatch '.$expected['id'];
}
$posts=get_posts(['post_type'=>'location','post_status'=>['publish','private','draft','pending','future'],'posts_per_page'=>-1,'orderby'=>'ID','order'=>'ASC']);
if(!$posts)$errors[]='No location records found.';
$changes=[];
foreach($posts as $post){
 $content=$post->post_content;
 if(strpos($content,'"name":"koala-location-hero"')===false){
  // Fresh per-location ACF-backed hero. Template fallback renders the remaining sections.
  // Original content and Bricks metadata remain recoverable; never copy pilot copy to other locations.
  $content=koala_beanstalk_location_hero_content($post->ID);
  $content.="\n<!-- wp:koala/location-partners /-->\n<!-- wp:koala/location-services-section /-->\n<!-- wp:koala/location-overview /-->\n";
  $content.=koala_beanstalk_location_quote_banner_content();
  foreach(['benefits','energy','expect'] as $name)$content.=koala_beanstalk_location_feature_content($name);
  $content.=koala_beanstalk_location_financing_content();
  foreach(['reviews','communities','nap','offers','education','final-cta'] as $name)$content.=koala_beanstalk_location_community_content($name);
 }
 $content=str_replace(['https://koalainsulation.local','http://koalainsulation.local','https:\/\/koalainsulation.local','http:\/\/koalainsulation.local'],'',$content);
 $changes[]=['ID'=>$post->ID,'slug'=>$post->post_name,'before'=>$post->post_content,'after'=>$content,'after_sha256'=>hash('sha256',$content)];
}
foreach($errors as $error)WP_CLI::warning($error);
if($errors)WP_CLI::error('Preflight failed; nothing changed.');
WP_CLI::log(count($posts).' locations; '.count($media).' compressed WebP images. Existing ACF, Bricks metadata, feeds, entries, and global parts are preserved.');
foreach($changes as $row)WP_CLI::log($row['slug'].': '.($row['before']===$row['after']?'already migrated':'will update'));
if($mode==='dry-run'){WP_CLI::success('Dry run passed; no database or media writes.');return;}
if(!$backupFile||!is_dir(dirname($backupFile))||file_exists($backupFile))WP_CLI::error('Pass a new backup JSON filename in an existing private directory outside the web root.');
$directory=realpath(dirname($backupFile));$webRoot=realpath(ABSPATH);
if($webRoot && ($directory===$webRoot || str_starts_with($directory,$webRoot.DIRECTORY_SEPARATOR)))WP_CLI::error('Backup must be outside the public web root.');
$options=[];foreach(['koala_beanstalk_locations_enabled','koala_beanstalk_location_media'] as $key)$options[$key]=get_option($key,null);
$backup=['site'=>home_url(),'created'=>gmdate('c'),'options'=>$options,'posts'=>$changes];
$handle=fopen($backupFile,'x');if(!$handle)WP_CLI::error('Cannot create backup.');
$payload=wp_json_encode($backup,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
$written=fwrite($handle,$payload);fclose($handle);chmod($backupFile,0600);
if($written!==strlen($payload))WP_CLI::error('Incomplete backup; no database changes made.');
WP_CLI::log('Rollback backup: '.$backupFile);
require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
$map=get_option('koala_beanstalk_location_media',[]);
foreach($media as $asset){
 $identity='beanstalk-location:'.$asset['file'];
 $ids=get_posts(['post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>2,'fields'=>'ids','meta_key'=>'_koala_beanstalk_asset','meta_value'=>$identity]);
 if(count($ids)>1)WP_CLI::error('Duplicate imported media identity '.$identity.'; activation was not changed.');
 if($ids){$id=(int)$ids[0];if(get_post_meta($id,'_koala_beanstalk_sha256',true)!==$asset['sha256'])WP_CLI::error('Imported asset differs: '.$identity);}
 else{
  $temp=wp_tempnam(basename($asset['file']));if(!$temp||!copy($root.$asset['file'],$temp))WP_CLI::error('Cannot prepare '.$asset['file']);
  $id=media_handle_sideload(['name'=>'koala-'.str_replace('/','-',$asset['file']),'tmp_name'=>$temp],0,$asset['title'],['post_title'=>$asset['title']]);
  if(is_wp_error($id)){if(file_exists($temp))unlink($temp);WP_CLI::error($id->get_error_message());}
  update_post_meta($id,'_koala_beanstalk_asset',$identity);update_post_meta($id,'_koala_beanstalk_sha256',$asset['sha256']);
  update_post_meta($id,'_wp_attachment_image_alt',$asset['alt']);
 }
 $map[$asset['file']]=$id;
 // Persist each successful import so interrupted runs can safely resume.
 update_option('koala_beanstalk_location_media',$map,false);
 WP_CLI::log('Media '.$id.': '.wp_get_attachment_url($id));
}
foreach($changes as $row){
 if($row['before']===$row['after'])continue;
 $result=wp_update_post(wp_slash(['ID'=>$row['ID'],'post_content'=>$row['after']]),true);
 if(is_wp_error($result)||get_post_field('post_content',$row['ID'])!==$row['after'])WP_CLI::error('Saved content verification failed for '.$row['slug'].'; restore using the backup.');
}
update_option('koala_beanstalk_locations_enabled',true,false);
if(!get_option('koala_beanstalk_locations_enabled'))WP_CLI::error('Activation setting did not persist.');
WP_CLI::success('All locations migrated; normal staging URLs now use Beanstalk. Purge caches and verify Cloudflare delivery and frontend QA before client review.');
