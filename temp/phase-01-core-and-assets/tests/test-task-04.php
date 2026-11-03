<?php
/**
 * Test 1.4 — DeBloat — حذف کامل CSS/JS گوتنبرگ از فرانت‌اند
 * فاز: 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
 * اجرا: /home/user/tools/php -f phase-01-core-and-assets/tests/test-task-04.php
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

echo "== Test 1.4: DeBloat — حذف کامل CSS/JS گوتنبرگ از فرانت‌اند ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'پاس شدن php -l');
// check(<expr>, 'در simulate خروجی wp_enqueue_scripts فرانت هیچ هندل block-library/global-styles/classic-theme-styles ثبت نشده باشد');
// check(<expr>, 'کد حذف شرطی به is_admin باشد');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 1.4)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
