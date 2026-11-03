# Task 3.14 — Hreflang سراسری + شمارنده بازدید (number2-viewc)

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-14.php`

## 🎯 هدف
متاباکس ترجمه‌ها روی همه انواع محتوا + ستون مرتب‌سازی + شمارنده بازدید با fallback و ضد-بات

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۶ و §۷
- agent.md §Phase3 Task3.3

## 📤 فایل‌های خروجی
- `inc/Admin/HreflangMeta.php`
- `inc/Frontend/ViewCounter.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. متاباکس «ترجمه‌های این محتوا»: hrefmeta_fa، hrefmeta_en، hrefmeta_ar (url) + same_key_hrefl (text) روی post/page/product/projects/services
2. همان فیلدها روی ترم‌های اصلی (product_cat، project_type، city) با هوک‌های ترم-متا
3. ستون ادمین same_key_hrefl روی همه لیست‌ها + sortable با pre_get_posts
4. ViewCounter: هوک template_redirect فقط روی single (is_singular در post/projects/product/services)، رد is_admin و preview و بات‌های شناخته‌شده UA + dedupe با کوکی ۳۰ دقیقه‌ای؛ افزایش update_post_meta number2-viewc++ (اتمیک با مقایسه‌و‌تنظیم ساده)
5. es_get_views($id): خواندن number2-viewc با fallback number-viewc
6. ستون بازدید در لیست‌ها

## ⛔ محدودیت‌ها و خط‌قرمزها
- کلیدها دقیق: hrefmeta_fa/en/ar، same_key_hrefl، number2-viewc، number-viewc
- پیش‌خوان و پرلود نباید بازدید اضافه کند

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip هر ۴ کلید hreflang روی post و term
- [ ] افزایش بازدید فقط در شرایط مجاز (تست منطق شرط‌ها با stub)
- [ ] es_get_views fallback صحیح
- [ ] ستون‌ها sortable

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-14.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
