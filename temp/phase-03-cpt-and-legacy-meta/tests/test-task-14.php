<?php
/**
 * Test — Task 3.14: متاباکس پروژه (`second-image` تا `six-image`, تاریخ، محصولات مرتبط)
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-14.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.14: متاباکس پروژه (`second-image` تا `six-image`, تاریخ، محصولات مرتبط) ==\n";
echo "   phase 3 | size L | files: 1 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// ۵ کلید تصویر
check('۵ کلید تصویر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اعتبار تاریخ
check('اعتبار تاریخ', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// سازگاری محصولات مرتبط
check('سازگاری محصولات مرتبط', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
