<?php
/**
 * Test — Task 7.19: برگه «سوالات متداول» (`template-faq.php`)
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-19.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.19: برگه «سوالات متداول» (`template-faq.php`) ==\n";
echo "   phase 7 | size L | files: 3 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// آکاردئون
check('آکاردئون', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// JSON-LD
check('JSON-LD', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف خالی
check('حذف خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
