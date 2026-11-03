# P03-T015 — متاباکس کاتالوگ و گالری محصول

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.2` · وابستگی‌ها: `P03-T012`, `P02-T006`

## هدف دقیق
فایل کاتالوگ و گالری با fallbackهای استاندارد/قدیمی همگام شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/ProductMetaboxes.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`product-catalog-file`, `galerry_produ`, `_product_image_gallery`, `gallery-filed`, `image-2`, `image-3`, `image-4`, `image-5`, `image-6`

## شروط پذیرش اختصاصی
- [ ] AC-01: product-catalog-file به‌صورت URL ذخیره شود.
- [ ] AC-02: galerry_produ لیست ID کامادار باشد.
- [ ] AC-03: _product_image_gallery/gallery-filed/image-2..6 fallback شوند.
- [ ] AC-04: ترتیب گالری حفظ شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: گالری legacy خوانده و بدون تخریب ذخیره شود.
- [ ] SC-02: حذف تصویر از گالری کلیدهای همگام را درست کند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
