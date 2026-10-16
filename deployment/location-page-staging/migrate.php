<?php
// Run with wp eval-file migrate.php [apply]. Defaults to a read-only preflight.
$manifest = json_decode(file_get_contents(__DIR__.'/content-manifest.json'), true, 512, JSON_THROW_ON_ERROR);
$apply = ($args[0] ?? '') === 'apply';
if (wp_get_environment_type() !== 'staging') WP_CLI::error('Target must have WP_ENVIRONMENT_TYPE=staging.');
$errors=[];
foreach($manifest['media'] as $media){
 if(get_post_meta($media['source_id'],'_wp_attached_file',true)!==$media['file'])$errors[]='Media identity mismatch: '.$media['source_id'].' expected '.$media['file'];
}
if(!class_exists('GFAPI'))WP_CLI::error('Gravity Forms is required.');
foreach($manifest['forms'] as $expected){
 $form=GFAPI::get_form($expected['id']);
 if(!$form || $form['title']!==$expected['title']){$errors[]='Form identity mismatch: '.$expected['id'];continue;}
 $actual=array_map(static fn($f)=>['id'=>$f->id,'type'=>$f->type,'label'=>$f->label],$form['fields']);
 if($actual!==$expected['fields'])$errors[]='Form field mismatch: '.$expected['id'];
}
$changes=[];
foreach($manifest['records'] as $row){
 // Transfer only the rebuilt pilot. Existing global parts and unrelated locations stay untouched.
 if($row['type']!=='location' || $row['slug']!=='traverse-city')continue;
 $target=get_page_by_path($row['slug'],OBJECT,$row['type']);
 if(!$target){$errors[]='Missing target location '.$row['slug'];continue;}
 $old=rtrim($manifest['source'],'/');$new='';
 $content=str_replace([$old,str_replace('/','\\/',$old)],[$new,str_replace('/','\\/',$new)],$row['content']);
 if(strpos($content,'koalainsulation.local')!==false)$errors[]='Unconverted Local URL in '.$row['slug'];
 $changes[]=['ID'=>$target->ID,'post_content'=>$content,'previous_content'=>$target->post_content];
 WP_CLI::log($row['slug'].': target ID '.$target->ID.', Local URLs converted: '.$row['local_urls']);
}
foreach($errors as $error)WP_CLI::warning($error);
if($errors)WP_CLI::error('Preflight failed; nothing changed. Reconcile target identities before applying.');
if(!$apply){WP_CLI::success('Preflight passed. No database changes. Pass apply to transfer pilot content.');return;}
$backup=tempnam(sys_get_temp_dir(),'koala-staging-content-').'.json';
if(file_put_contents($backup,wp_json_encode($changes,JSON_PRETTY_PRINT))===false)WP_CLI::error('Cannot write backup.');
WP_CLI::log('Previous content backup: '.$backup);
foreach($changes as $change){$result=wp_update_post(wp_slash(['ID'=>$change['ID'],'post_content'=>$change['post_content']]),true);if(is_wp_error($result))WP_CLI::error($result->get_error_message());if(get_post_field('post_content',$change['ID'])!==$change['post_content'])WP_CLI::error('Saved content verification failed.');}
WP_CLI::success('Pilot content transferred and verified. No ACF values, form feeds, entries, or global parts changed.');
