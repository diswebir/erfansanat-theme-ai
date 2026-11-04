# P04-T008 — مگامنوی شرکتی

> فاز 4: هدر، مگامنو، منوی موبایل و فوتر · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `4.1` · وابستگی‌ها: `P04-T005`, `P02-T010`

## هدف دقیق
پنل شرکتی/معرفی با لینک‌های درباره/مجوز/تماس ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/header/mega-corporate.php`

## منابع/مرجع
- `static-mirror/index.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: data-mega-menu-corporate حفظ شود.
- [x] AC-02: لینک‌ها از صفحات واقعی/options بیایند.
- [x] AC-03: تصویر/متن قابل ویرایش باشد.

## سناریوهای تست اختصاصی
- [x] SC-01: لینک درباره‌ما صحیح باشد.
- [x] SC-02: خالی‌بودن تصویر fallback داشته باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-008.php`
- اجرای تکی: `php temp/phase-04-header-footer-navigation/tests/test-task-008.php`
- تست فاز: `bash temp/phase-04-header-footer-navigation/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`

## وابستگی ثبت‌شده (P04-T008)
برای AC-03 (تصویر/متن قابل ویرایش) سه فیلد به `erfansanat/inc/Options/Schema.php` (تب megamenu) افزوده شد: `mega_corporate_title`، `mega_corporate_text`، `mega_corporate_image_id`. این یک افزونه‌ی سازگار به Schema فاز ۲ است؛ رگرسیون P02-T001 (۱۶ چک) و P02-T011 (۷ چک) پاس شد.


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-04-header-footer-navigation/tests/test-task-008.php` → **11 passed, 0 failed, 0 pending**.
