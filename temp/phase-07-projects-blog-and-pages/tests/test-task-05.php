<?php
/**
 * Test — Task 7.5: آرشیو پروژه‌ها (`archive-projects.php`)
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-05.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.5: آرشیو پروژه‌ها (`archive-projects.php`) ==\n";
echo "   phase 7 | size L | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// رندر
check('رندر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// شمارنده
check('شمارنده', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// فیلتر JS
check('فیلتر JS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// صفحه‌بندی
check('صفحه‌بندی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
