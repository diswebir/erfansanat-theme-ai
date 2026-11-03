# Task 1.4 — DeBloat — حذف کامل CSS/JS گوتنبرگ از فرانت‌اند

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-04.php`

## 🎯 هدف
هیچ فایل یا استایل درون‌خطی بلوک‌های وردپرس در فرانت لود نشود؛ بدون تخریب ادیتور ادمین

## 🔗 وابستگی‌ها
- Task 1.1

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase1 Task1.2

## 📤 فایل‌های خروجی
- `inc/Frontend/DeBloat.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. remove_action های wp_enqueue_scripts مربوط به wp-block-library، global-styles، classic-theme-styles
2. حذف استایل درون‌خطی svg-filters (duotone) و فایل block render style در صورتی که قالب کلاسیک است
3. غیرفعال‌سازی theme.json پیش‌فرض سازگاری (add_editor_style دست نزنیم — فقط فرانت)
4. همه حذف‌ها شرطی به !is_admin() باشند

## ⛔ محدودیت‌ها و خط‌قرمزها
- ادمین و ادیتور بلوکی باید کاملاً سالم بمانند
- هیچ fatal در صورت فعال بودن قالب‌های بلوکی جانبی

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] در simulate خروجی wp_enqueue_scripts فرانت هیچ هندل block-library/global-styles/classic-theme-styles ثبت نشده باشد
- [ ] کد حذف شرطی به is_admin باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-04.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
