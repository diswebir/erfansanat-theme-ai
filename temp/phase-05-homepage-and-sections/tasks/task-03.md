# Task 5.3 — تعاملات صفحه اصلی (اسلایدر هیرو، تایم‌لاین، تب‌ها، شمارنده)

**فاز:** 5 — صفحه اصلی و کامپوننت‌های ماژولار  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 5.2

## هدف

اطمینان از کارکرد تمام ماژول‌های `main.js` روی مارک‌آپ PHP-تولیدشده (بدون تغییر JS) و افزودن داده اولیه صحیح.

## فایل‌های هدف

- `assets/js/main.js (بدون تغییر)`
- `template-parts/home/*`
- `inc/Frontend/InlineData.php`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. بررسی `data-home1-scene-*` و تزریق تعداد صحیح صحنه‌ها
2. تب‌های پورت‌فولیو (`data-home2-port-*`) و تایم‌لاین (`data-home2-history-*`)
3. شمارنده‌ها (`data-counter-target`)
4. مودال ویدیو/لایت‌باکس

## شروط پاس شدن (Pass Conditions)

- [ ] هیچ خطای JS در کنسول نباشد
- [ ] همه تعاملات استاتیک در نسخه PHP کار کنند
- [ ] بدون تغییر در `main.js` (md5 یکسان)

## تست‌های خودکار

- تست md5 main.js
- تست وجود هوک‌های لازم
- بررسی نبود خطای JS با jsdom (در صورت امکان)

> اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-3.php`
