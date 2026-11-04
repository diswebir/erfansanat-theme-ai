# Task 4.8 — تست جامع DOM هدر/فوتر در برابر مرجع استاتیک

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-08.php`

## 🎯 هدف
اسکریپت مقایسه ساختاری: تعداد و ترتیب data-hookها و کلاس‌های کلیدی خروجی رندر شده با ref میرور برابر باشد

## 🔗 وابستگی‌ها
- Task 4.3
- Task 4.5
- Task 4.6

## 📎 مراجع (منابع برش/استخراج)
- temp/phase-04…/refs/*

## 📤 فایل‌های خروجی
- `بسط temp/phase-04…/tests/test-task-08.php (مقایسه DOM با DOMDocument)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. اسکریپت تست: رندر هدر/فوتر با walkerها + fixture داده → parse با DOMDocument → استخراج لیست data-* و کلاس‌ها → مقایسه با ref (اجازه اختلاف فقط در مقادیر داینامیک مجاز)
2. گزارش diff در صورت عدم تطابق (کدام hook/کلاس جا افتاده)
3. تست حالت‌های: با منو/بدون منو/با آپشن سفارشی

## ⛔ محدودیت‌ها و خط‌قرمزها
- مقایسه ساختاری نه byte-to-byte (محتوای داینامیک متفاوت است)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] تست بدون خطا اجرا شود
- [ ] ساختار hookهای JS سراسری یکسان باشد
- [ ] حالت بدون منو هم ساختار ref را داشته باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## 🔄 چرخه اجرا
- [x] READ MEMORY — refها/نقشه برش از HEADER_MAP
- [x] THINK & PLAN — فرگمنت‌های byte-exact + نقاط داینامیک
- [x] WRITE TEST — `test-task-08.php`
- [x] IMPLEMENT — بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — پاس کامل (مجموع فاز ۴: 212/212)
- [x] UPDATE MEMORY + COMMIT
