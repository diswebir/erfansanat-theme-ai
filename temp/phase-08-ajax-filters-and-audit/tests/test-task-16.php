<?php
/** Automated QA Test Script — Task 8.16: SEO fallback and structured data audit. */
declare(strict_types=1);
$root=dirname(__DIR__,3);$failures=[];
$assert=static function(bool $condition,string $label)use(&$failures):void{echo $condition?"  [PASS] {$label}\n":"  [FAIL] {$label}\n";if(!$condition)$failures[]=$label;};
require_once '/opt/wp-testbed/wp-load.php';$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.16: SEO fallback and structured data audit ===\n";
$class='ErfanSanat\\Theme\\Seo\\SeoAudit';
$assert(is_file($root.'/inc/Seo/SeoAudit.php')&&class_exists($class),'SeoAudit exists and autoloads');
$audit=new $class();$postId=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'SEO Audit QA']);
update_post_meta($postId,'_rank_math_title','Legacy title QA');update_post_meta($postId,'_rank_math_description','Legacy description QA');update_post_meta($postId,'_rank_math_canonical_url','https://example.test/seo-qa/');
$assert($audit->legacyMetaValue($postId,'title')==='Legacy title QA','Rank Math legacy title is read from its exact meta key');
$assert($audit->documentTitle('WordPress title',$postId)==='Legacy title QA','legacy SEO title is used only as a controlled fallback');
$assert($audit->canonicalUrl('https://example.test/default/',$postId)==='https://example.test/seo-qa/','canonical URL fallback uses the existing Rank Math metadata');
$tags=$audit->descriptionMarkup($postId);
$assert(str_contains($tags,'name="description"')&&str_contains($tags,'Legacy description QA'),'fallback description markup is escaped');
$assert(\ErfanSanat\Theme\Seo\HreflangTags::map()===['fa'=>'hrefmeta_fa','en'=>'hrefmeta_en','ar'=>'hrefmeta_ar'],'hreflang mapping preserves exact legacy keys');
$source=(string)file_get_contents($root.'/page-templates/template-faq.php');
$assert(str_contains($source,'application/ld+json')&&str_contains($source,'FAQPage')&&!str_contains((string)file_get_contents($root.'/inc/Seo/SeoAudit.php'),'application/ld+json'),'existing FAQ JSON-LD remains the single schema source');
define('SEOPRESS_VERSION','test-stub');
$assert($audit->documentTitle('SEOPress title',$postId)==='SEOPress title'&&$audit->canonicalUrl('SEOPress canonical',$postId)==='SEOPress canonical'&&$audit->descriptionMarkup($postId)==='','active SEO plugin metadata is never overridden or duplicated');
wp_delete_post($postId,true);
if($failures){fwrite(STDERR,"[FAILED] ".implode('; ',$failures)."\n");exit(1);}echo "[PASSED] Task 8.16 checks completed successfully.\n";
