# P01-T005 — Fallback index.php

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.1` · وابستگی‌ها: `P01-T004`

## هدف دقیق
یک index.php حداقلی و ایمن برای حالت نبود قالب تخصصی ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/index.php`

## منابع/مرجع
- `static-mirror/blog.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: header/footer و loop استاندارد را صدا بزند.
- [ ] AC-02: در نبود نوشته پیام خالی escape‌شده نشان دهد.
- [ ] AC-03: بدون صفحه‌ساز کار کند.

## سناریوهای تست اختصاصی
- [ ] SC-01: یک نوشته آزمایشی رندر شود.
- [ ] SC-02: آرشیو خالی fatal ندهد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-005.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-005.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
