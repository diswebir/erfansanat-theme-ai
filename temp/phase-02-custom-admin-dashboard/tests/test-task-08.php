<?php
/**
 * Test — Task 2.8: تب «برند و رنگ» با متغیرهای CSS
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-08.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.8: تب «برند و رنگ» با متغیرهای CSS ==\n";
echo "   phase 2 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// عدم خروجی پیش‌فرض
check('عدم خروجی پیش‌فرض', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// رد رنگ نامعتبر
check('رد رنگ نامعتبر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نبود !important
check('نبود !important', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
