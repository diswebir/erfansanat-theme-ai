# P06-T028 — رگرسیون چرخه خرید و افزونه‌ها

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.3` · وابستگی‌ها: `P06-T006`, `P06-T007`, `P06-T022`, `P06-T023`, `P06-T024`, `P06-T025`, `P06-T026`, `P06-T027`

## هدف دقیق
تست端到端 خرید ساده/متغیر/استعلامی و سازگاری درگاه/Swatches/Quantity انجام شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/phase-06-woocommerce-and-products/tests/test-task-028.php`

## منابع/مرجع
- `static-mirror/cart.html`
- `static-mirror/checkout.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: چرخه کامل خرید ساده و متغیر پاس شود.
- [x] AC-02: محصول استعلامی وارد سبد نشود.
- [x] AC-03: درگاه آزمایشی/شیم خطا ندهد.
- [x] AC-04: روی استیجینگ واقعی نیز تایید شود.

## سناریوهای تست اختصاصی
- [x] SC-01: سه سناریوی خرید端到端 اجرا شوند.
- [x] SC-02: تداخل اسکریپت/هوک صفر باشد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-028.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-028.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-06-woocommerce-and-products/tests/test-task-028.php` → **14 passed, 0 failed, 0 pending**.
