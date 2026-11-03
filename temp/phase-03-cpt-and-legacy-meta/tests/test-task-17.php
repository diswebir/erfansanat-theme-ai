<?php
/**
 * Test — Task 3.17: شمارنده بازدید `number2-viewc` (با fallback `number-viewc`)
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-17.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.17: شمارنده بازدید `number2-viewc` (با fallback `number-viewc`) ==\n";
echo "   phase 3 | size M | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// یک‌بار در نشست
check('یک‌بار در نشست', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// فیلتر ربات
check('فیلتر ربات', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// کلید نوشتن
check('کلید نوشتن', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// fallback خواندن
check('fallback خواندن', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
