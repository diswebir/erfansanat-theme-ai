<?php
/**
 * Test 6.8 — taxonomy-product_cat (+FAQ دسته) و taxonomy-product_tag
 * فاز: 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
 * اجرا: /home/user/tools/php -f phase-06-woocommerce-and-products/tests/test-task-08.php
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

echo "== Test 6.8: taxonomy-product_cat (+FAQ دسته) و taxonomy-product_tag ==\n";

/* گام WRITE TEST — شروط Pass Conditions این تسک:
// check(<expr>, 'رندر ترم fixture با thumbnail fallback');
// check(<expr>, 'FAQ ترم آکاردئونی');
// check(<expr>, 'breadcrumb صحیح');
// check(<expr>, 'صفحه‌بندی کار کند');
*/

check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (گام WRITE TEST تسک 6.8)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit((empty($failures) && $passes > 0) ? 0 : 1);
