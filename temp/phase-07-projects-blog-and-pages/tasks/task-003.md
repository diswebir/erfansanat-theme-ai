# P07-T003 — متادیتای تک‌پروژه

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.1` · وابستگی‌ها: `P07-T001`, `P03-T021`, `P03-T024`, `P03-T003`, `P03-T004`

## هدف دقیق
تاریخ تکمیل، شهر، نوع پروژه و بازدید در هدر/جزئیات پروژه نمایش داده شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/project/meta.php`

## منابع/مرجع
- `static-mirror/projects/esfahan-shohada-square.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: finish-pro-time بدون تبدیل اجباری نمایش داده شود.
- [x] AC-02: شهر/نوع لینک taxonomy درست بگیرند.
- [x] AC-03: number2-viewc خوانده شود.

## سناریوهای تست اختصاصی
- [x] SC-01: تاریخ 1404/01/01 درست نمایش داده شود.
- [x] SC-02: شهر فارسی لینک صحیح بگیرد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-003.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-003.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-003.php` → **12 passed, 0 failed, 0 pending**.
