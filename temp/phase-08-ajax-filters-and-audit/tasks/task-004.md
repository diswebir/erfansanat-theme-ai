# P08-T004 — اتصال JS مودال جستجو

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.1` · وابستگی‌ها: `P08-T002`, `P08-T003`, `P04-T012`

## هدف دقیق
مودال جستجو با debounce، abort درخواست قبلی، کیبورد و حالت خالی به AJAX وصل شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/assets/js/search.js`
- `erfansanat/template-parts/header/search-modal.php`

## منابع/مرجع
- `static-mirror/assets/js/main.js`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: data-global-search-* حفظ شوند.
- [x] AC-02: درخواست هم‌زمان نتایج را قاطی نکند.
- [x] AC-03: Escape/arrow/enter کار کنند.

## سناریوهای تست اختصاصی
- [x] SC-01: تایپ سریع فقط آخرین نتیجه را نشان دهد.
- [x] SC-02: کیبورد نتیجه را انتخاب کند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php` → **12 passed, 0 failed, 0 pending**.
- تست رفتاری DOM (test-task-004-dom.mjs): 10 passed, 0 failed — debounce/abort/keyboard/empty.
