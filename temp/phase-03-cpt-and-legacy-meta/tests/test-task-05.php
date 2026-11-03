<?php
/**
 * Test 3.5 — متاباکس محصول: تب شناسه‌های قانونی (۶ کلید) + Quick Edit + ستون لیست
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-05.php
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
echo "== Test 3.5: شناسه‌های قانونی + Quick Edit ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;
use ErfanSanat\Theme\Admin\QuickEdit;

// متاباکس ثبت شده روی product
do_action('add_meta_boxes');
$mb = $GLOBALS['__wp_hooks']['meta_boxes']['es-product-info'] ?? [];
check(($mb['screen'] ?? '') === 'product', 'متاباکس روی product ثبت شد');

// رندر تب شناسه‌ها
$post = new \stdClass();
$post->ID = 50;
$post->post_type = 'product';
$GLOBALS['__es_stub_post_types'][50] = 'product';
ob_start();
ProductMetaboxes::render($post);
$ui = (string) ob_get_clean();
foreach (['gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id'] as $key) {
    check(str_contains($ui, 'name="opt_meta[' . $key . ']"'), "اینپوت {$key}");
}
check(str_contains($ui, 'name="es_product_meta_nonce"'), 'نانس متاباکس');
check(str_contains($ui, 'data-es-pmeta-panel="ids"'), 'تب شناسه‌ها رندر شد');

// ذخیره: بدون نانس → رد
$_POST = ['opt_meta' => ['gs1_id' => 'X']];
ProductMetaboxes::save(50, $post);
check(!isset($GLOBALS['__wp_post_meta'][50]['gs1_id']), 'بدون نانس ذخیره نمی‌شود');

// ذخیره موفق: ۶ کلید با املای دقیق + sanitize
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => [
        'gs1_id'        => '6260000000011',
        'national_code' => '123456<script>',
        'hs_id'         => '9405',
        'isic_id'       => '27402',
        'cpc_id'        => '47190300',
        'product_id'    => 'ESE-001',
    ],
];
ProductMetaboxes::save(50, $post);
check(($GLOBALS['__wp_post_meta'][50]['gs1_id'] ?? '') === '6260000000011', 'ذخیره gs1_id');
check(($GLOBALS['__wp_post_meta'][50]['national_code'] ?? '') === '123456', 'ذخیره national_code (تگ حذف)');
check(($GLOBALS['__wp_post_meta'][50]['hs_id'] ?? '') === '9405' && ($GLOBALS['__wp_post_meta'][50]['isic_id'] ?? '') === '27402' && ($GLOBALS['__wp_post_meta'][50]['cpc_id'] ?? '') === '47190300' && ($GLOBALS['__wp_post_meta'][50]['product_id'] ?? '') === 'ESE-001', 'ذخیره چهار کلید باقی');

// Quick Edit: رندر فیلدها + هوک اسکریپت
ob_start();
QuickEdit::render_fields('es-identifiers', 'product');
$qe = (string) ob_get_clean();
check(str_contains($qe, 'name="opt_meta[gs1_id]"') && str_contains($qe, 'data-es-qe="product_id"'), 'فیلدهای Quick Edit با نام/دیتا');
ob_start();
QuickEdit::render_fields('es-identifiers', 'post');
check(trim((string) ob_get_clean()) === '', 'Quick Edit فقط برای product');

// ستون‌ها + داده پنهان برای پر کردن Quick Edit
$cols = ProductMetaboxes::columns(['title' => 'نام']);
check(isset($cols['es-identifiers'], $cols['es-sale-mode']), 'ستون‌های شناسه و وضعیت خرید');
ob_start();
ProductMetaboxes::column_content('es-identifiers', 50);
$col = (string) ob_get_clean();
check(str_contains($col, 'es-quickedit-data') && str_contains($col, 'data-gs1="6260000000011"'), 'دیتا-اتریبیوت‌ها برای Quick Edit');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
