<?php
/**
 * Test — Task 1.10: Preload/Preconnect فونت و LCP (شرطی)
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-10.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.10: Preload/Preconnect فونت و LCP (شرطی) ==\n";
echo "   phase 1 | size M | files: 1 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// preload شرطی
check('preload شرطی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم تکرار
check('عدم تکرار', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اعتبار HTML
check('اعتبار HTML', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
