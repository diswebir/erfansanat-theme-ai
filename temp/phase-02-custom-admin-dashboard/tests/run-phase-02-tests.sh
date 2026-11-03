#!/usr/bin/env bash
# Phase 2 test suite — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
set -uo pipefail
cd "$(dirname "$0")/.."          # temp/
source ../tools/harness/test-runner.sh

echo "########## PHASE 2 — 15 tasks ##########"
run_test "Task 2.1 — لایه آپشن: `es_option()` / `es_update_op" "test-task-01.php"
run_test "Task 2.2 — منوی ادمین سطح‌بالا + پوسته RTL داشبورد" "test-task-02.php"
run_test "Task 2.3 — موتور فیلدها: Renderer، Sanitizer، Schem" "test-task-03.php"
run_test "Task 2.4 — ذخیره AJAX امن (Nonce + Capability + rat" "test-task-04.php"
run_test "Task 2.5 — ریفیلد Media Picker (کتابخانه رسانه)" "test-task-05.php"
run_test "Task 2.6 — فیلد Repeater با Drag&Drop (بدون jQuery " "test-task-06.php"
run_test "Task 2.7 — تب «هدر» و «فوتر» (نوار اعلان، تلفن‌ها، " "test-task-07.php"
run_test "Task 2.8 — تب «برند و رنگ» با متغیرهای CSS" "test-task-08.php"
run_test "Task 2.9 — تب «آرشیوها» (تعداد ستون، تعداد آیتم، تر" "test-task-09.php"
run_test "Task 2.10 — تب «اسکریپت‌ها و ابزار» (هدر/فوتر/کدهای " "test-task-10.php"
run_test "Task 2.11 — Section Manager: Schema سکشن‌ها و متای `" "test-task-11.php"
run_test "Task 2.12 — ویرایشگر سکشن‌ها در ادمین + Drag&Drop" "test-task-12.php"
run_test "Task 2.13 — Renderer سکشن‌ها در فرانت + fallback" "test-task-13.php"
run_test "Task 2.14 — صادرات/واردات تنظیمات و `Reset` ایمن" "test-task-14.php"
run_test "Task 2.15 — گیت فاز ۲: تست امنیتی + عملکرد پنل" "test-task-15.php"

phase_summary "Phase 2"
