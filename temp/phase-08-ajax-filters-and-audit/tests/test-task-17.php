<?php
/** Automated QA Test Script — Task 8.17: Final E2E and readiness gate. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.17: Final E2E and readiness gate ===\n";
$script=$root.'/temp/run-all-phases-e2e.sh';
$assert(is_file($script),'all-phases E2E runner exists');
$bashPath = is_executable('/usr/bin/bash') ? '/usr/bin/bash' : (is_executable('/bin/bash') ? '/bin/bash' : null);
if ($bashPath !== null) {
    $lint=[];$status=0;exec(escapeshellarg($bashPath).' -n '.escapeshellarg($script).' 2>&1',$lint,$status);
    $assert($status===0,'E2E runner passes bash syntax check');
} else {
    echo "  [SKIP] Bash syntax check is delegated to the host runner; this PHP runtime has no Bash binary.\n";
    $assert(is_file($script),'E2E runner is available for the host syntax check');
}
$source=(string)file_get_contents($script);
foreach(['run-phase-01-tests.sh','run-phase-02-tests.sh','run-phase-03-tests.sh','run-phase-04-tests.sh','run-phase-05-tests.sh','run-phase-06-tests.sh','run-phase-07-tests.sh','run-phase-08-tests.sh','node --check','php -l','BLOCKED'] as $needle){$assert(str_contains($source,$needle),'E2E runner includes '.$needle);}
$assert(str_contains($source,'unpassed_tasks')&&str_contains($source,'[[ "$task_name" == "task-17" ]] && continue')&&str_contains($source,"*'[PASSED]'*"),'final gate detects every non-passed Phase 8 task while excluding Task 8.17 from its own dependency scan');
$taskFiles=glob($root.'/temp/phase-08-ajax-filters-and-audit/tasks/task-*.md')?:[];$unpassedTasks=[];foreach($taskFiles as $taskFile){if(basename($taskFile)==='task-17.md')continue;$statusLine='';foreach(file($taskFile,FILE_IGNORE_NEW_LINES)?:[] as $line){if(str_contains($line,'**وضعیت:**')){$statusLine=$line;break;}}if(!str_contains($statusLine,'[PASSED]'))$unpassedTasks[]=basename($taskFile,'.md');}
$assert($taskFiles!==[]&&$unpassedTasks===[],'every Phase 8 prerequisite is explicitly PASSED'.($unpassedTasks!==[]?': '.implode(', ',$unpassedTasks):''));
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Phase 8 final readiness gate passed; every prerequisite is explicitly PASSED.\n";
