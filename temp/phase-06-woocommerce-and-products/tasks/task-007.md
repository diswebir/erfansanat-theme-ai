# P06-T007 — Variation Swatches و Quantity

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.1` · وابستگی‌ها: `P06-T006`

## هدف دقیق
سواچ‌ها و کنترل تعداد WPC بدون شکستن JS مرجع کار کنند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/woocommerce/single-product/variable.php`
- `erfansanat/template-parts/product/purchase.php`

## منابع/مرجع
- `static-mirror/product/fandoghi-string.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: هوک‌های variation حفظ شوند.
- [x] AC-02: حداقل/حداکثر/گام تعداد WPC رعایت شود.
- [x] AC-03: سواچ رنگ/ویژگی درست انتخاب شود.

## سناریوهای تست اختصاصی
- [x] SC-01: محصول متغیر با سواچ خریداری شود.
- [x] SC-02: تعداد نامعتبر رد شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-007.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-007.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-06-woocommerce-and-products/tests/test-task-007.php` → **43 passed, 0 failed, 0 pending**.
- 31 PHP + 12 JSDOM (real main.js). Native Woo variation form replaces fallback when WC_VERSION present.
