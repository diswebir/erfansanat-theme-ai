# P07-T011 — کارت مقاله و فیلتر کلاینت

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.2` · وابستگی‌ها: `P07-T009`, `P05-T004`

## هدف دقیق
کارت مقاله و data-attrهای جستجو/دسته وبلاگ با مرجع parity داشته باشند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/cards/card-post.php`
- `erfansanat/template-parts/archive/blog-filter.php`

## منابع/مرجع
- `static-mirror/blog.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: data-search-item و کلاس‌های کارت حفظ شوند.
- [ ] AC-02: فیلتر دسته/جستجوی کلاینتی روی DOM واقعی کار کند.
- [ ] AC-03: تاریخ شمسی درست باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: جستجوی عنوان مقاله کارت‌ها را فیلتر کند.
- [ ] SC-02: دسته مقاله درست لینک شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-011.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-011.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
