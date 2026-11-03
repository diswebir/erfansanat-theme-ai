<?php
/**
 * Test — Task 4.6: فیلدهای آیتم منو در Appearance → Menus
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-06.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.6: فیلدهای آیتم منو در Appearance → Menus ==\n";
echo "   phase 4 | size L | files: 3 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// محل رندر
check('محل رندر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ذخیره/بازیابی
check('ذخیره/بازیابی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم صفحه جدا
check('عدم صفحه جدا', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// whitelist آیکون
check('whitelist آیکون', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
