<?php
/**
 * Test 3.13 — متای ترم product_cat — تصویر دسته (thumbnail) و FAQ دسته
 * فاز: 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 * اجرا: /home/user/tools/php -f phase-03-cpt-and-legacy-meta/tests/test-task-13.php
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

echo "== Test 3.13: متای ترم product_cat — تصویر دسته (thumbnail) و FAQ دسته ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'roundtrip thumbnail و faq روی ترم fixture');
// check(<expr>, 'فرم رندر هر دو محل (add/edit)');
// check(<expr>, 'helpers مقدار صحیح برگردانند');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 3.13)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
