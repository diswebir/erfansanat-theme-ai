# P01-T001 — هدر قالب، style.css و حداقل index.php

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.1` · وابستگی‌ها: ندارد

## هدف دقیق
متادیتای قالب، `style.css` پایه و یک `index.php` حداقلی برای اعتبار پوسته کلاسیک ساخته شود؛ CSS استاتیک هنوز کپی نمی‌شود و fallback کامل در `P01-T005` می‌ماند.

## اصلاح حین اجرا
وردپرس پوسته کلاسیک بدون `index.php` را با خطای `theme_no_index` معتبر نمی‌داند؛ بنابراین برای پاس شدن واقعی SC-01/SC-02، یک `index.php` حداقلیِ اعتبارسنجی پوسته در همین تسک اضافه شد. حلقه/هدر/فوتر کامل همچنان متعلق به `P01-T005` است.

## فایل‌های هدف
- `erfansanat/style.css`
- `erfansanat/index.php`

## منابع/مرجع
- `static-mirror/assets/css/styles.css`

## شروط پذیرش اختصاصی
- [x] AC-01: هدر Theme Name/Version/Text Domain/Requires PHP/WP معتبر باشد.
- [x] AC-02: style.css فقط هدر قالب باشد و CSS استاتیک را duplicate نکند.
- [x] AC-03: Text Domain با بارگذاری ترجمه یکسان باشد.
- [x] AC-04: index.php حداقلی باشد و پوسته را بدون خطای theme_no_index معتبر کند.

## سناریوهای تست اختصاصی
- [x] SC-01: WP قالب را در فهرست پوسته‌ها نشان دهد.
- [x] SC-02: فعال‌سازی بدون fatal انجام شود.

## نتیجه تست
`php temp/phase-01-core-and-assets/tests/test-task-001.php` → **12 passed, 0 failed, 0 skipped/pending**

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود. (این تسک خروجی فرانت‌اند ندارد)
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-001.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-001.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
