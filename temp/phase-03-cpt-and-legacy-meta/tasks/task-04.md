# Task 3.4 — Rewrite تکسونومی‌های ووکامرس (product_cat با with_front=true) و Flush نهایی

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-04.php`

## 🎯 هدف
حفظ ساختار سئوی فعلی URLها: /product_cat/{term} با with_front=true و flush خودکار هنگام فعال‌سازی قالب

## 🔗 وابستگی‌ها
- Task 3.1
- Task 3.3

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۳ (نکته مهم سئو product_cat)
- لینک زنده نمونه: https://erfansanat.com/product_cat/strings-and-lighting-decorations/

## 📤 فایل‌های خروجی
- `inc/Data/WooRewrite.php`
- `بسط هوک after_switch_theme`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. فیلتر woocommerce_taxonomy_args_product_cat: rewrite=[slug=product_cat, with_front=true, hierarchical=true]
2. فیلتر woocommerce_taxonomy_args_product_tag: slug=product-tag با with_front=false (مطابق بلوپرینت)
3. فیلتر woocommerce_taxonomy_args_product_brand: hierarchical=true
4. تأیید flush: after_switch_theme (از 1.3) پس از ثبت همه — flush در اولین init بعد از فعال‌سازی
5. مستند جدول URL نهایی در کامنت کلاس

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ تغییری در rewrite سینگل محصول (slug /product از خود ووکامرس فعلی حفظ می‌شود)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] فیلتر product_cat دقیقاً with_front=true و slug=product_cat
- [ ] فیلتر product_tag با slug=product-tag
- [ ] flush در activation زنجیره‌شده باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
