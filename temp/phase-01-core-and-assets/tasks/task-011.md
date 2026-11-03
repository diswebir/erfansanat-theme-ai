# P01-T011 — AssetManager: enqueue شرطی

> فاز 1: هسته قالب، Autoloader و Assets · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `1.3` · وابستگی‌ها: `P01-T010`

## هدف دقیق
پروفایل asset هر قالب فقط در همان صفحه بارگذاری شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/inc/Core/AssetManager.php`

## منابع/مرجع
- `temp/STATIC_TEMPLATE_MAP.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: front/single/archive/checkout/profileها جدا باشند.
- [x] AC-02: main.js فقط جایی که لازم است بارگذاری شود.
- [x] AC-03: asset صفحه دیگر نشت نکند.

## سناریوهای تست اختصاصی
- [x] SC-01: صفحه اصلی فقط assetهای home را بگیرد.
- [x] SC-02: single product asset محصول را بگیرد.
- [x] SC-03: checkout اسکریپت Woo را از دست ندهد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-01-core-and-assets/tests/test-task-011.php`
- اجرای تکی: `php temp/phase-01-core-and-assets/tests/test-task-011.php`
- تست فاز: `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-01-core-and-assets/tests/test-task-011.php` → **13 passed, 0 failed, 0 pending**.
- فاز ۱ runner: PASS.
