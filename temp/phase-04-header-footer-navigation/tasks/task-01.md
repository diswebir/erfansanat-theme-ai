# Task 4.1 — استخراج فایل‌های مرجع هدر/فوتر و سند نقشه برش

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-01.php`

## 🎯 هدف
قبل از برش، مرجع ثابت DOM و نقشه بلوک‌ها مستند شود تا تست‌های مقایسه‌ای فاز معیار داشته باشند

## 🔗 وابستگی‌ها
- (تسک شروع‌کننده — بدون وابستگی)

## 📎 مراجع (منابع برش/استخراج)
- uploads/static-mirror/index.html (هدر ~۶۷KB، فوتر ~۷KB)

## 📤 فایل‌های خروجی
- `temp/phase-04-header-footer-navigation/refs/header-ref.html`
- `temp/phase-04-header-footer-navigation/refs/footer-ref.html`
- `temp/phase-04-header-footer-navigation/refs/HEADER_MAP.md`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. استخراج بلوک header کامل از index.html به header-ref.html و footer به footer-ref.html
2. مستند HEADER_MAP.md: فهرست بلوک‌ها به ترتیب (نوار بالا؟ — بررسی، لوگو، ناو دسکتاپ، اکشن‌ها، سایدبار موبایل، مودال جستجو) + هر data-hook و نقطه داینامیک‌سازی (لینک‌ها، شمارنده سبد، متن‌ها)
3. مستند کلاس‌های CSS کلیدی مورد نیاز آیتم‌های منو (برای CSS Classes در فهرست‌های وردپرس)
4. ثبت تصمیم نقاط جایگزینی با آپشن/منو (جدول جایگزینی)

## ⛔ محدودیت‌ها و خط‌قرمزها
- ref دقیقاً از میرور (byte-exact بلوک)
- هیچ تغییر ظاهری در ref

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] دو فایل ref موجود و با میرور یکسان باشند
- [ ] HEADER_MAP.md همه data-hookهای هدر/فوتر را فهرست کند
- [ ] جدول نقاط داینامیک کامل باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-01.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## 🔄 چرخه اجرا
- [x] READ MEMORY — refها/نقشه برش از HEADER_MAP
- [x] THINK & PLAN — فرگمنت‌های byte-exact + نقاط داینامیک
- [x] WRITE TEST — `test-task-01.php`
- [x] IMPLEMENT — بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — پاس کامل (مجموع فاز ۴: 212/212)
- [x] UPDATE MEMORY + COMMIT
