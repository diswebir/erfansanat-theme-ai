# P03-T014 — متاباکس عنوان/وضعیت فروش محصول

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.2` · وابستگی‌ها: `P03-T012`

## هدف دقیق
زیرعنوان، عنوان انگلیسی، اطلاعیه و true_sale مدیریت شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Admin/ProductMetaboxes.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
`zireonvan`, `english_titl`, `before-sale-c`, `true_sale`

## شروط پذیرش اختصاصی
- [ ] AC-01: zireonvan/english_titl/before-sale-c/true_sale دقیق باشند.
- [ ] AC-02: true_sale فقط رشته true/false بپذیرد.
- [ ] AC-03: املای english_titl حفظ شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: محصول استعلامی و آنلاین درست ذخیره شوند.
- [ ] SC-02: مقدار نامعتبر true_sale رد شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-014.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-014.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
