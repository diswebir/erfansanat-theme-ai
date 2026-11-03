# Task 6.2 — single-product.php — هیرو محصول و گالری لایت‌باکس میرور

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-02.php`

## 🎯 هدف
هیرو (breadcrumb/H1/زیرعنوان/badges) و گالری دقیق میرور متصل به منابع گالری چندگانه

## 🔗 وابستگی‌ها
- Task 6.1
- Task 3.9

## 📎 مراجع (منابع برش/استخراج)
- product/wall-washer.html سکشن هیرو + gallery-*

## 📤 فایل‌های خروجی
- `single-product.php`
- `template-parts/single-product/hero.php`
- `template-parts/single-product/gallery.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو: breadcrumb سفارشی (خانه → دسته → محصول مطابق میرور)، H1، زیرعنوان zireonvan، عنوان انگلیسی english_titl کوچک زیر H1، badges وضعیت (product-active-purch-badge، وضعیت استعلام)
2. گالری: es_get_product_gallery() → تصویر اصلی gallery-main-img + thumbs (gallery-thumb) + کنترل‌های next/prev/lightbox/counter/expand با hookهای عین میرور
3. تصویر اصلی fallback: thumbnail ووکامرس → placeholder میرور
4. اتریبیوت‌های JS گالری (data-gallery-*) عیناً
5. بدون گالری ووکامرس پیش‌فرض (در 6.1 حذف شد)

## ⛔ محدودیت‌ها و خط‌قرمزها
- DOM گالری عین ref
- lightbox با JS موجود میرور کار کند

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] گالری از galerry_produ با fixture رندر شود
- [ ] fallback زنجیره‌ای تصویر صحیح
- [ ] هیرو شامل zireonvan/english_titl باشد
- [ ] hookهای lightbox حفظ شده باشند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
