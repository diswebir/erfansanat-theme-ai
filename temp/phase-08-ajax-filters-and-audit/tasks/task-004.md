# P08-T004 — اتصال JS مودال جستجو

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: data-global-search-* حفظ شوند.
- [ ] AC-02: درخواست هم‌زمان نتایج را قاطی نکند.
- [ ] AC-03: Escape/arrow/enter کار کنند.

## سناریوهای تست اختصاصی
- [ ] SC-01: تایپ سریع فقط آخرین نتیجه را نشان دهد.
- [ ] SC-02: کیبورد نتیجه را انتخاب کند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-004.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
