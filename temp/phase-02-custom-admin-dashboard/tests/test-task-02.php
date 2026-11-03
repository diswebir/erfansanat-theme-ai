<?php
/**
 * Test — Task 2.2: منوی ادمین سطح‌بالا + پوسته RTL داشبورد
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-02.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.2: منوی ادمین سطح‌بالا + پوسته RTL داشبورد ==\n";
echo "   phase 2 | size L | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// دسترسی‌پذیری
check('دسترسی‌پذیری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// شرطی‌بودن assets
check('شرطی‌بودن assets', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون خطای رندر
check('بدون خطای رندر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
