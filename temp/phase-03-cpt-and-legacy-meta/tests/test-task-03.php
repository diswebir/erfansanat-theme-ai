<?php
/**
 * Test — Task 3.3: ثبت تکسونومی `city`
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.3: ثبت تکسونومی `city` ==\n";
echo "   phase 3 | size M | files: 1 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// ثبت
check('ثبت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// لینک ترم
check('لینک ترم', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ایمنی حذف ترم
check('ایمنی حذف ترم', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
