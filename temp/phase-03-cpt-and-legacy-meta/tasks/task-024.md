# P03-T024 — ذخیره‌گاه شمارنده بازدید

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.3` · وابستگی‌ها: `P03-T012`

## هدف دقیق
خواندن/نوشتن number2-viewc با fallback قدیمی و قفل سبک پیاده شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/ViewCounter.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`number2-viewc`, `number-viewc`

## شروط پذیرش اختصاصی
- [x] AC-01: number2-viewc منبع اصلی باشد.
- [x] AC-02: number-viewc فقط fallback خواندن باشد.
- [x] AC-03: مقدار غیرعددی امن شود.
- [x] AC-04: افزایش اتمیک/بدون race باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: fallback درست خوانده شود.
- [x] SC-02: دو افزایش هم‌زمان مقدار را گم نکند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php` → **14 passed, 0 failed, 0 pending**.
