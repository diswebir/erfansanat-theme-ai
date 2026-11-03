# Task 8.1 — Live AJAX Search + Smart Filters

> فاز: ایجکس، فرم‌ها و ممیزی نهایی · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
جایگزینی کامل JetSearch/JetSmartFilters با موتور بومی ایجکس امن و سریع.

## فایل‌های هدف
- `erfansanat/inc/Core/AjaxSearch.php`
- `erfansanat/inc/Core/SmartFilters.php`
- `erfansanat/assets/js/filters.js`

## شروط پاس شدن (Pass Conditions)
- [ ] فیلتر محصول: متن/product_cat/قیمت/true_sale/pa_* + مرتب‌سازی + حذف فیلتر
- [ ] فیلتر پروژه: project_type/city + شمارنده
- [ ] پاسخ <200ms
- [ ] امنیت SQLi/XSS (nonce+sanitize+escape)
- [ ] به‌روزرسانی بدون رفرش

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
