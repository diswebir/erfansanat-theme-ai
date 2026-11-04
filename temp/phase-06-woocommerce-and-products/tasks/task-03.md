# Task 6.3 — single-product — تب‌های مشخصات و جدول ۱۸ ویژگی فنی (pa_*)

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[DONE — 17/17]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-03.php`

## 🎯 هدف
تب‌بندی prod-tab میرور با جدول کامل اتریبیوت‌ها + شناسه‌های قانونی

## 🔗 وابستگی‌ها
- Task 6.2
- Task 3.5

## 📎 مراجع (منابع برش/استخراج)
- product/wall-washer.html prod-tabs-container/prod-tab-btn/panel/icon

## 📤 فایل‌های خروجی
- `template-parts/single-product/specs-tabs.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کپی ساختار تب‌ها؛ تب‌ها: مشخصات فنی، ویژگی‌ها، کاربردها، سوالات متداول (فقط تب‌های دارای محتوا رندر شوند)
2. جدول مشخصات: همه attributes ووکامرس (pa_*) مقدار-دار با لیبل فارسی ثبت‌شده + اضافه‌کردن ۶ شناسه قانونی (gs1_id، national_code، hs_id، isic_id، cpc_id، product_id) با لیبل‌های میرور
3. دکمه scroll-to-specs حفظ شود
4. تب خالی → دکمه تب هم رندر نشود
5. خروجی جدول با کلاس‌های میرور (table spec)

## ⛔ محدودیت‌ها و خط‌قرمزها
- اتریبیوت خالی در جدول نیاید
- لیبل از taxonomy labels ووکامرس (نه هاردکد)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] فقط اتریبیوت‌های مقدار-دار در جدول (fixture)
- [ ] شناسه‌های قانونی در جدول باشند
- [ ] تب‌های خالی مخفی
- [ ] ساختار tabs مطابق ref

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-03.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
