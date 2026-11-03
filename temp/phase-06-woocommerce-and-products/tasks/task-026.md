# P06-T026 — حساب کاربری Woo

> فاز 6: ووکامرس، محصول تکی و آرشیو فروشگاه · وضعیت: `[PENDING]` · اندازه: اتمیک
> نگاشت از برنامه قبلی: `6.3` · وابستگی‌ها: `P06-T001`

## هدف دقیق
my-account و تب‌های سفارش/آدرس/جزئیات با پوسته مرجع ساخته شوند.

## مسئولیت و مرز تسک
- فقط فایل‌های هدف همین تسک تغییر کنند؛ تغییر فایل تسک دیگر ممنوع مگر با ثبت وابستگی.
- این تسک یک خروجی قابل تست دارد و نباید با تسک هم‌فاز ادغام شود.


## فایل‌های هدف
- `erfansanat/woocommerce/myaccount/my-account.php`
- `erfansanat/woocommerce/myaccount/dashboard.php`

## منابع/مرجع
- `static-mirror/checkout.html`

## کلیدهای داده/قرارداد
—

## شروط پذیرش اختصاصی
- [ ] AC-01: ناوبری حساب کاربری حفظ شود.
- [ ] AC-02: endpointهای Woo کار کنند.
- [ ] AC-03: fl-panel تداخل نکند.

## سناریوهای تست اختصاصی
- [ ] SC-01: ورود کاربر و مشاهده سفارش‌ها کار کند.
- [ ] SC-02: آدرس پیش‌فرض ذخیره شود.

## Definition of Done مشترک
- [ ] php -l روی فایل‌های PHP تغییر‌یافته بدون خطا باشد.
- [ ] هیچ TODO/Placeholder/مارک‌آپ حذف‌شده برای کوتاه‌سازی وجود نداشته باشد.
- [ ] کلاس‌ها، DOM، id و data-* مرجع استاتیک حفظ شوند؛ خروجی escape شود.
- [ ] تست اختصاصی تسک و تست‌های رگرسیون وابستگی‌ها اجرا و نتیجه در QA_REFLECTION_LOG ثبت شود.
- [ ] PROJECT_MEMORY و MASTER_PLAN به‌روزرسانی و تغییرات فقط روی برنچ arena/7378effd-erfansanat-theme-ai کامیت شوند.

## تست و اجرا
- اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-026.php`
- اجرای تکی: `php temp/phase-06-woocommerce-and-products/tests/test-task-026.php`
- تست فاز: `bash temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`
- تا زمانی که سناریوهای SC خودکار نشده‌اند، تست `[PENDING]` می‌دهد و در حالت strict پاس محسوب نمی‌شود.

## پروتکل اجرا
`READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit`
