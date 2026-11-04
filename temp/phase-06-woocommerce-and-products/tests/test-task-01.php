<?php
/**
 * Test 6.1 — Wrapper ووکامرس + Setup (حذف breadcrumb/sidebar/related، استایل‌ها، ستون‌ها)
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-01.php
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
echo "== Test 6.1: woocommerce wrapper + Setup ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Woo\Setup;

/* ---------- ۱) فیلترها حین بوت functions.php ثبت شده‌اند (Theme::boot → Woo\Setup) ---------- */
$boot_filters = array_keys($GLOBALS['__wp_hooks']['filters'] ?? []);
check(in_array('woocommerce_enqueue_styles', $boot_filters, true), 'بوت: فیلتر woocommerce_enqueue_styles ثبت شد');
check(in_array('loop_shop_columns', $boot_filters, true), 'بوت: فیلتر loop_shop_columns ثبت شد');

/* ---------- ۲) remove_actionهای پیش‌فرض ---------- */
$removed = $GLOBALS['__wp_hooks']['removed_actions'] ?? [];
$has = static function (string $hook, string $cb) use ($removed): bool {
    foreach ($removed as $r) {
        if (($r['hook'] ?? '') === $hook && ($r['cb'] ?? '') === $cb) {
            return true;
        }
    }
    return false;
};
check($has('woocommerce_before_main_content', 'woocommerce_breadcrumb'), 'حذف breadcrumb پیش‌فرض');
check($has('woocommerce_sidebar', 'woocommerce_get_sidebar'), 'حذف سایدبار پیش‌فرض');
check($has('woocommerce_after_single_product_summary', 'woocommerce_output_related_products'), 'حذف related products از سینگل');

/* ---------- ۳) استایل‌های ووکامرس: فقط blockui + select2 ---------- */
$wc_styles = [
    'layout'    => ['src' => 'layout.css'],
    'smallscreen' => ['src' => 'smallscreen.css'],
    'general'   => ['src' => 'general.css'],
    'buttons'   => ['src' => 'buttons.css'],
    'forms'     => ['src' => 'forms.css'],
    'blockui'   => ['src' => 'blockui.css'],
    'select2'   => ['src' => 'select2.css'],
    'photoswipe' => ['src' => 'photoswipe.css'],
];
$kept = apply_filters('woocommerce_enqueue_styles', $wc_styles);
check(array_keys($kept) === ['blockui', 'select2'], 'فیلتر استایل: فقط blockui و select2 باقی می‌مانند');
check(($kept['select2']['src'] ?? '') === 'select2.css', 'مقدار استایل select2 دست‌نخورده');
$kept_none = apply_filters('woocommerce_enqueue_styles', []);
check($kept_none === [], 'ورودی خالی → خروجی خالی');

/* ---------- ۴) ستون‌های loop از آپشن ---------- */
$GLOBALS['__wp_options']['erfansanat_theme_options'] = [];
es_option_reset_cache();
check((int) apply_filters('loop_shop_columns', 4) === 4, 'پیش‌فرض ستون‌ها = ۴');

$GLOBALS['__wp_options']['erfansanat_theme_options']['archives']['products_columns'] = '3';
es_option_reset_cache();
check((int) apply_filters('loop_shop_columns', 4) === 3, 'آپشن ۳ → سه ستون');

$GLOBALS['__wp_options']['erfansanat_theme_options']['archives']['products_columns'] = '2';
es_option_reset_cache();
check((int) apply_filters('loop_shop_columns', 4) === 2, 'آپشن ۲ → دو ستون');

$GLOBALS['__wp_options']['erfansanat_theme_options']['archives']['products_columns'] = '9';
es_option_reset_cache();
check((int) apply_filters('loop_shop_columns', 4) === 4, 'مقدار نامعتبر ۹ → کلمپ به ۴');

$GLOBALS['__wp_options']['erfansanat_theme_options']['archives']['products_columns'] = 'x';
es_option_reset_cache();
check((int) apply_filters('loop_shop_columns', 4) === 4, 'مقدار غیرعددی → پیش‌فرض ۴');

/* ---------- ۵) عنوان صفحه پیش‌فرض ووکامرس حذف ---------- */
check(apply_filters('woocommerce_show_page_title', true) === false, 'عنوان پیش‌فرض آرشیو ووکامرس خاموش (هیرو سفارشی میرور)');

/* ---------- ۶) wrapper woocommerce.php — ساختار shell میرور ---------- */
if (!function_exists('woocommerce_content')) {
    function woocommerce_content(): void {
        echo 'WC-CONTENT-MARKER';
    }
}
$GLOBALS['__es_theme_dir'] = $root . '/temp/phase-06-woocommerce-and-products/tests/fixtures/wrapper-shell';
ob_start();
include $root . '/woocommerce.php';
$wrapper = ob_get_clean();
check($wrapper === '<main>WC-CONTENT-MARKER</main>', 'wrapper: <main> بدون اتریبیوت دور woocommerce_content (بدون هدر/فوتر در شل خالی)');
$GLOBALS['__es_theme_dir'] = $root;

/* ---------- ۷) چک لغوی هر دو فایل (token_get_all — جایگزین php -l در wasm) ---------- */
$lex = static function (string $f): bool {
    if (!is_file($f)) {
        return false;
    }
    return count(token_get_all((string) file_get_contents($f))) > 12;
};
check($lex($root . '/woocommerce.php'), 'چک لغوی: woocommerce.php');
check($lex($root . '/inc/Woo/Setup.php'), 'چک لغوی: inc/Woo/Setup.php');
check(class_exists(Setup::class), 'کلاس Woo\Setup از اتولودر resolve و پارس شد');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
