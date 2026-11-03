# Task 6.2 — آرشیو محصولات + FAQ دسته + صفحه‌بندی

> فاز: ووکامرس، محصولات تکی و آرشیو · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
آرشیو فروشگاه و دسته/برچسب از استاتیک با توضیحات و FAQ اختصاصی دسته و صفحه‌بندی اختصاصی.

## فایل‌های هدف
- `erfansanat/woocommerce/archive-product.php`
- `erfansanat/woocommerce/taxonomy-product_cat.php`
- `erfansanat/woocommerce/taxonomy-product_tag.php`

## شروط پاس شدن (Pass Conditions)
- [ ] کارکرد /shop/ و /product_cat/.../
- [ ] رندر کارت محصول منطبق بر استاتیک
- [ ] نمایش FAQ و توضیحات دسته (faq روی product_cat)
- [ ] صفحه‌بندی اختصاصی

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-06-woocommerce-and-products/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
