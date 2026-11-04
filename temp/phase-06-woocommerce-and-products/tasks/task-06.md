# Task 6.6 — single-product — پروژه‌های مرتبط، محصولات مرتبط و دیدگاه‌ها

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[DONE — 22/22]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-06.php`

## 🎯 هدف
اتصال روابط متا + دیدگاه‌های ووکامرس با مارک‌آپ review میرور

## 🔗 وابستگی‌ها
- Task 6.2
- Task 3.10

## 📎 مراجع (منابع برش/استخراج)
- product/wall-washer.html سکشن‌های مرتبط + review-form/reviews-list/rv-*

## 📤 فایل‌های خروجی
- `template-parts/single-product/related-projects.php`
- `template-parts/single-product/related-products.php`
- `template-parts/single-product/reviews.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. پروژه‌های مرتبط: کوئری projects با IDهای project-ids → card-project؛ مخفی اگر خالی
2. محصولات مرتبط: همان product_cat (تعداد آپشن) با کارت محصول؛ جایگزین related پیش‌فرض حذف‌شده
3. دیدگاه‌ها: reviews-list با wp_list_comments (callback سفارشی تولید rv-name/rv-role/rv-comment + ستاره‌ها مطابق میرور) + فرم review-form (name/org/rating/comment) با فیلدهای هوک ووکامرس comment_form_defaults
4. org (سازمان) به‌عنوان meta دیدگاه ذخیره شود (comment_meta)
5. امتیاز: سازگار با rating ووکامرس (meta_rateion استاندارد)

## ⛔ محدودیت‌ها و خط‌قرمزها
- مارک‌آپ دیدگاه عین میرور
- امنیت: escaping همه مقادیر دیدگاه

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پروژه‌های مرتبط از متا (fixture)
- [ ] کامنت callback ساختار rv-* بدهد
- [ ] فرم شامل فیلد سازمان باشد
- [ ] سکشن‌های خالی مخفی

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
