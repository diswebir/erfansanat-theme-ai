# Task 1.6 — انتقال بی‌نقص دارایی‌های میرور به assets/ قالب

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-06.php`

## 🎯 هدف
کپی یک‌به‌یک styles.css، main.js، ۳ فونت Vazirmatn و تصاویر/ویدیو با حفظ ساختار پوشه‌ها تا هیچ مسیر نسبی url(... نشکند

## 🔗 وابستگی‌ها
- Task 1.1

## 📎 مراجع (منابع برش/استخراج)
- uploads/static-mirror/assets/*
- uploads/static-mirror/images/*

## 📤 فایل‌های خروجی
- `assets/css/styles.css`
- `assets/js/main.js`
- `assets/fonts/vazirmatn-{arabic,latin,latin-ext}.woff2`
- `assets/images/*`
- `assets/media/hero-video.mp4`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کopy assets/css/styles.css → assets/css/ (بدون هیچ تغییر در محتوا — minified است)
2. کopy assets/js/main.js → assets/js/
3. کopy ۳ فونت woff2 → assets/fonts/ (نام‌ها دقیقاً همان)
4. کopy images/*.jpg|svg و images/pexels/* → assets/images/ و assets/images/pexels/
5. کopy images/hero-video.mp4 → assets/media/hero-video.mp4
6. تأیید: رفرنس‌های url(../fonts/...) داخل CSS به مسیر فیزیکی assets/fonts/ resolve می‌شوند

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ تغییر نام فایل/ساختار پوشه — CSS و JS میرور عیناً حفظ می‌شوند
- فایل‌ها gitignore نشوند (assets قالب است، نه میرور)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] وجود فیزیکی هر ۳ فونت با نام دقیق در assets/fonts/
- [ ] styles.css و main.js با اندازه > 100KB موجود باشند
- [ ] ساختار پوشه assets دقیقاً مطابق میرور باشد (css/js/fonts/images)
- [ ] هر url(../fonts/X) داخل CSS به فایل موجود در assets/fonts/ برسد (تست path)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
