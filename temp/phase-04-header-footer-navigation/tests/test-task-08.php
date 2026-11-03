<?php
/**
 * Test — Task 4.8: منوی موبایل off-canvas (`aside[data-mobile-sidebar]`)
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-08.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.8: منوی موبایل off-canvas (`aside[data-mobile-sidebar]`) ==\n";
echo "   phase 4 | size M | files: 1 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// کارکرد JS
check('کارکرد JS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// سطوح منو
check('سطوح منو', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// fallback
check('fallback', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
