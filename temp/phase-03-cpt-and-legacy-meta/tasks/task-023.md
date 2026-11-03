# P03-T023 — متاباکس Hreflang پست و ترم

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.3` · وابستگی‌ها: `P03-T012`

## هدف دقیق
لینک‌های fa/en/ar و کلید مشترک روی نوشته/برگه/محصول/پروژه/ترم‌ها ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/HreflangMetabox.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`hrefmeta_fa`, `hrefmeta_en`, `hrefmeta_ar`, `same_key_hrefl`

## شروط پذیرش اختصاصی
- [ ] AC-01: hrefmeta_fa/en/ar و same_key_hrefl ذخیره شوند.
- [ ] AC-02: URL نامعتبر رد شود.
- [ ] AC-03: ستون same_key_hrefl sortable باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: مقدار روی post و term round-trip شود.
- [ ] SC-02: مرتب‌سازی ستون کار کند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-023.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-023.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
