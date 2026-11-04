<?php
/** Automated QA Test Script — Task 8.5: Product sale filter. */
declare(strict_types=1);
$root = dirname(__DIR__, 3); $failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void { echo $condition ? "  [PASS] {$label}\n" : "  [FAIL] {$label}\n"; if (!$condition) $failures[] = $label; };
require_once '/opt/wp-testbed/wp-load.php'; $GLOBALS['wp']->main();
echo "=== [TEST] Task 8.5: Product sale filter ===\n";
$class = 'ErfanSanat\\Theme\\Filters\\ProductSaleFilter';
$assert(is_file($root . '/inc/Filters/ProductSaleFilter.php'), 'ProductSaleFilter source exists');
$assert(class_exists($class), 'ProductSaleFilter autoloads');
$ids = [];
foreach ([['Online Sale QA','true'], ['Call Quote QA','false'], ['Default Online QA',null]] as [$title,$sale]) {
    $id = wp_insert_post(['post_type'=>'product','post_status'=>'publish','post_title'=>$title]); $ids[]=(int)$id;
    if ($sale !== null) update_post_meta($id,'true_sale',$sale);
}
$base = ['post_type'=>'product','post_status'=>'publish','posts_per_page'=>-1,'post__in'=>$ids];
$online = new WP_Query($class::apply($base,'online')); $onlineIds = wp_list_pluck($online->posts,'ID');
$assert(in_array($ids[0],$onlineIds,true) && in_array($ids[2],$onlineIds,true) && !in_array($ids[1],$onlineIds,true), 'online mode includes true_sale!=false and the legacy missing-meta default');
$call = new WP_Query($class::apply($base,'call')); $callIds = wp_list_pluck($call->posts,'ID');
$assert(count($callIds) === 1 && (int) $callIds[0] === $ids[1], 'call mode matches exact true_sale=false products');
$allArgs = $class::apply($base,'all');
$assert(!isset($allArgs['meta_query']), 'all mode leaves products unfiltered');
$badArgs = $class::apply($base,'unexpected');
$assert(!isset($badArgs['meta_query']), 'invalid modes safely fall back to all');
foreach ($ids as $id) wp_delete_post($id,true);
if ($failures) { fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n"); exit(1); }
echo "[PASSED] Task 8.5 checks completed successfully.\n";
