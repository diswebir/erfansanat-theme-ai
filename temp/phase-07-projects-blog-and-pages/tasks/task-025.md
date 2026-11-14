# P07-T025 — برگه استخدام

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.3` · وابستگی‌ها: `P02-T018`, `P02-T007`

## هدف دقیق
recruitment.php با چهار تب شغلی و فرم placeholder فاز ۸ ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/page-templates/recruitment.php`

## منابع/مرجع
- `static-mirror/recruitment.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: تب‌های IT/حسابداری/پروژه/R&D قابل ویرایش باشند.
- [x] AC-02: data-recruit-tabs حفظ شود.
- [x] AC-03: فرم تا فاز ۸ handler امن نداشته باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: سوئیچ تب شغلی کار کند.
- [x] SC-02: شرح شغل از تنظیمات بیاید.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-025.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-025.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-07)
- تست اختصاصی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-025.php` → **14 passed, 0 failed, 0 pending**.
- تست DOM: `node temp/phase-07-projects-blog-and-pages/tests/test-task-025-dom.mjs` → **8 passed, 0 failed**.
