# Task 3.9 — متاباکس محصول — گالری (galerry_produ + fallbackها) و فایل کاتالوگ

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-09.php`

## 🎯 هدف
مدیریت گالری سازگار با سه منبع دیتا + دانلود کاتالوگ، با خواندن fallback کامل

## 🔗 وابستگی‌ها
- Task 3.5

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۴ (فایل و گالری — galerry_produ, gallery-filed, image-2..6)

## 📤 فایل‌های خروجی
- `بسط ProductMetaboxes.php (تب gallery)`
- `inc/Data/ProductGallery.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. فیلد product-catalog-file: media picker فایل → ذخیره URL (نوع url مطابق دیتابیس)
2. فیلد galerry_produ: انتخاب چند تصویر (media picker multi) → ذخیره رشته آیدی‌های جداشده با کاما (دقیقاً فرمت فعلی)
3. کلاس ProductGallery::get_ids($product_id): ترتیب fallback: galerry_produ → _product_image_gallery (ووکامرس) → gallery-filed → image-2…image-6 (هر کدام که پر است، آیدی‌ها merge به ترتیب)
4. همگام‌سازی نوشتن: هنگام ذخیره galerry_produ، مقدار به _product_image_gallery هم نوشته شود (سازگاری افزونه‌ها/ووکامرس)
5. Preview thumbnails در متاباکس

## ⛔ محدودیت‌ها و خط‌قرمزها
- املا: galerry_produ و gallery-filed دقیقاً
- فرمت ذخیره galerry_produ = رشته کاما-جدا (نه آرایه)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] get_ids با هر ۴ منبع fixture مقدار صحیح برگرداند (ترتیب اولویت)
- [ ] ذخیره galerry_produ رشته کاما-جدا باشد و به _product_image_gallery هم sync شود
- [ ] کاتالوگ URL ذخیره/بازیابی شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-09.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
