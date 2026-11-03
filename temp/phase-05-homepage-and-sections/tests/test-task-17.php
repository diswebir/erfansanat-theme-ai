<?php
/**
 * Test — Task 5.17: چرا عرفان صنعت (تمایزها و گواهینامه‌ها)
 * Phase 5: صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-05-homepage-and-sections/tests/test-task-17.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 5.17: چرا عرفان صنعت (تمایزها و گواهینامه‌ها) ==\n";
echo "   phase 5 | size M | files: 2 | pass conditions: 6\n";

// --- Pass Conditions (6 شرط) -----------------------------------
// diff ساختاری سکشن
check('diff ساختاری سکشن', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اسکن هاردکد
check('اسکن هاردکد', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اثر داشبورد
check('اثر داشبورد', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
