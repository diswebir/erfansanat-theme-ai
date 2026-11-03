<?php
/**
 * Test — Task 2.7: تب «هدر» و «فوتر» (نوار اعلان، تلفن‌ها، شبکه‌ها، کپی‌رایت)
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-07.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.7: تب «هدر» و «فوتر» (نوار اعلان، تلفن‌ها، شبکه‌ها، کپی‌رایت) ==\n";
echo "   phase 2 | size L | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// اسکن هاردکد
check('اسکن هاردکد', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف کامل بلوک
check('حذف کامل بلوک', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اعتبار URL
check('اعتبار URL', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
