<?php
/**
 * Test — Task 1.8: انتقال دارایی‌های تصویری + ویدیو + پیش‌compress
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-08.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.8: انتقال دارایی‌های تصویری + ویدیو + پیش‌compress ==\n";
echo "   phase 1 | size L | files: 5 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// سلامت فایل‌ها
check('سلامت فایل‌ها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نسبت حجم WebP
check('نسبت حجم WebP', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// وجود پوستر
check('وجود پوستر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نگاشت مسیرها
check('نگاشت مسیرها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
