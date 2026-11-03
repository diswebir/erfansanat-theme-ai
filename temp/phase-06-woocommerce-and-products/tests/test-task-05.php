<?php
/**
 * Test — Task 6.5: جدول مشخصات فنی (۱۸ ویژگی `pa_*`)
 * Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-06-woocommerce-and-products/tests/test-task-05.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 6.5: جدول مشخصات فنی (۱۸ ویژگی `pa_*`) ==\n";
echo "   phase 6 | size L | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// ۱۸ ویژگی
check('۱۸ ویژگی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// پنهان‌سازی خالی
check('پنهان‌سازی خالی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// برچسب از تکسونومی
check('برچسب از تکسونومی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// حذف سکشن
check('حذف سکشن', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
