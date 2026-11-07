# Task 1.2: پاکسازی خروجی عمومی بدون شکستن افزونه‌های ضروری

[PASSED]

## هدف و برنامه فنی
حذف هدفمند block library، global styles و emoji در فرانت‌اند؛ حفظ خروجی موردنیاز مدیریت و افزونه‌های باقی‌مانده.

## فایل‌های هدف
- `inc/DeBloat.php`

## وابستگی‌ها
- 1.1

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] در صفحه عمومی آزمایشی هیچ asset گوتنبرگ یا ایموجی بارگذاری نشود.
- [x] وابستگی‌های ووکامرس، سواچ‌ها و افزونه ورود پیامکی حذف نشوند.
- [x] در ویرایشگر مدیریت، قابلیت‌های لازم حفظ شوند.

## آزمون
- `temp/phase-01-core-and-assets/tests/test-task-02.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-03

- PHPUnit: **۱۱ تست / ۴۵ assertion، بدون خطا** (`TaskP01T02Test`).
- HTTP: **۳۲ بررسی، بدون خطا** (`check-http.py --task 1.2`).
- حذف‌شده در فرانت‌اند: `wp-block-library`، `global-styles`، `classic-theme-styles`، `wp-emoji-*`، `wp-embed`، `generator`، `EditURI`, `shortlink`, لینک کشف REST و oEmbed.
- حفظ‌شده: `wc-blocks-style` و همه دارایی‌های ووکامرس، `canonical`، `robots`، feed autodiscovery، ویرایشگر بلوک صفحات و محصولات، تنظیمات ووکامرس و کتابخانه رسانه.
- روش: `is_admin`, REST/AJAX، feed، embed و preview از پاک‌سازی معاف‌اند؛ `wp_dequeue/deregister_style` فقط روی handleهای مشخص هسته؛ `wc-blocks-style` با شرط صریح نگه داشته می‌شود؛ فیلتر `erfansanat_debloat_front_end` برای خروج اضطراری وجود دارد.
- حذف تگ‌های `wp_head` از طریق بافر محدود به خود `wp_head` انجام می‌شود، نه بافر کل صفحه؛ تگ‌های نامرتبط دست‌نخورده می‌مانند و canonical/robots بازبینی شدند.
- اصلاح‌های آزمون: کلاس `DeBloat` باید با use import شود؛ پرچم‌های `is_*` باید روی نمونهٔ `WP_Query` ست شوند نه فقط `query_vars`؛ مقدار `$_SERVER['SERVER_NAME']` در bootstrap لازم است وگرنه هسته در `wp_resource_hints` هشدار می‌دهد.
- محدودیت: سواچ و ورود پیامکی در محیط محلی نصب نیستند؛ نبود دست‌زدن به handleهایشان با تست handle مصنوعی و بررسی کد سنجیده شد، نه با افزونه واقعی.
