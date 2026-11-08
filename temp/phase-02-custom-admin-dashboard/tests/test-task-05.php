<?php
/**
 * Test 2.5 — تب‌های «برند/استایل»، «اسکریپت سفارشی» و «آرشیوها»
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-05.php
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
echo "== Test 2.5: تب‌های برند/اسکریپت/آرشیو ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ThemeOptions;

$tabs   = ThemeOptions::tabs();
$pick   = static function (string $tab) use ($tabs): array {
    $out = [];
    foreach (($tabs[$tab]['fields'] ?? []) as $f) {
        $out[$f['key']] = $f;
    }
    return $out;
};

// برند — رنگ‌های استخراج‌شده از styles.css میرور
$brand = $pick('brand');
check(($brand['brand_primary']['default'] ?? '') === '#059669', 'رنگ اصلی برند = #059669 (emerald-600 میرور)');
check(($brand['brand_dark']['default'] ?? '') === '#065f46', 'رنگ تیره = #065f46');
check(($brand['brand_accent']['default'] ?? '') === '#10b981', 'رنگ مکمل = #10b981');
check(($brand['brand_primary']['type'] ?? '') === 'color', 'نوع فیلد رنگ');

// اسکریپت سفارشی
$custom = $pick('custom');
check(isset($custom['head_js'], $custom['footer_js']) && ($custom['head_js']['default'] ?? 'x') === '' , 'فیلدهای head_js/footer_js با پیش‌فرض خالی');
check(($custom['head_js']['type'] ?? '') === 'textarea', 'نوع فیلد اسکریپت textarea');

// آرشیوها — مطابق گرید میرور
$arch = $pick('archives');
check(($arch['products_per_page']['default'] ?? 0) === 20, 'پیش‌فرض ۲۰ محصول در صفحه (گرید میرور)');
check(($arch['products_columns']['default'] ?? '') === '4' && count($arch['products_columns']['options'] ?? []) === 3, 'ستون‌های محصولات: پیش‌فرض ۴ + سه گزینه');
check(($arch['projects_per_page']['default'] ?? 0) === 6, 'پیش‌فرض ۶ پروژه در صفحه (فاز ۷.۲: مطابق میرور projects.html)');
check(($arch['posts_per_page']['default'] ?? 0) === 9, 'پیش‌فرض ۹ مقاله در صفحه');

// clamp عدد در ذخیره: تست زنجیره sanitize عددی با min/max
check(\ErfanSanat\Theme\Admin\Sanitizer::field(['type' => 'number', 'min' => 1, 'max' => 100], '500') === 100, 'clamp آرشیو به max=۱۰۰');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
