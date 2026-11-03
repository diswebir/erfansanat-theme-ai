<?php
/**
 * Test — Task 8.4: فیلتر ایجکس پروژه‌ها (نوع/شهر + جستجو + مرتب‌سازی)
 * Phase 8: جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-08-ajax-filters-and-audit/tests/test-task-04.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 8.4: فیلتر ایجکس پروژه‌ها (نوع/شهر + جستجو + مرتب‌سازی) ==\n";
echo "   phase 8 | size L | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// ترکیب
check('ترکیب', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// شمارنده
check('شمارنده', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون JS
check('بدون JS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
