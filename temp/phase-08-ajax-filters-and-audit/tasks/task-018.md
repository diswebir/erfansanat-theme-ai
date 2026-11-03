# P08-T018 — ایمیل، Honeypot و Rate-limit

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.2` · وابستگی‌ها: `P08-T013`, `P08-T014`, `P08-T015`, `P08-T016`

## هدف دقیق
اعلان ایمیل، ضداسپم و محدودیت نرخ ارسال برای همه فرم‌ها ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Forms/Mailer.php`
- `erfansanat/inc/Forms/SpamGuard.php`

## منابع/مرجع
- `agent.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: wp_mail با گیرنده قابل تنظیم ارسال شود.
- [ ] AC-02: honeypot/nonce/rate-limit مشترک باشند.
- [ ] AC-03: خطای ایمیل ورودی را از دست ندهد.

## سناریوهای تست اختصاصی
- [ ] SC-01: ارسال پشت‌سرهم محدود شود.
- [ ] SC-02: ایمیل نامعتبر رد شود.
- [ ] SC-03: ثبت ورودی حتی با خطای ایمیل حفظ شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-018.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-018.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
