<?php
/**
 * Test — Task 6.6: Repeaterهای محصول در فرانت (مشخصات، ویژگی‌های کلیدی، کاربردها، FAQ)
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-06.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.6: Repeaterهای محصول در فرانت (مشخصات، ویژگی‌های کلیدی، کاربردها، FAQ) ==\n";
echo "   phase 6 | size L | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// رندر چهار Repeater
check('رندر چهار Repeater', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// آکاردئون
check('آکاردئون', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف خالی
check('حذف خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// kses
check('kses', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
