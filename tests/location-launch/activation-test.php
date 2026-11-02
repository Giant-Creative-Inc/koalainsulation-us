<?php
// Exercise deployed PHP directly without a WordPress database.
define('ABSPATH',__DIR__);$home='https://koalainsulation.com';$environment='production';$options=[];$id=42;$singular=true;$builder=false;$checks=0;
function add_action(...$args){}function add_filter(...$args){}
function home_url(){global $home;return $home;}
function wp_parse_url($url,$component){return parse_url($url,$component);}
function wp_get_environment_type(){global $environment;return $environment;}
function get_option($key,$default=false){global $options;return $options[$key]??$default;}
function get_queried_object_id(){global $id;return $id;}
function is_singular($type){global $singular;return $singular;}
function bricks_is_builder(){global $builder;return $builder;}
function gravity_form(...$args){return '';}
function get_post($requested){return (object)['ID'=>$requested,'post_type'=>'location','post_status'=>'publish'];}
function check($ok,$message){global $checks;++$checks;if(!$ok)throw new RuntimeException($message);}
require dirname(__DIR__,2).'/wp-content/themes/bricks/beanstalk/location-sidebar.php';
require dirname(__DIR__,2).'/wp-content/themes/bricks/beanstalk/location-estimate-demand.php';
check(!koala_beanstalk_sidebar_preview(),'Deploy stays disabled');$_GET['koala_beanstalk_sidebar']='1';
check(!koala_beanstalk_sidebar_preview(),'Query cannot activate');
$options['koala_beanstalk_locations_enabled']=true;check(!koala_beanstalk_sidebar_preview(),'Global option requires ID allowlist');
$options['koala_beanstalk_location_ids']=[42];unset($_GET['koala_beanstalk_sidebar']);check(koala_beanstalk_sidebar_preview(),'Migrated location activates');
$id=43;check(!koala_beanstalk_sidebar_preview(),'Unmigrated location stays legacy');$id=42;$builder=true;check(!koala_beanstalk_sidebar_preview(),'Builder preserved');$builder=false;
$_GET['bricks']='run';check(!koala_beanstalk_sidebar_preview(),'Builder query preserved');unset($_GET['bricks']);$singular=false;check(!koala_beanstalk_sidebar_preview(),'Other pages preserved');$singular=true;
$id=0;check(koala_beanstalk_estimate_permission(['id'=>42]),'REST route uses requested ID');check(!koala_beanstalk_estimate_permission(['id'=>43]),'REST excludes unlisted ID');
$home='https://koalainsulation.com/ca';check(!koala_beanstalk_location_environment_enabled(42),'Canada cannot accidentally activate');
$home='https://evil.test';check(!koala_beanstalk_location_environment_enabled(42),'Unknown production host blocked');
$home='https://koalainsulation.local';$environment='local';check(koala_beanstalk_sidebar_preview(),'Local preserved');
$home='https://staging.koalainsulation.com';$environment='staging';$options=[];check(!koala_beanstalk_sidebar_preview(),'Staging opt-in preserved');$_GET['koala_beanstalk_sidebar']='1';check(koala_beanstalk_sidebar_preview(),'Staging query preview preserved');
echo "PASS: $checks deployed activation/REST checks\n";
