# P06-T018 — آرشیو دسته product_cat

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PASSED]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.2` · وابستگی‌ها: `P06-T016`, `P03-T022`

## هدف دقیق
taxonomy-product_cat با توضیحات/تصویر/FAQ دسته ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/woocommerce/taxonomy-product_cat.php`

## منابع/مرجع
- `static-mirror/product_cat/strings-and-lighting-decorations.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [x] AC-01: اسلاگ product_cat و زیر‌دسته‌ها کار کنند.
- [x] AC-02: thumbnail/description/faq ترم خوانده شوند.
- [x] AC-03: ترم فارسی درست resolve شود.

## سناریوهای تست اختصاصی
- [x] SC-01: یک دسته با FAQ رندر شود.
- [x] SC-02: زیردسته 404 ندهد.

## Definition of Done مشترک
- [x] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [x] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [x] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [x] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [x] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-018.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-018.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`


## نتیجه اجرا (2026-11-03)
- تست اختصاصی: `php temp/phase-06-woocommerce-and-products/tests/test-task-018.php` → **13 passed, 0 failed, 0 pending**.
