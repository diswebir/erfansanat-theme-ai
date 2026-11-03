<?php
/**
 * Test 2.3 — تب «هدر و لوگو» + نوار اعلان: اسکیم، پیش‌فرض‌های میرور، override و رندر
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-03.php
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
echo "== Test 2.3: تب هدر و لوگو ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ThemeOptions;

$tabs  = ThemeOptions::tabs();
$header = $tabs['header']['fields'] ?? [];

$by_key = [];
foreach ($header as $f) {
    $by_key[$f['key']] = $f;
}

check(isset($by_key['logo']) && $by_key['logo']['type'] === 'media', 'فیلد logo از نوع media');
check(($by_key['logo']['default'] ?? '') === 'assets/images/ese-logo.svg', 'پیش‌فرض لوگو = ese-logo.svg قالب');
check(isset($by_key['sticky'], $by_key['search_toggle'], $by_key['cart_toggle']) && $by_key['sticky']['default'] === 1 && $by_key['search_toggle']['default'] === 1 && $by_key['cart_toggle']['default'] === 1, 'پیش‌فرض sticky/search/cart = روشن (مطابق میرور)');
check(($by_key['ann_enabled']['default'] ?? null) === 0, 'نوار اعلان پیش‌فرض خاموش (میرور نوار ندارد)');
check(isset($by_key['ann_bg']) && $by_key['ann_bg']['type'] === 'color', 'فیلد رنگ نوار اعلان');

// es_option با درخت پیش‌فرض (آپشن خالی)
es_option_reset_cache();
check(es_option('header.logo') === 'assets/images/ese-logo.svg', 'es_option پیش‌فرض لوگو را برمی‌گرداند');
check(es_option('header.sticky') === 1, 'es_option پیش‌فرض sticky = ۱');
check(es_option('header.ann_enabled') === 0, 'es_option پیش‌فرض ann_enabled = ۰');

// override ذخیره‌شده
es_stubs_set_option('erfansanat_theme_options', ['header' => ['sticky' => 0, 'logo' => 555]]);
es_option_reset_cache();
check(es_option('header.sticky') === 0, 'مقدار ذخیره‌شده روی پیش‌فرض غلبه می‌کند (sticky=۰)');
check(es_option('header.logo') === 555, 'مقدار ذخیره‌شده لوگو (آیدی رسانه)');
check(es_option('header.search_toggle') === 1, 'فیلدهای ذخیره‌نشده همچنان پیش‌فرض می‌گیرند');

// رندر صفحه: نام‌گذاری opt[header][…] + مقدار جاری
es_stubs_set_option('erfansanat_theme_options', []);
es_option_reset_cache();
$GLOBALS['__wp_current_user_can'] = true;
$_GET = ['page' => 'erfansanat-theme'];
ob_start();
ThemeOptions::render_page();
$html = (string) ob_get_clean();
check(str_contains($html, 'name="opt[header][logo]"'), 'رندر فیلد با نام opt[header][logo]');
check(str_contains($html, 'name="opt[header][sticky]"'), 'رندر سوییچ sticky');
check(preg_match('/name="opt\[header\]\[logo\]" value="assets\/images\/ese-logo\.svg"/', $html) === 1, 'مقدار پیش‌فرض لوگو داخل اینپوت');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
