<?php
/**
 * Test 3.12 — مشخصات پروژه: finish-pro-time + mortabet_mahsole_2 (حفظ فرمت) + ستون تاریخ
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-12.php
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
echo "== Test 3.12: مشخصات پروژه ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ProjectMetaboxes;

// رندر: فیلد تاریخ با placeholder میرور + پیکر محصولات
$post = new \stdClass();
$post->ID = 130;
$post->post_type = 'projects';
ob_start();
ProjectMetaboxes::render($post);
$ui = (string) ob_get_clean();
check(str_contains($ui, 'name="opt_project[finish-pro-time]"') && str_contains($ui, 'placeholder="1404/01/01"'), 'فیلد تاریخ با placeholder شمسی');
check(str_contains($ui, 'name="opt_project[mortabet_mahsole_2]"'), 'پیکر محصولات پروژه');

// ذخیره
$GLOBALS['__es_stub_post_types'][130] = 'projects';
$_POST = [
    'es_project_meta_nonce' => 'test-nonce-es_project_meta',
    'opt_project' => [
        'finish-pro-time'     => '1404/01/01<script>',
        'mortabet_mahsole_2'  => '33,44',
    ],
];
ProjectMetaboxes::save(130, $post);
check(($GLOBALS['__wp_post_meta'][130]['finish-pro-time'] ?? '') === '1404/01/01', 'ذخیره تاریخ (تگ حذف)');
check(($GLOBALS['__wp_post_meta'][130]['finish-pro-time'] ?? '') === '1404/01/01', 'فرمت شمسی حفظ شد');

$prods = $GLOBALS['__wp_post_meta'][130]['mortabet_mahsole_2'] ?? null;
check(is_array($prods) && ($prods['item-0']['id'] ?? 0) === 33 && ($prods['item-1']['id'] ?? 0) === 44, 'ذخیره محصولات پروژه (آرایه item-N)');

// حفظ فرمت JSON قدیمی
$GLOBALS['__es_stub_post_types'][131] = 'projects';
$GLOBALS['__wp_post_meta'][131] = ['mortabet_mahsole_2' => '{"item-0":{"id":55}}'];
$post131 = new \stdClass();
$post131->ID = 131;
$post131->post_type = 'projects';
$_POST = [
    'es_project_meta_nonce' => 'test-nonce-es_project_meta',
    'opt_project' => ['mortabet_mahsole_2' => '66'],
];
ProjectMetaboxes::save(131, $post131);
$stored = $GLOBALS['__wp_post_meta'][131]['mortabet_mahsole_2'];
check(is_string($stored) && json_decode((string) $stored, true)['item-0']['id'] === 66, 'فرمت JSON قدیمی mortabet_mahsole_2 حفظ شد');

// هلپر خواندن مقاوم
check(\ErfanSanat\Theme\Frontend\ProjectGallery::get_products(130) === [33, 44], 'es_get_project_products: آرایه');
check(\ErfanSanat\Theme\Frontend\ProjectGallery::get_products(131) === [66], 'es_get_project_products: JSON');

// ستون تاریخ
$cols = ProjectMetaboxes::columns(['title' => 'نام']);
check(isset($cols['es-finish-time']), 'ستون تاریخ تکمیل');
ob_start();
ProjectMetaboxes::column_content('es-finish-time', 130);
check(str_contains((string) ob_get_clean(), '1404/01/01'), 'محتوای ستون تاریخ');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
