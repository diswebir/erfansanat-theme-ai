# Task 1.1 — اسکلت پایه قالب + معماری OOP

> فاز: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
ساخت اسکلت قالب در erfansanat/ و معماری شیءگرا با Namespace اختصاصی، بدون خطا و با پشتیبانی‌های لازم.

## فایل‌های هدف
- `erfansanat/style.css`
- `erfansanat/functions.php`
- `erfansanat/index.php`
- `erfansanat/inc/Autoloader.php`
- `erfansanat/inc/Core/Theme.php`

## شروط پاس شدن (Pass Conditions)
- [ ] style.css دارای هدر معتبر قالب (Theme Name, Version, Text Domain, RTL)
- [ ] php -l روی همه فایل‌های PHP پاس شود
- [ ] فعال‌سازی قالب بدون Fatal/Notice
- [ ] add_theme_support برای title-tag, post-thumbnails, woocommerce, html5
- [ ] Autoloader با Namespace ErfanSanat\Theme کلاس‌ها را از inc/ بارگذاری کند

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-01-core-and-assets/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
