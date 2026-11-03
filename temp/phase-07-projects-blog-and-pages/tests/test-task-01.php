<?php
/**
 * Test — Task 7.1: `single-projects.php`: هیرو + گالری + شناسنامه
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-01.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.1: `single-projects.php`: هیرو + گالری + شناسنامه ==\n";
echo "   phase 7 | size L | files: 4 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// دو حالت گالری
check('دو حالت گالری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// تاریخ
check('تاریخ', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// لینک تاکسونومی
check('لینک تاکسونومی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// h1
check('h1', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
