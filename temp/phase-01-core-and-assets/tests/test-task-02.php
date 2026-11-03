<?php
/**
 * Test — Task 1.2: Autoloader PSR-4 دستی و Namespace `ErfanSanat\Theme`
 * Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-02.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 1.2: Autoloader PSR-4 دستی و Namespace `ErfanSanat\Theme` ==\n";
echo "   phase 1 | size M | files: 2 | pass conditions: 5\n";

// --- Pass Conditions (5 شرط) -----------------------------------
// بارگذاری خودکار
check('بارگذاری خودکار', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// ایمنی کلاس ناموجود
check('ایمنی کلاس ناموجود', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم تداخل با هسته
check('عدم تداخل با هسته', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// شمارش file_exists
check('شمارش file_exists', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
