<?php
/**
 * Test — Task 3.2: ثبت تکسونومی `project_type`
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.2: ثبت تکسونومی `project_type` ==\n";
echo "   phase 3 | size M | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// ثبت تکسونومی
check('ثبت تکسونومی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// لینک ترم
check('لینک ترم', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// آرشیو بدون 404
check('آرشیو بدون 404', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
