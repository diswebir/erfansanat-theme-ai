<?php
/**
 * Test 3.4 — Rewrite تکسونومی‌های ووکامرس: product_cat با with_front=true + flush فعال‌سازی
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-04.php
 */
declare(strict_types=1);

error_reporting(E_ALL);
$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else       { $failures[] = $label; echo "  FAIL  $label\n"; }
}

$root = dirname(ES_SCRIPT_PATH, 4);
echo "== Test 3.4: Rewrite تکسونومی‌های ووکامرس ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
require $root . '/functions.php';

use ErfanSanat\Theme\Data\WooRewrite;

check(class_exists(WooRewrite::class), 'کلاس WooRewrite resolve شد');

// فیلترها ثبت شده‌اند
$filters = $GLOBALS['__wp_hooks']['filters'];
$has = static function (string $hook, string $method) use ($filters): bool {
    foreach ($filters[$hook] ?? [] as $entry) {
        if (is_array($entry['cb']) && $entry['cb'][1] === $method) {
            return true;
        }
    }
    return false;
};
check($has('woocommerce_taxonomy_args_product_cat', 'product_cat_args'), 'فیلتر product_cat ثبت شد');
check($has('woocommerce_taxonomy_args_product_tag', 'product_tag_args'), 'فیلتر product_tag ثبت شد');
check($has('woocommerce_taxonomy_args_product_brand', 'product_brand_args'), 'فیلتر product_brand ثبت شد');

// اجرای فیلترها — نکته حیاتی سئو: with_front=true
$cat = WooRewrite::product_cat_args(['hierarchical' => false, 'rewrite' => ['slug' => 'product-category', 'with_front' => false]]);
check($cat['rewrite']['slug'] === 'product_cat', 'دسته: slug=product_cat (نه product-category)');
check($cat['rewrite']['with_front'] === true, 'دسته: with_front=TRUE (نکته حیاتی بلوپرینت سئو)');
check($cat['rewrite']['hierarchical'] === true, 'دسته: hierarchical=true');
check($cat['hierarchical'] === false, 'سایر آرگومان‌های ورودی دست‌نخورده');

$tag = WooRewrite::product_tag_args([]);
check($tag['rewrite']['slug'] === 'product-tag' && $tag['rewrite']['with_front'] === false, 'برچسب: slug=product-tag با with_front=false');

$brand = WooRewrite::product_brand_args(['hierarchical' => false]);
check($brand['hierarchical'] === true, 'برند: سلسله‌مراتبی');

// flush در فعال‌سازی قالب (از 1.3) — رگرسیون
$GLOBALS['__wp_hooks']['flushed'] = 0;
require $root . '/functions.php';
// activate مستقیم:
\ErfanSanat\Theme\ThemeSetup::activate();
check($GLOBALS['__wp_hooks']['flushed'] === 1, 'after_switch_theme → flush_rewrite_rules (لینک flush زنجیره)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
