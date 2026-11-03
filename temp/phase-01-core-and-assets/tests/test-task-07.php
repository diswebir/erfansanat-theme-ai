<?php
/**
 * Test — Task 1.7: انتقال بایت‌به‌بایت Assets استاتیک (CSS/JS/فونت/لوگو)
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-07.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.7: انتقال بایت‌به‌بایت Assets استاتیک (CSS/JS/فونت/لوگو) ==\n";
echo "   phase 1 | size L | files: 6 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// مقایسه md5
check('مقایسه md5', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حل مسیر فونت
check('حل مسیر فونت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم تغییر محتوا
check('عدم تغییر محتوا', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
