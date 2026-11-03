# Task 3.1 — ثبت CPT projects و تکسونومی‌های project_type و city

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php`

## 🎯 هدف
جایگزینی مستقیم رجیستر JetEngine با کد قالب — آرگومان‌ها بایت‌به‌بایت مطابق بلوپرینت

## 🔗 وابستگی‌ها
- Task 1.3

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۲.۱ و §۳
- agent.md §Phase3

## 📤 فایل‌های خروجی
- `inc/Data/PostTypes.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. register_post_type('projects'): public=true، has_archive=true، rewrite slug=projects with_front=false، hierarchical=false، menu_icon=dashicons-screenoptions، supports=[title,editor,thumbnail,comments,custom-fields,revisions]، show_in_rest=true
2. register_taxonomy('project_type', 'projects'): hierarchical=true، rewrite=[slug=project_type, with_front=false, hierarchical=false]، show_in_rest=true، show_admin_column=true
3. register_taxonomy('city', 'projects'): تنظیمات مشابه با slug=city
4. لیبل‌های فارسی برای همه (پروژه‌ها/نوع پروژه/محل پروژه)
5. ثبت در init با اولویت مناسب (پس از ووکامرس)

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ آرگومانی نباید با بلوپرینت فرق کند
- rewrite hierarchical=false برای تکسونومی‌ها (مطابق بلوپرینت)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] آرایه args ثبت projects دقیقاً شامل همه کلیدهای بلوپرینت باشد
- [ ] هر دو تکسونومی با slug صحیح و hierarchical=true ثبت شوند
- [ ] object_type هر دو تکسونومی = projects باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
