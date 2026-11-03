<?php
/**
 * Test — Task 8.1: اندپوینت جستجوی زنده (`SearchEndpoint`)
 * Phase 8: جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-08-ajax-filters-and-audit/tests/test-task-01.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 8.1: اندپوینت جستجوی زنده (`SearchEndpoint`) ==\n";
echo "   phase 8 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// زمان پاسخ
check('زمان پاسخ', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// تست SQLi/XSS
check('تست SQLi/XSS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم افشای پیش‌نویس
check('عدم افشای پیش‌نویس', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// nonce
check('nonce', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
