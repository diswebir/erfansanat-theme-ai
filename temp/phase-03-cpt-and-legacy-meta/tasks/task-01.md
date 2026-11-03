# Task 3.1 — ثبت CPTها/تکسونومی‌ها + Rewrite + ریدایرکت‌ها

> فاز: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
ثبت بومی projects/project_type/city/services و rewrite دقیق product_cat؛ فیلترهای ادمین؛ ریدایرکت project→/projects/.

## فایل‌های هدف
- `erfansanat/inc/Core/PostTypes.php`
- `erfansanat/inc/Core/Taxonomies.php`
- `erfansanat/inc/Core/Rewrites.php`
- `erfansanat/inc/Core/LegacyRedirects.php`

## شروط پاس شدن (Pass Conditions)
- [ ] projects با rewrite slug=projects, with_front=false, has_archive=true
- [ ] project_type و city با rewrite دقیق و hierarchical=true
- [ ] product_cat با slug=product_cat و with_front=true
- [ ] CPT services (D2) با has_archive=false
- [ ] فیلتر دراپ‌داون شهر/نوع در لیست ادمین پروژه‌ها
- [ ] ستون sortable same_key_hrefl
- [ ] ریدایرکت 301 برگه project به /projects/
- [ ] عدم 404 در لینک‌های قدیمی

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
