<?php
/**
 * Test — Task 6.10: تب‌های صفحه محصول (`data-prod-tab-*`) و نظرات
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-10.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.10: تب‌های صفحه محصول (`data-prod-tab-*`) و نظرات ==\n";
echo "   phase 6 | size L | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// سوییچ تب
check('سوییچ تب', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ثبت نظر
check('ثبت نظر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// aria
check('aria', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
