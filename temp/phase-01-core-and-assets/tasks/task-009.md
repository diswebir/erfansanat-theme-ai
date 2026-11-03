# P01-T009 — انتقال JS و رسانه‌های پایه

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.3` · وابستگی‌ها: `P01-T008`

## هدف دقیق
main.js، لوگو، تصاویر پایه و ویدیوی هیرو به assets/ منتقل شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/assets/js/main.js`
- `erfansanat/assets/images/`

## منابع/مرجع
- `static-mirror/assets/js/main.js`
- `static-mirror/images/`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: main.js بدون تغییر محتوایی کپی شود.
- [ ] AC-02: مسیرهای تصویر/ویدیو در CSS/JS قابل resolve باشند.
- [ ] AC-03: فایل‌های placeholder فقط fallback باشند.

## سناریوهای تست اختصاصی
- [ ] SC-01: assetها HTTP 200 بدهند.
- [ ] SC-02: هیرو ویدیو در صفحه اصلی لود شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-009.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-009.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
