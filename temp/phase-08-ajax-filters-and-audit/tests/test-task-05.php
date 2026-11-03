<?php
/**
 * Test — Task 8.5: جدول فرم‌ها + `FormRepository` + migration نسخه‌دار
 * Phase 8: جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-08-ajax-filters-and-audit/tests/test-task-05.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 8.5: جدول فرم‌ها + `FormRepository` + migration نسخه‌دار ==\n";
echo "   phase 8 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// ساخت جدول
check('ساخت جدول', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اسکیما
check('اسکیما', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// محافظت آپلود
check('محافظت آپلود', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// migration یک‌بار
check('migration یک‌بار', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
