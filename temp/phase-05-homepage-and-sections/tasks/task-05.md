# Task 5.5 — سکشن تایم‌لاین تاریخچه (history)

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-05.php`

## 🎯 هدف
تایم‌لاین اسکرولی ۶ مرحله‌ای با progress bar — داده از Repeater

## 🔗 وابستگی‌ها
- Task 5.1

## 📎 مراجع (منابع برش/استخراج)
- index.html data-history-scroll-track (total=6)، history-stage/progress-bar/step-label

## 📤 فایل‌های خروجی
- `template-parts/home/section-history.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کپی مارک‌آپ کانتینر با اتریبیوت‌های data-history-scroll-track و data-history-total داینامیک (تعداد ردیف Repeater)
2. Repeater مراحل: سال، عنوان، متن، تصویر اختیاری — پیش‌فرض ۶ مرحله میرور (استخراج متن دقیق از index.html)
3. progress-bar و step-label برای هر مرحله رندر شود
4. اگر مراحل ≠ ۶ → data-history-total آپدیت (JS میرور بر اساس total است)

## ⛔ محدودیت‌ها و خط‌قرمزها
- total همیشه = تعداد مراحل فعال

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] ۶ مرحله پیش‌فرض مطابق متن میرور
- [ ] total داینامیک با تعداد ردیف‌ها
- [ ] ساختار stageها مطابق ref

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-05.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
