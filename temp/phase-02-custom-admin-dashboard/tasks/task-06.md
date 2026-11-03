# Task 2.6 — هلپر نهایی es_option + تزریق تنظیمات به فرانت‌اند

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-06.php`

## 🎯 هدف
اتصال کامل آپشن‌ها به فرانت: توابع رندر آماده برای فاز ۴ به بعد

## 🔗 وابستگی‌ها
- Task 2.3
- Task 2.4
- Task 2.5

## 📎 مراجع (منابع برش/استخراج)
- بخش‌های مرتبط header/footer میرور

## 📤 فایل‌های خروجی
- `بسط inc/Helpers.php`
- `inc/Frontend/OptionsOutput.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. es_option نهایی: پشتیبانی مسیر تودرتو es_option('header.logo') با نقطه
2. OptionsOutput: هوک wp_head برای custom_head_js + CSS variables برند؛ هوک wp_footer برای custom_footer_js
3. تابع es_contact_args(): آرایه آماده تلفن/ایمیل/آدرس/سوشال با fallback میرور (برای footer و سکشن تماس)
4. تابع es_announcement_args() برای نوار اعلان

## ⛔ محدودیت‌ها و خط‌قرمزها
- خروجی‌ها escaping کامل داشته باشند
- بدون کوئری اضافه — همه از کش es_option

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] es_option('header.logo') و مسیرهای تودرتوی مشابه درست کار کند (تست دو حالت آپشن خالی/پر)
- [ ] اسکریپت سفارشی در head/footer درج شود
- [ ] es_contact_args مقادیر پیش‌فرض میرور را در حالت خالی برگرداند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
