#!/usr/bin/env bash
# Phase 5 test suite — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار
# Runs the whole phase inside ONE php-wasm boot (~100s of boot time per process).
set -uo pipefail
cd "$(dirname "$0")/../../.."      # repository root
exec node tools/harness/run-phase.mjs temp/phase-05-homepage-and-sections/tests

# --- per-task breakdown (one process per task, slow; kept for reference) ---
: <<'REFERENCE'
source tools/harness/test-runner.sh
cd temp
run_test "Task 5.1 — front-page.php + حلقه رندر ۱۵ سکشن" "test-task-01.php"
run_test "Task 5.2 — هیرو واریانت home1 (اسلایدر صحنه + ویدیو" "test-task-02.php"
run_test "Task 5.3 — معرفی پروژه‌های نورپردازی شهری" "test-task-03.php"
run_test "Task 5.4 — گالری نمونه‌کارها (تب اسلایدر نمونه‌کاره" "test-task-04.php"
run_test "Task 5.5 — درباره + شمارنده‌های آماری" "test-task-05.php"
run_test "Task 5.6 — درباره تاسیس شرکت (بلوک متنی + تصویر)" "test-task-06.php"
run_test "Task 5.7 — تاریخچه شرکت (۱۳۸۰ تا ۱۴۰۵) با اسکرول تع" "test-task-07.php"
run_test "Task 5.8 — محصولات منتخب (۴ کارت دسته)" "test-task-08.php"
run_test "Task 5.9 — بنر دانلود کاتالوگ جامع" "test-task-09.php"
run_test "Task 5.10 — خدمات تخصصی (ردیف‌های تعاملی)" "test-task-10.php"
run_test "Task 5.11 — استانداردهای کیفیت و ماندگاری (۴ کارت)" "test-task-11.php"
run_test "Task 5.12 — پشتیبانی فنی و گارانتی (نقشه/ویژگی‌ها)" "test-task-12.php"
run_test "Task 5.13 — مشتریان و سازمان‌های همکار" "test-task-13.php"
run_test "Task 5.14 — ویترین مقالات (۴ کارت)" "test-task-14.php"
run_test "Task 5.15 — CTA تماس/استعلام قیمت" "test-task-15.php"
run_test "Task 5.16 — نقشه موقعیت کارخانه + اطلاعات تماس" "test-task-16.php"
run_test "Task 5.17 — چرا عرفان صنعت (تمایزها و گواهینامه‌ها)" "test-task-17.php"
run_test "Task 5.18 — کارت‌های استاندارد: card-product, card-p" "test-task-18.php"
run_test "Task 5.19 — لایه کوئری بهینه صفحه اصلی (HomeQueries)" "test-task-19.php"
run_test "Task 5.20 — واریانت‌های آماده home2 و home3 (غیرفعال" "test-task-20.php"
run_test "Task 5.21 — گیت فاز ۵: تست رندر و کوئری صفحه اصلی" "test-task-21.php"
phase_summary "Phase 5"
REFERENCE
