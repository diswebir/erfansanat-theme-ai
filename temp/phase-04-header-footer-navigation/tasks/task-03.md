# Task 4.3 — جستجوی گلوبال هدر و سبد خرید هدر

**فاز:** 4 — هدر، مگامنو، منوی موبایل و فوتر  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 4.2

## هدف

اتصال مودال جستجو (`data-global-search-*`) به کوئری واقعی محصولات/پروژه‌ها و شمارنده سبد خرید هدر به WooCommerce.

## فایل‌های هدف

- `template-parts/header/search-modal.php`
- `inc/Search/LiveSearch.php`
- `inc/Woo/HeaderCart.php`

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

1. رندر داده اولیه جستجو از سرور (بدون کوئری اضافه)
2. Endpoint ایجکس امن برای جستجوی زنده
3. شمارنده سبد از `WC()->cart` با Fragment
4. حالت خالی/لودینگ با همان کلاس‌های استاتیک

## شروط پاس شدن (Pass Conditions)

- [ ] جستجو بدون رفرش نتیجه بدهد (<۲۰۰ms)
- [ ] خروجی escape شده و کوئری با `$wpdb->prepare`/`WP_Query` امن باشد
- [ ] شمارنده سبد با افزودن محصول به‌روز شود
- [ ] در نبود ووکامرس، هدر بدون خطا رندر شود

## تست‌های خودکار

- تست endpoint
- تست escaping
- تست fragment سبد
- تست نبود WooCommerce

> اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-3.php`
