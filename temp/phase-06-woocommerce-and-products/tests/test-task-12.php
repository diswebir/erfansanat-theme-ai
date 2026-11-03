<?php
/**
 * Test — Task 6.12: آرشیو فروشگاه و دسته‌ها (`archive-product.php`, `taxonomy-product_cat.php`)
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-12.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.12: آرشیو فروشگاه و دسته‌ها (`archive-product.php`, `taxonomy-product_cat.php`) ==\n";
echo "   phase 6 | size L | files: 4 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// رندر دو آرشیو
check('رندر دو آرشیو', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// صفحه‌بندی
check('صفحه‌بندی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// FAQ دسته
check('FAQ دسته', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
