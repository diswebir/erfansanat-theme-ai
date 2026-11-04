# P06-T004 — خلاصه عنوان/زیرعنوان/عنوان انگلیسی

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.1` · وابستگی‌ها: `P06-T002`, `P03-T014`

## هدف دقیق
نام، zireonvan و english_titl در summary محصول رندر شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/template-parts/product/summary.php`

## منابع/مرجع
- `static-mirror/product/delsa-light-tree.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: املای english_titl حفظ شود.
- [x] AC-02: زیرعنوان خالی DOM اضافه نسازد.
- [x] AC-03: عنوان‌ها escape شوند.

## سناریوهای تست اختصاصی
- [x] SC-01: محصول با/بدون زیرعنوان درست رندر شود.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-004.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-004.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-06-woocommerce-and-products/tests/test-task-004.php` → **17 passed, 0 failed, 0 pending**.
