<?php
/**
 * Test — Task 7.18: برگه «مجوزها و افتخارات» (`template-licenses.php`)
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-18.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.18: برگه «مجوزها و افتخارات» (`template-licenses.php`) ==\n";
echo "   phase 7 | size M | files: 2 | pass conditions: 2\n";

// --- Pass Conditions (2 شرط) -----------------------------------
// گالری
check('گالری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف خالی
check('حذف خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
