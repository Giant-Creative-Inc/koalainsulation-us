<?php
// In-memory WordPress adapter: no real database, media upload, email or HTTP request.
$root=sys_get_temp_dir().'/koala-lifecycle-'.bin2hex(random_bytes(4));mkdir($root.'/public/wp-admin/includes',0700,true);
foreach(['file','media','image'] as $name) file_put_contents($root.'/public/wp-admin/includes/'.$name.'.php','<?php');
define('ABSPATH',$root.'/public/');define('OBJECT','OBJECT');define('ARRAY_A','ARRAY_A');
require dirname(__DIR__,2).'/deployment/location-page-launch/migration.php';
$package=dirname(__DIR__,2).'/deployment/location-page-launch';$theme=dirname(__DIR__,2).'/wp-content/themes/bricks';
$options=[];$posts=[];$meta=[];$writes=0;$failPost=0;$delivery=true;$checks=0;$importFails=false;$imports=0;$feeds=[];$failOption='';
foreach([101=>'boise',102=>'dallas'] as $id=>$slug){$posts[$id]=(object)['ID'=>$id,'post_name'=>$slug,'post_status'=>'publish','post_type'=>'location','post_title'=>ucfirst($slug),'post_modified_gmt'=>'before','post_content'=>'Legacy '.$slug];$meta[$id]=['seo'=>['Original title'],'routing'=>['original-owner']];}
$dependencies=json_decode(file_get_contents($package.'/dependencies.json'),true);$manifest=json_decode(file_get_contents($package.'/media-manifest.json'),true);
foreach($dependencies['media'] as $a)$meta[$a['source_id']]['_wp_attached_file']=[$a['file']];
$assets=[];foreach($manifest as $i=>$a){$id=10000+$i;$assets[$a['file']]=$id;$meta[$id]=['_koala_beanstalk_asset'=>['beanstalk-location:'.$a['file']],'_koala_beanstalk_sha256'=>[$a['sha256']]];}
class WP_Error extends Exception { function get_error_message(){return $this->getMessage();} }
class WP_CLI { static function log($s){} }
class GFAPI { static function get_feeds(...$args){global $feeds;return $feeds;} static function get_form($id){global $dependencies;foreach($dependencies['forms'] as $f)if($f['id']===$id){$f['fields']=array_map(fn($x)=>(object)$x,$f['fields']);$f['is_active']=true;return $f;}return false;} }
class FakeDB {public $options='wp_options';function get_results($q,$type){return [['option_name'=>'kgi_quote_form_id','option_value'=>'12']];}}
$wpdb=new FakeDB();
function get_option($key,$default=false){global $options;return array_key_exists($key,$options)?$options[$key]:$default;}
function update_option($key,$value,$autoload=false){global $options,$writes,$failOption;++$writes;if($key===$failOption)return false;$options[$key]=$value;return true;}
function delete_option($key){global $options,$writes;++$writes;unset($options[$key]);return true;}
function home_url(){return 'https://koalainsulation.com';}
function wp_get_environment_type(){return 'production';}
function get_template_directory(){global $theme;return $theme;}
function get_post_meta($id,$key=null,$single=false){global $meta;return $key===null?($meta[$id]??[]):($single?($meta[$id][$key][0]??''):($meta[$id][$key]??[]));}
function get_posts($args){global $assets;return isset($args['meta_value']) && isset($assets[substr($args['meta_value'],strlen('beanstalk-location:'))])?[$assets[substr($args['meta_value'],strlen('beanstalk-location:'))]]:[];}
function get_page_by_path($slug,$output,$type){global $posts;foreach($posts as $p)if($p->post_name===$slug)return clone $p;return null;}
function get_post($id){global $posts;return isset($posts[$id])?clone $posts[$id]:null;}
function get_post_field($key,$id){global $posts;return $posts[$id]->$key;}
function wp_slash($a){return $a;}
function is_wp_error($r){return $r instanceof Exception;}
function wp_update_post($args,$errors){global $posts,$writes,$failPost;++$writes;if($args['ID']===$failPost)throw new RuntimeException('Simulated interrupted save');$posts[$args['ID']]->post_content=$args['post_content'];$posts[$args['ID']]->post_modified_gmt='after';return $args['ID'];}
function wp_tempnam($name){global $root;return tempnam($root,'media');}
function media_handle_sideload($file,$parent,$description,$post){global $importFails,$imports,$assets;++$imports;if($importFails){unlink($file['tmp_name']);return new WP_Error('Simulated import failure');}unlink($file['tmp_name']);return 30000+$imports;}
function update_post_meta($id,$key,$value){global $meta,$assets,$writes;++$writes;$meta[$id][$key]=[$value];if($key==='_koala_beanstalk_asset')$assets[substr($value,strlen('beanstalk-location:'))]=$id;return true;}
function wp_get_attachment_url($id){return 'https://images.example.test/'.$id.'.webp';}
function wp_remote_get($url,$args){global $delivery;return ['status'=>$delivery?200:404,'type'=>'image/webp'];}
function wp_remote_retrieve_header($r,$k){return $r['type'];}
function wp_remote_retrieve_response_code($r){return $r['status'];}
function koala_beanstalk_location_hero_content($id){return '<!-- wp:group {"metadata":{"name":"koala-location-hero"}} -->Location '.$id.'<!-- /wp:group --><img src="https://koalainsulation.local/wp-content/themes/bricks/beanstalk/assets/location-hero/phone.svg">';}
function koala_beanstalk_location_quote_banner_content(){return '<!-- quote -->';}
function koala_beanstalk_location_feature_content($name){return '<!-- '.$name.' -->';}
function koala_beanstalk_location_financing_content(){return '<!-- finance -->';}
function koala_beanstalk_location_community_content($name){return '<!-- '.$name.' -->';}
function check($ok,$message){global $checks;++$checks;if(!$ok)throw new RuntimeException($message);}
function rejects($callback,$needle){try{$callback();}catch(RuntimeException $e){check(str_contains($e->getMessage(),$needle),$e->getMessage());return;}throw new RuntimeException('Expected '.$needle);}
$originalMeta=$meta;$plan=Koala_Location_Migration::plan(['boise','dallas']);
check($writes===0,'Dry run must not write');check(!str_contains($plan['posts'][0]['after'],'koalainsulation.local'),'Pattern URLs become same-site relative');check($plan['posts'][0]['after']!==$plan['posts'][1]['after'],'Location-specific generation');
check($plan['options']['koala_beanstalk_locations_enabled']['exists']===false,'Absent option captured');
$path=$root.'/plan.json';Koala_Location_Migration::write_new($path,$plan);check(Koala_Location_Migration::load($path)===$plan,'Plan integrity readback');
$failOption='koala_beanstalk_locations_enabled';rejects(fn()=>Koala_Location_Migration::apply($plan,$root.'/option-failed.json'),'persist');check($posts[101]->post_content==='Legacy boise','Failed option write blocks content changes');$failOption='';$writes=0;
$feeds=[['id'=>8,'meta'=>['target'=>'changed']]];rejects(fn()=>Koala_Location_Migration::apply($plan,$root.'/feed-drift.json'),'drift');check($writes===0,'Feed drift rejected before writes');$feeds=[];
$posts[101]->post_content='Concurrent edit';rejects(fn()=>Koala_Location_Migration::apply($plan,$root.'/drift.json'),'drift');check($writes===0 && !file_exists($root.'/drift.json'),'Drift rejected before writes');$posts[101]->post_content='Legacy boise';
Koala_Location_Migration::apply($plan,$root.'/journal.json');
check(get_option('koala_beanstalk_locations_enabled')===false,'Apply does not activate');
check(get_option('koala_beanstalk_location_ids')===[101,102],'Explicit ID list');
check($meta===$originalMeta,'SEO/routing metadata preserved');
$prior=$writes;Koala_Location_Migration::verify($plan);check($prior===$writes,'Verify read-only');
$delivery=false;rejects(fn()=>Koala_Location_Migration::activate($plan),'Image delivery');check(!get_option('koala_beanstalk_locations_enabled'),'Image failure blocks activation');$delivery=true;
Koala_Location_Migration::activate($plan);check(get_option('koala_beanstalk_locations_enabled')===true,'Separate verified activation');
$posts[101]->post_content='Editorial change';rejects(fn()=>Koala_Location_Migration::rollback($plan),'drift');check(!get_option('koala_beanstalk_locations_enabled'),'Rollback drift still disables renderer');check($posts[101]->post_content==='Editorial change','Editorial change never overwritten');$posts[101]->post_content=$plan['posts'][0]['after'];
Koala_Location_Migration::rollback($plan);check($posts[101]->post_content==='Legacy boise' && $posts[102]->post_content==='Legacy dallas','Original content restored');check($options===[],'Originally absent options removed');check($meta===$originalMeta,'Unrelated meta retained');
$plan=Koala_Location_Migration::plan(['boise','dallas']);$failPost=102;
rejects(fn()=>Koala_Location_Migration::apply($plan,$root.'/interrupted.json'),'interrupted');check(!get_option('koala_beanstalk_locations_enabled'),'Interrupted apply stays disabled');$failPost=0;
Koala_Location_Migration::rollback($plan);check($posts[101]->post_content==='Legacy boise' && $posts[102]->post_content==='Legacy dallas','Interrupted apply reversible');
Koala_Location_Migration::rollback($plan);check($options===[],'Rollback safely repeatable');
// A rollback must keep unrelated media-map additions made after migration.
$plan=Koala_Location_Migration::plan(['boise','dallas']);Koala_Location_Migration::apply($plan,$root.'/map-journal.json');
$options['koala_beanstalk_location_media']['unrelated-design.webp']=99999;
Koala_Location_Migration::rollback($plan);
check(($options['koala_beanstalk_location_media']['unrelated-design.webp']??null)===99999,'Rollback preserves unrelated media mappings');
// Import failure must leave content recoverable and activation disabled.
$first=$manifest[0]['file'];unset($assets[$first]);$plan=Koala_Location_Migration::plan(['boise']);$importFails=true;
rejects(fn()=>Koala_Location_Migration::apply($plan,$root.'/import-failed.json'),'import failure');check(!get_option('koala_beanstalk_locations_enabled'),'Import failure stays disabled');
check($posts[101]->post_content==='Legacy boise','Import failure preserves original content');Koala_Location_Migration::rollback($plan);$importFails=false;
$plan=Koala_Location_Migration::plan(['boise']);Koala_Location_Migration::apply($plan,$root.'/import-success.json');Koala_Location_Migration::verify($plan);$importCount=$imports;
check($plan['media'][0]['existing_id']===0,'New design attachment imported');Koala_Location_Migration::rollback($plan);
$plan=Koala_Location_Migration::plan(['boise']);check($plan['media'][0]['existing_id']>0,'Rerun finds managed attachment');
Koala_Location_Migration::apply($plan,$root.'/import-rerun.json');check($imports===$importCount,'Rerun does not duplicate attachments');Koala_Location_Migration::rollback($plan);
$bad=$plan;$bad['site']='https://koalainsulation.com/ca';Koala_Location_Migration::write_new($root.'/wrong-site.json',$bad);rejects(fn()=>Koala_Location_Migration::load($root.'/wrong-site.json'),'different site');
$bad=$plan;$bad['posts'][0]['after']='Tampered';Koala_Location_Migration::write_new($root.'/tampered.json',$bad);rejects(fn()=>Koala_Location_Migration::load($root.'/tampered.json'),'integrity');
foreach(glob($root.'/*.json') as $p)unlink($p);foreach(glob($root.'/public/wp-admin/includes/*') as $p)unlink($p);rmdir($root.'/public/wp-admin/includes');rmdir($root.'/public/wp-admin');rmdir($root.'/public');rmdir($root);
echo "PASS: $checks in-memory migration lifecycle checks\n";
