# Task 8.3 — ممیزی نهایی سئو/عملکرد و E2E

**فاز:** 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 8.2

## هدف

SEOPress، Hreflang، JSON-LD، escaping، ریسپانسیو و اجرای مجدد همه تست‌ها.

## فایل‌های هدف

- `temp/phase-08-ajax-filters-and-audit/tests/run-all-phases-e2e.sh`

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

1. اسکن escaping (`esc_html`/`esc_attr`/`wp_kses`*) در همه قالب‌ها
2. بررسی JSON-LD مطابق استاتیک
3. بررسی Hreflang
4. اجرای E2E همه فازها

## شروط پاس شدن (Pass Conditions)

- [ ] ۱۰۰٪ تست‌های فاز ۱–۸ پاس
- [ ] صفر Warning در `WP_DEBUG=true`
- [ ] بدون خطای 404 داخلی
- [ ] آماده استقرار

## تست‌های خودکار

- E2E کامل
- lint امنیتی
- تست JSON-LD
- تست Hreflang

> اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-3.php`
