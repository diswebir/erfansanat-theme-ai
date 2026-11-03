# Task 7.1 — تک‌پروژه + آرشیوهای پروژه

> فاز: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
single-projects و آرشیوهای نوع/شهر از استاتیک با گالری، تاریخ، شهر/نوع و محصولات استفاده‌شده.

## فایل‌های هدف
- `erfansanat/single-projects.php`
- `erfansanat/archive-projects.php`
- `erfansanat/taxonomy-project_type.php`
- `erfansanat/taxonomy-city.php`

## شروط پاس شدن (Pass Conditions)
- [ ] گالری second-image..six-image (املای foure-image)
- [ ] finish-pro-time, city, project_type, mortabet_mahsole_2
- [ ] رندر آرشیو نوع/شهر منطبق بر استاتیک (slug-agnostic — D3)

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-07-projects-blog-and-pages/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
