import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import test from 'node:test';

const root=process.env.KOALA_THEME_ROOT || path.resolve('wp-content/themes/bricks');
const source=fs.readFileSync(path.join(root,'assets/js/custom-service.js'),'utf8');
function helper(fetch,window={ajaxData:{ajax_url:'/wp-admin/admin-ajax.php'}}){
 const match=source.match(/function koalaFindNearbyLocations\(zipCode\)\s*\{[\s\S]*?\n\}/);
 assert.ok(match,'shared cached ZIP lookup helper must exist');
 const context=vm.createContext({window,fetch,URLSearchParams,console:{error(){}}});
 vm.runInContext(match[0],context);return context.koalaFindNearbyLocations;
}
test('nearby search uses one server lookup and maps the matched owned ZIP',async()=>{
 const calls=[];const find=helper(async(url,options)=>{calls.push({url,options});return {json:async()=>({status:'matched',locations:[{id:22,slug:'example',title:'Example',address:'Address',phone:'555',website:'/example',zipcode:'12345',matched_code:'12346',distance:4.5}]})};});
 const result=await find('12346');assert.equal(calls.length,1);assert.equal(calls[0].url,'/wp-admin/admin-ajax.php');
 assert.deepEqual([...calls[0].options.body],[['action','kgi_find_location'],['code','12346']]);
 assert.equal(result.locations[0].matchedZipcode[0],'12346');assert.equal(result.locations[0].distance,4.5);assert.equal(result.locations[0].locationId,22);assert.equal(result.message,'');
});
test('lookup preserves visitor-facing no-match and rate-limit messages',async()=>{
 for(const status of ['no_locations','invalid_input','rate_limited','lookup_failed']){
  const result=await helper(async()=>({json:async()=>({status,message:'Fixture message',locations:[]})}))('12346');
  assert.equal(result.status,status);assert.equal(result.message,'Fixture message');assert.equal(result.locations.length,0);
 }
});
test('transport failures resolve safely instead of leaving the UI waiting',async()=>{
 const result=await helper(async()=>{throw new Error('network');})('12346');assert.equal(result.status,'lookup_failed');assert.ok(result.message);assert.equal(result.locations.length,0);
});
test('localized koalaData endpoint works when ajaxData is absent',async()=>{
 let requested;await helper(async(url)=>{requested=url;return {json:async()=>({status:'no_locations',locations:[]})};},{koalaData:{ajax_url:'/fixture-endpoint'}})('12346');assert.equal(requested,'/fixture-endpoint');
});
test('all six fallback callers use the shared helper without legacy radius/distance actions',()=>{
 for(const file of ['custom-service.js','custom/all-pages.js','custom/location-page.js']){
  const code=fs.readFileSync(path.join(root,'assets/js',file),'utf8');
  assert.equal((code.match(/koalaFindNearbyLocations\(zipCode\)\.then/g)||[]).length,2,file);
  assert.doesNotMatch(code,/get_zip_codes_in_radius|get_zip_codes_distance_in_miles|api_key\s*:/,file);
 }
});
