<?php
/**
 * Test — Task 4.12: گیت فاز ۴: مقایسه DOM هدر/فوتر با مرجع
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-12.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.12: گیت فاز ۴: مقایسه DOM هدر/فوتر با مرجع ==\n";
echo "   phase 4 | size S | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// درصد انطباق
check('درصد انطباق', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اجرای فاز
check('اجرای فاز', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون Warning
check('بدون Warning', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
