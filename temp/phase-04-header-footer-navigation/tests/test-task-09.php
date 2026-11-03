<?php
/**
 * Test — Task 4.9: `template-parts/header/search-modal.php` (مارک‌آپ مودال جستجو)
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-09.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.9: `template-parts/header/search-modal.php` (مارک‌آپ مودال جستجو) ==\n";
echo "   phase 4 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// مارک‌آپ
check('مارک‌آپ', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// محدودیت تعداد
check('محدودیت تعداد', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// کارکرد باز/بسته
check('کارکرد باز/بسته', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
