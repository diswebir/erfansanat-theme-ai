<?php
/**
 * Test — Task 2.1: لایه آپشن: `es_option()` / `es_update_option()` با کش و fallback
 * Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-01.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 2.1: لایه آپشن: `es_option()` / `es_update_option()` با کش و fallback ==\n";
echo "   phase 2 | size M | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// شمارش get_option
check('شمارش get_option', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// dot-notation
check('dot-notation', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// تطابق پیش‌فرض با استاتیک
check('تطابق پیش‌فرض با استاتیک', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم ایجاد آپشن جدید
check('عدم ایجاد آپشن جدید', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
