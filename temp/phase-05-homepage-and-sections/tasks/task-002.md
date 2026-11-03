# P05-T002 — کارت استاندارد پروژه

> فاز 5: صفحه اصلی و کامپوننت‌های ماژولار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `5.2` · وابستگی‌ها: `P03-T001`, `P03-T024`

## هدف دقیق
card-project.php با data-project-* و تصویر/شهر/نوع/بازدید ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/cards/card-project.php`

## منابع/مرجع
- `static-mirror/projects.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: data-project-item/type/types/location/views/rating/search-text حفظ شوند.
- [ ] AC-02: لینک/تصویر/شهر از DB بیایند.
- [ ] AC-03: بدون داده اضافه DOM نشکند.

## سناریوهای تست اختصاصی
- [ ] SC-01: کارت یک پروژه واقعی رندر شود.
- [ ] SC-02: data-attrها با متا/ترم برابر باشند.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-002.php`
- اجرای تکی: `php temp/phase-05-homepage-and-sections/tests/test-task-002.php`
- تست فاز: `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
