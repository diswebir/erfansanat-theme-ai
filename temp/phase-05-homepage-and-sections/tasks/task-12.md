# Task 5.12 — تست جامع رندر صفحه اصلی (مقایسه با index.html و سکشن‌های toggle)

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-12.php`

## 🎯 هدف
رندر کامل front-page در حالت پیش‌فرض = ساختار میرور؛ تغییر ترتیب/حذف از ادمین اثر واقعی داشته باشد

## 🔗 وابستگی‌ها
- Task 5.2 … 5.11

## 📎 مراجع (منابع برش/استخراج)
- index.html
- temp/phase-05…/tests/

## 📤 فایل‌های خروجی
- `بسط temp/phase-05…/tests/test-task-12.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. رندر all sections با fixture data → استخراج hookها/کلاس‌ها → مقایسه با index.html (ساختاری)
2. تست toggle: سکشن disabled → خروجی نباید شامل id/data-hookهای آن باشد
3. تست reorder: ترتیب جدید در خروجی رعایت شود
4. تست کوئری‌ها: سکشن‌های داینامیک با fixture دیتابیس (stub WP_Query ساده) args صحیح

## ⛔ محدودیت‌ها و خط‌قرمزها
- تست سریع و بدون دیتابیس واقعی (stub)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر پیش‌فرض ساختاری مطابق میرور
- [ ] toggle و reorder اثر کنند
- [ ] args کوئری‌ها صحیح

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-12.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
