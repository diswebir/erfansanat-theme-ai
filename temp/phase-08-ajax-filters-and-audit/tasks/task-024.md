# P08-T024 — E2E نهایی و بسته انتشار

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.3` · وابستگی‌ها: `P08-T019`, `P08-T020`, `P08-T021`, `P08-T022`, `P08-T023`

## هدف دقیق
اجرای همه تست‌ها، ساخت زیپ قالب و گزارش آمادگی استقرار انجام شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/run-all-phases-e2e.sh`
- `temp/tools/package-theme.sh`
- `temp/RELEASE_CHECKLIST.md`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: ۱۰۰٪ تست‌ها پاس و صفر skip غیرمجاز باشد.
- [x] AC-02: زیپ فقط erfansanat/ باشد.
- [x] AC-03: گزارش نهایی شامل تست/SEO/پرفورمنس/rollback باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: run-all-phases-e2e.sh exit 0 بدهد.
- [x] SC-02: زیپ انتشار روی نصب تمیز فعال شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-024.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-024.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-024.php` → **15 passed, 0 failed, 0 pending**.
