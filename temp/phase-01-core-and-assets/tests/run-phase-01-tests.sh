#!/usr/bin/env bash
# Phase 1 test suite — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک
# Runs the whole phase inside ONE php-wasm boot (~100s of boot time per process).
set -uo pipefail
cd "$(dirname "$0")/../../.."      # repository root
exec node tools/harness/run-phase.mjs temp/phase-01-core-and-assets/tests

# --- per-task breakdown (one process per task, slow; kept for reference) ---
: <<'REFERENCE'
source tools/harness/test-runner.sh
cd temp
run_test "Task 1.1 — هدر قالب در style.css و متادیتای نسخه" "test-task-01.php"
run_test "Task 1.2 — Autoloader PSR-4 دستی و Namespace ErfanS" "test-task-02.php"
run_test "Task 1.3 — Kernel قالب و ثبت add_theme_supportها" "test-task-03.php"
run_test "Task 1.4 — functions.php سبک + index.php + screensh" "test-task-04.php"
run_test "Task 1.5 — هارنس رندر کامل قالب (tools/harness/rend" "test-task-05.php"
run_test "Task 1.6 — DeBloat: حذف کامل CSS/JS بلاک‌ها، ایموجی" "test-task-06.php"
run_test "Task 1.7 — انتقال بایت‌به‌بایت Assets استاتیک (CSS/" "test-task-07.php"
run_test "Task 1.8 — انتقال دارایی‌های تصویری + ویدیو + پیش‌c" "test-task-08.php"
run_test "Task 1.9 — AssetManager با Enqueue شرطی و نسخه‌گذار" "test-task-09.php"
run_test "Task 1.10 — Preload/Preconnect فونت و LCP (شرطی)" "test-task-10.php"
run_test "Task 1.11 — ثبت جایگاه‌های منو و load_theme_textdoma" "test-task-11.php"
run_test "Task 1.12 — گیت فاز ۱: اجرای کامل تست‌ها + بودجه عمل" "test-task-12.php"
phase_summary "Phase 1"
REFERENCE
