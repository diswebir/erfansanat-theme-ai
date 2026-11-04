<?php
/**
 * Test — Task 6.4: جعبه خرید: قیمت، وضعیت `true_sale`، افزودن به سبد
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-04.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.4: جعبه خرید: قیمت، وضعیت `true_sale`، افزودن به سبد ==\n";
echo "   phase 6 | size L | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// دو حالت
check('دو حالت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// متغیر + Swatches
check('متغیر + Swatches', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اطلاعیه شرطی
check('اطلاعیه شرطی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون فرم در حالت استعلام
check('بدون فرم در حالت استعلام', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
