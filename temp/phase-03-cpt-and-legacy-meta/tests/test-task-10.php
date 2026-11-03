<?php
/**
 * Test 3.10 — روابط محصول: project-ids و usage_countr با حفظ فرمت + RelatedPicker + هلپرها
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-10.php
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
echo "== Test 3.10: روابط محصول ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\RelatedPicker;
use ErfanSanat\Theme\Admin\ProductMetaboxes;

// ---- ذخیره روابط: فرمت آرایه ----
$GLOBALS['__es_stub_post_types'][90] = 'product';
$GLOBALS['__wp_post_meta'][90] = [];
$post = new \stdClass();
$post->ID = 90;
$post->post_type = 'product';
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => [
        'project-ids'   => '5,7,5,abc',
        'usage_countr'  => [3, '4'],
    ],
];
ProductMetaboxes::save(90, $post);

$projects = $GLOBALS['__wp_post_meta'][90]['project-ids'] ?? null;
check(is_array($projects) && array_keys($projects) === ['item-0', 'item-1'], 'project-ids: ذخیره آرایه item-N (dedup + فقط عدد)');
check(($projects['item-0']['id'] ?? 0) === 5 && ($projects['item-1']['id'] ?? 0) === 7, 'آیدی‌های ۵ و ۷');

$usages = $GLOBALS['__wp_post_meta'][90]['usage_countr'] ?? null;
check(is_array($usages) && ($usages['item-0']['id'] ?? 0) === 3 && ($usages['item-1']['id'] ?? 0) === 4, 'usage_countr با املای دقیق ذخیره شد');

// ---- حفظ فرمت JSON موجود ----
$GLOBALS['__es_stub_post_types'][91] = 'product';
$GLOBALS['__wp_post_meta'][91] = ['project-ids' => '{"item-0":{"id":11},"item-1":{"id":12}}'];
$post91 = new \stdClass();
$post91->ID = 91;
$post91->post_type = 'product';
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => ['project-ids' => '15'],
];
ProductMetaboxes::save(91, $post91);
$stored91 = $GLOBALS['__wp_post_meta'][91]['project-ids'];
check(is_string($stored91) && str_starts_with((string) $stored91, '{') && json_decode((string) $stored91, true)['item-0']['id'] === 15, 'فرمت JSON قدیمی project-ids حفظ شد');

// ---- RelatedPicker::current_ids خواندن مقاوم ----
check(RelatedPicker::current_ids(90, 'project-ids') === [5, 7], 'current_ids: آرایه item-N');
check(RelatedPicker::current_ids(91, 'project-ids') === [15], 'current_ids: JSON');

// ---- رندر پیکر ----
$GLOBALS['__es_stub_related_titles'] = [5 => 'تونل نوری قم', 7 => 'میدان شهدای اصفهان'];
ob_start();
RelatedPicker::render('opt_meta[project-ids]', 'projects', RelatedPicker::source_posts(), 'پروژه‌های مرتبط', 'project-ids', 90);
$ui = (string) ob_get_clean();
check(str_contains($ui, 'name="opt_meta[project-ids]" value="5,7"'), 'پیکر: مقدار فعلی در hidden');
check(str_contains($ui, 'تونل نوری قم') && str_contains($ui, 'data-es-related-chips'), 'پیکر: چیپ‌ها با عنوان');

// ---- AJAX: بدون نانس رد / با نانس+فیلتر نتیجه ----
$_POST = [];
try {
    RelatedPicker::ajax_search();
    check(false, 'AJAX بدون نانس رد می‌شود');
} catch (\ES_WpDieException $e) {
    check(true, 'AJAX بدون نانس رد می‌شود');
}

$GLOBALS['__wp_current_user_can'] = true;
$GLOBALS['__wp_hooks']['filters']['es_related_search_results'] = [[
    'cb' => static fn(array $r, string $s): array => [['id' => 42, 'title' => 'نتیجه ' . $s]],
    'priority' => 10,
]];
$_POST = ['nonce' => 'test-nonce-es_related', 's' => 'تونل', 'query_type' => 'projects', 'source' => 'posts'];
try {
    RelatedPicker::ajax_search();
    check(false, 'AJAX موفق → استثنای JSON');
} catch (\ES_JsonExitException $e) {
    $results = $e->response['data']['results'] ?? [];
    check($e->response['success'] === true && ($results[0]['id'] ?? 0) === 42, 'AJAX نتایج از فیلتر توسعه');
}

// AJAX بدون کاپابیلیتی
$GLOBALS['__wp_current_user_can'] = false;
$_POST = ['nonce' => 'test-nonce-es_related', 's' => 'x', 'query_type' => 'projects', 'source' => 'posts'];
try {
    RelatedPicker::ajax_search();
    check(false, 'AJAX بدون کاپابیلیتی رد می‌شود');
} catch (\ES_JsonExitException $e) {
    check($e->response['success'] === false, 'AJAX بدون کاپابیلیتی → خطای JSON');
}

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
