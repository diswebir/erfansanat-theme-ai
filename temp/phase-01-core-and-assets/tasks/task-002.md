# P01-T002 — Bootstrap و ثابت‌های قالب

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.1` · وابستگی‌ها: `P01-T001`

## هدف دقیق
functions.php حداقلی، ثابت‌های مسیر/نسخه و بارگذاری Autoloader را راه‌اندازی کند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/functions.php`
- `erfansanat/inc/Constants.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: ABSPATH guard داشته باشد.
- [ ] AC-02: ثابت‌های ES_THEME_DIR/ES_THEME_URI/ES_VERSION تعریف شوند.
- [ ] AC-03: بارگذاری دوباره Autoloader رخ ندهد.

## سناریوهای تست اختصاصی
- [ ] SC-01: دو بار include کردن bootstrap خطا ندهد.
- [ ] SC-02: مسیرها روی نصب تست درست resolve شوند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-002.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-002.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
