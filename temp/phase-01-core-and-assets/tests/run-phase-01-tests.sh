#!/usr/bin/env bash
# Phase 1 test suite — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
set -uo pipefail
cd "$(dirname "$0")/.."          # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 1 — 12 tasks ##########"
run_test "Task 1.1 — هدر قالب در `style.css` و متادیتای نسخه" "test-task-01.php"
run_test "Task 1.2 — Autoloader PSR-4 دستی و Namespace `Erfan" "test-task-02.php"
run_test "Task 1.3 — Kernel قالب و ثبت `add_theme_support`ها" "test-task-03.php"
run_test "Task 1.4 — `functions.php` سبک + `index.php` + `scr" "test-task-04.php"
run_test "Task 1.5 — هارنس رندر کامل قالب (`tools/harness/ren" "test-task-05.php"
run_test "Task 1.6 — DeBloat: حذف کامل CSS/JS بلاک‌ها، ایموجی" "test-task-06.php"
run_test "Task 1.7 — انتقال بایت‌به‌بایت Assets استاتیک (CSS/" "test-task-07.php"
run_test "Task 1.8 — انتقال دارایی‌های تصویری + ویدیو + پیش‌c" "test-task-08.php"
run_test "Task 1.9 — `AssetManager` با Enqueue شرطی و نسخه‌گذ" "test-task-09.php"
run_test "Task 1.10 — Preload/Preconnect فونت و LCP (شرطی)" "test-task-10.php"
run_test "Task 1.11 — ثبت جایگاه‌های منو و `load_theme_textdom" "test-task-11.php"
run_test "Task 1.12 — گیت فاز ۱: اجرای کامل تست‌ها + بودجه عمل" "test-task-12.php"

phase_summary "Phase 1"
