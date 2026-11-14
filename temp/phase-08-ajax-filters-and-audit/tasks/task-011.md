# P08-T011 — کارایی/کش فیلتر و جستجو

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.1` · وابستگی‌ها: `P08-T002`, `P08-T003`, `P08-T005`, `P08-T006`, `P08-T007`, `P08-T008`, `P08-T009`, `P08-T010`

## هدف دقیق
بودجه پاسخ، ایندکس/کش سبک و invalidation فیلترها تنظیم شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/SmartFilters.php`
- `erfansanat/inc/Core/AjaxSearch.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: پاسخ p95 در QA زیر 200ms باشد.
- [x] AC-02: کش نتایج با تغییر محتوا invalid شود.
- [x] AC-03: کوئری N+1 نداشته باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: بنچمارک ۱۰۰/۱۰۰ آیتم ثبت شود.
- [x] SC-02: انتشار/ویرایش پست کش را پاک کند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php` → **12 passed, 0 failed, 0 pending**.
