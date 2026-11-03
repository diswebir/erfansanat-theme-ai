<?php
/**
 * Test — Task 3.6: ستون‌های ادمین + فیلترهای دراپ‌داون پروژه‌ها
 * Phase 3: معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-03-cpt-and-legacy-meta/tests/test-task-06.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 3.6: ستون‌های ادمین + فیلترهای دراپ‌داون پروژه‌ها ==\n";
echo "   phase 3 | size M | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// ستون‌ها
check('ستون‌ها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// فیلتر
check('فیلتر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// مرتب‌سازی
check('مرتب‌سازی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// صفحه‌بندی
check('صفحه‌بندی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
