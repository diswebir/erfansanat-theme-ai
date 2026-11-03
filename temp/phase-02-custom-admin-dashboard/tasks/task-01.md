# Task 2.1: زیرساخت تنظیمات و ذخیره AJAX امن

[PASSED]

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
- [x] کاربر بدون manage_options نتواند بخواند یا ذخیره کند.
- [x] nonce نامعتبر رد شود.
- [x] تنظیمات فقط در erfansanat_theme_options ذخیره شوند.
- [x] es_option مقدار ذخیره‌شده و fallback صحیح بدهد.

## آزمون
- `temp/phase-02-custom-admin-dashboard/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-03

- PHPUnit: **۹ تست / ۴۲ assertion، بدون خطا** (`TaskP02T01Test`).
- HTTP: **۱۴ بررسی، بدون خطا** (`check-http.py --task 2.1`).
- ساختار: `inc/Settings.php` (اسکیمای تایپ‌دار، sanitize، کش خواندن)، `inc/helpers.php` (`es_option`/`es_text`/`es_url`/`es_enabled`/`es_asset`)، `inc/Admin/ThemeOptions.php` (منو، تب‌ها، media، repeater، endpoint)، `assets/admin/options.js` و `assets/admin/options.css`.
- امنیت: `manage_options` برای همهٔ مسیرها، nonce اختصاصی برای endpoint، sanitize فیلد‌به‌فیلد با KSES، کلید ناشناخته دور ریخته می‌شود، مقدار عددی به بازهٔ مجاز clamp می‌شود، اسکریپت سفارشی فقط با `unfiltered_html` ذخیره می‌شود.
- همهٔ تنظیمات در یک ردیف `erfansanat_theme_options` ذخیره می‌شوند (تست تعداد ردیف قبل/بعد) و ذخیرهٔ partial بقیهٔ مقادیر را حفظ می‌کند.
- مقاوم‌سازی خواندن: ردیف خراب (string یا نوع اشتباه در یک کلید) به پیش‌فرض برمی‌گردد؛ تست اختصاصی دارد.
- اصلاح‌های انجام‌شده: ثبت `wp_ajax_*` روی `admin_init` (قبلاً حتی مدیر هم nonce را از دست می‌داد)، اعتبارسنجی مقدارهای ذخیره‌شده (نه فقط کلیدها)، جداسازی تابع JSON برای آزمون‌پذیری، و رفع ۱۳۹ ایراد PHPCS.
