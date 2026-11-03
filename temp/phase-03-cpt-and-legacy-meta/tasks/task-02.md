# Task 3.2: متاباکس کامل محصول و Round-trip متاهای JetEngine

[PENDING]

## هدف و برنامه فنی
پشتیبانی همه کلیدهای محصول، گالری fallback، روابط و repeaterها؛ تشخیص فرمت خام و حفظ کلیدهای دارای غلط املای تاریخی.

## فایل‌های هدف
- `inc/Admin/ProductMetaboxes.php`
- `inc/Data/LegacyMeta.php`

## وابستگی‌ها
- 3.1

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] همه کلیدهای محصول در DATA_BLUEPRINT پوشش داده شوند.
- [ ] JSON، آرایه سریالایز‌شده و مقدار خالی round-trip شوند.
- [ ] گالری از کلیدهای قدیمی fallback بخواند.
- [ ] محصول استعلامی و قابل خرید درست تفکیک شوند.
- [ ] Quick Edit با nonce/capability و sanitization امن باشد.

## آزمون
- `temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
