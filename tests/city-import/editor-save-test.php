<?php
require __DIR__.'/editor-validation-test.php';
function get_post_type($id){return 'resources-landing-pa';}
function current_user_can(...$args){return $GLOBALS['can_edit']??true;}
function wp_is_post_revision($id){return false;}
function wp_is_post_autosave($id){return $GLOBALS['autosave']??false;}
function wp_unslash($v){return $v;}
function get_post($id){return $GLOBALS['post'];}
function get_post_meta(...$args){return '';}
function get_current_user_id(){return 1;}
function set_transient($key,$error,...$args){$GLOBALS['error']=$error;}
function sanitize_title($title){return strtolower(str_replace(' ','-',$title));}
function wp_update_post($values,...$args){foreach($values as $key=>$value){if($key!=='ID')$GLOBALS['post']->$key=$value;}return $values['ID'];}
$GLOBALS['post']=(object)['ID'=>101,'post_type'=>'resources-landing-pa','post_status'=>'draft','post_title'=>'Editor Test City','post_name'=>''];
$_POST=['acf'=>$posted+['field_beanstalk_city_enabled'=>1]];
$editor->save(101);
if(empty($GLOBALS['generated'])||!empty($GLOBALS['error'])||$GLOBALS['post']->post_name!=='editor-test-city')throw new RuntimeException('First editor draft save must assign a slug before generating content.');
$GLOBALS['generated']=false;$GLOBALS['can_edit']=false;
$editor->save(101);
if($GLOBALS['generated'])throw new RuntimeException('Unauthorized saves cannot regenerate city content.');
$GLOBALS['can_edit']=true;$GLOBALS['autosave']=true;
$editor->save(101);
if($GLOBALS['generated'])throw new RuntimeException('Autosaves cannot regenerate city content.');
$GLOBALS['autosave']=false;$_POST=[];
$editor->save(101);
if($GLOBALS['generated'])throw new RuntimeException('Ordinary saves without explicit city fields must be ignored.');
echo "City editor first-save tests passed\n";
