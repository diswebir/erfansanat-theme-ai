<?php
/**
 * Test 1.2 — Autoloader PSR-4 با Namespace ErfanSanat\Theme و هلپرهای پایه
 * فاز: 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
 * اجرا: /home/user/tools/php -f phase-01-core-and-assets/tests/test-task-02.php
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

echo "== Test 1.2: Autoloader PSR-4 با Namespace ErfanSanat\Theme و هلپرهای پایه ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'php -l هر دو فایل پاس شود');
// check(<expr>, 'اتولودر یک کلاس fixture داخل inc/ را بدون require دستی resolve کند (تست با فایل فرضی)');
// check(<expr>, 'es_option در حالت نبودِ آپشن، مقدار default را برگرداند');
// check(<expr>, 'es_get_template مسیر فایل موجود را resolve کند و extract امن args انجام شود');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 1.2)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
