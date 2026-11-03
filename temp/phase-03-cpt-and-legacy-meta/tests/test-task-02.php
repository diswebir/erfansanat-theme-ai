<?php
/**
 * Test 3.2 — فیلترهای ادمین لیست پروژه‌ها (دراپ‌داون city/project_type + ستون same_key_hrefl)
 * فاز: 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 * اجرا: /home/user/tools/php -f phase-03-cpt-and-legacy-meta/tests/test-task-02.php
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

echo "== Test 3.2: فیلترهای ادمین لیست پروژه‌ها (دراپ‌داون city/project_type + ستون same_key_hrefl) ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'پاس شدن php -l');
// check(<expr>, 'دو دراپ‌داون تکسونومی رندر شود');
// check(<expr>, 'parse_query فیلترها را به tax_query تبدیل کند (تست args)');
// check(<expr>, 'ستون same_key_hrefl sortable باشد');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 3.2)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
