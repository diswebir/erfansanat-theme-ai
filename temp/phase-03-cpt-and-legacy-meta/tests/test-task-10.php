<?php
/**
 * Test — Task 3.10: تب فایل و گالری محصول + همگام‌سازی
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-10.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.10: تب فایل و گالری محصول + همگام‌سازی ==\n";
echo "   phase 3 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// همگام‌سازی
check('همگام‌سازی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// fallback قدیمی
check('fallback قدیمی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اعتبار کاتالوگ
check('اعتبار کاتالوگ', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
