<?php
/**
 * Test — Task 1.11: ثبت جایگاه‌های منو و `load_theme_textdomain`
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-11.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.11: ثبت جایگاه‌های منو و `load_theme_textdomain` ==\n";
echo "   phase 1 | size S | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// ثبت جایگاه‌ها
check('ثبت جایگاه‌ها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نام‌های فارسی
check('نام‌های فارسی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ایمنی در نبود منو
check('ایمنی در نبود منو', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
