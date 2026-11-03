# Task 4.2 — Custom Nav Walkers

> فاز: هدر، مگامنو، منوی موبایل، فوتر · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
Walkerهای اختصاصی برای ۴ موقعیت منو با تولید DOM دقیقاً منطبق بر استاتیک.

## فایل‌های هدف
- `erfansanat/inc/Core/NavWalkers.php`
- `erfansanat/inc/Core/Menus.php`

## شروط پاس شدن (Pass Conditions)
- [ ] ثبت ۴ موقعیت: main-mega-menu, mobile-menu, tolidat-sherkat, landing
- [ ] DOM منوها منطبق بر مارک‌آپ استاتیک (data-mega-*, data-mobile-acc-*)
- [ ] کارکرد JS باز/بسته مگامنو و منوی موبایل

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-04-header-footer-navigation/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
