# Task 3.3 — متاباکس پروژه‌ها، FAQ دسته‌ها، Hreflang و شمارنده بازدید

**فاز:** 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 3.1

## هدف

متاباکس پروژه با ۵ تصویر گالری (با املای دقیق `foure-image`)، تاریخ تکمیل، محصولات مرتبط؛ متاباکس FAQ تکسونومی `product_cat` با Media؛ متاباکس Hreflang؛ شمارنده `number2-viewc`.

## فایل‌های هدف

- `inc/Admin/Metaboxes/ProjectMeta.php`
- `inc/Admin/Metaboxes/TermMeta.php`
- `inc/Admin/Metaboxes/Hreflang.php`
- `inc/Meta/Views.php`

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

1. گالری پروژه با همگام‌سازی دوطرفه بین فیلد مدرن و `second-image..six-image`
2. `finish-pro-time` با اعتبارسنجی الگوی تاریخ جلالی
3. `mortabet_mahsole_2` انتخابگر چندگانه محصول
4. Term meta: `thumbnail` (media) و `faq` (repeater) برای `product_cat`
5. Hreflang برای post/page/product/projects + همه تکسونومی‌ها + ستون `same_key_hrefl`
6. `number2-viewc` با fallback خواندن `number-viewc` و ضدتکرار در یک session

## شروط پاس شدن (Pass Conditions)

- [ ] همه ۷ کلید پروژه و ۴ کلید Hreflang و ۲ کلید تکسونومی تست شوند
- [ ] گالری پروژه با هیچ‌یک از کلیدهای قدیمی گم نشود
- [ ] تاریخ نامعتبر رد شود
- [ ] افزایش بازدید بدون تأثیر بر زمان پاسخ (<5ms) و بدون شمارش ربات‌ها

## تست‌های خودکار

- CRUD کامل متاها
- تست گالری دوطرفه
- تست هنجاری تاریخ
- تست شمارنده بازدید

> اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-3.php`
