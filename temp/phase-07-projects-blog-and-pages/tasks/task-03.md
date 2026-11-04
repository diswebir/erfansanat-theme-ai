# Task 7.3 — taxonomy-project_type.php و taxonomy-city.php

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-03.php`

## 🎯 هدف
آرشیو ترم‌های پروژه مطابق میرور city/*.html و project_type/*.html

## 🔗 وابستگی‌ها
- Task 7.2

## 📎 مراجع (منابع برش/استخراج)
- city/esfahan.html
- project_type/light-tunnel.html

## 📤 فایل‌های خروجی
- `taxonomy-project_type.php`
- `taxonomy-city.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو ترم: عنوان ترم + توضیح ترم + تصویر (اختیاری از متای ترم یا placeholder میرور)
2. گرید پروژه‌های ترم با card-project + نوار فیلتر مشترک (با پیش‌انتخاب ترم جاری)
3. صفحه‌بندی میرور
4. شمارش پروژه‌های ترم

## ⛔ محدودیت‌ها و خط‌قرمزها
- URLهای /project_type/{term} و /city/{term} حفظ

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر ترم fixture
- [ ] گرید و صفحه‌بندی
- [ ] پیش‌انتخاب فیلتر جاری

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
