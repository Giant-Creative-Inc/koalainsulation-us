<?php
/** Run with php tests/city-import/csv-test.php. No WordPress writes. */
class WP_Error {
 public function __construct(public string $code, public string $message) {}
 public function get_error_message() { return $this->message; }
}
function __( $s, $domain = '' ) { return $s; }
function is_wp_error($v) { return $v instanceof WP_Error; }
function sanitize_title($v) { return strtolower(preg_replace('/\s+/','-',trim($v))); }
function sanitize_text_field($v) { return strip_tags($v); }
function wp_json_encode($v) { return json_encode($v); }
function get_posts($args) { return $args['post_type']==='location' ? [(object)['ID'=>5,'post_name'=>'southeast-denver']] : ($args['name']==='chesterfield' ? ($GLOBALS['legacy']??[]) : $GLOBALS['matches']); }
function wp_get_upload_dir() { return ['baseurl'=>'https://koalainsulation.local/wp-content/uploads']; }
function has_term($term,$tax,$id) { return $GLOBALS['city']; }
function current_user_can(...$args) { return $GLOBALS['allowed']; }
function attachment_url_to_postid($url) { return str_contains($url,'known.jpg') ? 50 : 0; }
function wp_attachment_is_image($id) { return $id === 50; }
function get_post_meta(...$args) { return ($args[1]??'')==='rl_related_location' ? [4931] : 'Existing alt'; }
require __DIR__ . '/../../wp-content/plugins/beanstalk-content-engine/src/CityPageCsv.php';
use GiantCreative\BeanstalkContentEngine\CityPageCsv;
function check($condition,$message) { if (!$condition) { throw new RuntimeException($message); } }
$GLOBALS['matches']=[(object)['ID'=>10,'post_name'=>'ho-ho-kus-nj']];
$GLOBALS['city']=true; $GLOBALS['allowed']=true;
$row=['title'=>'Ho-Ho-Kus','slug'=>'ho-ho-kus-nj','related_location_id'=>'80587','state_name'=>'New Jersey','state_abbreviation'=>'NJ','commercial_services_image_1'=>'https://example.com/known.jpg'];
$result=CityPageCsv::normalize($row);
check(!is_wp_error($result),'valid template row');
check($result['post_id']===10 && $result['service_area_name']==='Ho-Ho-Kus','slug and title mapping');
check($result['service_type']==='Insulation services','schema default');
check(json_decode($result['commercial_services_image_1'],true)===['id'=>50,'alt'=>'Existing alt'],'URL maps to attachment');
check($result['testimonials_fallback_link_label']==='Read more reviews','missing required field default');
$GLOBALS['matches'][]=(object)['ID'=>11,'post_name'=>'ho-ho-kus-nj'];
check(is_wp_error(CityPageCsv::normalize($row)),'ambiguous slug rejected');
$GLOBALS['matches']=[];
$GLOBALS['legacy']=[(object)['ID'=>69221,'post_name'=>'chesterfield']];
$legacyRow=$row; $legacyRow['slug']='chesterfield-mo'; $legacyRow['title']='Chesterfield'; $legacyRow['state_abbreviation']='MO'; $legacyRow['related_location_id']='4931';
$resolved=CityPageCsv::normalize($legacyRow);
check(!is_wp_error($resolved) && $resolved['post_id']===69221 && $resolved['slug']==='chesterfield','state-suffixed CSV must update existing unsuffixed city in same location');
$GLOBALS['matches']=[(object)['ID'=>88322,'post_name'=>'chesterfield-mo']];
check(is_wp_error(CityPageCsv::normalize($legacyRow)),'two city URLs for same location must block import');
$GLOBALS['matches']=[];
$legacyRow['related_location_id']='999';
$other=CityPageCsv::normalize($legacyRow);
check(!is_wp_error($other) && $other['_import_action']==='create','legacy city from another franchise must not be matched');
$GLOBALS['legacy']=[];

$spaced=$row; $spaced['slug']='denham springs-la';
$normalized=CityPageCsv::normalize($spaced);
check(!is_wp_error($normalized) && $normalized['slug']==='denham-springs-la','spaces normalized before creating or matching pages');
$new=CityPageCsv::normalize($row);
check(!is_wp_error($new) && $new['post_id']===0 && $new['_import_action']==='create','missing page prepared for draft creation');
$GLOBALS['matches']=[(object)['ID'=>10,'post_name'=>'ho-ho-kus-nj']];
$GLOBALS['city']=false;
check(is_wp_error(CityPageCsv::normalize($row)),'non-city resource rejected');
$GLOBALS['city']=true; $GLOBALS['allowed']=false;
check(is_wp_error(CityPageCsv::normalize($row)),'edit permission enforced');
$GLOBALS['allowed']=true;
$row['related_location_id']='/southeast-denver';
$result=CityPageCsv::normalize($row);
check(!is_wp_error($result) && $result['related_location_id']===5,'franchise slug resolved to published location ID');
$row['related_location_id']='80587';
$row['commercial_services_image_1']='https://example.com/missing.jpg';
check(is_wp_error(CityPageCsv::normalize($row)),'unknown image rejected');
$row['commercial_services_image_1']=''; $row['related_location_id']='80587junk';
check(is_wp_error(CityPageCsv::normalize($row)),'invalid relation rejected');
$path=tempnam(sys_get_temp_dir(),'city');
file_put_contents($path,"\xEF\xBB\xBFtitle,slug,related_location_id,state_abbreviation,state_name\nCity,city-nj,10,NJ,New Jersey\n");
check(count(CityPageCsv::read($path))===1,'BOM CSV read');
file_put_contents($path,"title,title\nA,B\n");
check(is_wp_error(CityPageCsv::read($path)),'duplicate headers rejected');
file_put_contents($path,"title,slug,related_location_id,state_abbreviation,state_name\nA,b,1,NJ\n");
check(is_wp_error(CityPageCsv::read($path)),'malformed row rejected');
file_put_contents($path,"title,slug,related_location_id,state_abbreviation,state_name,hero_heading\n,,,,,Template copy\nTown,town-nj,5,NJ,New Jersey,Hello\n");
$parsed=CityPageCsv::read($path);
check(count($parsed)===1 && $parsed[0]['__csv_row']===3,'blank identity template rows skipped and row numbers retained');
unlink($path);
echo "City CSV tests passed\n";
