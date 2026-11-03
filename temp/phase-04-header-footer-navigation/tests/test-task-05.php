<?php
/**
 * Test — Task 4.5: Walker مگامنو پروژه‌ها (تب نوع/شهر)
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-05.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.5: Walker مگامنو پروژه‌ها (تب نوع/شهر) ==\n";
echo "   phase 4 | size L | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// پوشش ترم‌ها
check('پوشش ترم‌ها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// لینک صحیح
check('لینک صحیح', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
