# Task 3.10 — متاباکس محصول — روابط (project-ids و usage_countr)

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-10.php`

## 🎯 هدف
انتخاب چندگانه پروژه‌های مرتبط و کاربردها (ترم‌های project_type) با ذخیره آرایه/JSON مطابق ساختار فعلی

## 🔗 وابستگی‌ها
- Task 3.7
- Task 3.1

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۴ (روابط)

## 📤 فایل‌های خروجی
- `بسط ProductMetaboxes.php (تب relations)`
- `inc/Admin/RelatedPicker.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کامپوننت RelatedPicker: input جستجو (ajax admin posts/ترم‌ها) + لیست انتخاب‌شده sortable — بدون select2 خارجی (UI داخلی)
2. project-ids: ذخیره آرایه از IDهای projects (اگر داده موجود JSON بود JSON بماند — از RepeaterEngine الگو بگیرد)
3. usage_countr: ذخیره آرایه از term_idهای project_type (املای دقیق کلید)
4. helpers: es_get_product_projects($id)، es_get_product_usages($id) با خواندن مقاوم (آرایه/JSON/کاما-جدا)

## ⛔ محدودیت‌ها و خط‌قرمزها
- املا: project-ids (خط‌تیره) و usage_countr (بدون y)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip هر دو کلید با فرمت‌های آرایه/JSON
- [ ] جستجوی پیکر با nonce و capability
- [ ] helpers هر دو فرمت را بخوانند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-10.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
