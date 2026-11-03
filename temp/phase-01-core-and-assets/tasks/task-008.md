# P01-T008 — انتقال CSS و فونت‌ها

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.3` · وابستگی‌ها: `P01-T001`

## هدف دقیق
styles.css و فونت‌های وزیرمتن عیناً به assets/ کپی شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/assets/css/styles.css`
- `erfansanat/assets/fonts/`

## منابع/مرجع
- `static-mirror/assets/css/styles.css`
- `static-mirror/assets/fonts/`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: hash فایل CSS/فونت با مرجع یکسان باشد.
- [ ] AC-02: url(...)های نسبی فونت نشکنند.
- [ ] AC-03: فونت‌ها local و بدون CDN باشند.

## سناریوهای تست اختصاصی
- [ ] SC-01: HTTP 200 برای CSS/woff2.
- [ ] SC-02: مقایسه بایتی با mirror پاس شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-008.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-008.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
