# Task 5.6 — سکشن دسته‌بندی محصولات (categories)

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-06.php`

## 🎯 هدف
گرید دسته‌های product_cat با تصویر ترم (متای فاز 3.13) و fallback

## 🔗 وابستگی‌ها
- Task 5.1
- Task 3.13

## 📎 مراجع (منابع برش/استخراج)
- index.html سکشن #products و home1-categories-grid

## 📤 فایل‌های خروجی
- `template-parts/home/section-categories.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. WP_Term_Query product_cat با hide_empty و تعداد از فیلد سکشن
2. هر ترم: تصویر از es_get_category_thumb (fallback: تصویر پیش‌فرض میرور یا اولین محصول)
3. مارک‌آپ گرید و hover میرور حفظ شود؛ لینک به /product_cat/{slug}
4. فیلتر ترم‌های نمایش‌داده‌شده: فیلد انتخاب دستی ترم‌ها در سکشن (اختیاری؛ پیش‌فرض همه)
5. عنوان/زیرعنوان سکشن از fields

## ⛔ محدودیت‌ها و خط‌قرمزها
- بدون کوئری اضافه در حلقه (thumbnail ترم یک متاست)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] ترم‌های خالی طبق میرور مدیریت شوند
- [ ] fallback تصویر کار کند
- [ ] لینک ترم صحیح

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
