<?php
/**
 * Test 1.3 — Theme Supports، ثبت ۴ موقعیت منو و هوک‌های فعال‌سازی
 * فاز: 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
 * اجرا: /home/user/tools/php -f phase-01-core-and-assets/tests/test-task-03.php
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

echo "== Test 1.3: Theme Supports، ثبت ۴ موقعیت منو و هوک‌های فعال‌سازی ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'پاس شدن php -l');
// check(<expr>, 'هر ۵ theme support در کد موجود باشد (title-tag, post-thumbnails, html5, custom-logo, woocommerce)');
// check(<expr>, '۴ menu location با کلیدهای دقیق ثبت شده باشند');
// check(<expr>, 'هوک after_switch_theme به flush_rewrite_rules متصل باشد');
// check(<expr>, 'add_image_size برای کارت‌ها ثبت شده باشد');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 1.3)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
