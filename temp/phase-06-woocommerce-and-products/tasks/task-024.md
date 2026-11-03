# P06-T024 — فرم Checkout

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.3` · وابستگی‌ها: `P06-T022`

## هدف دقیق
form-checkout با فیلدهای خریدار/آدرس/سفارش و هوک‌های پرداخت ساخته شود.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/woocommerce/checkout/form-checkout.php`

## منابع/مرجع
- `static-mirror/checkout.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: data-checkout-page و گرید مرجع حفظ شوند.
- [ ] AC-02: woocommerce_checkout_* هوک‌ها حفظ شوند.
- [ ] AC-03: فیلدهای حقیقی/حقوقی قابل تنظیم باشند.

## سناریوهای تست اختصاصی
- [ ] SC-01: checkout با فیلد اجباری خالی خطا بدهد.
- [ ] SC-02: سفارش آزمایشی ثبت شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-024.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-024.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
