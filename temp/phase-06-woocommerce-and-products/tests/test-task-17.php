<?php
/**
 * Test — Task 6.17: صفحه‌های ووکامرس بدون قالب استاتیک (`search`, `404`, `page`)
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-17.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.17: صفحه‌های ووکامرس بدون قالب استاتیک (`search`, `404`, `page`) ==\n";
echo "   phase 6 | size M | files: 2 | pass conditions: 3\n";

// --- Pass Conditions (3 شرط) -----------------------------------
// کد وضعیت
check('کد وضعیت', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون خطا
check('بدون خطا', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// بدون کلاس جدید
check('بدون کلاس جدید', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
