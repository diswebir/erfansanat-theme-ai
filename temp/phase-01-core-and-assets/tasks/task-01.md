# تسک 1.1: اسکلت قالب و معماری OOP

- فاز: زیرساخت هسته قالب، Autoloader و انتقال Assets استاتیک
- وضعیت: `[PASSED]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `style.css`, `functions.php`, `index.php`, `inc/`

## شرح
ساخت `style.css`، `functions.php`، `index.php` و معماری namespaceدار `ErfanSanat\Theme` در `inc/`؛ autoloader و bootstrap امن.

## Pass Conditions
- [x] `php -l` روی فایل‌های PHP بدون خطا (PHP WASM CLI 8.5.12)؛
- [x] قالب دارای header استاندارد معتبر و در WordPress 7.2-beta3 فعال‌سازی/بارگذاری شد؛
- [x] ثبت `title-tag`, `post-thumbnails`, `woocommerce`, `html5`؛
- [x] کلاس‌ها با namespace `ErfanSanat\Theme` بارگذاری شوند.

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
