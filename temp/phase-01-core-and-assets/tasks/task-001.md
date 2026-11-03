# P01-T001 — هدر قالب و style.css

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.1` · وابستگی‌ها: ندارد

## هدف دقیق
فقط متادیتای قالب و فایل style.css پایه ساخته شود؛ CSS استاتیک هنوز کپی نمی‌شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.
- یادداشت: RTL از تنظیمات سایت می‌آید؛ هدر قالب نباید CSS اضافه تزریق کند.

## فایل‌های هدف
- `erfansanat/style.css`

## منابع/مرجع
- `static-mirror/assets/css/styles.css`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: هدر Theme Name/Version/Text Domain/Requires PHP/WP معتبر باشد.
- [ ] AC-02: style.css فقط هدر قالب باشد و CSS استاتیک را duplicate نکند.
- [ ] AC-03: Text Domain با بارگذاری ترجمه یکسان باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: WP قالب را در فهرست پوسته‌ها نشان دهد.
- [ ] SC-02: فعال‌سازی بدون fatal انجام شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-001.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-001.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
