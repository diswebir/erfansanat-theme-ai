<?php
/**
 * Test — Task 7.11: آرشیو دسته نوشته (`category.php`) + برچسب/نویسنده
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-11.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.11: آرشیو دسته نوشته (`category.php`) + برچسب/نویسنده ==\n";
echo "   phase 7 | size M | files: 4 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// رندر دسته‌ها
check('رندر دسته‌ها', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// آرشیوهای دیگر
check('آرشیوهای دیگر', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ۴۰۴
check('۴۰۴', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
