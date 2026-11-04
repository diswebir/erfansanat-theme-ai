<?php
/**
 * Test 7.6 — search.php و 404.php
 * فاز: 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
 * اجرا: /home/user/tools/php -f phase-07-projects-blog-and-pages/tests/test-task-06.php
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

echo "== Test 7.6: search.php و 404.php ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'نتایج با کارت درست هر نوع');
// check(<expr>, 'فیلتر type کار کند');
// check(<expr>, '404 رندر و فرم کار کند');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 7.6)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
