# P03-T009 — ریدایرکت aliasهای محصول/دسته

> فاز 3: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `3.3` · وابستگی‌ها: `P03-T005`, `P03-T008`

## هدف دقیق
مسیرهای products/* و category-* قدیمی به مقصد اصلی 301 شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/LegacyRedirects.php`

## منابع/مرجع
- `temp/STATIC_TEMPLATE_MAP.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: products/slug → product/slug باشد.
- [ ] AC-02: products/category-* → product_cat متناظر باشد.
- [ ] AC-03: products.html → shop باشد.
- [ ] AC-04: نگاشت از دیتای لایو قابل تنظیم باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: هر alias یک 301 و مقصد 200 بدهد.
- [ ] SC-02: query string حفظ شود.
- [ ] SC-03: حلقه ریدایرکت ایجاد نشود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-009.php`
- اجرای تکی: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-009.php`
- تست فاز: `bash temp/phase-03-cpt-and-legacy-meta/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
