# Task 5.1 — برش ۱۵ سکشن صفحه اصلی به `template-parts/home/`

**فاز:** 5 — صفحه اصلی و کامپوننت‌های ماژولار  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

هر سکشن یک فایل، با رندر پویا بر اساس Section Manager و حفظ کامل مارک‌آپ؛ فقط واریانت هیروی فعال (home1) رندر می‌شود، دو واریانت دیگر به‌صورت فایل آماده و از داشبورد قابل فعال‌سازی‌اند.

## فایل‌های هدف

- `front-page.php`
- `template-parts/home/section-hero.php`
- `section-projects.php`
- `section-gallery.php`
- `section-about.php`
- `section-timeline.php`
- `section-products.php`
- `section-catalog.php`
- `section-services.php`
- `section-quality.php`
- `section-support.php`
- `section-clients.php`
- `section-blog.php`
- `section-cta.php`
- `section-map.php`
- `template-parts/home/hero-variants/hero-home2.php`
- `hero-home3.php`

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

1. استخراج هر سکشن با ابزار برش (offsets دقیق از `temp/_scan/sections.json`)
2. پارامتری‌کردن متون/تصاویر/لینک‌ها از Theme Options و Section Schema
3. حفظ `data-home1-*`, `data-home2-*` و همه کلاس‌ها
4. انتخاب واریانت هیرو از آپشن (`es_option('home_hero_variant')`)
5. حذف `custom=...` و `style`های تولیدی React در صورت بی‌اثر بودن

## شروط پاس شدن (Pass Conditions)

- [ ] رندر پیش‌فرض با استاتیک منطبق باشد (تست ساختاری سکشن‌به‌سکشن)
- [ ] تغییر ترتیب/خاموش‌کردن سکشن از داشبورد کار کند
- [ ] هیچ متن فارسی هاردکدی در فایل‌های سکشن نباشد (به‌جز fallback پیش‌فرض در Registry)
- [ ] تمام ۱۵ سکشن موجود باشند

## تست‌های خودکار

- مقایسه سکشن‌به‌سکشن
- تست ترتیب
- اسکن متن هاردکد
- شمارش ۱۵ فایل

> اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-1.php`
