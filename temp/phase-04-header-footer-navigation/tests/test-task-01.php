<?php
/**
 * Test — Task 4.1: `head` سفارشی: `wp_head`، متا، Hreflang و JSON-LD پایه
 * Phase 4: هدر، مگامنو، منوی موبایل و فوتر
 *
 * اجرا:  node tools/harness/run-tests.mjs temp/phase-04-header-footer-navigation/tests/test-task-01.php
 *
 * قاعده: هیچ assertی حق ندارد بدون پیاده‌سازی واقعی PASS شود.
 * تا زمانی که `check()` با منطق واقعی پر نشود، این فایل با PENDING/NOT OK می‌ماند.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../tests/bootstrap.php';

echo "== Task 4.1: `head` سفارشی: `wp_head`، متا، Hreflang و JSON-LD پایه ==\n";
echo "   phase 4 | size M | files: 2 | pass conditions: 4\n";

// --- Pass Conditions (4 شرط) -----------------------------------
// OG شرطی
check('OG شرطی', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// عدم تکرار
check('عدم تکرار', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// Hreflang
check('Hreflang', function () { return null; /* IMPLEMENT: return true when {a} holds */ });
// اعتبار JSON-LD
check('اعتبار JSON-LD', function () { return null; /* IMPLEMENT: return true when {a} holds */ });

// --- summary -----------------------------------------------------------------
summary();
