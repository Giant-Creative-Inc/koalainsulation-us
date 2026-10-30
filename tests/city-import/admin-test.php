<?php
namespace GiantCreative\BeanstalkContentEngine { class CityPageImportJob {} }
namespace {
class Response extends RuntimeException { public function __construct(public $data,public $status=200){parent::__construct();} }
class WP_Error {public function __construct(private $code,private $message){} public function get_error_message(){return $this->message;} }
function is_wp_error($v){return $v instanceof WP_Error;}
function current_user_can($cap){return $GLOBALS['admin'];}
function get_current_user_id(){return $GLOBALS['user'];}
function check_ajax_referer(...$args){if(!$GLOBALS['nonce'])throw new Response('nonce',403);}
function wp_send_json_error($data,$status=200){throw new Response($data,$status);}
function wp_send_json_success($data){throw new Response($data);}
function add_option($key,...$args){if(isset($GLOBALS['options'][$key]))return false;$GLOBALS['options'][$key]=true;return true;}
function delete_option($key){unset($GLOBALS['options'][$key]);}
function get_transient($key){return $GLOBALS['jobs'][$key]??false;}
function set_transient($key,$job,...$args){$GLOBALS['jobs'][$key]=$job;}
function sanitize_key($v){return $v;}
function sanitize_text_field($v){return $v;}
function wp_unslash($v){return $v;}
define('HOUR_IN_SECONDS',3600);
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageAdminImport.php';
$controller=new GiantCreative\BeanstalkContentEngine\CityPageAdminImport(new GiantCreative\BeanstalkContentEngine\CityPageImportJob());
function invoke($controller){try{$controller->request();}catch(Response $r){return $r;}throw new RuntimeException('No response');}
function check($condition,$message){if(!$condition)throw new RuntimeException($message);}
$GLOBALS['admin']=false;$GLOBALS['nonce']=true;$GLOBALS['user']=1;$GLOBALS['options']=[];
check(invoke($controller)->status===403 && !$GLOBALS['options'],'unauthorized requests rejected before locks');
$GLOBALS['admin']=true;$GLOBALS['nonce']=false;
check(invoke($controller)->status===403,'invalid nonce rejected');
$GLOBALS['nonce']=true;
$GLOBALS['options']['beanstalk_city_import_lock']=true;
check(invoke($controller)->status===409,'concurrent request rejected');
$GLOBALS['options']=[];
$_POST=['operation'=>'status','job'=>'wrong'];
$GLOBALS['jobs']['beanstalk_city_import_1']=['id'=>'expected','rows'=>[],'results'=>[],'cursor'=>0];
check(invoke($controller)->status===400,'wrong job rejected');
check(!$GLOBALS['options'],'lock released after error');
$_POST=['operation'=>'resume'];
check(invoke($controller)->data['job']==='expected','owner can resume');
$GLOBALS['user']=2;
check(invoke($controller)->status===400,'another administrator cannot read owner job');
$_POST=['operation'=>'upload'];
check(invoke($controller)->status===400,'missing uploaded file rejected');
echo "City admin security tests passed\n";
}
