# Task 1.8 — زیرساخت تست — WP Stubs و Bootstrap تست‌های واحد (بدون نصب وردپرس)

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-08.php`

## 🎯 هدف
چون تست‌ها روی PHP wasm بدون وردپرس اجرا می‌شوند، یک لایه stub سبک توابع وردپرس لازم است تا کلاس‌های قالب unit-test پذیر شوند

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- agent.md §چرخه تست هر تسک

## 📤 فایل‌های خروجی
- `temp/harness/wp-stubs.php`
- `temp/harness/README.md`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. ساخت wp-stubs.php با پیاده‌سازی سبک: add_action/add_filter/remove_action/apply_filters/do_action/get_option/update_option/esc_html/esc_attr/esc_url/wp_kses_post/sanitize_text_field/absint/current_user_can/wp_send_json_success/wp_send_json_error و مشابه
2. مکانیزم درون‌ریزی stub: در هر تست با require_once و گارد function_exists
3. مستند در README: چگونه تست جدید stubها را لود کند و متدهای available
4. تست نمونه: یک کلاس واقعی قالب (Helpers::es_option) با stub get_option تست شود

## ⛔ محدودیت‌ها و خط‌قرمزها
- stubها فقط برای تست — هرگز داخل کد قالب require نشوند
- رفتار stub باید با وردپرس واقعی یکی باشد (کش آپشن و... )

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] temp/harness/wp-stubs.php موجود و بدون syntax error باشد
- [ ] README نحوه استفاده را مستند کرده باشد
- [ ] تست نمونه es_option با stub پاس شود
- [ ] گارد function_exists جلوگیری از redeclare را داشته باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
