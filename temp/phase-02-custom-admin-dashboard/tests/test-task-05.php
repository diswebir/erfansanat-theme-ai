<?php
/**
 * Test — Task 2.5: ریفیلد Media Picker (کتابخانه رسانه)
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-05.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.5: ریفیلد Media Picker (کتابخانه رسانه) ==\n";
echo "   phase 2 | size M | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// ذخیره ID
check('ذخیره ID', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// رد ID نامعتبر
check('رد ID نامعتبر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// CSV چندتایی
check('CSV چندتایی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// پاک‌سازی
check('پاک‌سازی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
