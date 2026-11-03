<?php
/**
 * Test 1.1 — اسکلت پایه قالب و بوت‌استرپ (style.css / functions.php / index.php)
 * فاز: 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
 * اجرا: /home/user/tools/php -f phase-01-core-and-assets/tests/test-task-01.php
 *
 * نحوه کار: PHP 8.4 wasm بدون وردپرس اجرا می‌شود؛ برای تست کلاس‌هایی که به توابع
 * وردپرس نیاز دارند ابتدا stubها را require کنید:
 *   require '/home/user/erfansanat-theme-ai/temp/harness/wp-stubs.php';
 * (بعد از Task 1.8) — رفتار stubها مستند: temp/harness/README.md
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

echo "== Test 1.1: اسکلت پایه قالب و بوت‌استرپ (style.css / functions.php / index.php) ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'php -l هر سه فایل پاس شود');
// check(<expr>, 'هدر style.css شامل Theme Name و Text Domain: erfansanat باشد');
// check(<expr>, 'گارد ABSPATH در functions.php و index.php وجود داشته باشد');
// check(<expr>, 'ثابت‌های ERFANSANAT_VERSION / ERFANSANAT_DIR / ERFANSANAT_URI تعریف شده باشند');
// check(<expr>, 'index.php شامل get_header() و get_footer() باشد');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 1.1)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
