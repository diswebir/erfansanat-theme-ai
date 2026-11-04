# Task 2.2 — موتور فیلدها (FieldRenderer) و Sanitizer اختصاصی

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-02.php`

## 🎯 هدف
کامپوننت‌های رابط ادمین (۸ نوع فیلد + repeater) و sanitize امن هر نوع، قابل استفاده مجدد در تب‌ها و SectionManager

## 🔗 وابستگی‌ها
- Task 2.1

## 📎 مراجع (منابع برش/استخراج)
- مقادیر پیش‌فرض از میرور (footer/index)

## 📤 فایل‌های خروجی
- `inc/Admin/FieldRenderer.php`
- `inc/Admin/Sanitizer.php`
- `بسط assets/admin/admin.js (media picker، color، repeater، switch)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. FieldRenderer::render(array $field, mixed $value): پشتیبانی text، textarea، url، number، color، switch، select، media (wp.media picker با preview)، repeater (ردیف‌های تکرارشونده با add/remove/move)
2. نام‌گذاری فیلدها به‌صورت nesting: opt[header][logo] یا opt[sections][home][fields][...]
3. Sanitizer::sanitize($field, $value) با نگاشت نوع: text→sanitize_text_field، textarea→wp_kses_post، url→esc_url_raw، number→absint/float، color→sanitize_hex_color، switch→boolean، select→whitelist، repeater→بازگشتی
4. JS: کامپوننت‌های مینیمال vanilla (بدون jQuery برای UI خودمان؛ wp.media مجاز) — data-es-field type
5. آیکون‌های ادمین SVG inline

## ⛔ محدودیت‌ها و خط‌قرمزها
- صفر وابستگی خارجی (CDN ممنوع)
- خروجی رندر با escaping کامل

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] رندر موفق هر ۹ نوع فیلد (تست snapshot خروجی)
- [ ] Sanitizer ورودی آلوده (<script>, javascript:, -1, رنگ نامعتبر) را تمیز کند (تست هر نوع)
- [ ] repeater آرایه تو در تو را بازگشتی sanitize کند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-02.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
