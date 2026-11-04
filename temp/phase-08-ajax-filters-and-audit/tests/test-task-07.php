<?php
/** Automated QA Test Script — Task 8.7: Product filter AJAX endpoint. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
$optionsClass = 'ErfanSanat\\Theme\\Admin\\OptionsStore';
$originalOptions = get_option($optionsClass::OPTION_NAME, []);
$enabledMap = \ErfanSanat\Theme\Filters\FilterRegistry::defaultEnabled();
update_option($optionsClass::OPTION_NAME, array_merge(is_array($originalOptions) ? $originalOptions : [], ['enabled_filters' => $enabledMap]));
$optionsClass::flushCache();
echo "=== [TEST] Task 8.7: Product filter AJAX endpoint ===\n";
$class='ErfanSanat\\Theme\\Ajax\\ProductFilterEndpoint';
$assert(is_file($root.'/inc/Ajax/ProductFilterEndpoint.php'),'ProductFilterEndpoint source exists');
$assert(class_exists($class),'ProductFilterEndpoint autoloads');
$endpoint=new $class();$routes=apply_filters('erfan_sanat_ajax_routes',[]);
$assert(isset($routes['product_filter'])&&is_callable($routes['product_filter']),'product filter route is registered');
$ids=[];
for($i=1;$i<=14;$i++){$id=wp_insert_post(['post_type'=>'product','post_status'=>'publish','post_title'=>'Endpoint QA Product '.str_pad((string)$i,2,'0',STR_PAD_LEFT)]);$ids[]=(int)$id;update_post_meta($id,'_price',(string)($i*100));update_post_meta($id,'true_sale',$i===1?'false':'true');}
$all=$endpoint->search(['search'=>'Endpoint QA','sort'=>'price-asc','page'=>'1']);
$assert(($all['count']??0)>=14&&($all['data']['pages']??0)>=2,'result reports full count and pagination metadata');
$assert(str_contains($all['html']??'','es-card-product'),'response renders existing product-card markup');
$assert(str_contains($all['data']['pagination_html']??'','page-numbers'),'paginated query returns standard WordPress page links');
$assert((int)($all['data']['items'][0]??0)===$ids[0],'numeric price sort returns the lowest-priced matching product first');
$call=$endpoint->search(['search'=>'Endpoint QA','true_sale'=>'call']);
$assert(($call['data']['items']??[])===[$ids[0]],'sale mode composes with the product query');
$empty=$endpoint->search(['product_cat'=>'no-such-product-category-qa']);
$assert(($empty['count']??-1)===0&&str_contains($empty['data']['empty_html']??'','محصولی مطابق فیلترهای انتخابی پیدا نشد'),'no results returns the existing empty-state markup');
foreach($ids as $id)wp_delete_post($id,true);
update_option($optionsClass::OPTION_NAME, $originalOptions); $optionsClass::flushCache();
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Task 8.7 checks completed successfully.\n";
