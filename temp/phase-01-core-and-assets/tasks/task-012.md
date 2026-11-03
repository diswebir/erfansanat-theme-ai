# P01-T012 — یکپارچگی asset و بسته‌بندی

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.3` · وابستگی‌ها: `P01-T011`

## هدف دقیق
تست رگرسیون assetها و خروجی زیپ فقط پوشه erfansanat/ تهیه شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/tools/package-theme.sh`
- `temp/phase-01-core-and-assets/tests/test-task-012.php`

## منابع/مرجع
- `static-mirror/`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: زیپ شامل static-mirror/temp/node_modules نباشد.
- [x] AC-02: همه url(...)های CSS resolve شوند.
- [x] AC-03: فونت/تصویر/ویدیو 200 بدهند.

## سناریوهای تست اختصاصی
- [x] SC-01: unzip و فعال‌سازی پوسته بسته‌بندی‌شده موفق باشد.
- [x] SC-02: asset شکسته صفر باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-012.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-012.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-01-core-and-assets/tests/test-task-012.php` → **9 passed, 0 failed, 0 pending**.
- فاز ۱ runner: PASS.
