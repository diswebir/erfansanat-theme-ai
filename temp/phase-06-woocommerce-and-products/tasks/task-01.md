# Task 6.1 — قالب تک‌محصول (single-product)

> فاز: ووکامرس، محصولات تکی و آرشیو · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
پیاده‌سازی تک‌محصول از استاتیک با اتصال همه متاها و منطق true_sale (آنلاین/استعلامی).

## فایل‌های هدف
- `erfansanat/woocommerce/single-product.php`
- `erfansanat/woocommerce/content-single-product.php`
- `erfansanat/template-parts/product/`

## شروط پاس شدن (Pass Conditions)
- [ ] گالری galerry_produ + گالری ووکامرس + fallbackها
- [ ] zireonvan, english_titl, شناسه‌های قانونی, before-sale-c, product-catalog-file
- [ ] جدول ۱۸ ویژگی pa_* + repeaterها + usage_product/usage_countr
- [ ] project-ids (پروژه‌های مرتبط) + faq + محصولات مرتبط + دیدگاه‌ها
- [ ] true_sale=true → افزودن به سبد؛ false → استعلام/تماس
- [ ] عدم نمایش سکشن خالی وقتی متا پر نیست

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-06-woocommerce-and-products/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
