<?php
/**
 * Test — Task 2.10: تب «اسکریپت‌ها و ابزار» (هدر/فوتر/کدهای سفارشی)
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-10.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.10: تب «اسکریپت‌ها و ابزار» (هدر/فوتر/کدهای سفارشی) ==\n";
echo "   phase 2 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// خروجی خالی
check('خروجی خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// محل تزریق
check('محل تزریق', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// دسترسی
check('دسترسی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
