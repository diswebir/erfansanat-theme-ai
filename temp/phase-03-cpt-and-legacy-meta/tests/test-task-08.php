<?php
/**
 * Test — Task 3.8: `MetaRepository` + `RepeaterCodec` (سازگاری دوطرفه JetEngine)
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-08.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.8: `MetaRepository` + `RepeaterCodec` (سازگاری دوطرفه JetEngine) ==\n";
echo "   phase 3 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// round-trip
check('round-trip', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// فرمت تک‌سطری
check('فرمت تک‌سطری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// CSV
check('CSV', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// کش متا
check('کش متا', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
