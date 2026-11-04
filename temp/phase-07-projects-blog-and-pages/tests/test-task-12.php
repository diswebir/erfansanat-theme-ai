<?php
/**
 * Test — Task 7.12: تک‌نوشته (`single.php`): محتوا + TOC + اشتراک‌گذاری
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-12.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.12: تک‌نوشته (`single.php`): محتوا + TOC + اشتراک‌گذاری ==\n";
echo "   phase 7 | size L | files: 5 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// TOC
check('TOC', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اشتراک
check('اشتراک', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بازدید
check('بازدید', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حالت خالی
check('حالت خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
