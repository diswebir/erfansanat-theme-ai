# Task 3.12 — متاباکس پروژه — تاریخ تکمیل و محصولات استفاده‌شده

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-12.php`

## 🎯 هدف
مدیریت finish-pro-time (متنی با فرمت شمسی) و انتخاب محصولات پروژه (mortabet_mahsole_2)

## 🔗 وابستگی‌ها
- Task 3.10
- Task 3.11

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۵
- بخش مشخصات سینگل پروژه میرور

## 📤 فایل‌های خروجی
- `بسط ProjectMetaboxes.php (تب info)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. فیلد finish-pro-time (text) با placeholder 1404/01/01 و help سازگاری WP-Parsidate (نمایش فرانت با parsidate در صورت فعال بودن)
2. فیلد mortabet_mahsole_2: RelatedPicker محصولات ووکامرس (ذخیره آرایه ID)
3. helper: es_get_project_products($id) با خواندن مقاوم
4. ستون‌های ادمین پروژه: شهر/نوع (از 3.2) + تاریخ تکمیل

## ⛔ محدودیت‌ها و خط‌قرمزها
- املا دقیق: finish-pro-time، mortabet_mahsole_2 (با ۲ انگلیسی)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip هر دو کلید
- [ ] es_get_project_products فرمت آرایه/JSON/کاما را بخواند
- [ ] ستون تاریخ در لیست پروژه‌ها رندر شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-12.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
