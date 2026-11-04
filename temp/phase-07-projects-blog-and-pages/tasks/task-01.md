# Task 7.1 — single-projects.php — سینگل کامل پروژه

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-01.php`

## 🎯 هدف
هیرو، گالری شش‌تایی، مشخصات (تاریخ/شهر/نوع)، بدنه، محصولات پروژه و پروژه‌های مرتبط

## 🔗 وابستگی‌ها
- Task 3.11
- Task 3.12
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- projects/esfahan-shohada-square.html (هیرو/گالری/مشخصات/محصولات)

## 📤 فایل‌های خروجی
- `single-projects.php`
- `template-parts/single-project/*.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو: breadcrumb (خانه → پروژه‌ها → پروژه)، H1، شهر در عنوان مطابق میرور
2. گالری: es_get_project_gallery() → مارک‌آپ lightbox میرور (gallery-*)
3. مشخصات: finish-pro-time (نمایش شمسی سازگار WP-Parsidate)، ترم city با لینک آرشیو، ترم project_type با لینک — جدول مشخصات میرور
4. بدنه: the_content با استایل میرور (تایپوگرافی)
5. محصولات پروژه: mortabet_mahsole_2 → کارت‌های card-product؛ سکشن مخفی اگر خالی
6. پروژه‌های مرتبط: هم‌city یا هم project_type (کوئری سبک) + share-btn میرور

## ⛔ محدودیت‌ها و خط‌قرمزها
- سکشن‌های خالی مخفی
- hookهای گالری عین میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر fixture با همه بخش‌ها
- [ ] متاهای پروژه صحیح نمایش داده شوند
- [ ] محصولات مرتبط از متا
- [ ] گالری lightbox کار کند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-01.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
