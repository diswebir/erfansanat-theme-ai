<?php
/**
 * Test 3.8 — Repeaterهای محصول با کلیدهای دقیق دیتابیس + حفظ فرمت قدیمی
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-08.php
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
echo "== Test 3.8: Repeaterهای محصول ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;

// اسکیم‌ها با کلیدهای دقیق
$schemas = ProductMetaboxes::repeater_schemas();
check(isset($schemas['product-feature']), 'کلید product-feature');
check(isset($schemas['product-feature2_copy']), 'کلید product-feature2_copy (با _copy)');
check(isset($schemas['usage_product']), 'کلید usage_product');
check(isset($schemas['faq']), 'کلید faq');

$f2 = $schemas['product-feature2_copy']['fields'];
check($f2[0]['key'] === 'add-new-feature' && $f2[1]['key'] === 'vaule_of_variable', 'زیرکلیدهای feature2: add-new-feature + vaule_of_variable (املای دیتابیس)');
check($schemas['product-feature']['fields'][0]['key'] === 'add-new-feature', 'زیرکلید product-feature: add-new-feature');
check($schemas['usage_product']['fields'][0]['key'] === 'add-new-usage', 'زیرکلید usage_product: add-new-usage');
check($schemas['faq']['fields'][0]['key'] === 'name_question' && $schemas['faq']['fields'][1]['key'] === 'answer_question', 'زیرکلیدهای faq');

// رندر با دیتای JSON قدیمی (فرمت فعلی دیتابیس زنده)
$post = new \stdClass();
$post->ID = 70;
$post->post_type = 'product';
$GLOBALS['__es_stub_post_types'][70] = 'product';
$GLOBALS['__wp_post_meta'][70]['faq'] = '{"item-0":{"name_question":"ق۱","answer_question":"ج۱"}}';
ob_start();
ProductMetaboxes::render($post);
$ui = (string) ob_get_clean();
check(str_contains($ui, 'name="opt_meta_rep[faq][0][name_question]"'), 'رندر repeater با نام nesting');
check(str_contains($ui, 'value="ق۱"'), 'مقدار دیتای قدیمی در UI');

// ذخیره: فرمت JSON قدیمی باید JSON بماند
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta_rep' => [
        'faq' => [
            ['name_question' => 'ق۱ جدید', 'answer_question' => 'ج۱ جدید'],
            ['name_question' => 'ق۲', 'answer_question' => 'ج۲'],
        ],
        'product-feature' => [
            ['add-new-feature' => 'ولتاژ ۲۴ ولت'],
        ],
        'product-feature2_copy' => [
            ['add-new-feature' => 'گارانتی', 'vaule_of_variable' => '۲۴ ماهه'],
        ],
        'usage_product' => [
            ['add-new-usage' => 'نورپردازی نما'],
        ],
    ],
];
ProductMetaboxes::save(70, $post);

$faq_stored = $GLOBALS['__wp_post_meta'][70]['faq'];
check(is_string($faq_stored) && str_starts_with((string) $faq_stored, '{'), 'فرمت JSON قدیمی faq حفظ شد');
$faq_decoded = json_decode((string) $faq_stored, true);
check(isset($faq_decoded['item-0']['name_question']) && $faq_decoded['item-0']['name_question'] === 'ق۱ جدید' && isset($faq_decoded['item-1']), 'دو ردیف جدید با item-N');

$feat_stored = $GLOBALS['__wp_post_meta'][70]['product-feature'];
check(!is_string($feat_stored) && isset($feat_stored['item-0']['add-new-feature']) && $feat_stored['item-0']['add-new-feature'] === 'ولتاژ ۲۴ ولت', 'product-feature ذخیره (بدون فرمت قبلی → آرایه item-N)');

$f2_stored = $GLOBALS['__wp_post_meta'][70]['product-feature2_copy'];
check(($f2_stored['item-0']['vaule_of_variable'] ?? '') === '۲۴ ماهه', 'vaule_of_variable با املای دقیق ذخیره شد');

$usage_stored = $GLOBALS['__wp_post_meta'][70]['usage_product'];
check(($usage_stored['item-0']['add-new-usage'] ?? '') === 'نورپردازی نما', 'usage_product ذخیره شد');

// فرمت سریالایز قدیمی هم حفظ شود
$GLOBALS['__wp_post_meta'][71] = [];
$GLOBALS['__es_stub_post_types'][71] = 'product';
$GLOBALS['__wp_post_meta'][71]['product-feature'] = serialize(['item-0' => ['add-new-feature' => 'قدیمی']]);
$post71 = new \stdClass();
$post71->ID = 71;
$post71->post_type = 'product';
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta_rep' => ['product-feature' => [['add-new-feature' => 'جدید']]],
];
ProductMetaboxes::save(71, $post71);
$serial_stored = $GLOBALS['__wp_post_meta'][71]['product-feature'];
check(is_string($serial_stored) && str_starts_with((string) $serial_stored, 'a:1:{'), 'فرمت سریالایز قدیمی حفظ شد');
$un = unserialize((string) $serial_stored, ['allowed_classes' => false]);
check(($un['item-0']['add-new-feature'] ?? '') === 'جدید', 'محتوای جدید در فرمت سریالایز');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
