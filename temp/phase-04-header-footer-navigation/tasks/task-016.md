# P04-T016 — CTA مشترک تماس

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.1` · وابستگی‌ها: `P02-T012`

## هدف دقیق
سکشن id=contact مشترک بین صفحات از تنظیمات ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/global/cta-contact.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: id=contact و کلاس‌های scroll-mt حفظ شوند.
- [x] AC-02: متن/دکمه/تلفن از options بیایند.
- [x] AC-03: در صفحاتی که مرجع ندارد قابل خاموش‌شدن باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: CTA در صفحه اصلی و shop رندر شود.
- [x] SC-02: خاموش‌کردن آن DOM را حذف کند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-016.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-016.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-04-header-footer-navigation/tests/test-task-016.php` → **22 passed, 0 failed, 0 pending**.
