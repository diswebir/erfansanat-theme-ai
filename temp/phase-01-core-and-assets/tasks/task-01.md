# Task 1.1: اسکلت قالب، Autoloader و پشتیبانی ووکامرس

[PENDING]

## هدف و برنامه فنی
ساختار شیءگرا با namespace `ErfanSanat\Theme`؛ بارگذاری خودکار امن کلاس‌ها؛ بدون وابستگی به Elementor/Jet.

## فایل‌های هدف
- `style.css`
- `functions.php`
- `index.php`
- `inc/`

## وابستگی‌ها
- محیط آزمایشی

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [ ] قالب در وردپرس آزمایشی فعال شود و هیچ Fatal/Warning منتسب به قالب ایجاد نکند.
- [ ] همه فایل‌های PHP با `php -l` پاس شوند.
- [ ] پشتیبانی title-tag، post-thumbnails، woocommerce و html5 ثبت شود.
- [ ] هیچ فایل مرجع در static-mirror تغییر نکند.

## آزمون
- `temp/phase-01-core-and-assets/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.
