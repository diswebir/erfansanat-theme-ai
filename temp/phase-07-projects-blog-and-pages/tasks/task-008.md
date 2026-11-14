# P07-T008 — آرشیو شهر

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.1` · وابستگی‌ها: `P07-T006`, `P03-T004`

## هدف دقیق
taxonomy-city با shell آرشیو و اسلاگ فارسی لایو ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/taxonomy-city.php`

## منابع/مرجع
- `static-mirror/city/esfahan.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: /city/اصفهان/ بدون هاردکد اسلاگ انگلیسی کار کند.
- [x] AC-02: توضیحات/شمارنده ترم درست باشد.
- [x] AC-03: حالت خالی امن باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: ترم فارسی اصفهان رندر شود.
- [x] SC-02: شهر بدون پروژه پیام خالی بدهد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-008.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-008.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-008.php` → **10 passed, 0 failed, 0 pending**.
