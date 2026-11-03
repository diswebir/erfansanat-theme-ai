<?php
/**
 * Test 6.5 — single-product — ناحیه خرید: true_sale، تعداد (min/step)، سوئچ‌ها و toast
 * فاز: 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
 * اجرا: /home/user/tools/php -f phase-06-woocommerce-and-products/tests/test-task-05.php
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

echo "== Test 6.5: single-product — ناحیه خرید: true_sale، تعداد (min/step)، سوئچ‌ها و toast ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'وضعیت true با fixture: فرم add-to-cart استاندارد + کلاس‌های میرور');
// check(<expr>, 'وضعیت false: دکمه استعلام + is_purchasable فیلترشده');
// check(<expr>, 'qty اتریبیوت‌های min/step مطابق میرور');
// check(<expr>, 'toast hook حفظ شده');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 6.5)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
