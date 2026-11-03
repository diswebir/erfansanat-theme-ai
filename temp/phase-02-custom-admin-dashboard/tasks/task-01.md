# Task 2.1: زیرساخت تنظیمات و ذخیره AJAX امن

[PENDING]

## هدف و برنامه فنی
پنل RTL تب‌بندی‌شده با مدیریت یک option و کش خواندن؛ ذخیره AJAX با بررسی مجوز و nonce.

## فایل‌های هدف
- `inc/Admin/ThemeOptions.php`
- `inc/helpers.php`

## وابستگی‌ها
- 1.3

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] کاربر بدون manage_options نتواند بخواند یا ذخیره کند.
- [ ] nonce نامعتبر رد شود.
- [ ] تنظیمات فقط در erfansanat_theme_options ذخیره شوند.
- [ ] es_option مقدار ذخیره‌شده و fallback صحیح بدهد.

## آزمون
- `temp/phase-02-custom-admin-dashboard/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
