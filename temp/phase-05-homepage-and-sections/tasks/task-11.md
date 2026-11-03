# Task 5.11 — کارت‌های استاندارد: card-product / card-project / card-post

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-11.php`

## 🎯 هدف
سه قالب کارت مشترک مطابق کارت‌های میرور — قابل استفاده در همه آرشیوها و سکشن‌ها

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- کارت محصول: گرید shop.html (product-item با image/title/price/sku/purchasable/rating/views)
- کارت پروژه: projects.html (project-item)
- کارت مقاله: blog.html (blog-item)

## 📤 فایل‌های خروجی
- `template-parts/cards/card-product.php`
- `template-parts/cards/card-project.php`
- `template-parts/cards/card-post.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. card-product: برش دقیق کارت میرور؛ args: object محصول یا WP_Post؛ اتصال: تصویر (get_the_post_thumbnail یا اولین گالری)، title، price (wc_price)، sku، badge purchasable (true_sale)، rating (ووکامرس avg)، views (number2-viewc)
2. card-project: تصویر شاخص/اولین گالری، title، location (city term)، date (finish-pro-time)، rating/views مطابق میرور
3. card-post: تصویر، دسته، عنوان، خلاصه کوتاه، تاریخ، زمان مطالعه تخمینی
4. همه argsها با $args['post'] پارامتری؛ استفاده در حلقه‌ها با es_get_template
5. بدون وضعیت خطا در نبود تصویر (placeholder میرور)

## ⛔ محدودیت‌ها و خط‌قرمزها
- کلاس‌ها/data-hookهای کارت عین میرور
- escaping کامل خروجی

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر هر سه کارت با fixture بدون خطا
- [ ] وضعیت بدون تصویر placeholder
- [ ] badge purchasable دو وضعیت

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-11.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
