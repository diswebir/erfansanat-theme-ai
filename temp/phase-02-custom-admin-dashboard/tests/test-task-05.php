<?php
/**
 * Test 2.5 — تب «برند/استایل» + «اسکریپت‌های سفارشی» + «تنظیمات آرشیوها»
 * فاز: 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 * اجرا: /home/user/tools/php -f phase-02-custom-admin-dashboard/tests/test-task-05.php
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

echo "== Test 2.5: تب «برند/استایل» + «اسکریپت‌های سفارشی» + «تنظیمات آرشیوها» ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'پاس شدن php -l');
// check(<expr>, 'per_page پیش‌فرض محصولات = ۲۰');
// check(<expr>, 'اسکریپت سفارشی در head/footer دقیقاً چاپ شود (تست رشته)');
// check(<expr>, 'CSS variable برند فقط در صورت مقدار متفاوت تزریق شود');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 2.5)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
