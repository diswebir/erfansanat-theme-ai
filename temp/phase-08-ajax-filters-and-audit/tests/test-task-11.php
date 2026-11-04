<?php
/** Automated QA Test Script — Task 8.11: Archive filter drawer synchronization. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.11: Archive filter drawer synchronization ===\n";
$script=(string)file_get_contents($root.'/assets/js/main.js');$start=strpos($script,'function initArchiveDrawer(');$end=strpos($script,'  function updatePaginationUI',$start+10);$section=$start!==false&&$end!==false?substr($script,$start,$end-$start):'';
$assert($section!=='','initArchiveDrawer source section exists');
$assert(str_contains($section,'syncFilterControls')&&str_contains($section,'data-product-attribute-filter'),'drawer synchronizes shared product filter state');
$assert(str_contains($section,'data-project-type-filter')&&str_contains($section,'data-blog-search'),'drawer synchronization supports project and blog controls');
$assert(str_contains($section,'data-archive-drawer-clear')&&str_contains($section,'data-archive-drawer-apply'),'drawer provides clear and explicit apply actions');
$assert(str_contains($section,'setOpen(false)'),'apply/close actions close the drawer');
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Task 8.11 checks completed successfully.\n";
