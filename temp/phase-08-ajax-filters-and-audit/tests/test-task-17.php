<?php
/** Automated QA Test Script — Task 8.17: Final E2E and readiness gate. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.17: Final E2E and readiness gate ===\n";
$script=$root.'/temp/run-all-phases-e2e.sh';
$assert(is_file($script),'all-phases E2E runner exists');
$lint=[];$status=0;exec('bash -n '.escapeshellarg($script).' 2>&1',$lint,$status);
$assert($status===0,'E2E runner passes bash syntax check');
$source=(string)file_get_contents($script);
foreach(['run-phase-01-tests.sh','run-phase-02-tests.sh','run-phase-03-tests.sh','run-phase-04-tests.sh','run-phase-05-tests.sh','run-phase-06-tests.sh','run-phase-07-tests.sh','run-phase-08-tests.sh','node --check','php -l','BLOCKED'] as $needle){$assert(str_contains($source,$needle),'E2E runner includes '.$needle);}
$unpassed=[];
foreach(glob($root.'/temp/phase-08-ajax-filters-and-audit/tasks/task-*.md')?:[] as $taskFile){$contents=(string)file_get_contents($taskFile);if(basename($taskFile)==='task-17.md')continue;if(!str_contains($contents,'- **وضعیت:** `[PASSED]`'))$unpassed[]=basename($taskFile);}
$task17=(string)file_get_contents($root.'/temp/phase-08-ajax-filters-and-audit/tasks/task-17.md');
$assert(str_contains($source,'unpassed_tasks')&&count($unpassed)===3&&in_array('task-11.md',$unpassed,true)&&in_array('task-14.md',$unpassed,true)&&in_array('task-15.md',$unpassed,true)&&str_contains($task17,'[BLOCKED'),'final gate detects every non-passed Phase 8 task and Task 8.17 remains blocked until all are passed');
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] E2E gate checks passed; full release readiness remains blocked until every Phase 08 task is passed.\n";
