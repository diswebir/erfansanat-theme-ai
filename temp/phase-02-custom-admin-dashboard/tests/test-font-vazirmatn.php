<?php
/**
 * Test Font — ادغام کامل وزیرمتن در پنل پیشخوان (فونت‌فیس + وراثت + preload)
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-font-vazirmatn.php
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
echo "== Test Font: وزیرمتن در پنل پیشخوان ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
require $root . '/functions.php';

/* ---------- ۱) فایل‌های فونت روی دیسک ---------- */
foreach (['vazirmatn-arabic.woff2', 'vazirmatn-latin-ext.woff2', 'vazirmatn-latin.woff2'] as $f) {
    check(file_exists($root . '/assets/fonts/' . $f) && filesize($root . '/assets/fonts/' . $f) > 1000, "فایل فونت موجود: {$f}");
}

/* ---------- ۲) @font-face در admin.css ---------- */
$css = (string) file_get_contents($root . '/assets/admin/admin.css');
check(substr_count($css, '@font-face') === 3, 'admin.css: سه @font-face وزیرمتن');
check(substr_count($css, "font-family:Vazirmatn") === 3, 'admin.css: هر سه بلوک family=Vazirmatn');
check(substr_count($css, 'font-weight:100 900') === 3, 'admin.css: وزن متغیر ۱۰۰-۹۰۰ در هر سه');
check(substr_count($css, 'font-display:swap') === 3, 'admin.css: font-display swap در هر سه');
foreach (['../fonts/vazirmatn-arabic.woff2', '../fonts/vazirmatn-latin-ext.woff2', '../fonts/vazirmatn-latin.woff2'] as $ref) {
    check(str_contains($css, $ref), "admin.css: ارجاع {$ref}");
    // مسیر نسبی واقعاً به فایل موجود می‌رسد
    check(file_exists($root . '/assets/admin/' . $ref), "مسیر نسبی معتبر: {$ref}");
}

/* ---------- ۳) وراثت فونت روی کنترل‌های فرم ---------- */
check(str_contains($css, 'font-family: Vazirmatn, Tahoma, sans-serif'), 'admin.css: font-family پنل = وزیرمتن');
check(preg_match('/\.es-admin button,\s*\.es-admin input,\s*\.es-admin select,\s*\.es-admin textarea,\s*\.es-admin \.button\s*\{\s*font-family: inherit;/s', $css) === 1, 'admin.css: وراثت فونت برای button/input/select/textarea/.button');

/* ---------- ۴) preload در AdminAssets ---------- */
$_GET['page'] = \ErfanSanat\Theme\Admin\ThemeOptions::MENU_SLUG;
\ErfanSanat\Theme\Admin\AdminAssets::enqueue('toplevel_page_erfansanat');
$head_hooks = $GLOBALS['__wp_hooks']['actions']['admin_head'] ?? [];
$has_preload = false;
foreach ($head_hooks as $h) {
    if (($h['cb'][1] ?? '') === 'preload_fonts') {
        $has_preload = true;
    }
}
check($has_preload, 'AdminAssets: هوک admin_head → preload_fonts ثبت شد');

ob_start();
\ErfanSanat\Theme\Admin\AdminAssets::preload_fonts();
$head = ob_get_clean();
check(substr_count($head, '<link rel="preload"') === 3, 'preload: سه لینک پیش‌بارگذاری');
check(substr_count($head, 'as="font"') === 3 && substr_count($head, 'crossorigin') === 3, 'preload: as=font و crossorigin');
$style_queue = $GLOBALS['__wp_hooks']['enqueued_styles']['erfansanat-admin'] ?? null;
check($style_queue !== null, 'AdminAssets: استایل پنل در صفحه تنظیمات انکیو شد');

/* ---------- ۵) خارج از صفحه پنل: چیزی انکیو نشود ---------- */
unset($_GET['page']);
$GLOBALS['__wp_hooks']['actions']['admin_head'] = [];
$GLOBALS['__wp_hooks']['enqueued_styles'] = [];
\ErfanSanat\Theme\Admin\AdminAssets::enqueue('edit.php');
check(($GLOBALS['__wp_hooks']['enqueued_styles']['erfansanat-admin'] ?? null) === null, 'خارج از پنل: استایل انکیو نشد (هدر سبک)');
check(($GLOBALS['__wp_hooks']['actions']['admin_head'] ?? []) === [], 'خارج از پنل: preload ثبت نشد');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
