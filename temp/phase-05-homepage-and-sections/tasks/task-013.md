# P05-T013 — سکشن محصولات منتخب

> فاز 5: صفحه اصلی و کامپوننت‌های ماژولار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `5.2` · وابستگی‌ها: `P05-T003`

## هدف دقیق
منتخب محصولات با WP_Query product و کارت استاندارد ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/home/section-products.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: id=products حفظ شود.
- [ ] AC-02: محصولات منتخب/دسته از تنظیمات بیایند.
- [ ] AC-03: محصول ناموجود/پیش‌نویس نمایش داده نشود.

## سناریوهای تست اختصاصی
- [ ] SC-01: تعداد/دسته کارت‌ها با تنظیم برابر باشد.
- [ ] SC-02: کارت محصول data-attr کامل داشته باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-013.php`
- اجرای تکی: `php temp/phase-05-homepage-and-sections/tests/test-task-013.php`
- تست فاز: `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
