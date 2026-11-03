# P01-T007 — DeBloat: head و ایموجی

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.2` · وابستگی‌ها: `P01-T006`

## هدف دقیق
تگ‌های اضافی wp_head و ایموجی‌ها پاک شوند؛ SEO/feed حفظ شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/DeBloat.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: emoji script/style حذف شود.
- [x] AC-02: generator/rsd/wlwmanifest/shortlink اضافی حذف شوند.
- [x] AC-03: title/meta/feed/hreflang حذف نشوند.

## سناریوهای تست اختصاصی
- [x] SC-01: wp_head خروجی تمیز داشته باشد.
- [x] SC-02: فید و سئو پس از پاکسازی سالم بمانند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-007.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-007.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-01-core-and-assets/tests/test-task-007.php` → **11 passed, 0 failed, 0 pending**.
- فاز ۱ runner: PASS.
