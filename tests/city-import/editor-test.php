<?php
class WP_Error {public function __construct(...$args){}}
function wp_strip_all_tags($s){return strip_tags($s);}
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageEditor.php';
use GiantCreative\BeanstalkContentEngine\CityPageEditor;
$fields=['hero_heading'=>['type'=>'text','target'=>['name'=>'hero','block'=>'core/heading','attribute'=>'content']], 'why_koala_heading'=>['type'=>'rich-text','target'=>['name'=>'why','block'=>'core/heading','attribute'=>'content']], 'faq_1_question'=>['type'=>'text','target'=>['name'=>'faq','block'=>'core/details','attribute'=>'summary']], 'why_koala_image'=>['type'=>'image','target'=>['name'=>'image','block'=>'core/image','attribute'=>'image']]];
$blocks=[['blockName'=>'core/group','innerBlocks'=>[
 ['blockName'=>'core/heading','attrs'=>['metadata'=>['name'=>'hero']],'innerHTML'=>'<h1 class="x">Hello &amp; welcome</h1>'],
 ['blockName'=>'core/heading','attrs'=>['metadata'=>['name'=>'why']],'innerHTML'=>'<h2>Why <strong>Koala?</strong></h2>'],
 ['blockName'=>'core/details','attrs'=>['metadata'=>['name'=>'faq']],'innerHTML'=>'<details><summary>Question?</summary><p>Answer</p></details>'],
 ['blockName'=>'core/image','attrs'=>['metadata'=>['name'=>'image'],'id'=>42,'alt'=>'Image description'],'innerHTML'=>'<figure><img /></figure>']
]]];
$values=CityPageEditor::extract($blocks,$fields);
if($values['hero_heading']!=='Hello & welcome'||$values['why_koala_heading']!=='Why <strong>Koala?</strong>'||$values['faq_1_question']!=='Question?'||$values['why_koala_image']!==42||$values['why_koala_image_alt']!=='Image description')throw new RuntimeException('Editor fields must round-trip existing block copy, formatting, FAQs and images.');
$values=CityPageEditor::extract([['blockName'=>'core/heading','attrs'=>['metadata'=>['name'=>'other']],'innerHTML'=>'<h1>Unrelated</h1>']],$fields);
if($values)throw new RuntimeException('Unrelated blocks must not populate city fields.');
echo "City editor extraction tests passed\n";
