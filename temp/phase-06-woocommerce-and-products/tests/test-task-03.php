<?php
/**
 * Test — Task 6.3: گالری محصول + لایت‌باکس
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-03.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.3: گالری محصول + لایت‌باکس ==\n";
echo "   phase 6 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// fallback زنجیره‌ای
check('fallback زنجیره‌ای', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// لایت‌باکس
check('لایت‌باکس', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون گالری
check('بدون گالری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// lazy
check('lazy', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
