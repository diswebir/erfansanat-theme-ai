# P03-T020 — متاباکس گالری پروژه

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.3` · وابستگی‌ها: `P03-T012`, `P02-T006`

## هدف دقیق
پنج تصویر legacy پروژه و گالری مدرن همگام مدیریت شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/ProjectMetaboxes.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`second-image`, `three-image`, `foure-image`, `five-image`, `six-image`

## شروط پذیرش اختصاصی
- [x] AC-01: second-image/three-image/foure-image/five-image/six-image دقیق باشند.
- [x] AC-02: گالری مدرن ترتیب را به کلیدهای legacy map کند.
- [x] AC-03: foure-image تغییر املا ندهد.

## سناریوهای تست اختصاصی
- [x] SC-01: تصویر چهارم در foure-image ذخیره شود.
- [x] SC-02: حذف/جابجایی گالری همگام بماند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php` → **16 passed, 0 failed, 0 pending**.
