<?php
namespace GiantCreative\BeanstalkContentEngine {class CityPageEditor{} class CityPageUpdater{}}
namespace {
function is_user_logged_in(){return true;}
function get_current_user_id(){return $GLOBALS['user']??1;}
function current_user_can(...$args){return true;}
function sanitize_key($v){return $v;}
function wp_unslash($v){return $v;}
function get_transient($key){return $key==='beanstalk_city_preview_1_token'?$GLOBALS['snapshot']:false;}
function nocache_headers(){}
function add_filter(...$args){}
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageEditorPreview.php';
$service=new GiantCreative\BeanstalkContentEngine\CityPageEditorPreview(new GiantCreative\BeanstalkContentEngine\CityPageEditor,new GiantCreative\BeanstalkContentEngine\CityPageUpdater);
$GLOBALS['snapshot']=['post_id'=>10,'title'=>'Preview City','content'=>'Preview blocks','meta'=>['rl_related_location'=>['4931']]];
$_GET=['beanstalk_city_preview'=>'token'];
$service->load_snapshot();
$original=(object)['ID'=>10,'post_title'=>'Live City','post_content'=>'Live blocks'];
$preview=$service->post($original);
if($preview->post_content!=='Preview blocks'||$preview->post_title!=='Preview City'||$original->post_content!=='Live blocks')throw new RuntimeException('Preview must replace request content without mutating the live post object.');
if($service->metadata(null,10,'rl_related_location',true)!==[['4931']])throw new RuntimeException('Preview metadata must use the selected location and match WordPress metadata filter semantics.');
if($service->metadata(null,99,'rl_related_location',true)!==null)throw new RuntimeException('Other posts must remain unchanged.');
$GLOBALS['user']=2;
$other=new GiantCreative\BeanstalkContentEngine\CityPageEditorPreview(new GiantCreative\BeanstalkContentEngine\CityPageEditor,new GiantCreative\BeanstalkContentEngine\CityPageUpdater);
$other->load_snapshot();
if($other->post($original)!==$original)throw new RuntimeException('Another user cannot access a saved preview.');
echo "City editor preview isolation tests passed\n";
}
