<?php
namespace GiantCreative\BeanstalkContentEngine {
 class PatternRegistry {public function manifest($id){return ['version'=>1,'fields'=>[],'structuredData'=>['profile'=>'koala/service-area','fields'=>[]]];}}
 class ContentValidator {public function validate(...$args){return [];}}
 class ContentBuilder {public function build(...$args){return $GLOBALS['content'];}}
 class CityPageContext {public function prepare(...$args){return [];}public function apply_existing(...$args){return true;}}
}
namespace {
class WP_Error {public function __construct(...$args){}}
function __($s,...$args){return $s;}
function is_wp_error($v){return $v instanceof WP_Error;}
function get_post($id){return $GLOBALS['post'];}
function current_user_can(...$args){return true;}
function sanitize_title($s){return $s;}
function metadata_exists(...$args){return false;}
function get_post_meta($id,$key,...$args){return $GLOBALS['meta'][$key]??'';}
function wp_get_object_terms(...$args){return [];}
function wp_update_post($values,...$args){$GLOBALS['post']->post_content=stripslashes($values['post_content']);return 10;}
function wp_json_encode($value){return json_encode($value);}
function wp_slash($value){return is_string($value)?addslashes($value):$value;}
function update_post_meta($id,$key,$value){$GLOBALS['meta'][$key]=is_string($value)?stripslashes($value):$value;}
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageUpdater.php';
use GiantCreative\BeanstalkContentEngine as BCE;
$GLOBALS['post']=(object)['ID'=>10,'post_type'=>'resources-landing-pa','post_status'=>'publish','post_name'=>'town','post_content'=>'before'];
$GLOBALS['content']='<!-- wp:paragraph {"metadata":{"name":"city"},"content":"O\u2019Fallon"} --><p>A \\ B</p><!-- /wp:paragraph -->';
$service=new BCE\CityPageUpdater(new BCE\PatternRegistry,new BCE\ContentValidator,new BCE\ContentBuilder,new BCE\CityPageContext);
$result=$service->update(['post_id'=>10,'slug'=>'town'],true);
if(is_wp_error($result)||$GLOBALS['post']->post_content!==$GLOBALS['content'])throw new RuntimeException('WordPress must store generated block content without losing JSON escapes or backslashes.');
echo "City updater content preservation test passed\n";
}
