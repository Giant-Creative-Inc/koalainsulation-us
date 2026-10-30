<?php
namespace GiantCreative\BeanstalkContentEngine {
 class PatternRegistry {public function manifest($id){return ['fields'=>['hero_heading'=>['type'=>'text','required'=>true]],'_pattern_path'=>'fixture'];}}
 class ContentValidator {public function validate($content,$manifest){return empty($content['hero_heading'])?new \WP_Error('missing','Heading required'):$content;}}
 class ContentBuilder {public function build(...$args){return '<!-- wp:paragraph --><p>Content</p><!-- /wp:paragraph -->';}}
 class CityPageContext {public function prepare($pattern,$type,$context,...$args){return $context['related_location_id']===4931?[]:new \WP_Error('location','Invalid location');}}
 class CityPageUpdater {const TEMPLATE_VALUE='koala/city-page';const TEMPLATE_META='_beanstalk_template';public function update($row,$apply){if(empty($row['slug'])||$row['slug']!==$GLOBALS['post']->post_name)return new \WP_Error('slug','Slug mismatch');$GLOBALS['generated']=true;return ['action'=>'updated'];}}
}
namespace {
 class WP_Error {public function __construct(...$args){}public function get_error_message(){return "Validation failed";}}
 function is_wp_error($v){return $v instanceof WP_Error;}
 function wp_json_encode($v){return json_encode($v);}
 require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageEditor.php';
 use GiantCreative\BeanstalkContentEngine as BCE;
 $editor=new BCE\CityPageEditor(new BCE\PatternRegistry,new BCE\ContentValidator,new BCE\ContentBuilder,new BCE\CityPageContext,new BCE\CityPageUpdater);
 $fields=['related_location_id'=>4931,'state_name'=>'Missouri','state_abbreviation'=>'mo','service_type'=>'Insulation services','hero_heading'=>'Services in {{service_area_name}}'];
 $posted=[];foreach($fields as $key=>$value)$posted['field_beanstalk_city_'.$key]=$value;
 $row=$editor->prepare($posted,'Test City','test-city');
 if(is_wp_error($row)||$row['state_abbreviation']!=='MO'||$row['hero_heading']!==$fields['hero_heading'])throw new RuntimeException('Valid editor fields must prepare the same import row.');
 foreach(['related_location_id','state_name','state_abbreviation','hero_heading'] as $key){$bad=$posted;$bad['field_beanstalk_city_'.$key]='';if(!is_wp_error($editor->prepare($bad,'Test City','test-city')))throw new RuntimeException('Missing '.$key.' must fail before save.');}
 if(!is_wp_error($editor->prepare($posted,'','test-city')))throw new RuntimeException('Missing city title must fail.');
 echo "City editor validation tests passed\n";
}
