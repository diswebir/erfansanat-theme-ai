# P03-T002 — ثبت CPT services

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.1` · وابستگی‌ها: `P03-T001`

## هدف دقیق
پست‌تایپ خدمات جدید با آرشیو غیرمستقل و rewrite services ثبت شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/PostTypes.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`services`

## شروط پذیرش اختصاصی
- [x] AC-01: has_archive=false و rewrite slug=services باشد.
- [x] AC-02: supports مطابق D2 باشد.
- [x] AC-03: تداخل با برگه smartelement ایجاد نکند.

## سناریوهای تست اختصاصی
- [x] SC-01: تک‌خدمت /services/slug/ بدهد.
- [x] SC-02: آرشیو مستقل 404/redirect طبق طراحی باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php` → **12 passed, 0 failed, 0 pending**.
