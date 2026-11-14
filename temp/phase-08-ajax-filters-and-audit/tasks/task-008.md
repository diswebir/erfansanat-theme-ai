# P08-T008 — فیلتر ۱۸ ویژگی pa_*

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.1` · وابستگی‌ها: `P08-T005`, `P03-T007`

## هدف دقیق
فیلترهای ویژگی فنی با نام/مقدار واقعی و سازگاری با سواچ ساخته شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/SmartFilters.php`
- `erfansanat/template-parts/archive/product-filters.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: هر ۱۸ ویژگی در صورت وجود فیلتر شود.
- [x] AC-02: pa_color مقدار رنگی درست بگیرد.
- [x] AC-03: ویژگی بدون مقدار UI خالی نسازد.

## سناریوهای تست اختصاصی
- [x] SC-01: فیلتر IP/ولتاژ/رنگ نتیجه درست بدهد.
- [x] SC-02: مقدار ناموجود رد شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-008.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-008.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-008.php` → **11 passed, 0 failed, 0 pending**.
- رفتاری: ۵ چک جدید در test-task-006-dom.mjs — مجموع DOM 19 passed, 0 failed.
