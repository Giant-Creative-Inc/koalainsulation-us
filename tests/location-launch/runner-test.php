<?php
// Focused executable migration regressions; no WordPress or database needed.
$library = dirname(__DIR__, 2) . '/deployment/location-page-launch/migration.php';
if (!is_file($library)) { fwrite(STDERR, "FAIL: production migration library does not exist\n"); exit(1); }
require $library;
$checks = 0;
function check($ok, $message) { global $checks; ++$checks; if (!$ok) throw new RuntimeException($message); }
function rejects($callback, $message) { try { $callback(); } catch (RuntimeException $e) { check(str_contains($e->getMessage(), $message), $e->getMessage()); return; } throw new RuntimeException('Expected rejection: '.$message); }
check(Koala_Location_Migration::site_allowed('https://koalainsulation.com', 'production'), 'US production allowed');
check(!Koala_Location_Migration::site_allowed('https://koalainsulation.com/ca', 'production'), 'Canada rejected');
check(!Koala_Location_Migration::site_allowed('https://evil.test', 'production'), 'Unknown site rejected');
check(!Koala_Location_Migration::site_allowed('https://koalainsulation.com', 'staging'), 'Mismatched environment rejected');
rejects(fn()=>Koala_Location_Migration::validate_slugs(['savannah']), 'excluded');
rejects(fn()=>Koala_Location_Migration::validate_slugs(['testing']), 'excluded');
rejects(fn()=>Koala_Location_Migration::validate_slugs(['boise','boise']), 'duplicate');
rejects(fn()=>Koala_Location_Migration::validate_slugs([]), 'empty');
check(Koala_Location_Migration::validate_slugs(['boise','dallas']) === ['boise','dallas'], 'Explicit location scope');
$dir = sys_get_temp_dir().'/koala-migration-test-'.bin2hex(random_bytes(4)); mkdir($dir,0700);
Koala_Location_Migration::write_new($dir.'/plan.json', ['version'=>1]);
check(json_decode(file_get_contents($dir.'/plan.json'),true)['version']===1,'Plan readback');
rejects(fn()=>Koala_Location_Migration::write_new($dir.'/plan.json',[]),'exists');
rejects(fn()=>Koala_Location_Migration::private_path(__DIR__.'/public/test.json', __DIR__.'/public'),'outside');
rejects(fn()=>Koala_Location_Migration::assert_snapshot(['post'=>1], ['post'=>2]),'drift');
Koala_Location_Migration::assert_snapshot(['post'=>1], ['post'=>1]);
check(true,'Identical snapshot accepted');
unlink($dir.'/plan.json');rmdir($dir);
echo "PASS: $checks migration safeguards\n";
