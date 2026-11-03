# Task 2.1 — پنل داشبورد اختصاصی (ThemeOptions)

> فاز: داشبورد اختصاصی ادمین + Section Builder · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
پنل تب‌بندی‌شده RTL با ذخیره AJAX، بدون وابستگی به افزونه، امن و بهینه.

## فایل‌های هدف
- `erfansanat/inc/Admin/ThemeOptions.php`
- `erfansanat/inc/Helpers.php`

## شروط پاس شدن (Pass Conditions)
- [ ] بررسی current_user_can('manage_options') و Nonce در ذخیره
- [ ] ذخیره در آپشن واحد erfansanat_theme_options
- [ ] Helper ایستا es_option($key,$default) با کش استاتیک
- [ ] ظاهر مدرن RTL و تب‌بندی

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-02-custom-admin-dashboard/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
