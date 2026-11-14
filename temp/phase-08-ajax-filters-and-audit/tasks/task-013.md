# P08-T013 — فرم تماس با ما

> فاز 8: ایجکس، فرم‌ها، ممیزی و استقرار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `8.2` · وابستگی‌ها: `P08-T012`, `P07-T021`

## هدف دقیق
فرم contact با name/nonce/honeypot و پیام موفق/خطا به handler وصل شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/forms/contact.php`
- `erfansanat/inc/Forms/ContactForm.php`

## منابع/مرجع
- `static-mirror/contact.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: فیلدهای مرجع حفظ و name اضافه شوند.
- [x] AC-02: honeypot پر درخواست را رد کند.
- [x] AC-03: پیام‌ها escape شوند.

## سناریوهای تست اختصاصی
- [x] SC-01: ارسال معتبر ذخیره شود.
- [x] SC-02: honeypot/nonce نامعتبر رد شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-013.php`
- اجرای تکی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-013.php`
- تست فاز: `bash temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-08-ajax-filters-and-audit/tests/test-task-013.php` → **18 passed, 0 failed, 0 pending**.
