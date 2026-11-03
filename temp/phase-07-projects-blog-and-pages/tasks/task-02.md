# Task 7.2 — وبلاگ + تک‌نوشته (TOC/دیدگاه) + جستجو + 404

> فاز: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
قالب‌های وبلاگ و تک‌نوشته با TOC و دیدگاه (D6)، اشتراک، شمارنده بازدید، مرتبط؛ search و 404.

## فایل‌های هدف
- `erfansanat/home.php`
- `erfansanat/archive.php`
- `erfansanat/category.php`
- `erfansanat/single.php`
- `erfansanat/search.php`
- `erfansanat/404.php`
- `erfansanat/template-parts/post/`

## شروط پاس شدن (Pass Conditions)
- [ ] تک‌نوشته: TOC + اشتراک + number2-viewc (افزایش خودکار) + مرتبط + دیدگاه
- [ ] کارکرد همه صفحات محتوایی
- [ ] search.php و 404.php با همان design system

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-07-projects-blog-and-pages/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
