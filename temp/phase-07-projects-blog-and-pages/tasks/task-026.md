# P07-T026 — برگه خدمات smartelement

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.3` · وابستگی‌ها: `P03-T002`, `P02-T018`

## هدف دقیق
services.html به برگه smartelement متصل شود و لیست خدمات/مسیر همکاری را رندر کند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/page-templates/services.php`

## منابع/مرجع
- `static-mirror/services.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: لیست خدمات از CPT services بیاید.
- [x] AC-02: مسیر همکاری/CTA قابل ویرایش باشد.
- [x] AC-03: آرشیو مستقل services ساخته نشود.

## سناریوهای تست اختصاصی
- [x] SC-01: پنج خدمت در smartelement لیست شوند.
- [x] SC-02: لینک تک‌خدمت درست باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-026.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-026.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-026.php` → **15 passed, 0 failed, 0 pending**.
