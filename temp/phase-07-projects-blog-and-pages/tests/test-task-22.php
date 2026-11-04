<?php
/**
 * Test — Task 7.22: `page.php` عمومی + نگاشت خودکار قالب‌های برگه
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-22.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.22: `page.php` عمومی + نگاشت خودکار قالب‌های برگه ==\n";
echo "   phase 7 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// برگه ناشناس
check('برگه ناشناس', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// نگاشت شش برگه
check('نگاشت شش برگه', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اسلاگ فارسی
check('اسلاگ فارسی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
