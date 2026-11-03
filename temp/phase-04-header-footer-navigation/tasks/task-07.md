# Task 4.7 — Walkerهای tolidat-sherkat و landing

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-07.php`

## 🎯 هدف
دو موقعیت منوی ساده: تولیدات شرکت (فوتر/هدر) و منوی اسکرول لندینگ‌ها

## 🔗 وابستگی‌ها
- Task 4.4

## 📎 مراجع (منابع برش/استخراج)
- لینک‌های فوتر میرور (تولیدات)
- agent.md §موقعیت‌های منو

## 📤 فایل‌های خروجی
- `inc/Nav/SimpleWalker.php`
- `inc/Nav/LandingWalker.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. SimpleWalker: ul/li با کلاس‌های میرور فوتر (بدون زیرمنو یا یک سطح)
2. LandingWalker: آیتم‌ها به‌صورت data-scroll link (anchor به id سکشن‌های لندینگ) + active on scroll با JS میرور (hook حفظ)
3. فراخوانی tolidat-sherkat در جای مشخص‌شده HEADER_MAP.md (فوتر/هدر)
4. fallback هر دو: لینک‌های پیش‌فرض میرور

## ⛔ محدودیت‌ها و خط‌قرمزها
- کلاس‌ها مطابق مرجع
- منوی landing فقط در قالب‌های لندینگ لود شود

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر هر دو walker با fixture صحیح
- [ ] fallback کار کند
- [ ] انکرهای landing به سکشن‌ها اشاره کنند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-07.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
