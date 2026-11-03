# P07-T008 — آرشیو شهر

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PENDING]` · اندازه: اتمیک
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
- [ ] AC-01: /city/اصفهان/ بدون هاردکد اسلاگ انگلیسی کار کند.
- [ ] AC-02: توضیحات/شمارنده ترم درست باشد.
- [ ] AC-03: حالت خالی امن باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: ترم فارسی اصفهان رندر شود.
- [ ] SC-02: شهر بدون پروژه پیام خالی بدهد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-008.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-008.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
