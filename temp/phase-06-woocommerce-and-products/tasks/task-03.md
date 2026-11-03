# Task 6.3 — سبد خرید، پرداخت و حساب کاربری با UI استاتیک

**فاز:** 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 6.2

## هدف

Override قالب‌های cart/checkout/my-account با مارک‌آپ استاتیک، سازگار با درگاه بانکی و افزونه‌های WPC/Swatches.

## فایل‌های هدف

- `woocommerce/cart/cart.php`
- `woocommerce/cart/cart-empty.php`
- `woocommerce/checkout/form-checkout.php`
- `woocommerce/myaccount/my-account.php`
- `inc/Woo/CheckoutCompat.php`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. حفظ هوک‌های استاندارد (`woocommerce_before_cart_table` و …)
2. جایگزینی JS سبد استاتیک با رویدادهای ووکامرس یا رندر سرور
3. سازگاری با Variation Swatches و WPC Quantity
4. پشتیبانی از RTL و `IRT`

## شروط پاس شدن (Pass Conditions)

- [ ] چرخه خرید کامل (افزودن → سبد → پرداخت → سفارش) بدون خطا
- [ ] درگاه بانکی قالب را نشکند (لایه‌های JS ووکامرس سالم)
- [ ] هیچ کد راست‌چین/چپ‌چین یا ارز اشتباه نباشد

## تست‌های خودکار

- تست رندر قالب‌ها
- تست وجود هوک‌ها
- تست نبود اسکریپت تکراری
- تست سازگاری افزونه‌ها

> اسکریپت: `temp/phase-06-woocommerce-and-products/tests/test-task-3.php`
