# Task 5.2 — اتصال سکشن‌های داینامیک به WP_Query و کارت‌های استاندارد

**فاز:** 5 — صفحه اصلی و کامپوننت‌های ماژولار  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 5.1

## هدف

دسته‌های `product_cat`، محصولات منتخب، آخرین پروژه‌ها، آخرین مقالات، آمار و مشتریان با کوئری بهینه و کارت‌های `template-parts/cards/*`.

## فایل‌های هدف

- `template-parts/cards/card-product.php`
- `card-project.php`
- `card-post.php`
- `inc/Query/HomeQueries.php`

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

1. `WP_Query` با `no_found_rows`, `update_post_meta_cache=false`, `fields` مناسب
2. Transient cache با invalidation روی `save_post`
3. کارت‌ها با همان مارک‌آپ کارت‌های میرور
4. دسته‌ها از `get_terms` با `thumbnail` و شمارش
5. آمار/شمارنده‌ها از آپشن (قابل ویرایش)

## شروط پاس شدن (Pass Conditions)

- [ ] صفر محتوای هاردکد (فقط fallback پیش‌فرض)
- [ ] ≤ ۳۰ کوئری در صفحه اصلی
- [ ] کارت‌ها با ۳ فایل استاندارد رندر شوند
- [ ] با خالی بودن یک CPT، سکشن حذف/خالی رندر نشود

## تست‌های خودکار

- شمارش کوئری
- تست کارت‌ها
- تست حالت خالی
- تست cache invalidation

> اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-2.php`
