# Task 3.13 — متای ترم product_cat — تصویر دسته (thumbnail) و FAQ دسته

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-13.php`

## 🎯 هدف
فرم‌های افزودن/ویرایش دسته محصول با فیلد تصویر و سوالات متداول تکرارشونده (کلیدهای thumbnail و faq)

## 🔗 وابستگی‌ها
- Task 3.7

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۸
- FAQ سکشن product_cat/*.html میرور

## 📤 فایل‌های خروجی
- `inc/Admin/CategoryTermMeta.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هوک product_cat_add_form_fields / edit_form_fields: فیلد media «thumbnail» (ذخیره آیدی در termmeta با کلید thumbnail مطابق دیتابیس) + repeater faq با زیرفیلدهای name_question/answer_question (ذخیره با RepeaterEngine::write_term)
2. هوک created_/edited_product_cat برای ذخیره با nonce و capability manage_product_terms
3. helper: es_get_category_faq($term_id)، es_get_category_thumb($term_id) با fallback به تصویر اول محصول دسته (اختیاری در فرانت)
4. ستون تصویر دسته در لیست دسته‌ها

## ⛔ محدودیت‌ها و خط‌قرمزها
- کلید متای ترم دقیقاً thumbnail و faq (بدون پیشوند) — سازگار با JetEngine فعلی

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip thumbnail و faq روی ترم fixture
- [ ] فرم رندر هر دو محل (add/edit)
- [ ] helpers مقدار صحیح برگردانند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-13.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
