# P08-T010 — فیلتر پروژه‌ها

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.1` · وابستگی‌ها: `P08-T001`, `P03-T003`, `P03-T004`, `P07-T006`

## هدف دقیق
فیلتر project_type/city با شمارنده و جستجوی متنی پروژه‌ها ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/SmartFilters.php`
- `erfansanat/assets/js/project-filters.js`
- `erfansanat/template-parts/archive/project-filters.php`

## منابع/مرجع
- `static-mirror/projects.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: نوع/شهر فارسی درست فیلتر شوند.
- [ ] AC-02: data-project-count واقعی باشد.
- [ ] AC-03: کارت‌ها بدون رفرش عوض شوند.

## سناریوهای تست اختصاصی
- [ ] SC-01: فیلتر شهر اصفهان فقط پروژه‌های همان شهر را بدهد.
- [ ] SC-02: شمارنده نوع پروژه درست باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-010.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-010.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
