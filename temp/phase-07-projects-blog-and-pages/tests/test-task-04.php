<?php
/**
 * Test — Task 7.4: پروژه‌های شاخص مرتبط (سایر پروژه‌های هم‌نوع)
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-04.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.4: پروژه‌های شاخص مرتبط (سایر پروژه‌های هم‌نوع) ==\n";
echo "   phase 7 | size M | files: 1 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// عدم نمایش جاری
check('عدم نمایش جاری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اولویت
check('اولویت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف خالی
check('حذف خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
