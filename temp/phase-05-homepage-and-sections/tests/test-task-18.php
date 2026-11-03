<?php
/**
 * Test — Task 5.18: کارت‌های استاندارد: `card-product`, `card-project`, `card-post`
 * Phase 5: صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-05-homepage-and-sections/tests/test-task-18.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 5.18: کارت‌های استاندارد: `card-product`, `card-project`, `card-post` ==\n";
echo "   phase 5 | size L | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// انطباق کارت
check('انطباق کارت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// پر بودن هوک‌ها
check('پر بودن هوک‌ها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// سه مصرف‌کننده
check('سه مصرف‌کننده', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت بدون تصویر
check('حالت بدون تصویر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
