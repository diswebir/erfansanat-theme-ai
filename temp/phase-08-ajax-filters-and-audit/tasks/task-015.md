# P08-T015 — فرم استخدام

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.2` · وابستگی‌ها: `P08-T012`, `P07-T025`

## هدف دقیق
فرم recruitment با واحد شغلی، رزومه و اعتبارسنجی فایل ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/forms/recruitment.php`
- `erfansanat/inc/Forms/RecruitmentForm.php`

## منابع/مرجع
- `static-mirror/recruitment.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: واحد IT/حسابداری/پروژه/R&D ذخیره شود.
- [ ] AC-02: فقط پسوندهای مجاز رزومه پذیرفته شود.
- [ ] AC-03: حجم فایل محدود شود.

## سناریوهای تست اختصاصی
- [ ] SC-01: درخواست شغلی با رزومه مجاز ثبت شود.
- [ ] SC-02: فایل php/exe رد شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-015.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-015.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
