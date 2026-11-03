# P08-T022 — Dry-run مهاجرت روی استیجینگ

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.3` · وابستگی‌ها: `P03-T002`, `P03-T012`, `P07-T026`

## هدف دقیق
مهاجرت محتوای خدمات/متاها/منوها روی کپی استیجینگ با گزارش قبل/بعد انجام شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/tools/staging-migration-dry-run.sh`
- `temp/MIGRATION_REPORT_TEMPLATE.md`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: قبل از مهاجرت بک‌آپ گرفته شود.
- [ ] AC-02: تعداد پست/ترم/متا قبل و بعد مقایسه شود.
- [ ] AC-03: هیچ کلید legacy تغییر نام ندهد.

## سناریوهای تست اختصاصی
- [ ] SC-01: dry-run بدون نوشتن روی لایو اجرا شود.
- [ ] SC-02: گزارش تفاوت‌ها تولید شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-022.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-022.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
