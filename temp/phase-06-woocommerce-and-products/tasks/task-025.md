# P06-T025 — رسید/پیش‌فاکتور Checkout

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.3` · وابستگی‌ها: `P06-T024`

## هدف دقیق
بخش رسید ثبت سفارش و پیش‌فاکتور چاپی پس از پرداخت ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/woocommerce/checkout/thankyou.php`
- `erfansanat/template-parts/checkout/proforma.php`

## منابع/مرجع
- `static-mirror/checkout.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: اطلاعات سفارش واقعی چاپ شود.
- [ ] AC-02: نسخه چاپی RTL خوانا باشد.
- [ ] AC-03: دسترسی فقط به صاحب سفارش/مجاز باشد.

## سناریوهای تست اختصاصی
- [ ] SC-01: صاحب سفارش رسید را ببیند.
- [ ] SC-02: کاربر دیگر دسترسی نگیرد.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-025.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-025.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
