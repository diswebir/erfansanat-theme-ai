<?php
/**
 * Test — Task 6.1: راه‌اندازی پوشه `woocommerce/` و `declare_compatibility`
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-01.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.1: راه‌اندازی پوشه `woocommerce/` و `declare_compatibility` ==\n";
echo "   phase 6 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// اعلام سازگاری
check('اعلام سازگاری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ایمنی بدون ووکامرس
check('ایمنی بدون ووکامرس', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون قالب قدیمی
check('بدون قالب قدیمی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
