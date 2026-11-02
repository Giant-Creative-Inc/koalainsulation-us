<?php
// Standalone WordPress API doubles; never connects to a database.
define('ABSPATH', '/');
function add_action(...$args) {}
function add_filter(...$args) {}
function get_the_ID() { return 4923; }
function get_post_meta($id, $key, $single=true) { return ['show_location_areas_serviced'=>1,'local_cities'=>[1,2,3]][$key] ?? ''; }
function get_post_status($id) { return 'publish'; }
function get_the_title($id) { return [1=>'PHOENIX',2=>'Gilbert',3=>'COOKEVILLE'][$id]; }
function esc_html($s) { return htmlspecialchars($s, ENT_QUOTES); }
function wpautop($s) { return $s; }
function wp_kses_post($s) { return $s; }
function koala_beanstalk_render_location_hero_quote($args) { return ''; }
require dirname(__DIR__,2) . '/wp-content/themes/bricks/beanstalk/location-community.php';
$cases=[['PHOENIX','Phoenix'],['SAN TAN VALLEY','San Tan Valley'],['COOKEVILLE','Cookeville'],['DALLAS, NC','Dallas, NC'],['PATRICK AFB','Patrick AFB'],['MCFARLAND','McFarland'],["O'FALLON","O'Fallon"],['ADA','Ada'],['McKinney','McKinney'],['Lee’s Summit','Lee’s Summit'],['Roanoke, VA','Roanoke, VA']];
foreach($cases as [$input,$expected]) {
    $actual=koala_beanstalk_location_city_label($input);
    if($actual!==$expected)throw new RuntimeException("$input: expected $expected, got $actual");
}
$html=koala_beanstalk_render_location_community(['section'=>'communities']);
if(!str_contains($html,'<li>Phoenix</li><li>Gilbert</li><li>Cookeville</li>'))throw new RuntimeException('Rendered city order/casing failed');
if(isset($argv[1])) {
    $rows=json_decode(file_get_contents($argv[1]),true,512,JSON_THROW_ON_ERROR);$changes=[];
    foreach($rows as $row)foreach($row['cities'] as $city) {
        $before=$city['title'];$after=koala_beanstalk_location_city_label($before);
        if(preg_match('/\p{Ll}/u',$before)&&$after!==$before)throw new RuntimeException('Mixed case altered: '.$before);
        if($before!==$after&&$row['shown'])$changes[]=['location'=>$row['slug'],'city_id'=>$city['id'],'before'=>$before,'after'=>$after];
    }
    echo json_encode($changes,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),"\n";
} else echo "City label cases, abbreviations, mixed case and rendering order passed.\n";
