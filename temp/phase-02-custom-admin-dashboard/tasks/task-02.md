# Task 2.2 — تب‌های مدیریتی سراسری

> فاز: داشبورد اختصاصی ادمین + Section Builder · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
تب‌های هدر/مگامنو/لوگو/اعلان/فوتر/تماس/شبکه‌های/رنگ‌ها/اسکریپت‌ها/آرشیو با پیش‌فرض دقیق استاتیک.

## فایل‌های هدف
- `erfansanat/inc/Admin/ThemeOptions.php`
- `erfansanat/inc/Defaults.php`

## شروط پاس شدن (Pass Conditions)
- [ ] هر فیلد مقدار پیش‌فرض برابر محتوای استاتیک داشته باشد
- [ ] تغییر ادمین بلافاصله در فرانت‌اند اعمال شود
- [ ] فیلدهای Media/Link/Repeater پشتیبانی شوند

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-02-custom-admin-dashboard/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
