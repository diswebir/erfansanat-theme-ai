# Task 5.3 — سکشن گالری پروژه‌ها (projects)

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-03.php`

## 🎯 هدف
نمایش آخرین/برگزیده پروژه‌ها از WP_Query با کارت استاندارد

## 🔗 وابستگی‌ها
- Task 5.1
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- index.html سکشن #projects

## 📤 فایل‌های خروجی
- `template-parts/home/section-projects.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. WP_Query projects با posts_per_page از فیلد سکشن (پیش‌فرض مطابق میرور)، orderby: تاریخ یا انتخاب دستی (فیلد آیدی‌ها اختیاری)
2. حلقه با card-project.php (تسک 5.11) — هیچ کارت inline
3. فیلد عنوان/متن معرفی سکشن + لینک «مشاهده همه» به archive-projects
4. no_found_rows=true برای پرفورمنس؛ اگر خالی → کل سکشن مخفی

## ⛔ محدودیت‌ها و خط‌قرمزها
- صفر محتوای هاردکد
- کوئری سبک (fields ids در صورت نیاز)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] کوئری args صحیح (تست)
- [ ] سکشن خالی رندر نشود
- [ ] کارت‌ها از card-project استفاده کنند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
