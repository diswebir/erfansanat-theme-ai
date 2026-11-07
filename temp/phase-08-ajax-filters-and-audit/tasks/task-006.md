# P08-T006 — UI فیلترهای آرشیو محصول

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.1` · وابستگی‌ها: `P08-T005`, `P06-T016`

## هدف دقیق
drawer/checkbox/range/search فیلترها با data-attr مرجع به endpoint وصل شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/archive/product-filters.php`
- `erfansanat/assets/js/product-filters.js`

## منابع/مرجع
- `static-mirror/shop.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: DOM فیلتر مرجع حفظ شود.
- [x] AC-02: انتخاب‌ها در URL/state قابل اشتراک باشند.
- [x] AC-03: بدون رفرش کارت‌ها عوض شوند.

## سناریوهای تست اختصاصی
- [x] SC-01: انتخاب دسته کارت‌ها را فیلتر کند.
- [x] SC-02: دکمه حذف فیلترها حالت اول را برگرداند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-006.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-006.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-006.php` → **16 passed, 0 failed, 0 pending**.
- تست رفتاری DOM (test-task-006-dom.mjs): 9 passed, 0 failed — فیلتر دسته/جستجو/حذف/URL با endpoint واقعی.
