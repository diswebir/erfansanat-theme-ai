<?php
/**
 * Test — Task 2.6: فیلد Repeater با Drag&Drop (بدون jQuery UI)
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-06.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.6: فیلد Repeater با Drag&Drop (بدون jQuery UI) ==\n";
echo "   phase 2 | size L | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// ترتیب
check('ترتیب', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// انطباق فرمت
check('انطباق فرمت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// round-trip دو فرمت
check('round-trip دو فرمت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف سطر خالی
check('حذف سطر خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
