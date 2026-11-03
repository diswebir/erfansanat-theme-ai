<?php
/**
 * Test — Task 1.1: هدر قالب در `style.css` و متادیتای نسخه
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-01.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.1: هدر قالب در `style.css` و متادیتای نسخه ==\n";
echo "   phase 1 | size S | files: 2 | pass conditions: 5\n";

// --- Pass Conditions (5 شرط) -----------------------------------
// پارس هدر قالب
check('پارس هدر قالب', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// همگامی نسخه
check('همگامی نسخه', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نبود قاعده CSS
check('نبود قاعده CSS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// گارد ABSPATH
check('گارد ABSPATH', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
