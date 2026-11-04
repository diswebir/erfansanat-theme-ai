<?php
/**
 * Test — Task 8.8: فرم استخدام با آپلود رزومه
 * Phase 8: جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-08-ajax-filters-and-audit/tests/test-task-08.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 8.8: فرم استخدام با آپلود رزومه ==\n";
echo "   phase 8 | size M | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// پذیرش PDF
check('پذیرش PDF', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// رد فایل مخرب
check('رد فایل مخرب', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// محدودیت حجم
check('محدودیت حجم', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نام‌گذاری
check('نام‌گذاری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
