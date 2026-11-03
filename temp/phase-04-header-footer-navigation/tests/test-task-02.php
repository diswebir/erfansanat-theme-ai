<?php
/**
 * Test 4.2 — header.php — shell سند، hreflang و فراخوانی‌های استاندارد وردپرس
 * فاز: 04 — هدر، مگامنو، منوی موبایل و فوتر
 * اجرا: /home/user/tools/php -f phase-04-header-footer-navigation/tests/test-task-02.php
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

echo "== Test 4.2: header.php — shell سند، hreflang و فراخوانی‌های استاندارد وردپرس ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'پاس شدن php -l');
// check(<expr>, 'سه فراخوانی language_attributes/body_class/wp_body_open و wp_head موجود');
// check(<expr>, 'تگ link hreflang فقط برای متاهای پر تولید شود (تست شرطی)');
// check(<expr>, 'فیلتر غیرفعال‌سازی داخلی موجود باشد');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 4.2)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
