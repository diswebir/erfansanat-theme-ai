<?php
/**
 * Test — Task 3.9: متاباکس «اطلاعات محصولات» — تب شناسه‌ها و عناوین
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-09.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.9: متاباکس «اطلاعات محصولات» — تب شناسه‌ها و عناوین ==\n";
echo "   phase 3 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// CRUD کلیدها
check('CRUD کلیدها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// املای کلید
check('املای کلید', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// مقدار true_sale
check('مقدار true_sale', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// nonce
check('nonce', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
