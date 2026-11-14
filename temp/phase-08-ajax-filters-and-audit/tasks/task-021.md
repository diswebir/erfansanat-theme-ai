# P08-T021 — ممیزی ریسپانسیو و دسترس‌پذیری

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.3` · وابستگی‌ها: `P04-T020`, `P05-T021`, `P06-T028`, `P07-T028`

## هدف دقیق
تست 375/768/1440، کیبورد، کنتراست و overflow کل قالب انجام شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php`

## منابع/مرجع
- `static-mirror/`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: overflow افقی صفر باشد.
- [x] AC-02: مودال/drawer/آکاردئون با کیبورد کار کنند.
- [x] AC-03: تصاویر alt و فرم‌ها label داشته باشند.

## سناریوهای تست اختصاصی
- [x] SC-01: اسنپ‌شات سه viewport پاس شود.
- [x] SC-02: پیمایش کیبورد صفحات کلیدی ممکن باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php` → **13 passed, 0 failed, 0 pending**.
