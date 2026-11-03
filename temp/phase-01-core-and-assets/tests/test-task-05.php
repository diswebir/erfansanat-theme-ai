<?php
/**
 * Test — Task 1.5: هارنس رندر کامل قالب (`tools/harness/render.php`)
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-05.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.5: هارنس رندر کامل قالب (`tools/harness/render.php`) ==\n";
echo "   phase 1 | size M | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// رندر صفحه اصلی
check('رندر صفحه اصلی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// گزارش کوئری
check('گزارش کوئری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// چند مسیر در یک اجرا
check('چند مسیر در یک اجرا', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// گزارش Warning
check('گزارش Warning', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
