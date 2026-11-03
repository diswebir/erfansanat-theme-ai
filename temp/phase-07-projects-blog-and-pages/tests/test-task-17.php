<?php
/**
 * Test — Task 7.17: برگه «تماس با ما» (`template-contact.php`) + فرم
 * Phase 7: صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-07-projects-blog-and-pages/tests/test-task-17.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 7.17: برگه «تماس با ما» (`template-contact.php`) + فرم ==\n";
echo "   phase 7 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// فرم بدون JS
check('فرم بدون JS', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// nonce
check('nonce', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// داده‌های تماس
check('داده‌های تماس', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
