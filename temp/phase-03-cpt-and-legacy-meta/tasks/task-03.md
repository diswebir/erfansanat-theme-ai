# Task 3.3 — ثبت CPT services (تصمیم Q2 کارفرما)

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php`

## 🎯 هدف
پست‌تایپ خدمات به‌صورت Native — مطابق تصمیم ثبت‌شده D-005

## 🔗 وابستگی‌ها
- Task 1.3

## 📎 مراجع (منابع برش/استخراج)
- تصمیم کارفرما Q2 (temp/PROJECT_MEMORY.md D-005)
- services.html و services/* میرور

## 📤 فایل‌های خروجی
- `بسط inc/Data/PostTypes.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. register_post_type('services'): public=true، has_archive=false (فهرست از برگه با قالب listing در فاز ۷)، rewrite slug=services with_front=false، show_in_rest=true
2. supports: title، editor، thumbnail، excerpt، custom-fields، revisions
3. menu_icon=dashicons-lightbulb + لیبل فارسی «خدمات»
4. اتصال متاهای سراسری hreflang/بازدید به این CPT (فاز 3.14 به آن ارجاع می‌دهد)
5. بدون تکسونومی اختصاصی در این تسک (در صورت نیاز بعدی، فیلتر توسعه)

## ⛔ محدودیت‌ها و خط‌قرمزها
- has_archive=false تا با برگه فهرست خدمات تداخل rewrite نداشته باشد

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] args دقیق (has_archive=false, slug=services, show_in_rest=true)
- [ ] supports کامل ثبت شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
