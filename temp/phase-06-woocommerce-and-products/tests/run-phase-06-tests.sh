#!/usr/bin/env bash
# Phase 6 test suite — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه
# Runs the whole phase inside ONE php-wasm boot (~100s of boot time per process).
set -uo pipefail
cd "$(dirname "$0")/../../.."      # repository root
exec node tools/harness/run-phase.mjs temp/phase-06-woocommerce-and-products/tests

# --- per-task breakdown (one process per task, slow; kept for reference) ---
: <<'REFERENCE'
source tools/harness/test-runner.sh
cd temp
run_test "Task 6.1 — راه‌اندازی پوشه woocommerce/ و declare_c" "test-task-01.php"
run_test "Task 6.2 — هیرو محصول (template-parts/product/hero." "test-task-02.php"
run_test "Task 6.3 — گالری محصول + لایت‌باکس" "test-task-03.php"
run_test "Task 6.4 — جعبه خرید: قیمت، وضعیت true_sale، افزودن" "test-task-04.php"
run_test "Task 6.5 — جدول مشخصات فنی (۱۸ ویژگی pa_*)" "test-task-05.php"
run_test "Task 6.6 — Repeaterهای محصول در فرانت (مشخصات، ویژگ" "test-task-06.php"
run_test "Task 6.7 — شناسه‌های قانونی (gs1_id, national_code," "test-task-07.php"
run_test "Task 6.8 — دانلود کاتالوگ محصول (product-catalog-fi" "test-task-08.php"
run_test "Task 6.9 — پروژه‌های مرتبط محصول (project-ids) + کا" "test-task-09.php"
run_test "Task 6.10 — تب‌های صفحه محصول (data-prod-tab-*) و نظ" "test-task-10.php"
run_test "Task 6.11 — محصولات مشابه و مکمل" "test-task-11.php"
run_test "Task 6.12 — آرشیو فروشگاه و دسته‌ها (archive-product" "test-task-12.php"
run_test "Task 6.13 — آرشیو برچسب و برند (taxonomy-product_tag" "test-task-13.php"
run_test "Task 6.14 — سبد خرید (woocommerce/cart/*) با UI استا" "test-task-14.php"
run_test "Task 6.15 — تسویه حساب (woocommerce/checkout/*)" "test-task-15.php"
run_test "Task 6.16 — حساب کاربری (woocommerce/myaccount/*) + " "test-task-16.php"
run_test "Task 6.17 — صفحه‌های ووکامرس بدون قالب استاتیک (sear" "test-task-17.php"
run_test "Task 6.18 — گیت فاز ۶: تست سرتاسری فروشگاه" "test-task-18.php"
phase_summary "Phase 6"
REFERENCE
