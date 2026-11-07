# Task 1.1: اسکلت قالب، Autoloader و پشتیبانی ووکامرس

[PASSED]

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
- [x] قالب در وردپرس آزمایشی فعال شود و هیچ Fatal/Warning منتسب به قالب ایجاد نکند.
- [x] همه فایل‌های PHP با `php -l` پاس شوند.
- [x] پشتیبانی title-tag، post-thumbnails، woocommerce و html5 ثبت شود.
- [x] هیچ فایل مرجع در static-mirror تغییر نکند.

## آزمون
- `temp/phase-01-core-and-assets/tests/test-task-01.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-03

- PHPUnit: **۹ تست / ۵۳ assertion، بدون خطا** (`TaskP01T01Test`).
- HTTP: **۱۴ بررسی، بدون خطا** (`check-http.py --task 1.1`): خانه/فروشگاه/سبد ۲۰۰، ۴۰۴ ناشناخته، `dir="rtl"`, `lang="fa-IR"`, یک `<title>`، viewport، نبود خطای PHP، بدون هشدار جدید در debug.log، مرجع استاتیک دست‌نخورده.
- `phpcs --standard=phpcs.xml.dist`: صفر خطا و صفر هشدار؛ نسخهٔ PSR-4 نام فایل کلاس‌ها به‌صورت مستند معاف شده است.
- `php -l` روی همه فایل‌های PHP قالب: بدون خطا.
- افزوده‌های تسک: `phpcs.xml.dist`، `tools/run-php-tests.sh` (اجرای قراردادهای نام‌گذاری‌شده با نام تسک)، بازنویسی `tools/phpunit` برای انتقال کد خروج، `header.php`/`footer.php`/`sidebar.php` سازگاری، `.local` preview سالم.
- محدودیت: توقف/فعال‌سازی دوبارهٔ قالب و درخواست HTTP از داخل همان پروسهٔ PHP قابل آزمون نیست؛ پوشش با curl انجام شد.
