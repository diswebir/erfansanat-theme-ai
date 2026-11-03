# Task 6.8 — taxonomy-product_cat (+FAQ دسته) و taxonomy-product_tag

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-08.php`

## 🎯 هدف
آرشیو دسته با هیرو/توضیح/FAQ و آرشیو برچسب مطابق میرور

## 🔗 وابستگی‌ها
- Task 6.7
- Task 3.13

## 📎 مراجع (منابع برش/استخراج)
- product_cat/wall-washer.html (هیرو H1 سئو، توضیح، گرید، FAQ)

## 📤 فایل‌های خروجی
- `taxonomy-product_cat.php`
- `taxonomy-product_tag.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو: عنوان ترم (لیبل سئویی فعلی ترم)، توضیح ترم، تصویر هیرو از متای thumbnail ترم (3.13) با fallback میرور
2. گرید کارت‌های محصول ترم جاری + صفحه‌بندی (نوار فیلتر مشترک 6.7 مجدد استفاده)
3. FAQ دسته: آکاردئون faq میرور از متای ترم — مخفی اگر خالی
4. product_tag: گرید ساده + صفحه‌بندی
5. breadcrumb صحیح (خانه → دسته)

## ⛔ محدودیت‌ها و خط‌قرمزها
- قواعد URL دست نخورده (product_cat/{term})
- FAQ فقط اگر متا پر

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر ترم fixture با thumbnail fallback
- [ ] FAQ ترم آکاردئونی
- [ ] breadcrumb صحیح
- [ ] صفحه‌بندی کار کند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
