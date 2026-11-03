# Task 6.2: آرشیو فروشگاه و دسته/برچسب با صفحه‌بندی

[PENDING]

## هدف و برنامه فنی
کارت‌های مرجع، توضیحات و FAQ ترم، صفحه‌بندی و initial filter صحیح؛ حفظ مسیرهای سئو.

## فایل‌های هدف
- `archive-product.php`
- `taxonomy-product_cat.php`
- `taxonomy-product_tag.php`

## وابستگی‌ها
- 6.1

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] shop و product_cat و product-tag پاسخ 200 بدهند.
- [ ] محصول درست در دسته و صفحه مربوطه باشد.
- [ ] FAQ/image ترم از کلیدهای موجود خوانده شود.
- [ ] صفحه‌بندی و حالت بدون نتیجه کار کند.

## آزمون
- `temp/phase-06-woocommerce-and-products/tests/test-task-02.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
