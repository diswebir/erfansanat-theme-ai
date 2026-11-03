<?php
/**
 * Test 3.11 — متاباکس پروژه — گالری شش‌تایی قدیمی + گالری مدرن همگام
 * فاز: 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 * اجرا: /home/user/tools/php -f phase-03-cpt-and-legacy-meta/tests/test-task-11.php
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

echo "== Test 3.11: متاباکس پروژه — گالری شش‌تایی قدیمی + گالری مدرن همگام ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'roundtrip هر ۵ کلید قدیمی');
// check(<expr>, 'همگام‌سازی ۵ آیدی مدرن → کلیدهای قدیمی با ترتیب صحیح (تست آرایه)');
// check(<expr>, 'es_get_project_gallery dedup و ترتیب درست');
// check(<expr>, 'مقدار URL قدیمی به آیدی تبدیل شود (fixture)');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 3.11)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
