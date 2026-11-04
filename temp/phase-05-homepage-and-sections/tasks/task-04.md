# Task 5.4 — سکشن درباره ما + آمار شمارنده

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-04.php`

## 🎯 هدف
برش سکشن‌های #about و معرفی + بلوک آمار با شمارنده انیمیشنی میرور

## 🔗 وابستگی‌ها
- Task 5.1

## 📎 مراجع (منابع برش/استخراج)
- index.html سکشن #about و aria-label معرفی/آمار

## 📤 فایل‌های خروجی
- `template-parts/home/section-about.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کپی مارک‌آپ؛ فیلدها: عنوان، متن معرفی، تصویر، لینک درباره ما
2. Repeater آمار: counter-target (عدد)، counter-suffix، برچسب — پیش‌فرض اعداد میرور
3. اتریبیوت‌های data-counted و ساختار میرور حفظ شود (JS شمارنده با IntersectionObserver کار می‌کند)
4. بخش معرفی دوم (aria-label تاسیس) اگر سکشن جدا است → فایل جدا section-about-intro.php

## ⛔ محدودیت‌ها و خط‌قرمزها
- اعداد فارسی با فرمت میرور
- hookهای شمارنده عیناً

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] شمارنده‌ها با اتریبیوت‌های صحیح رندر شوند
- [ ] Repeater آمار کار کند
- [ ] ساختار مطابق ref

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
