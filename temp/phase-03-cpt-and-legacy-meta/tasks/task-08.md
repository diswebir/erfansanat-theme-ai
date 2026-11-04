# Task 3.8 — متاباکس محصول — Repeaterهای ویژگی‌ها/کاربردها/FAQ با موتور 3.7

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-08.php`

## 🎯 هدف
چهار فیلد تکرارشونده محصول با UI افزودن/حذف/جابه‌جایی و کلیدهای دقیق دیتابیس

## 🔗 وابستگی‌ها
- Task 3.7
- Task 2.2

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۴ (فیلدهای تکرارشونده)

## 📤 فایل‌های خروجی
- `بسط ProductMetaboxes.php (تب repeaters)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. product-feature → زیرفیلد add-new-feature (متن ویژگی)
2. product-feature2_copy → دو زیرفیلد: add-new-feature (عنوان مثل گارانتی) + vaule_of_variable (مقدار مثل ۲۴ ماهه — املای دقیق دیتابیس)
3. usage_product → زیرفیلد add-new-usage
4. faq → دو زیرفیلد: name_question + answer_question
5. UI repeater با FieldRenderer (render در متاباکس، سرویس ذخیره از RepeaterEngine::write با detect فرمت read اولیه)
6. نمایش preview تعداد ردیف‌ها در لیست محصولات (اختیاری)

## ⛔ محدودیت‌ها و خط‌قرمزها
- املای دقیق: product-feature2_copy، vaule_of_variable، add-new-feature، add-new-usage، name_question، answer_question

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip هر ۴ repeater با fixture JSON و serial
- [ ] زیرکلیدها دقیقاً مطابق بلوپرینت (تست رشته‌ای)
- [ ] UI افزودن/حذف ردیف در JS کار کند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
