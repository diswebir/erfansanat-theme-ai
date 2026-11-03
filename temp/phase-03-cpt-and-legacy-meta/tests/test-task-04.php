<?php
/**
 * Test — Task 3.4: تصحیح Rewriteهای ووکامرس (`product_cat`, `product-tag`, سینگل)
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-04.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.4: تصحیح Rewriteهای ووکامرس (`product_cat`, `product-tag`, سینگل) ==\n";
echo "   phase 3 | size L | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// لینک‌های سه‌گانه
check('لینک‌های سه‌گانه', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نبود flush در مسیر عادی
check('نبود flush در مسیر عادی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
