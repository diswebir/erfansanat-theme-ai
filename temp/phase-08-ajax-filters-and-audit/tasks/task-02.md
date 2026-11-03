# Task 8.2 — جستجوی زنده (Live Search) جایگزین JetSearch

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-02.php`

## 🎯 هدف
جستجوی اینستنت محصولات/پروژه‌ها/مقالات در مودال جستجوی هدر با رندر partial سمت سرور

## 🔗 وابستگی‌ها
- Task 8.1

## 📎 مراجع (منابع برش/استخراج)
- مودال جستجوی هدر میرور (search-modal، global-search-input، search-item)

## 📤 فایل‌های خروجی
- `inc/Ajax/LiveSearch.php`
- `assets/js/live-search.js (نازک روی hookهای میرور)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هندلر es_live_search: پارامتر q + type — کوئری سه‌گانه (product/projects/post) هرکدام سقف ۵ با no_found_rows؛ highlight کلیدواژه با esc (بدون تزریق HTML کاربر)
2. رندر ردیف‌های search-item میرور سمت سرور (تصویر/عنوان/قیمت/نوع) — partial HTML
3. live-search.js: اتصال debounce ۲۵۰ms به input موجود (hook حفظ)، نمایش/پنهان مودال مطابق JS میرور؛ نتیجه خالی → پیام میرور
4. کش کوتاه transient برای کوئری‌های تکراری (۶۰ ثانیه)
5. هدف < ۲۰۰ms روی دیتاست تستی (بنچمارک در تست)

## ⛔ محدودیت‌ها و خط‌قرمزها
- همه خروجی escaping
- بدون افزودن کتابخانه

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاسخ partial شامل markup میرور search-item
- [ ] XSS ورودی q ایمن (تست payload)
- [ ] کش کار کند
- [ ] بنچمارک < ۲۰۰ms روی fixture

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
