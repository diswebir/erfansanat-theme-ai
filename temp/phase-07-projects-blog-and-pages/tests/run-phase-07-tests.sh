#!/usr/bin/env bash
# Phase 7 test suite — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی
set -uo pipefail
cd "$(dirname "$0")/.."          # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 7 — 23 tasks ##########"
run_test "Task 7.1 — `single-projects.php`: هیرو + گالری + شن" "test-task-01.php"
run_test "Task 7.2 — تب‌های جزئیات پروژه + فرآیند اجرا" "test-task-02.php"
run_test "Task 7.3 — محصولات استفاده‌شده در پروژه (`mortabet_" "test-task-03.php"
run_test "Task 7.4 — پروژه‌های شاخص مرتبط (سایر پروژه‌های هم‌" "test-task-04.php"
run_test "Task 7.5 — آرشیو پروژه‌ها (`archive-projects.php`)" "test-task-05.php"
run_test "Task 7.6 — آرشیو نوع پروژه (`taxonomy-project_type." "test-task-06.php"
run_test "Task 7.7 — آرشیو شهر (`taxonomy-city.php`)" "test-task-07.php"
run_test "Task 7.8 — آرشیو/فهرست خدمات (`services.html` → برگ" "test-task-08.php"
run_test "Task 7.9 — صفحه جزئیات خدمت (۵ صفحه: میدان، خیابان،" "test-task-09.php"
run_test "Task 7.10 — آرشیو وبلاگ (`home.php`) با فیلتر دسته و" "test-task-10.php"
run_test "Task 7.11 — آرشیو دسته نوشته (`category.php`) + برچس" "test-task-11.php"
run_test "Task 7.12 — تک‌نوشته (`single.php`): محتوا + TOC + ا" "test-task-12.php"
run_test "Task 7.13 — نظرات تک‌نوشته + مقالات مرتبط" "test-task-13.php"
run_test "Task 7.14 — نتایج جستجو (`search.php`)" "test-task-14.php"
run_test "Task 7.15 — صفحه ۴۰۴ اختصاصی" "test-task-15.php"
run_test "Task 7.16 — برگه «درباره ما» (`template-about.php`)" "test-task-16.php"
run_test "Task 7.17 — برگه «تماس با ما» (`template-contact.php" "test-task-17.php"
run_test "Task 7.18 — برگه «مجوزها و افتخارات» (`template-lice" "test-task-18.php"
run_test "Task 7.19 — برگه «سوالات متداول» (`template-faq.php`" "test-task-19.php"
run_test "Task 7.20 — برگه «شرایط گارانتی» (`template-refund.p" "test-task-20.php"
run_test "Task 7.21 — برگه «فرصت‌های شغلی» (`template-recruitm" "test-task-21.php"
run_test "Task 7.22 — `page.php` عمومی + نگاشت خودکار قالب‌های" "test-task-22.php"
run_test "Task 7.23 — گیت فاز ۷: رندر همه قالب‌ها + جستجوی ۴۰۴" "test-task-23.php"

phase_summary "Phase 7"
