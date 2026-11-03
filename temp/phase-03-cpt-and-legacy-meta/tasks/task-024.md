# P03-T024 — ذخیره‌گاه شمارنده بازدید

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: number2-viewc منبع اصلی باشد.
- [ ] AC-02: number-viewc فقط fallback خواندن باشد.
- [ ] AC-03: مقدار غیرعددی امن شود.
- [ ] AC-04: افزایش اتمیک/بدون race باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: fallback درست خوانده شود.
- [ ] SC-02: دو افزایش هم‌زمان مقدار را گم نکند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
