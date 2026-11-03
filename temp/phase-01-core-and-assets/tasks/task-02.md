# Task 1.2 — ماژول DeBloat (پاکسازی هسته)

> فاز: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
حذف کامل استایل/اسکریپت گوتنبرگ، ایموجی‌ها و تگ‌های اضافی wp_head برای حداکثر سرعت.

## فایل‌های هدف
- `erfansanat/inc/Core/DeBloat.php`

## شروط پاس شدن (Pass Conditions)
- [ ] wp-block-library و global-styles dequeue شوند
- [ ] ایموجی‌ها (print_emoji_detection_script, print_emoji_styles) حذف شوند
- [ ] تگ‌های اضافی wp_head (generator, rsd, wlwmanifest, shortlink) پاک شوند
- [ ] در سورس HTML خروجی هیچ CSS/JS بلوک وردپرس نباشد

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-01-core-and-assets/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
