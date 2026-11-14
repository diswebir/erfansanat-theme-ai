# P06-T001 — Shim سازگاری ووکامرس برای QA

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.1` · وابستگی‌ها: `P03-T005`, `P03-T007`

## هدف دقیق
محیط تست بدون Woo واقعی، product/taxonomyها و توابع موردنیاز قالب را شبیه‌سازی کند؛ روی لایو غیرفعال باشد.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `temp/tests/woocommerce-shim.php`

## منابع/مرجع
- `temp/DATA_BLUEPRINT.md`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: فقط در محیط QA بارگذاری شود.
- [x] AC-02: product/product_cat/product_tag/brand و قیمت/سبد پایه شبیه‌سازی شوند.
- [x] AC-03: با Woo واقعی تداخل نکند.

## سناریوهای تست اختصاصی
- [x] SC-01: تست محصول بدون Woo اجرا شود.
- [x] SC-02: با Woo واقعی shim غیرفعال بماند.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-001.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-001.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-06-woocommerce-and-products/tests/test-task-001.php` → **32 passed, 0 failed, 0 pending**.
