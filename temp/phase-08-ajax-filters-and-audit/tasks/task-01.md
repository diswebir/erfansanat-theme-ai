# Task 8.1 — جستجوی زنده و فیلترهای هوشمند (جایگزین JetSearch/JetSmartFilters)

**فاز:** 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

فیلتر محصولات بر اساس متن/دسته/قیمت/وضعیت فروش/`pa_*` + مرتب‌سازی؛ فیلتر پروژه بر اساس نوع/شهر با شمارنده.

## فایل‌های هدف

- `inc/Ajax/SearchEndpoint.php`
- `inc/Ajax/FilterEndpoint.php`
- `assets/js/filters.js`
- `template-parts/archive/filters.php`

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

1. Endpoint با Nonce، rate-limit سبک و `WP_Query` امن
2. پارامترهای فیلتر whitelist شده
3. فرagment HTML کارت‌ها
4. سازگاری با SEO (لینک‌های قابل crawl برای فیلترهای اصلی)

## شروط پاس شدن (Pass Conditions)

- [ ] پاسخ < ۲۰۰ms در محیط تست
- [ ] مقاوم در برابر SQLi/XSS (تست payload)
- [ ] بدون رفرش صفحه
- [ ] کارکرد بدون جاوااسکریپت (fallback فرم GET)

## تست‌های خودکار

- تست زمان پاسخ
- تست payloadهای امنیتی
- تست fallback بدون JS

> اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-1.php`
