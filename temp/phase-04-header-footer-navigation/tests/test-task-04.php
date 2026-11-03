<?php
/**
 * Test — Task 4.4: Walker مگامنو محصولات (تب‌ها + ستون‌ها)
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-04.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.4: Walker مگامنو محصولات (تب‌ها + ستون‌ها) ==\n";
echo "   phase 4 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// انطباق مارک‌آپ
check('انطباق مارک‌آپ', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// کارکرد JS
check('کارکرد JS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// fallback
check('fallback', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// شمارش کوئری
check('شمارش کوئری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
