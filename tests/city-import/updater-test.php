<?php
namespace GiantCreative\BeanstalkContentEngine {
 class PatternRegistry {public function manifest($id){return ['version'=>1,'fields'=>[],'structuredData'=>['profile'=>'koala/service-area','fields'=>[]]];}}
 class ContentValidator {public function validate(...$args){return empty($GLOBALS['invalid'])?[]:new \WP_Error;}}
 class ContentBuilder {public function build(...$args){return $GLOBALS['content'];}}
 class CityPageContext {public function prepare(...$args){return [];}public function apply_existing(...$args){return empty($GLOBALS['context_fail'])?true:new \WP_Error;}}
}
namespace {
class WP_Error {public function __construct(...$args){}}
function __($s,...$args){return $s;}
function is_wp_error($v){return $v instanceof WP_Error;}
function get_post($id){return $GLOBALS['post'];}
function current_user_can(...$args){return $args[0]==='publish_posts'?($GLOBALS['can_publish']??true):!empty($GLOBALS['can_create'] ?? true);}
function get_post_type_object($type){return (object)['cap'=>(object)['create_posts'=>'edit_posts','publish_posts'=>'publish_posts']];}
function get_posts($args){return !empty($GLOBALS['collision'])?[$GLOBALS['post']]:[];}
function sanitize_text_field($s){return strip_tags($s);}
function wp_insert_post($values,...$args){$GLOBALS['insert_count']=($GLOBALS['insert_count']??0)+1;$GLOBALS['post']=(object)array_merge(['ID'=>20,'post_content'=>''],$values);$GLOBALS['collision']=true;return 20;}
function wp_delete_post($id,$force){$GLOBALS['deleted']=$id;}
function wp_set_object_terms(...$args){}
function delete_post_meta(...$args){}
function sanitize_title($s){return $s;}
function metadata_exists(...$args){return false;}
function get_post_meta($id,$key,...$args){return $GLOBALS['meta'][$key]??'';}
function wp_get_object_terms(...$args){return [];}
function wp_update_post($values,...$args){if(isset($values['post_content']))$GLOBALS['post']->post_content=stripslashes($values['post_content']);if(isset($values['post_status'])){if(($GLOBALS['meta']['_beanstalk_template']??'')!=='koala/city-page')throw new RuntimeException('Publishing must happen after complete setup.');$GLOBALS['post']->post_status=$values['post_status'];}return 10;}
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
$seo_row=['post_id'=>10,'slug'=>'town','meta_title'=>'Insulation in Town | Koala','meta_description'=>'Local insulation services for Town.','canonical_url'=>'https://koalainsulation.com/location/town'];
$before_meta=$GLOBALS['meta'];
if(is_wp_error($service->update($seo_row,false))||$GLOBALS['meta']!==$before_meta)throw new RuntimeException('SEO preview must not write metadata.');
$result=$service->update($seo_row,true);
foreach(['meta_title'=>'_yoast_wpseo_title','meta_description'=>'_yoast_wpseo_metadesc','canonical_url'=>'_yoast_wpseo_canonical'] as $column=>$key){if(($GLOBALS['meta'][$key]??null)!==$seo_row[$column])throw new RuntimeException('SEO import must save '.$column.' to Yoast metadata.');}
$before_meta=$GLOBALS['meta'];
$service->update(['post_id'=>10,'slug'=>'town','meta_title'=>'','canonical_url'=>''],true);
if($GLOBALS['meta']!==$before_meta)throw new RuntimeException('Blank or omitted SEO cells must preserve existing values.');
foreach(['javascript:alert(1)','/relative-path','https://example.com/path#fragment','https://user:pass@example.com/path','invalid url'] as $bad){
 if(!is_wp_error($service->update(['post_id'=>10,'slug'=>'town','canonical_url'=>$bad],true)))throw new RuntimeException('Invalid canonical URL must fail before writes: '.$bad);
}
$service->update(['post_id'=>10,'slug'=>'town','meta_title'=>'','_seo_clear_empty'=>true],true);
if($GLOBALS['meta']['_yoast_wpseo_title']!=='')throw new RuntimeException('Explicit editor clearing must restore Yoast automatic defaults.');
$row=['title'=>'New City','slug'=>'town'];
$before=$GLOBALS['insert_count']??0;
$result=$service->create($row,false);
if(is_wp_error($result)||($GLOBALS['insert_count']??0)!==$before)throw new RuntimeException('Creation preview must never insert a page.');
$result=$service->create($row,true);
if(is_wp_error($result)||$result['action']!=='created'||$GLOBALS['post']->post_status!=='publish'||$GLOBALS['post']->post_content!==$GLOBALS['content']||($GLOBALS['meta']['_beanstalk_template']??'')!=='koala/city-page')throw new RuntimeException('New City Page must be a complete enabled published page.');
if(!is_wp_error($service->create($row,true))||$GLOBALS['insert_count']!==1)throw new RuntimeException('Repeated creation must reject an existing slug.');
$GLOBALS['collision']=false;$GLOBALS['invalid']=true;
if(!is_wp_error($service->create($row,true))||$GLOBALS['insert_count']!==1)throw new RuntimeException('Invalid content must not insert a draft.');
$GLOBALS['invalid']=false;$GLOBALS['can_create']=false;
if(!is_wp_error($service->create($row,true)))throw new RuntimeException('Create permission must be enforced.');
$GLOBALS['can_create']=true;$GLOBALS['can_publish']=false;
if(!is_wp_error($service->create($row,true)))throw new RuntimeException('Publish permission must be enforced before insertion.');
$GLOBALS['can_publish']=true;$GLOBALS['context_fail']=true;
if(!is_wp_error($service->create($row,true))||($GLOBALS['deleted']??0)!==20)throw new RuntimeException('Failed draft setup must remove the partial draft.');
echo "City updater content preservation and draft creation tests passed\n";
}
