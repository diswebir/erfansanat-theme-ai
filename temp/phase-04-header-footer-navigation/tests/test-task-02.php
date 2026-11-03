<?php
/**
 * Test — Task 4.2: `header.php`: نوار اعلان + لوگو + نوار ناوبری
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-02.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.2: `header.php`: نوار اعلان + لوگو + نوار ناوبری ==\n";
echo "   phase 4 | size L | files: 4 | pass conditions: 5\n";

// --- Pass Conditions (5 شرط) -----------------------------------
// diff ساختاری
check('diff ساختاری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون لینک html
check('بدون لینک html', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون custom
check('بدون custom', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// toggle نوار
check('toggle نوار', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// aria-label
check('aria-label', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
