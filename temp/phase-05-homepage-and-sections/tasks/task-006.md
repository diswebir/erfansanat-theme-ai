# P05-T006 — اسلایدهای انتخابی هیرو از پروژه‌ها

> فاز 5: صفحه اصلی و کامپوننت‌های ماژولار · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `5.2` · وابستگی‌ها: `P05-T005`, `P03-T001`, `P02-T007`

## هدف دقیق
Repeater انتخاب پروژه برای صحنه‌های غیر ویدیویی هیرو ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Options/Tabs/HomeTab.php`
- `erfansanat/template-parts/home/section-hero.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: فقط پروژه‌های منتشرشده قابل انتخاب باشند.
- [ ] AC-02: ترتیب اسلایدها حفظ شود.
- [ ] AC-03: پروژه حذف‌شده اسلاید را خراب نکند.
- [ ] AC-04: عنوان/تصویر/لینک از پروژه بیایند.

## سناریوهای تست اختصاصی
- [ ] SC-01: دو پروژه انتخاب و به ترتیب رندر شوند.
- [ ] SC-02: پروژه پیش‌نویس نمایش داده نشود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-006.php`
- اجرای تکی: `php temp/phase-05-homepage-and-sections/tests/test-task-006.php`
- تست فاز: `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
