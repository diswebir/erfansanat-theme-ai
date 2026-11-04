#!/usr/bin/env bash
# Phase 8 test suite — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی
# Runs the whole phase inside ONE php-wasm boot (~100s of boot time per process).
set -uo pipefail
cd "$(dirname "$0")/../../.."      # repository root
exec node tools/harness/run-phase.mjs temp/phase-08-ajax-filters-and-audit/tests

# --- per-task breakdown (one process per task, slow; kept for reference) ---
: <<'REFERENCE'
source tools/harness/test-runner.sh
cd temp
run_test "Task 8.1 — اندپوینت جستجوی زنده (SearchEndpoint)" "test-task-01.php"
run_test "Task 8.2 — UI جستجوی زنده در مودال هدر (Debounce + " "test-task-02.php"
run_test "Task 8.3 — فیلتر ایجکس محصولات (دسته، قیمت، IP، ولت" "test-task-03.php"
run_test "Task 8.4 — فیلتر ایجکس پروژه‌ها (نوع/شهر + جستجو + " "test-task-04.php"
run_test "Task 8.5 — جدول فرم‌ها + FormRepository + migration" "test-task-05.php"
run_test "Task 8.6 — فرم تماس/مشاوره: پردازش، Honeypot، Nonce" "test-task-06.php"
run_test "Task 8.7 — فرم استعلام قیمت محصول (پاپ‌آپ)" "test-task-07.php"
run_test "Task 8.8 — فرم استخدام با آپلود رزومه" "test-task-08.php"
run_test "Task 8.9 — فرم بازخورد مشتریان (client-feedback)" "test-task-09.php"
run_test "Task 8.10 — لیست پیام‌ها در داشبورد (فیلتر، وضعیت، ج" "test-task-10.php"
run_test "Task 8.11 — تنظیمات فرم‌ها (ایمیل مقصد، موضوع، autor" "test-task-11.php"
run_test "Task 8.12 — ممیزی Hreflang و چندزبانه" "test-task-12.php"
run_test "Task 8.13 — ممیزی JSON-LD (Organization, WebSite, Br" "test-task-13.php"
run_test "Task 8.14 — ممیزی امنیتی: escaping، capability، nonc" "test-task-14.php"
run_test "Task 8.15 — ممیزی عملکرد: Core Web Vitals و بودجه با" "test-task-15.php"
run_test "Task 8.16 — ممیزی ریسپانسیو و RTL (بدون تغییر CSS)" "test-task-16.php"
run_test "Task 8.17 — ممیزی سازگاری با افزونه‌های باقی‌مانده" "test-task-17.php"
run_test "Task 8.18 — گیت فاز ۸ + E2E نهایی (run-all-phases-e2" "test-task-18.php"
phase_summary "Phase 8"
REFERENCE
