<?php
/** Automated QA Test Script — Task 8.6: Product attribute filter builder. */
declare(strict_types=1);
$root = dirname(__DIR__, 3); $failures=[];
$assert = static function(bool $condition,string $label) use (&$failures): void { echo $condition ? "  [PASS] {$label}\n" : "  [FAIL] {$label}\n"; if(!$condition)$failures[]=$label; };
require_once '/opt/wp-testbed/wp-load.php'; $GLOBALS['wp']->main();
echo "=== [TEST] Task 8.6: Product attribute filter builder ===\n";
$class='ErfanSanat\\Theme\\Filters\\ProductAttributeFilters';
$assert(is_file($root.'/inc/Filters/ProductAttributeFilters.php'),'ProductAttributeFilters source exists');
$assert(class_exists($class),'ProductAttributeFilters autoloads');
$attributes=\ErfanSanat\Theme\Filters\FilterRegistry::attributes();
foreach($attributes as $taxonomy=>$label){
    $clauses=$class::buildTaxQuery([$taxonomy=>['qa-term']]);
    $assert(isset($clauses[0]['taxonomy']) && $clauses[0]['taxonomy']===$taxonomy && $clauses[0]['terms']===['qa-term'],'known attribute is independently mapped: '.$taxonomy);
}
$unknown=$class::buildTaxQuery(['pa_not-registered'=>['invalid'],'product_cat'=>['cat']]);
$assert($unknown===[],'unknown keys and non-attribute taxonomies are ignored');
$args=$class::apply(['tax_query'=>[['taxonomy'=>'product_cat','field'=>'slug','terms'=>['lights']]]],['pa_color'=>['warm','cool']]);
$assert(($args['tax_query']['relation']??'')==='AND' && count($args['tax_query'])===3,'attribute clause is AND-merged with existing category filtering');
$optionName=\ErfanSanat\Theme\Admin\OptionsStore::OPTION_NAME; $old=get_option($optionName,[]);
$map=is_array($old['enabled_filters']??null)?$old['enabled_filters']:[]; $map['attr_pa_color']=false;
update_option($optionName,array_merge(is_array($old)?$old:[],['enabled_filters'=>$map])); \ErfanSanat\Theme\Admin\OptionsStore::flushCache();
$disabled=$class::buildTaxQuery(['pa_color'=>['warm']]);
$assert($disabled===[],'dashboard-disabled attributes are omitted from query clauses');
update_option($optionName,$old); \ErfanSanat\Theme\Admin\OptionsStore::flushCache();
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);} echo "[PASSED] Task 8.6 checks completed successfully.\n";
