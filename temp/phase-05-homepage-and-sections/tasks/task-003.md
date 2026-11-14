# P05-T003 — کارت استاندارد محصول

> فاز 5: صفحه اصلی و کامپوننت‌های ماژولار · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `5.2` · وابستگی‌ها: `P03-T014`, `P03-T007`, `P03-T024`

## هدف دقیق
card-product.php با قیمت/دسته/IP/ولتاژ/بازدید/حالت خرید ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/cards/card-product.php`

## منابع/مرجع
- `static-mirror/shop.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: data-product-* مرجع حفظ شوند.
- [x] AC-02: true_sale حالت خرید/استعلام را تعیین کند.
- [x] AC-03: قیمت/تصویر/دسته واقعی باشند.

## سناریوهای تست اختصاصی
- [x] SC-01: محصول آنلاین و استعلامی درست رندر شوند.
- [x] SC-02: data-product-price با قیمت واقعی برابر باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-05-homepage-and-sections/tests/test-task-003.php`
- اجرای تکی: `php temp/phase-05-homepage-and-sections/tests/test-task-003.php`
- تست فاز: `bash temp/phase-05-homepage-and-sections/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-05-homepage-and-sections/tests/test-task-003.php` → **31 passed, 0 failed, 0 pending**.
