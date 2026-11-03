# Task 5.8 — سکشن خدمات پس از فروش + مشتریان

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-08.php`

## 🎯 هدف
دو سکشن اعتمادساز: aftersale و لوگوی مشتریان/سازمان‌ها

## 🔗 وابستگی‌ها
- Task 5.1

## 📎 مراجع (منابع برش/استخراج)
- index.html aria-label خدمات پس از فروش + مشتریان و سازمان‌های همکار

## 📤 فایل‌های خروجی
- `template-parts/home/section-aftersale.php`
- `template-parts/home/section-customers.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. aftersale: کپی مارک‌آپ + فیلدهای متن/آیکون/لینک گارانتی (لینک به refund_returns)
2. customers: Repeater لوگوها (تصویر+نام+لینک اختیاری) — پیش‌فرض لوگوهای میرور (استخراج از HTML)
3. گرید لوگوها با کلاس‌های میرور و lazy loading تصاویر
4. اگر Repeater خالی → سکشن مخفی

## ⛔ محدودیت‌ها و خط‌قرمزها
- لوگوهای پیش‌فرض از assets میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] Repeater لوگو کار کند
- [ ] سکشن خالی مخفی
- [ ] ساختار مطابق ref

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
