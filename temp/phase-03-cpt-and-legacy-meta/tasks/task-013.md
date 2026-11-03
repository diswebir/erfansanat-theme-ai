# P03-T013 — متاباکس شناسه‌های صنعتی محصول

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.2` · وابستگی‌ها: `P03-T012`

## هدف دقیق
شش شناسه قانونی/صنعتی با املای دقیق و Quick Edit پایه ساخته شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/ProductMetaboxes.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`, `product_id`

## شروط پذیرش اختصاصی
- [ ] AC-01: gs1_id/national_code/hs_id/isic_id/cpc_id/product_id ذخیره شوند.
- [ ] AC-02: مقدار متنی حفظ شود؛ صفر پیشرو حذف نشود.
- [ ] AC-03: nonce/capability رعایت شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: هر کلید round-trip شود.
- [ ] SC-02: ویرایش سریع مقدار را خراب نکند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-013.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-013.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
