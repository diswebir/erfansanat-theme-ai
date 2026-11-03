# P07-T027 — تک‌خدمت

> فاز 7: پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `7.3` · وابستگی‌ها: `P03-T002`, `P07-T026`

## هدف دقیق
single-services.php با جزئیات/اقلام اجرایی/مسیر اجرا/CTA مرجع ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/single-services.php`
- `erfansanat/template-parts/service/details.php`

## منابع/مرجع
- `static-mirror/services/square-lighting.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: id=service-details حفظ شود.
- [ ] AC-02: اقلام اجرایی/مراحل قابل ویرایش باشند.
- [ ] AC-03: خدمت ناموجود fatal ندهد.

## سناریوهای تست اختصاصی
- [ ] SC-01: یک خدمت کامل رندر شود.
- [ ] SC-02: لینک بازگشت به smartelement درست باشد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-027.php`
- اجرای تکی: `php temp/phase-07-projects-blog-and-pages/tests/test-task-027.php`
- تست فاز: `bash temp/phase-07-projects-blog-and-pages/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
