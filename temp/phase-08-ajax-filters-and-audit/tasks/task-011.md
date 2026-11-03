# P08-T011 — کارایی/کش فیلتر و جستجو

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: پاسخ p95 در QA زیر 200ms باشد.
- [ ] AC-02: کش نتایج با تغییر محتوا invalid شود.
- [ ] AC-03: کوئری N+1 نداشته باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: بنچمارک ۱۰۰/۱۰۰ آیتم ثبت شود.
- [ ] SC-02: انتشار/ویرایش پست کش را پاک کند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-011.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
