# Task 7.1: تک‌پروژه و آرشیوهای پروژه/شهر/نوع

[PENDING]

## هدف و برنامه فنی
گالری شش‌تصویری، تاریخ تکمیل، شهر/نوع و محصولات استفاده‌شده؛ برگزیده‌ها در برگه project جدا از آرشیو.

## فایل‌های هدف
- `single-projects.php`
- `archive-projects.php`
- `taxonomy-project_type.php`
- `taxonomy-city.php`

## وابستگی‌ها
- 6.3

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] تمام جزئیات پروژه و روابط در fixture رندر شوند.
- [ ] آرشیو شهر و نوع فقط محتوای همان ترم را نشان دهند.
- [ ] گالری و لایت‌باکس با data-attribute مرجع کار کنند.
- [ ] برگه project و آرشیو projects تداخل نداشته باشند.

## آزمون
- `temp/phase-07-projects-blog-and-pages/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
