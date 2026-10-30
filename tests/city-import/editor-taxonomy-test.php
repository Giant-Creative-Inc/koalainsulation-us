<?php
require __DIR__.'/editor-validation-test.php';
function get_post($id){return $GLOBALS['post'];}
function get_post_meta(...$args){return '';}
function get_term_by(...$args){return (object)['term_id'=>13];}
function wp_get_object_terms(...$args){return [9];}
$GLOBALS['post']=(object)['ID'=>101,'post_type'=>'resources-landing-pa','post_status'=>'auto-draft'];
$_GET=['beanstalk_city'=>'1'];
$args=$editor->default_terms(['taxonomy'=>'resources-page-type'],101);
if(($args['selected_cats']??[])!==[9,13])throw new RuntimeException('New City Page screen must preselect areas-served while preserving assigned terms.');
$args=$editor->default_terms(['taxonomy'=>'resources-page-type','selected_cats'=>[28]],101);
if($args['selected_cats']!==[28,13])throw new RuntimeException('Explicit selections must be preserved.');
$args=['taxonomy'=>'category'];
if($editor->default_terms($args,101)!==$args)throw new RuntimeException('Other taxonomies must stay untouched.');
$_GET=[];
$args=['taxonomy'=>'resources-page-type'];
if($editor->default_terms($args,101)!==$args)throw new RuntimeException('Ordinary resource creation must not preselect a city taxonomy.');
echo "City editor taxonomy defaults tests passed\n";
