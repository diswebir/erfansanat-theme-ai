# Task 5.1: پانزده سکشن خانه و رندر ترتیبی

[PENDING]

## هدف و برنامه فنی
برش سکشن‌های S01 تا S15 مطابق STATIC_TEMPLATE_MAP و اتصال به SectionManager بدون طراحی مجدد.

## فایل‌های هدف
- `front-page.php`
- `template-parts/home/`

## وابستگی‌ها
- 4.2

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] در پیش‌فرض، ترتیب و DOM سکشن‌ها با مرجع برابر باشد.
- [ ] تغییر ترتیب و خاموش‌کردن سکشن در خروجی اعمال شود.
- [ ] هیچ سکشن مرجع حذف یا با placeholder عوض نشود.

## آزمون
- `temp/phase-05-homepage-and-sections/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
