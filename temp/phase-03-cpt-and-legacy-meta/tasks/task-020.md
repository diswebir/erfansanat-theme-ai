# P03-T020 — متاباکس گالری پروژه

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: second-image/three-image/foure-image/five-image/six-image دقیق باشند.
- [ ] AC-02: گالری مدرن ترتیب را به کلیدهای legacy map کند.
- [ ] AC-03: foure-image تغییر املا ندهد.

## سناریوهای تست اختصاصی
- [ ] SC-01: تصویر چهارم در foure-image ذخیره شود.
- [ ] SC-02: حذف/جابجایی گالری همگام بماند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-020.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
