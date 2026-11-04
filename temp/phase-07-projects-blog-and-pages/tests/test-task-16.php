<?php
/**
 * Test — Task 7.16: برگه «درباره ما» (`template-about.php`)
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-16.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.16: برگه «درباره ما» (`template-about.php`) ==\n";
echo "   phase 7 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// رندر
check('رندر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ویرایش‌پذیری
check('ویرایش‌پذیری', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اسلاگ فارسی
check('اسلاگ فارسی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
