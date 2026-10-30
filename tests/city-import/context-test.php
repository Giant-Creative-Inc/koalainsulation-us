<?php
/** Unchanged ACF relationships must succeed when stored values verify. */
class WP_Error { public function __construct(...$args){} }
function __($s,...$args){return $s;}
function is_wp_error($v){return $v instanceof WP_Error;}
function wp_set_object_terms(...$args){return [7];}
function wp_get_object_terms(...$args){return [7];}
function update_field(...$args){return false;}
function get_post_meta($id,$key,...$args){return $key==='rl_related_location' ? $GLOBALS['relationship'] : 'field_670cb342e69f1';}
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageContext.php';
$service=new GiantCreative\BeanstalkContentEngine\CityPageContext();
$GLOBALS['relationship']=['80587'];
if($service->apply_existing(86149,['areas_served_term_id'=>7,'related_location_id'=>80587])!==true)throw new RuntimeException('Unchanged stored relationship must succeed despite ACF returning false.');
$GLOBALS['relationship']=['4931'];
if(!is_wp_error($service->apply_existing(86149,['areas_served_term_id'=>7,'related_location_id'=>80587])))throw new RuntimeException('Incorrect stored relationship must still fail.');
echo "City context unchanged-relationship regression passed\n";
