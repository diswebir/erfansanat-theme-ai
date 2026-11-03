<?php
/**
 * Test — Task 3.12: Repeaterهای محصول: `product-feature`, `product-feature2_copy`, `usage_product`, `faq`
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-12.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.12: Repeaterهای محصول: `product-feature`, `product-feature2_copy`, `usage_product`, `faq` ==\n";
echo "   phase 3 | size L | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// CRUD چهارگانه
check('CRUD چهارگانه', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// املای کلید
check('املای کلید', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نمونه JetEngine
check('نمونه JetEngine', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// سطر خالی
check('سطر خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
