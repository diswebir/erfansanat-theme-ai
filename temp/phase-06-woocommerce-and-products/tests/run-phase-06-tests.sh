#!/usr/bin/env bash
# Phase 6 test suite — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
set -uo pipefail
cd "$(dirname "$0")/.."          # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 6 — 18 tasks ##########"
run_test "Task 6.1 — راه‌اندازی پوشه `woocommerce/` و `declar" "test-task-01.php"
run_test "Task 6.2 — هیرو محصول (`template-parts/product/hero" "test-task-02.php"
run_test "Task 6.3 — گالری محصول + لایت‌باکس" "test-task-03.php"
run_test "Task 6.4 — جعبه خرید: قیمت، وضعیت `true_sale`، افزو" "test-task-04.php"
run_test "Task 6.5 — جدول مشخصات فنی (۱۸ ویژگی `pa_*`)" "test-task-05.php"
run_test "Task 6.6 — Repeaterهای محصول در فرانت (مشخصات، ویژگ" "test-task-06.php"
run_test "Task 6.7 — شناسه‌های قانونی (`gs1_id`, `national_co" "test-task-07.php"
run_test "Task 6.8 — دانلود کاتالوگ محصول (`product-catalog-f" "test-task-08.php"
run_test "Task 6.9 — پروژه‌های مرتبط محصول (`project-ids`) + " "test-task-09.php"
run_test "Task 6.10 — تب‌های صفحه محصول (`data-prod-tab-*`) و " "test-task-10.php"
run_test "Task 6.11 — محصولات مشابه و مکمل" "test-task-11.php"
run_test "Task 6.12 — آرشیو فروشگاه و دسته‌ها (`archive-produc" "test-task-12.php"
run_test "Task 6.13 — آرشیو برچسب و برند (`taxonomy-product_ta" "test-task-13.php"
run_test "Task 6.14 — سبد خرید (`woocommerce/cart/*`) با UI اس" "test-task-14.php"
run_test "Task 6.15 — تسویه حساب (`woocommerce/checkout/*`)" "test-task-15.php"
run_test "Task 6.16 — حساب کاربری (`woocommerce/myaccount/*`) " "test-task-16.php"
run_test "Task 6.17 — صفحه‌های ووکامرس بدون قالب استاتیک (`sea" "test-task-17.php"
run_test "Task 6.18 — گیت فاز ۶: تست سرتاسری فروشگاه" "test-task-18.php"

phase_summary "Phase 6"
