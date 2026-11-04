<?php
/**
 * Test 3.14 — ViewCounter: کلید number2-viewc + fallback number-viewc + cookie dedupe + رد ادمین/بات/پیش‌نمایش/فید
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-14.php
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
echo "== Test 3.14: ViewCounter ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Frontend\ViewCounter;

// هوک wp ثبت شده
do_action('init');
check(isset($GLOBALS['__wp_hooks']['actions']['wp']), 'هوک wp ثبت شد');

// ---- شمارش عادی ----
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64) Chrome/120';
$_COOKIE = [];
$GLOBALS['__wp_is_admin'] = false;
$GLOBALS['__es_stub_current_post_type'] = 'product';
$GLOBALS['__es_stub_is_preview'] = false;
$GLOBALS['__es_stub_is_feed'] = false;
$GLOBALS['__es_stub_queried_object_id'] = 300;
$GLOBALS['__wp_post_meta'][300] = [];

do_action('wp');
check(($GLOBALS['__wp_post_meta'][300]['number2-viewc'] ?? '0') === '1', 'اولین بازدید → number2-viewc=1');

// cookie dedupe
$_COOKIE['es_vc_300'] = '1';
do_action('wp');
check(($GLOBALS['__wp_post_meta'][300]['number2-viewc'] ?? '0') === '1', 'کوکی موجود → شمارش تکرار نمی‌شود');

// بازدید دوم بدون کوکی
unset($_COOKIE['es_vc_300']);
do_action('wp');
check(($GLOBALS['__wp_post_meta'][300]['number2-viewc'] ?? '0') === '2', 'بازدید دوم → 2');

// ---- fallback عدد قدیمی ----
$GLOBALS['__wp_post_meta'][301] = ['number-viewc' => '7'];
$GLOBALS['__es_stub_queried_object_id'] = 301;
do_action('wp');
check(ViewCounter::get_views(301) === 8, 'fallback number-viewc=7 → شمارش از ۸');
check(isset($GLOBALS['__wp_post_meta'][301]['number2-viewc']), 'کلید جدید number2-viewc نوشته شد');

// ---- رد: ادمین ----
$GLOBALS['__wp_is_admin'] = true;
$GLOBALS['__es_stub_queried_object_id'] = 302;
$GLOBALS['__wp_post_meta'][302] = [];
do_action('wp');
check(empty($GLOBALS['__wp_post_meta'][302]), 'ادمین → شمرده نمی‌شود');
$GLOBALS['__wp_is_admin'] = false;

// ---- رد: بات ----
$_SERVER['HTTP_USER_AGENT'] = 'Googlebot/2.1 (+http://www.google.com/bot.html)';
$GLOBALS['__es_stub_queried_object_id'] = 303;
$GLOBALS['__wp_post_meta'][303] = [];
do_action('wp');
check(empty($GLOBALS['__wp_post_meta'][303]), 'بات → شمرده نمی‌شود');
check(ViewCounter::is_bot() === true, 'is_bot: Googlebot');

// بدون UA
unset($_SERVER['HTTP_USER_AGENT']);
check(ViewCounter::is_bot() === true, 'is_bot: بدون UA');

// ---- رد: پیش‌نمایش و فید ----
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 Chrome';
$GLOBALS['__es_stub_is_preview'] = true;
$GLOBALS['__es_stub_queried_object_id'] = 304;
$GLOBALS['__wp_post_meta'][304] = [];
do_action('wp');
check(empty($GLOBALS['__wp_post_meta'][304]), 'پیش‌نمایش → شمرده نمی‌شود');
$GLOBALS['__es_stub_is_preview'] = false;

$GLOBALS['__es_stub_is_feed'] = true;
$GLOBALS['__es_stub_queried_object_id'] = 305;
$GLOBALS['__wp_post_meta'][305] = [];
do_action('wp');
check(empty($GLOBALS['__wp_post_meta'][305]), 'فید → شمرده نمی‌شود');
$GLOBALS['__es_stub_is_feed'] = false;

// ---- پست‌تایپ خارج لیست ----
$GLOBALS['__es_stub_current_post_type'] = 'page';
$GLOBALS['__es_stub_queried_object_id'] = 306;
$GLOBALS['__wp_post_meta'][306] = [];
do_action('wp');
check(empty($GLOBALS['__wp_post_meta'][306]), 'page خارج لیست → شمرده نمی‌شود');

// ---- ستون بازدید ----
$cols = ViewCounter::columns(['title' => 'نام']);
check(isset($cols['es-views']), 'ستون بازدید محصولات');
ob_start();
ViewCounter::column_content('es-views', 300);
check(str_contains((string) ob_get_clean(), '2'), 'محتوای ستون بازدید');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
