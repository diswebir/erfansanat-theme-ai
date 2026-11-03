# P08-T019 — ممیزی SEO و Schema

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.3` · وابستگی‌ها: `P04-T017`, `P03-T023`

## هدف دقیق
سازگاری SEOPress/rank_math، hreflang، JSON-LD و canonical ممیزی شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/Seo.php`
- `temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php`

## منابع/مرجع
- `agent.md`
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: تگ title/canonical تکراری نباشد.
- [ ] AC-02: JSON-LD سازمان/محصول/مقاله معتبر باشد.
- [ ] AC-03: hreflangها معتبر و بدون تداخل باشند.

## سناریوهای تست اختصاصی
- [ ] SC-01: خروجی head صفحه اصلی/محصول/مقاله validate شود.
- [ ] SC-02: متاهای rank_math قدیمی حفظ شوند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-019.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
