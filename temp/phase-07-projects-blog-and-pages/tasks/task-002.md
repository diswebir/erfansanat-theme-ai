# P07-T002 — گالری تک‌پروژه

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.1` · وابستگی‌ها: `P07-T001`, `P03-T020`

## هدف دقیق
تصویر شاخص و پنج تصویر legacy در گالری/لایت‌باکس مرجع رندر شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/project/gallery.php`

## منابع/مرجع
- `static-mirror/projects/esfahan-shohada-square.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: foure-image دقیق خوانده شود.
- [x] AC-02: ترتیب تصاویر حفظ شود.
- [x] AC-03: تصویر خالی/حذف‌شده رندر نشود.

## سناریوهای تست اختصاصی
- [x] SC-01: پروژه با ۶ تصویر گالری کامل بدهد.
- [x] SC-02: لایت‌باکس کار کند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-002.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-002.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-002.php` → **22 passed, 0 failed, 0 pending**.
