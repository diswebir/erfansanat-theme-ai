# P08-T012 — Schema و ذخیره‌گاه فرم‌ها

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.2` · وابستگی‌ها: `P01-T004`

## هدف دقیق
CPT/جدول ورودی فرم‌ها با نوع فرم، وضعیت خوانده‌شدن و متادیتا ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Forms/Schema.php`
- `erfansanat/inc/Forms/Entries.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: چهار نوع contact/quote/recruitment/feedback تعریف شود.
- [ ] AC-02: ورودی‌ها sanitize و وضعیت unread/read داشته باشند.
- [ ] AC-03: دسترسی فقط نقش مجاز باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: ثبت ورودی آزمایشی ذخیره شود.
- [ ] SC-02: کاربر غیرمجاز لیست را نبیند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-012.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-012.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
