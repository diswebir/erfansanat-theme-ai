# Task 7.2 — وبلاگ، تک‌نوشته، جستجو و ۴۰۴

**فاز:** 7 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 7.1

## هدف

`home.php`, `archive.php`, `category.php`, `single.php` (TOC، اشتراک‌گذاری، بازدید `number2-viewc`، مرتبط‌ها، دیدگاه‌ها)، `search.php`, `404.php`.

## فایل‌های هدف

- `home.php`
- `archive.php`
- `category.php`
- `single.php`
- `search.php`
- `404.php`
- `template-parts/blog/*.php`

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

1. آرشیو با فیلتر دسته و جستجو (`data-blog-*`)
2. TOC خودکار از سرتیترهای محتوا
3. افزایش بازدید با `number2-viewc`
4. مرتبط‌ها بر اساس دسته

## شروط پاس شدن (Pass Conditions)

- [ ] تمام صفحات محتوایی رندر شوند
- [ ] شمارنده بازدید افزایش یابد (با fallback `number-viewc`)
- [ ] جستجو نتایج صحیح بدهد
- [ ] ۴۰۴ استایل‌شده و با کد وضعیت صحیح

## تست‌های خودکار

- تست آرشیو/تک‌نوشته
- تست شمارنده
- تست جستجو
- تست ۴۰۴

> اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-2.php`
