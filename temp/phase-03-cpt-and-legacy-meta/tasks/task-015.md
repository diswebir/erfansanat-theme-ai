# P03-T015 — متاباکس کاتالوگ و گالری محصول

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PASSED]` · اندازه: اتمیک
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
- [x] AC-01: product-catalog-file به‌صورت URL ذخیره شود.
- [x] AC-02: galerry_produ لیست ID کامادار باشد.
- [x] AC-03: _product_image_gallery/gallery-filed/image-2..6 fallback شوند.
- [x] AC-04: ترتیب گالری حفظ شود.

## سناریوهای تست اختصاصی
- [x] SC-01: گالری legacy خوانده و بدون تخریب ذخیره شود.
- [x] SC-02: حذف تصویر از گالری کلیدهای همگام را درست کند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-015.php` → **16 passed, 0 failed, 0 pending**.
