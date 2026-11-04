# Task 1.3: انتقال کامل Assets قالب استاتیک به پوشه assets/ و ساخت کلاس AssetManager با filemtime

## فاز مربوطه
- **Phase 1:** زیرساخت هسته قالب، Autoloader، DeBloat و انتقال بی‌نقص Assets استاتیک (`temp/phase-01-core-and-assets`)
- **وضعیت:** `[PASSED]`

## شرح مهندسی تسک
کپی دقیق و بدون تغییر فایل‌های `styles.css`، `main.js`، ۳ فونت `vazirmatn-*.woff2` و ۲۲ فایل تصویر/ویدیو از `static-mirror/` به پوشه `assets/` قالب؛ پیاده‌سازی کلاس `AssetManager.php` برای Enqueue استاندارد با نسخه‌گذاری خودکار `filemtime`، افزودن تگ‌های استاندارد Preload فونت فارسی و تصویر هیرو در `wp_head`، و تزریق شیء تنظیمات ایجکس `esThemeData` (`ajaxUrl`, `nonce`, `homeUrl`, `assetsUrl`, `filterUrlMutation: false`) به اسکریپت اصلی.

## فایل‌های هدف (Target Files)
- `assets/css/styles.css`
- `assets/js/main.js`
- `assets/fonts/*`
- `assets/images/*`
- `inc/Core/AssetManager.php`
- `inc/Helpers/AssetHelper.php`

## شروط پاس شدن (Pass Conditions)
- [x] موجود بودن دقیق فایل‌های `assets/css/styles.css`، `assets/js/main.js`، هر ۳ فونت `vazirmatn-*.woff2` و تمامی ۲۲ فایل رسانه در `assets/images/`.
- [x] صحت ۱۰۰٪ مسیرهای نسبی `url(...)` داخل `assets/css/styles.css` و وجود فیزیکی فایل‌های هدف روی دیسک.
- [x] ثبت و چاپ صحیح استایل `erfansanat-styles` و اسکریپت `erfansanat-main` با پارامتر نسخه `ver=` مبتنی بر `filemtime`.
- [x] تزریق شیء جاوااسکریپت `window.esThemeData` شامل `ajaxUrl`، `nonce` و `assetsUrl` در خروجی صفحه.
- [x] بازگشت کد وضعیت `HTTP 200` هنگام درخواست واقعی با `curl` برای صفحه اصلی، فایل CSS، فایل JS و هر ۳ فایل فونت `woff2`.

## چرخه اجرایی الزامی (6-Step CoT + TDD Loop)
1. `[READ MEMORY]` مطالعه `temp/PROJECT_MEMORY.md`، `temp/STATIC_TEMPLATE_MAP.md` و این فایل تسک.
2. `[THINK & PLAN]` تحلیل ساختار DOM استاتیک و طراحی دقیق معماری PHP/JS.
3. `[WRITE TEST]` تکمیل اسکریپت تست خودکار در `temp/phase-01-core-and-assets/tests/test-task-03.php`.
4. `[IMPLEMENT]` پیاده‌سازی کامل کد بدون هیچ‌گونه Placeholder یا حذف کلاس‌های CSS.
5. `[RUN TEST & CHECK CONDITIONS]` اجرای تست و ثبت نتیجه در `temp/QA_REFLECTION_LOG.md`.
6. `[UPDATE MEMORY]` به‌روزرسانی `temp/PROJECT_MEMORY.md` و `temp/MASTER_PLAN.md` + ثبت Git Commit.
