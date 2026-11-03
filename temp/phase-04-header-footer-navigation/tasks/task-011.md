# P04-T011 — تب‌های داخلی مگامنوی موبایل

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.2` · وابستگی‌ها: `P04-T006`, `P04-T007`, `P04-T010`

## هدف دقیق
تب محصولات و نوع/شهر پروژه در drawer موبایل کار کند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/NavWalkers.php`
- `erfansanat/template-parts/header/mobile-menu.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: data-mobile-prod-tab/panel و data-mobile-proj-tab/panel حفظ شوند.
- [ ] AC-02: منبع داده با دسکتاپ یکسان باشد.
- [ ] AC-03: سوئیچ تب بدون reload کار کند.

## سناریوهای تست اختصاصی
- [ ] SC-01: تب نوع/شهر در موبایل عوض شود.
- [ ] SC-02: تعداد آیتم‌ها با تنظیمات برابر باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-011.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-011.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
