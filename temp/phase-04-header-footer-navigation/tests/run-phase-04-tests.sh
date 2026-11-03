#!/usr/bin/env bash
# Phase 4 test suite — هدر، مگامنو، منوی موبایل و فوتر
set -uo pipefail
cd "$(dirname "$0")/.."          # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 4 — 12 tasks ##########"
run_test "Task 4.1 — `head` سفارشی: `wp_head`، متا، Hreflang " "test-task-01.php"
run_test "Task 4.2 — `header.php`: نوار اعلان + لوگو + نوار ن" "test-task-02.php"
run_test "Task 4.3 — `footer.php` + بازگشت به بالا + اطلاعات " "test-task-03.php"
run_test "Task 4.4 — Walker مگامنو محصولات (تب‌ها + ستون‌ها)" "test-task-04.php"
run_test "Task 4.5 — Walker مگامنو پروژه‌ها (تب نوع/شهر)" "test-task-05.php"
run_test "Task 4.6 — فیلدهای آیتم منو در Appearance → Menus" "test-task-06.php"
run_test "Task 4.7 — تنظیمات نمایشی منو در داشبورد (بدون مدیر" "test-task-07.php"
run_test "Task 4.8 — منوی موبایل off-canvas (`aside[data-mobi" "test-task-08.php"
run_test "Task 4.9 — `template-parts/header/search-modal.php`" "test-task-09.php"
run_test "Task 4.10 — شمارنده سبد خرید هدر (WooCommerce Fragme" "test-task-10.php"
run_test "Task 4.11 — سازگاری کامل با `main.js` (بدون تغییر فا" "test-task-11.php"
run_test "Task 4.12 — گیت فاز ۴: مقایسه DOM هدر/فوتر با مرجع" "test-task-12.php"

phase_summary "Phase 4"
