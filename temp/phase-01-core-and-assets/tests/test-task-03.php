<?php
/**
 * Test — Task 1.3: Kernel قالب و ثبت `add_theme_support`ها
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-03.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.3: Kernel قالب و ثبت `add_theme_support`ها ==\n";
echo "   phase 1 | size M | files: 3 | pass conditions: 5\n";

// --- Pass Conditions (5 شرط) -----------------------------------
// theme supports
check('theme supports', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// رفتار بدون ووکامرس
check('رفتار بدون ووکامرس', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم ثبت align-wide
check('عدم ثبت align-wide', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ثبت ماژول
check('ثبت ماژول', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
