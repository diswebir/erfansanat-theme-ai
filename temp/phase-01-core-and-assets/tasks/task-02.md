# Task 1.2 — ماژول DeBloat و پاکسازی سرصفحه

**فاز:** 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 1.0

## هدف

حذف کامل CSS/JS گوتنبرگ و بلاک‌ها، ایموجی، oEmbed، `wp-embed.js`، و متاتگ‌های اضافی `wp_head` برای رسیدن به بودجه عملکرد.

## فایل‌های هدف

- `inc/Performance/DeBloat.php`

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

1. حذف `wp-block-library`, `wp-block-library-theme`, `global-styles`, `classic-theme-styles`
2. حذف اکشن‌های ایموجی (`print_emoji_*`, `wp_emoji_*`) و فیلترهای `the_content` مرتبط
3. حذف `wp_oembed_add_discovery_links`, `wp_oembed_add_host_js`, `rest_output_link_wp_head`
4. حذف `wp_generator`, `rsd_link`, `wlwmanifest_link`, `shortlink`
5. غیرفعال‌سازی `dashicons` در فرانت (فقط برای کاربر وارد‌شده)
6. حذف `wp-block-library` از `wp_enqueue_scripts` با اولویت ۱۰۰

## شروط پاس شدن (Pass Conditions)

- [ ] هیچ فایل `wp-includes/css/dist/block-library/*` در سورس HTML خروجی نباشد
- [ ] هیچ `wp-emoji`/`wp-embed` در سورس نباشد
- [ ] تعداد `<link rel=stylesheet>` فقط ۲ عدد (styles.css + افزونه‌های لازم)
- [ ] `global-styles` و `classic-theme-styles` حذف شده باشند
- [ ] صفحه ادمین و ویرایشگر بدون خطا کار کند (DeBloat فقط فرانت)

## تست‌های خودکار

- شبیه‌سازی wp_enqueue_scripts و بررسی نبود handleها
- بررسی خروجی wp_head برای الگوهای ممنوع
- تست ایزوله‌بودن ادمین

> اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-2.php`
