# P03-T002 — ثبت CPT services

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: has_archive=false و rewrite slug=services باشد.
- [ ] AC-02: supports مطابق D2 باشد.
- [ ] AC-03: تداخل با برگه smartelement ایجاد نکند.

## سناریوهای تست اختصاصی
- [ ] SC-01: تک‌خدمت /services/slug/ بدهد.
- [ ] SC-02: آرشیو مستقل 404/redirect طبق طراحی باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-002.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
