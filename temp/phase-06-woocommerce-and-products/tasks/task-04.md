# Task 6.4 — single-product — Repeaterها، FAQ، کاتالوگ و اطلاعیه قبل از خرید

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-04.php`

## 🎯 هدف
رندر product-feature، feature2 دو بخشی، usage_product/usage_countr، faq آکاردئونی، دکمه کاتالوگ و نوار اطلاعیه

## 🔗 وابستگی‌ها
- Task 6.2
- Task 3.8

## 📎 مراجع (منابع برش/استخراج)
- product/wall-washer.html سکشن‌های ویژگی/کاربرد/FAQ/کاتالوگ

## 📤 فایل‌های خروجی
- `template-parts/single-product/features.php`
- `template-parts/single-product/usages.php`
- `template-parts/single-product/faq.php`
- `template-parts/single-product/notice.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. features: لیست Repeater product-feature با آیکون چک میرور
2. feature2: گرید دو بخشی از product-feature2_copy (عنوان + مقدار با کلاس‌های میرور)
3. usages: ادغام Repeater usage_product و ترم‌های usage_countr (dedup) با آیکون میرور و لینک به آرشیو ترم
4. faq: آکاردئون faq-list با faq-toggle/body/icon — hookها عیناً
5. کاتالوگ: دکمه دانلود product-catalog-file (باز در تب جدید) — مخفی اگر خالی
6. notice: نوار before-sale-c بالای ناحیه خرید — مخفی اگر خالی

## ⛔ محدودیت‌ها و خط‌قرمزها
- سکشن/بخش خالی رندر نشود
- املاهای دقیق متا از فاز ۳ خوانده شود

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] هر ۴ منبع با fixture رندر شوند
- [ ] ادغام usages بدون تکرار
- [ ] FAQ آکاردئون با JS میرور کار کند
- [ ] بخش‌های خالی مخفی

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
