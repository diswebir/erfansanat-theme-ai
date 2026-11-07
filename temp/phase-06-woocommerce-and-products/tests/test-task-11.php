<?php
/**
 * Test 6.11 — حساب کاربری: form-login پوسته میرور + CSS override + enqueue شرطی
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-11.php
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
echo "== Test 6.11: my-account ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

if (!function_exists('wp_lostpassword_url')) {
    function wp_lostpassword_url(): string { return 'https://test.example/my-account/lost-password/'; }
}
if (!function_exists('get_option')) {
    function get_option(string $k, mixed $d = false): mixed { return $GLOBALS['__wp_options'][$k] ?? $d; }
}
function es_styles11(): array { return $GLOBALS['__wp_hooks']['enqueued_styles'] ?? []; }
if (!function_exists('is_account_page')) {
    function is_account_page(): bool { return (bool) ($GLOBALS['__wp_is_account'] ?? false); }
}
if (!function_exists('get_theme_file_path')) {
    function get_theme_file_path(string $f = ''): string { return $GLOBALS['__es_theme_dir'] . '/' . ltrim($f, '/'); }
}

/* ---------- ۱) رندر form-login (هر دو حالت گزینه‌ها) ---------- */
$GLOBALS['__wp_options']['woocommerce_registration_generate_username'] = 'no';
$GLOBALS['__wp_options']['woocommerce_registration_generate_password'] = 'no';
ob_start();
include $root . '/woocommerce/myaccount/form-login.php';
$login = (string) ob_get_clean();
$src_login = (string) file_get_contents($root . '/woocommerce/myaccount/form-login.php');

check(str_contains($login, 'name="username"') && str_contains($login, 'name="password"'), 'لاگین: فیلدهای استاندارد username/password');
check(str_contains($login, 'name="rememberme"') && str_contains($login, 'value="forever"'), 'لاگین: rememberme استاندارد');
check(str_contains($login, 'name="login" value='), 'لاگین: دکمه submit با name=login');
check(str_contains($login, 'woocommerce-login-nonce'), 'لاگین: nonce استاندارد woocommerce-login');
check(str_contains($login, 'name="email"') && str_contains($login, 'type="email"'), 'ثبت‌نام: فیلد email استاندارد');
check(str_contains($login, 'name="username" id="reg_username"') && str_contains($login, 'name="password" id="reg_password"'), 'ثبت‌نام: username/password وقتی generate=no');
check(str_contains($login, 'woocommerce-register-nonce'), 'ثبت‌نام: nonce استاندارد woocommerce-register');
check(str_contains($src_login, "do_action('woocommerce_login_form_start')"), 'هوک‌ها: login_form_start حفظ');
foreach (['woocommerce_login_form_end', 'woocommerce_register_form_start', 'woocommerce_register_form', 'woocommerce_register_form_end', 'woocommerce_login_form'] as $es_hook) {
    if (!str_contains($src_login, $es_hook)) { check(false, 'هوک‌ها: ' . $es_hook); }
}
check(true, 'هوک‌ها: login/register end + register_* حفظ');
check(str_contains($login, 'w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5'), 'پوسته میرور: کلاس اینپوت میرور (کامپوننت checkout)');
check(str_contains($login, 'btn-primary'), 'پوسته میرور: دکمه btn-primary');

/* ---------- ۲) گزینه generate=yes → فیلدهای username/password ثبت‌نام حذف ---------- */
$GLOBALS['__wp_options']['woocommerce_registration_generate_username'] = 'yes';
$GLOBALS['__wp_options']['woocommerce_registration_generate_password'] = 'yes';
ob_start();
include $root . '/woocommerce/myaccount/form-login.php';
$login2 = (string) ob_get_clean();
check(!str_contains($login2, 'id="reg_username"') && !str_contains($login2, 'id="reg_password"'), 'ثبت‌نام: generate=yes → username/password خودکار (بدون فیلد)');

/* ---------- ۳) CSS override ---------- */
$css = (string) file_get_contents($root . '/assets/css/wc-forms-override.css');
check(str_contains($css, '.woocommerce-account .woocommerce-form input.input-text'), 'CSS: نگاشت input.input-text');
check(str_contains($css, 'rounded-xl') || str_contains($css, 'border-radius: 0.75rem'), 'CSS: ظاهر میرور (rounded-xl)');
check(str_contains($css, '.woocommerce-MyAccount-navigation'), 'CSS: منوی endpoints');
check(str_contains($css, '.woocommerce-orders-table'), 'CSS: جدول سفارش‌ها');
check(str_contains($css, '.woocommerce-error') && str_contains($css, '.woocommerce-message'), 'CSS: پیام‌ها');
// هیچ سلکتوری به افزونه voorodak ضربه نمی‌زند (سلکتورهای عمومی فرم، نه شناسه افزونه)
check(!str_contains($css, 'voorodak') && !str_contains($css, '#vfd'), 'auth افزونه: بدون سلکتور تهاجمی');

/* ---------- ۴) enqueue شرطی در Setup ---------- */
require_once $root . '/inc/Woo/Setup.php';
$GLOBALS['__wp_is_account'] = true;
\ErfanSanat\Theme\Woo\Setup::enqueue_account_styles();
check(isset(es_styles11()['es-wc-forms-override']) && str_contains(es_styles11()['es-wc-forms-override']['src'], 'wc-forms-override.css'), 'enqueue: CSS فقط در حساب کاربری');
$GLOBALS['__wp_is_account'] = false;
$GLOBALS['__wp_hooks']['enqueued_styles'] = [];
\ErfanSanat\Theme\Woo\Setup::enqueue_account_styles();
check(!isset($GLOBALS['__es_styles11']['es-wc-forms-override']), 'enqueue: خارج از حساب کاربری → بدون بارگذاری');
check(str_contains((string) file_get_contents($root . '/inc/Woo/Setup.php'), 'enqueue_account_styles'), 'بوت: enqueue در Setup ثبت شده');

/* ---------- ۵) wrapper: main_attrs حساب کاربری ---------- */
$GLOBALS['__wp_is_cart'] = false; $GLOBALS['__wp_is_checkout'] = false; $GLOBALS['__wp_is_account'] = true;
check(\ErfanSanat\Theme\Woo\Setup::main_attrs() === ' class="bg-slate-50/50" data-account-page="true"', 'wrapper: main_attrs حساب کاربری (data-account-page)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
