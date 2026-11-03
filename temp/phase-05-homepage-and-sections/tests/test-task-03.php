<?php
/**
 * Test 5.3 — سکشن گالری پروژه‌ها (projects)
 * فاز: 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
 * اجرا: /home/user/tools/php -f phase-05-homepage-and-sections/tests/test-task-03.php
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

echo "== Test 5.3: سکشن گالری پروژه‌ها (projects) ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'کوئری args صحیح (تست)');
// check(<expr>, 'سکشن خالی رندر نشود');
// check(<expr>, 'کارت‌ها از card-project استفاده کنند');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 5.3)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
