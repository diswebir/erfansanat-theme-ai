<?php
/**
 * Test — Task 4.3: `footer.php` + بازگشت به بالا + اطلاعات تماس
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-03.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.3: `footer.php` + بازگشت به بالا + اطلاعات تماس ==\n";
echo "   phase 4 | size M | files: 4 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// diff فوتر
check('diff فوتر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// لینک‌های صحیح
check('لینک‌های صحیح', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// وجود wp_footer
check('وجود wp_footer', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
