<?php
/** Render the real header without a WordPress database. */
define('ABSPATH',__DIR__);$location=true;$existing=[];
function koala_beanstalk_sidebar_preview(){global $location;return $location;}
function koala_beanstalk_get_navigation_data(){global $existing;return ['base'=>'https://koala.test/traverse-city','name'=>'Traverse City','state'=>'Michigan','phone'=>'','services'=>[],'resources'=>$existing];}
function wp_parse_args($args,$defaults){return array_merge($defaults,$args);}
function get_template_directory_uri(){return 'https://koala.test/theme';}
function home_url($path){return 'https://koala.test'.$path;}
function esc_url($v){return $v;}function esc_attr($v){return $v;}function esc_html($v){return $v;}
function render_header(){ob_start();include dirname(__DIR__,2).'/wp-content/themes/bricks/beanstalk/header.php';return ob_get_clean();}
$html=render_header();
foreach(['https://koala.test/traverse-city/faq','https://koalainsulationfranchise.com/'] as $url)if(!str_contains($html,'href="'.$url.'"'))throw new RuntimeException('Missing legacy destination '.$url);
$existing=[['title'=>'FAQ','url'=>'https://koala.test/traverse-city/faq/'],['title'=>'Franchise','url'=>'https://koalainsulationfranchise.com/']];$html=render_header();
if(substr_count($html,'>FAQ<')!==1 || substr_count($html,'>Franchise<')!==1)throw new RuntimeException('Duplicate Resources links');
$location=false;$existing=[];$html=render_header();if(str_contains($html,'/faq"') || str_contains($html,'koalainsulationfranchise.com'))throw new RuntimeException('Other page menus changed');
echo "PASS: location FAQ/Franchise restored, duplicates avoided, other native headers unchanged\n";
