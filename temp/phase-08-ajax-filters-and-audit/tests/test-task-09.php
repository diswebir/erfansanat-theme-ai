<?php
/**
 * Test — Task 8.9: فرم بازخورد مشتریان (`client-feedback`)
 * Phase 8: جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-08-ajax-filters-and-audit/tests/test-task-09.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 8.9: فرم بازخورد مشتریان (`client-feedback`) ==\n";
echo "   phase 8 | size M | files: 1 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// بازه امتیاز
check('بازه امتیاز', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نوع ذخیره
check('نوع ذخیره', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
