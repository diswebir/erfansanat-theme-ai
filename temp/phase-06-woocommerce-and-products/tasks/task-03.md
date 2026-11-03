# Task 6.3 — سبد/پرداخت/حساب کاربری + auth

> فاز: ووکامرس، محصولات تکی و آرشیو · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
پوسته استاتیک برای cart/checkout/my-account و صفحه auth با حفظ هوک‌ها و سازگاری درگاه/افزونه‌ها.

## فایل‌های هدف
- `erfansanat/woocommerce/cart/cart.php`
- `erfansanat/woocommerce/checkout/form-checkout.php`
- `erfansanat/woocommerce/myaccount/`
- `erfansanat/page-templates/auth.php`

## شروط پاس شدن (Pass Conditions)
- [ ] چرخه کامل خرید با شورت‌کدها/هوک‌های استاندارد ووکامرس
- [ ] عدم تداخل اسکریپت
- [ ] سازگاری با درگاه بانکی، Variation Swatches، WPC Quantity
- [ ] صفحه auth با voorodak/fl-panel

## تست
- اسکریپت: `tests/test-task-03.php` (اجرا: `php temp/phase-06-woocommerce-and-products/tests/test-task-03.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
