# P03-T003 — ثبت تکسونومی project_type

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.1` · وابستگی‌ها: `P03-T001`

## هدف دقیق
نوع پروژه به‌صورت سلسله‌مراتبی و slug-agnostic ثبت شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/Taxonomies.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: hierarchical=true و rewrite دقیق باشد.
- [x] AC-02: به projects متصل باشد.
- [x] AC-03: ترم فارسی بدون تغییر slug کار کند.

## سناریوهای تست اختصاصی
- [x] SC-01: ترم «تونل نوری» لینک صحیح بگیرد.
- [x] SC-02: زیرترم بدون 404 resolve شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-003.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-003.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-003.php` → **15 passed, 0 failed, 0 pending**.
