# Task 3.11 — متاباکس پروژه — گالری شش‌تایی قدیمی + گالری مدرن همگام

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-11.php`

## 🎯 هدف
مدیریت تصاویر پروژه با کلیدهای تاریخی (second/three/foure/five/six-image) و یک گالری مدرن که با همین کلیدها sync می‌شود

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۵ (دقت به املای foure-image)
- گالری سینگل پروژه میرور

## 📤 فایل‌های خروجی
- `inc/Admin/ProjectMetaboxes.php (تب gallery)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. پنج فیلد media مجزا: second-image، three-image، foure-image، five-image، six-image (ذخیره آیدی عددی — مطابق دیتابیس فعلی)
2. گالری مدرن: multi-picker آیدی‌ها در متای _es_project_gallery + دکمه «همگام‌سازی با کلیدهای قدیمی»: اولین ۵ آیدی به ترتیب به کلیدهای قدیمی نوشته شود (و نیز _product_image_gallery معادل ندارد — فقط همان کلیدها)
3. تابع es_get_project_gallery($id): merge منظم آیدی‌های ۶ کلید قدیمی + متای مدرن (dedup، ترتیب)
4. خواندن مقاوم: مقدار آیدی ممکن است رشته URL قدیمی باشد → تابع تبدیل URL به attachment_id (query پست‌های attachment)

## ⛔ محدودیت‌ها و خط‌قرمزها
- املای دقیق: foure-image (نه four)
- داده قدیمی هرگز پاک نشود — فقط همگام‌سازی افزودنی

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip هر ۵ کلید قدیمی
- [ ] همگام‌سازی ۵ آیدی مدرن → کلیدهای قدیمی با ترتیب صحیح (تست آرایه)
- [ ] es_get_project_gallery dedup و ترتیب درست
- [ ] مقدار URL قدیمی به آیدی تبدیل شود (fixture)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-11.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
