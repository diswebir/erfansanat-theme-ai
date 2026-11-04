<?php
/**
 * Test — Task 6.13: آرشیو برچسب و برند (`taxonomy-product_tag.php`, `taxonomy-product_brand.php`)
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-13.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.13: آرشیو برچسب و برند (`taxonomy-product_tag.php`, `taxonomy-product_brand.php`) ==\n";
echo "   phase 6 | size M | files: 2 | pass conditions: 2\n";

// --- Pass Conditions (2 شرط) -----------------------------------
// رندر
check('رندر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
