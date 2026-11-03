# Task 3.2 — موتور متاباکس محصولات (سازگار با JetEngine)

> فاز: CPTها، تکسونومی‌ها و متاباکس‌های Legacy · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
متاباکس تب‌بندی‌شده برای تمام کلیدهای متای محصول + Repeaterها با حفظ فرمت ذخیره فعلی.

## فایل‌های هدف
- `erfansanat/inc/Admin/ProductMetaboxes.php`
- `erfansanat/inc/Core/LegacyMeta.php`

## شروط پاس شدن (Pass Conditions)
- [ ] خواندن داده serialized و JSON بدون تخریب
- [ ] ذخیره مجدد با همان فرمت
- [ ] همه کلیدها: gs1_id,national_code,hs_id,isic_id,cpc_id,product_id,zireonvan,english_titl,before-sale-c,true_sale,product-catalog-file,galerry_produ,project-ids,usage_countr
- [ ] Repeaterها: product-feature, product-feature2_copy(vaule_of_variable), usage_product, faq
- [ ] Fallback گالری: _product_image_gallery, gallery-filed, image-2..6

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
