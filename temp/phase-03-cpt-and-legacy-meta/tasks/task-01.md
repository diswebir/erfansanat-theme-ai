# Task 3.1: ثبت projects، services، تکسونومی‌ها و Rewriteها

[PENDING]

## هدف و برنامه فنی
ثبت projects/project_type/city طبق بلوپرینت؛ services مستقل با smartelement به‌عنوان آیتم؛ حفظ product_cat و product-tag و ستون‌های ادمین.

## فایل‌های هدف
- `inc/ContentTypes.php`
- `inc/Rewrites.php`

## وابستگی‌ها
- 2.3

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] آرشیو و تک‌پروژه و تکسونومی‌ها بدون 404 باشند.
- [ ] services و تک‌خدمت‌ها قابل مشاهده باشند؛ smartelement یکی از خدمات باشد.
- [ ] اسلاگ‌های حیاتی و with_front مطابق بلوپرینت باشند.
- [ ] aliasها فقط پس از audit مسیر فعال شوند.
- [ ] فیلتر شهر/نوع و ستون same_key_hrefl کار کند.

## آزمون
- `temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
