# P03-T007 — نقشه سازگاری ۱۸ ویژگی pa_*

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.1` · وابستگی‌ها: `P03-T005`

## هدف دقیق
نام/برچسب/نوع ۱۸ ویژگی موجود ووکامرس فهرست و در کد map شود؛ ثبت تکراری انجام نشود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/AttributeMap.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: هر ۱۸ pa_* دقیقاً map شده باشد.
- [ ] AC-02: pa_color نوع color داشته باشد.
- [ ] AC-03: ویژگی موجود دوباره register نشود.

## سناریوهای تست اختصاصی
- [ ] SC-01: نام ویژگی‌ها با DB برابر باشد.
- [ ] SC-02: ویژگی ناموجود fatal ندهد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-007.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-007.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
