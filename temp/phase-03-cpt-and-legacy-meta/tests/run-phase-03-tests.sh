#!/usr/bin/env bash
# Phase 3 test suite — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
set -uo pipefail
cd "$(dirname "$0")/.."          # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 3 — 18 tasks ##########"
run_test "Task 3.1 — ثبت پست‌تایپ `projects` (Native)" "test-task-01.php"
run_test "Task 3.2 — ثبت تکسونومی `project_type`" "test-task-02.php"
run_test "Task 3.3 — ثبت تکسونومی `city`" "test-task-03.php"
run_test "Task 3.4 — تصحیح Rewriteهای ووکامرس (`product_cat`," "test-task-04.php"
run_test "Task 3.5 — Flush نسخه‌دار + مانیتور ۴۰۴ پس از فعال‌" "test-task-05.php"
run_test "Task 3.6 — ستون‌های ادمین + فیلترهای دراپ‌داون پروژ" "test-task-06.php"
run_test "Task 3.7 — ستون‌های ادمین نوشته‌ها/محصولات/دسته‌ها" "test-task-07.php"
run_test "Task 3.8 — `MetaRepository` + `RepeaterCodec` (سازگ" "test-task-08.php"
run_test "Task 3.9 — متاباکس «اطلاعات محصولات» — تب شناسه‌ها " "test-task-09.php"
run_test "Task 3.10 — تب فایل و گالری محصول + همگام‌سازی" "test-task-10.php"
run_test "Task 3.11 — تب روابط محصول (`project-ids`, `usage_co" "test-task-11.php"
run_test "Task 3.12 — Repeaterهای محصول: `product-feature`, `p" "test-task-12.php"
run_test "Task 3.13 — Quick Edit برای شناسه‌ها و وضعیت فروش" "test-task-13.php"
run_test "Task 3.14 — متاباکس پروژه (`second-image` تا `six-im" "test-task-14.php"
run_test "Task 3.15 — ترم‌متای `product_cat`: تصویر و FAQ" "test-task-15.php"
run_test "Task 3.16 — Hreflang (`hrefmeta_fa/en/ar`, `same_key" "test-task-16.php"
run_test "Task 3.17 — شمارنده بازدید `number2-viewc` (با fallb" "test-task-17.php"
run_test "Task 3.18 — گیت فاز ۳: تست یکپارچگی داده" "test-task-18.php"

phase_summary "Phase 3"
