<?php
/**
 * Test 1.5 — DeBloat — ایموجی، wp_head cleanup و حذف متاهای زائد
 * فاز: 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
 * اجرا: /home/user/tools/php -f phase-01-core-and-assets/tests/test-task-05.php
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

echo "== Test 1.5: DeBloat — ایموجی، wp_head cleanup و حذف متاهای زائد ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'پاس شدن php -l');
// check(<expr>, 'لیست کامل remove_actionها در کد موجود باشد (emoji, rsd, wlw, generator, shortlink, rest, oembed, adjacent)');
// check(<expr>, 'هیچ remove_action بدون گارد is_admin نباشد (به‌جز emoji که سراسری امن است)');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 1.5)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
