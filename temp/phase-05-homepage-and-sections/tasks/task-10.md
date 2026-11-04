# Task 5.10 — سکشن تماس با ما + نقشه کارخانه

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-10.php`

## 🎯 هدف
اطلاعات تماس داینامیک + نقشه (iframe/تصویر) از داشبورد

## 🔗 وابستگی‌ها
- Task 5.1
- Task 2.6

## 📎 مراجع (منابع برش/استخراج)
- index.html سکشن #contact + aria-label موقعیت کارخانه روی نقشه

## 📤 فایل‌های خروجی
- `template-parts/home/section-contact.php`
- `template-parts/home/section-map.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. تماس: es_contact_args برای تلفن/ایمیل/آدرس + متن دعوت + دکمه (لینک تماس/فرم)
2. نقشه: فیلد embed iframe گوگل/نشان (textarea) با wp_kses گسترده مجاز iframe؛ fallback تصویر میرور
3. فرم تماس در این سکشن به مارک‌آپ contact میرور در فاز 8.6 متصل می‌شود (اسکلت form با action placeholder الان)
4. سکشن‌های خالی مخفی

## ⛔ محدودیت‌ها و خط‌قرمزها
- iframe فقط از فیلد ادمین (هرگز ورودی کاربر)
- fallback نقشه تصویری

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] اطلاعات تماس داینامیک
- [ ] iframe sanitize و فقط از آپشن
- [ ] fallback رندر

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-10.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
