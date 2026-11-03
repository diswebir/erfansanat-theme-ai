# Task 6.11 — حساب کاربری (my-account) و صفحه auth (ورود پیامکی voorodak)

**فاز:** 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-06-woocommerce-and-products/tests/test-task-11.php`

## 🎯 هدف
پوسته یکدست صفحات حساب کاربری ووکامرس و هماهنگی با افزونه ورود پیامکی

## 🔗 وابستگی‌ها
- Task 6.10

## 📎 مراجع (منابع برش/استخراج)
- کامپوننت‌های فرم میرور (input/button/label)

## 📤 فایل‌های خروجی
- `woocommerce/myaccount/*.php overrides (سبک)`
- `assets/css/wc-forms-override.css (یا در styles میرور enqueue شرطی)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. استایل لاگین/ثبت‌نام/داشبورد/سفارش‌ها با کلاس‌های فرم میرور (بدون تغییر markup اساسی ووکامرس — override نقطه‌ای)
2. بررسی صفحه auth افزونه voorodak: حفظ کارکرد + هماهنگی ظاهری (بدون دخالت در فلو OTP)
3. منوی حساب کاربری (endpoints) با استایل میرور
4. صفحات orders/addresses/downloads با جدول‌های میرورگونه

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ override تخریب‌کننده روی منطق ووکامرس
- فقط پوسته

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] لاگین/ثبت‌نام رندر بدون خطا
- [ ] صفحات endpoint کار کنند
- [ ] auth افزونه بدون تداخل

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-06-woocommerce-and-products/tests/test-task-11.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
