<?php
/**
 * Test 7.4 — home.php / archive.php / category.php — وبلاگ و فیلتر دسته
 * فاز: 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
 * اجرا: /home/user/tools/php -f phase-07-projects-blog-and-pages/tests/test-task-04.php
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

echo "== Test 7.4: home.php / archive.php / category.php — وبلاگ و فیلتر دسته ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'فیلتر cat با GET کار کند');
// check(<expr>, 'pagination و شمارنده');
// check(<expr>, 'حالت خالی');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 7.4)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
