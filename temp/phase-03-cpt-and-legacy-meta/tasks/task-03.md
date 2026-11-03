# Task 3.3: متای پروژه، ترم، ترجمه و بازدید

[PENDING]

## هدف و برنامه فنی
متاباکس‌های بومی برای گالری پروژه، تاریخ و روابط؛ FAQ/تصویر دسته؛ hreflang و شمارنده بازدید با fallback.

## فایل‌های هدف
- `inc/Admin/ProjectMetaboxes.php`
- `inc/Admin/TermMetaboxes.php`
- `inc/Admin/ContentMetaboxes.php`

## وابستگی‌ها
- 3.2

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] خواندن/نوشتن تمام کلیدهای پروژه و ترم پاس شود.
- [ ] foure-image و mortabet_mahsole_2 دقیقاً حفظ شوند.
- [ ] hrefmeta_fa/en/ar روی محتوا و ترم کار کند.
- [ ] number2-viewc با fallback عددی از number-viewc خوانده شود.
- [ ] داده سایر افزونه‌ها هنگام ذخیره حذف نشود.

## آزمون
- `temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
