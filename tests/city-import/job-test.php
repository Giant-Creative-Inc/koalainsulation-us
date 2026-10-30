<?php
namespace GiantCreative\BeanstalkContentEngine {
 class CityPageUpdater {
  public array $calls=[];
  public function create($row,$apply=false) { $this->calls[]=$apply; return ['post_id'=>20,'action'=>$apply?'created':'validated']; }
  public function update($row,$apply=false) { $this->calls[]=$apply; return ['post_id'=>$row['post_id'],'action'=>$apply?'updated':'validated']; }
 }
}
namespace {
require __DIR__.'/csv-test.php';
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/CityPageImportJob.php';
use GiantCreative\BeanstalkContentEngine\CityPageImportJob;
use GiantCreative\BeanstalkContentEngine\CityPageUpdater;
$GLOBALS['city']=true; $GLOBALS['allowed']=true;
$row=['title'=>'Town','slug'=>'ho-ho-kus-nj','related_location_id'=>'5','state_name'=>'New Jersey','state_abbreviation'=>'NJ'];
$updater=new CityPageUpdater(); $service=new CityPageImportJob($updater);
$job=$service->create([$row,$row]);
$service->preview($job);
check($job['results'][0]['status']==='validated','preview validates');
check($job['results'][1]['status']==='error','duplicate target rejected');
check(!in_array(true,$updater->calls,true),'preview never writes pages');
$service->apply($job,[0,1]);
check($job['results'][0]['status']==='updated','valid selection applies');
check(count(array_filter($updater->calls))===1,'invalid selection skipped');
$service->apply($job,[0]);
check(count(array_filter($updater->calls))===1,'replay cannot apply completed row');
$job=$service->create([$row]);
$service->apply($job,[0]);
check(count(array_filter($updater->calls))===1,'unpreviewed row cannot apply');
$GLOBALS['matches']=[];
$job=$service->create([$row,$row]);
$service->preview($job);
check($job['results'][0]['status']==='validated' && str_contains($job['results'][0]['detail'],'create new draft'),'preview labels new drafts');
check($job['results'][1]['status']==='error','duplicate new slug rejected');
$service->apply($job,[0]);
check($job['results'][0]['status']==='created','missing page is created on apply');
echo "City import job tests passed\n";
}
