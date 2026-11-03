<?php
/**
 * Test — Task 7.6: آرشیو نوع پروژه (`taxonomy-project_type.php`)
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-06.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.6: آرشیو نوع پروژه (`taxonomy-project_type.php`) ==\n";
echo "   phase 7 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// رندر انواع
check('رندر انواع', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// kses
check('kses', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
