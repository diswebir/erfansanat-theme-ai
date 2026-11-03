# Task 3.6 — متاباکس محصول — تب عناوین و وضعیت فروش (zireonvan/english_titl/true_sale/before-sale-c)

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-06.php`

## 🎯 هدف
مدیریت زیرعنوان، عنوان انگلیسی (املا خاص)، اطلاعیه قبل از خرید و وضعیت خرید آنلاین/استعلامی

## 🔗 وابستگی‌ها
- Task 3.5

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۴ (عناوین و وضعیت فروش)
- hero سینگل محصول میرور (زیرعنوان)

## 📤 فایل‌های خروجی
- `بسط ProductMetaboxes.php (تب titles)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. فیلد zireonvan (text) با help «زیرعنوان نمایشی زیر H1 سینگل»
2. فیلد english_titl (text) با help صریح: «کلید دیتابیس english_titl است (بدون e پایانی) — تغییر ندهید»
3. فیلد before-sale-c (textarea، wp_kses_post) — همگام با پنل «اطلاعات محصول» ووکامرس (کلید یکسان)
4. فیلد true_sale (radio true/false، پیش‌فرض true) با توضیح اثر فرانت (دکمه خرید/استعلام)
5. ستون وضعیت خرید (آنلاین/استعلامی) در لیست محصولات با badge رنگی
6. تابع هلپر es_product_is_purchasable($product_id) → (bool) true_sale !== false

## ⛔ محدودیت‌ها و خط‌قرمزها
- املا: english_titl و true_sale (underscore) و before-sale-c (خط‌تیره) دقیقاً

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip هر ۴ کلید
- [ ] true_sale پیش‌فرض true وقتی متا خالی است
- [ ] ستون لیست محصولات دو وضعیت را نشان دهد
- [ ] es_product_is_purchasable صحیح برگرداند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
