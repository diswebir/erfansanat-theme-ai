<?php
/**
 * Test — Task 6.18: گیت فاز ۶: تست سرتاسری فروشگاه
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-18.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.18: گیت فاز ۶: تست سرتاسری فروشگاه ==\n";
echo "   phase 6 | size S | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// اجرای فاز
check('اجرای فاز', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// چرخه خرید
check('چرخه خرید', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون Warning
check('بدون Warning', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
