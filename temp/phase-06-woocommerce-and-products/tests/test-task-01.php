<?php
/**
 * Test 06.01 — قالب تک‌محصول
 * فاز: phase-06-woocommerce-and-products
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-01.php
 *
 * اسکلت تست — در گام WRITE TEST تسک کامل می‌شود.
 * توجه (D-001): PHP 8.4 wasm — کل تست‌های تسک در همین یک فایل اجرا شود.
 */
declare(strict_types=1);

error_reporting(E_ALL);
$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else { $failures[] = $label; echo "  FAIL  $label\n"; }
}

echo "== Test 06.01: قالب تک‌محصول ==\n";

// TODO(test): در گام WRITE TEST، شروط Pass Conditions بالا به check() نگاشت می‌شوند.
check(false, 'PLACEHOLDER — این تست هنوز پیاده‌سازی نشده است (در گام WRITE TEST تکمیل شود)');

echo "\nResult: {$passes} passed, " . count($failures) . " failed\n";
exit(empty($failures) && $passes > 0 ? 0 : 1);
