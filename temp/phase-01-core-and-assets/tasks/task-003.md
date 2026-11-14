# P01-T003 — Autoloader اختصاصی

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.1` · وابستگی‌ها: `P01-T002`

## هدف دقیق
Autoloader فقط کلاس‌های ErfanSanat\Theme را از inc/ بارگذاری کند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Autoloader.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: PSR-4-like mapping برای Namespace قالب داشته باشد.
- [x] AC-02: کلاس خارج از Namespace را بارگذاری نکند.
- [x] AC-03: فایل ناموجود را بدون fatal رد کند.

## سناریوهای تست اختصاصی
- [x] SC-01: بارگذاری کلاس نمونه موفق باشد.
- [x] SC-02: کلاس ناشناخته خطای قابل‌کنترل بدهد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-003.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-003.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-01-core-and-assets/tests/test-task-003.php` → **6 passed, 0 failed, 0 pending**.
- فاز ۱ runner: PASS.
