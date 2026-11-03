# Task 3.3 — متاباکس پروژه‌ها + FAQ دسته + Hreflang + بازدید

> فاز: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
متاباکس پروژه (تصاویر با املای دقیق)، FAQ و thumbnail دسته محصولات، Hreflang چندزبانه و شمارنده بازدید.

## فایل‌های هدف
- `erfansanat/inc/Admin/ProjectMetaboxes.php`
- `erfansanat/inc/Admin/TermMetaboxes.php`
- `erfansanat/inc/Admin/HreflangMetabox.php`
- `erfansanat/inc/Core/ViewCounter.php`

## شروط پاس شدن (Pass Conditions)
- [ ] کلیدهای second-image,three-image,foure-image,five-image,six-image + گالری مدرن همگام
- [ ] finish-pro-time و mortabet_mahsole_2
- [ ] product_cat -> faq(name_question,answer_question) + thumbnail
- [ ] hrefmeta_fa/en/ar + same_key_hrefl
- [ ] number2-viewc + fallback number-viewc
- [ ] تست خواندن/نوشتن روی همه کلیدها

## تست
- اسکریپت: `tests/test-task-03.php` (اجرا: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
