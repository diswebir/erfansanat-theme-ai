<?php
/**
 * Test 2.1 — هسته ThemeOptions: منو، صفحه تب‌دار، AJAX امن (Nonce/Capability/آپشن واحد autoload=false)
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-01.php
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
echo "== Test 2.1: هسته ThemeOptions ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';

function es_reset_admin_env(string $root): void {
    es_stubs_reset();
    $GLOBALS['__es_theme_dir']       = $root;
    $GLOBALS['__wp_is_admin']        = true;
    $GLOBALS['__wp_current_user_can'] = true;
    $_GET  = ['page' => 'erfansanat-theme'];
    $_POST = [];
    require $root . '/functions.php';
}

es_reset_admin_env($root);
check(class_exists(\ErfanSanat\Theme\Admin\ThemeOptions::class), 'کلاس ThemeOptions از اوتولودر resolve شد');
check(class_exists(\ErfanSanat\Theme\Admin\AdminAssets::class), 'کلاس AdminAssets resolve شد');
check(class_exists(\ErfanSanat\Theme\Admin\FieldRenderer::class), 'کلاس FieldRenderer resolve شد (پیش‌نیاز 2.2)');
check(class_exists(\ErfanSanat\Theme\Admin\Sanitizer::class), 'کلاس Sanitizer resolve شد (پیش‌نیاز 2.2)');

// ثبت هوک‌ها در init
\ErfanSanat\Theme\Admin\ThemeOptions::init();
$actions = $GLOBALS['__wp_hooks']['actions'];
$has_ajax = false;
foreach ($actions['wp_ajax_es_theme_options_save'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'] === [\ErfanSanat\Theme\Admin\ThemeOptions::class, 'ajax_save']) {
        $has_ajax = true;
    }
}
check($has_ajax, 'اکشن wp_ajax_es_theme_options_save → ajax_save ثبت شد');

// منو + کاپابیلیتی
\ErfanSanat\Theme\Admin\ThemeOptions::register_menu();
$menu = $GLOBALS['__wp_hooks']['admin_pages']['erfansanat-theme'] ?? [];
check(($menu['type'] ?? '') === 'menu' && ($menu['capability'] ?? '') === 'manage_options', 'add_menu_page با کاپابیلیتی manage_options');
check(isset($menu['callback']), 'کال‌بک صفحه ثبت شده');

// رندر صفحه تب‌دار (با دسترسی)
ob_start();
\ErfanSanat\Theme\Admin\ThemeOptions::render_page();
$html = (string) ob_get_clean();
check(str_contains($html, 'data-es-options'), 'کانتینر data-es-options رندر شد');
check(str_contains($html, 'data-es-save'), 'دکمه ذخیره data-es-save موجود');
check(substr_count($html, 'data-es-tab-btn=') >= 5, '۵ تب رندر شد (header/footer/brand/custom/archives)');
check(str_contains($html, 'data-es-tab-panel="header"'), 'پنل تب header موجود');
check(str_contains($html, 'name="es_admin_nonce"'), 'فیلد نانس داخل فرم');
check(str_contains($html, 'data-es-options-form'), 'فرم data-es-options-form موجود');

// رندر بدون کاپابیلیتی → wp_die
es_reset_admin_env($root);
$GLOBALS['__wp_current_user_can'] = false;
try {
    ob_start();
    \ErfanSanat\Theme\Admin\ThemeOptions::render_page();
    ob_end_clean();
    check(false, 'رندر بدون کاپابیلیتی wp_die می‌دهد');
} catch (\ES_WpDieException $e) {
    check(true, 'رندر بدون کاپابیلیتی wp_die می‌دهد');
}

// AJAX — بدون نانس → die
es_reset_admin_env($root);
$_POST = ['opt' => ['x' => 'y']];
try {
    \ErfanSanat\Theme\Admin\ThemeOptions::ajax_save();
    check(false, 'ذخیره بدون نانس رد می‌شود');
} catch (\ES_WpDieException $e) {
    check(true, 'ذخیره بدون نانس رد می‌شود (die −1)');
}

// AJAX — نانس درست، بدون کاپابیلیتی → خطای ۴۰۳
es_reset_admin_env($root);
$GLOBALS['__wp_current_user_can'] = false;
$_POST = ['nonce' => 'test-nonce-es_admin', 'opt' => []];
try {
    \ErfanSanat\Theme\Admin\ThemeOptions::ajax_save();
    check(false, 'ذخیره بدون کاپابیلیتی خطای ۴۰۳ می‌دهد');
} catch (\ES_JsonExitException $e) {
    check(($e->response['code'] ?? 0) === 403 && $e->response['success'] === false, 'ذخیره بدون کاپابیلیتی → JSON خطای ۴۰۳');
}

// AJAX — ذخیره موفق: آپشن واحد + autoload=false + sanitize + کلید ناشناخته دور ریخته
es_reset_admin_env($root);
$_POST = [
    'nonce' => 'test-nonce-es_admin',
    'opt'   => [
        'unknown_key'   => 'باید دور ریخته شود',
    ],
];
try {
    \ErfanSanat\Theme\Admin\ThemeOptions::ajax_save();
} catch (\ES_JsonExitException $e) {
    check($e->response['success'] === true, 'ذخیره با نانس و کاپابیلیتی موفق (JSON موفقیت)');
}
$saved = $GLOBALS['__wp_options']['erfansanat_theme_options'] ?? 'MISSING';
check(is_array($saved), 'ذخیره روی آپشن واحد erfansanat_theme_options');
check(!array_key_exists('unknown_key', $saved), 'کلید ناشناخته (خارج اسکیم) ذخیره نمی‌شود');
$autoload_map = $GLOBALS['__wp_option_autoload'] ?? [];
check(($autoload_map['erfansanat_theme_options'] ?? 'MISSING') === false, 'آپشن با autoload=false ذخیره شد');

// AdminAssets — فقط روی صفحه تنظیمات
es_reset_admin_env($root);
\ErfanSanat\Theme\Admin\AdminAssets::enqueue('toplevel_page_erfansanat-theme');
$styles = $GLOBALS['__wp_hooks']['enqueued_styles'];
check(isset($styles['erfansanat-admin']) && str_ends_with($styles['erfansanat-admin']['src'], 'assets/admin/admin.css'), 'admin.css فقط روی صفحه تنظیمات انکیو شد');
check(isset($GLOBALS['__wp_hooks']['localized']['erfansanat-admin']), 'ESAdmin با نانس localize شد');

es_reset_admin_env($root);
$_GET = ['page' => 'tools.php'];
\ErfanSanat\Theme\Admin\AdminAssets::enqueue('tools_page_x');
check(!isset($GLOBALS['__wp_hooks']['enqueued_styles']['erfansanat-admin']), 'در صفحه دیگر ادمین، دارایی‌های داشبورد لود نمی‌شوند');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
