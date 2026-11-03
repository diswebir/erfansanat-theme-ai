<?php
/**
 * Test — Task 1.4: `functions.php` سبک + `index.php` + `screenshot.png`
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-04.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.4: `functions.php` سبک + `index.php` + `screenshot.png` ==\n";
echo "   phase 1 | size S | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// شمارش خطوط
check('شمارش خطوط', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// فراخوانی هدر/فوتر
check('فراخوانی هدر/فوتر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ابعاد screenshot
check('ابعاد screenshot', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
