# Task 1.3 — انتقال Assets + AssetManager (enqueue شرطی)

> فاز: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
انتقال تمام CSS/JS/فونت/تصاویر استاتیک به assets/ و enqueue شرطی per-template با نسخه‌گذاری filemtime.

## فایل‌های هدف
- `erfansanat/assets/css/styles.css`
- `erfansanat/assets/js/main.js`
- `erfansanat/assets/fonts/`
- `erfansanat/inc/Core/AssetManager.php`

## شروط پاس شدن (Pass Conditions)
- [ ] همه assetهای استاتیک به assets/ منتقل شوند
- [ ] AssetManager فایل هر صفحه را فقط در همان قالب enqueue کند
- [ ] نسخه‌گذاری با filemtime
- [ ] مسیرهای نسبی url(...) داخل CSS نشکنند
- [ ] فونت‌های فارسی با HTTP 200 لود شوند

## تست
- اسکریپت: `tests/test-task-03.php` (اجرا: `php temp/phase-01-core-and-assets/tests/test-task-03.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
