# تسک 3.3: متاباکس پروژه، term meta، hreflang و views

- فاز: CPTها، Taxonomyها و سازگاری متادیتای legacy
- وضعیت: `[PASSED]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `inc/Admin/ProjectMetaboxes.php`, `inc/Admin/TermMetaboxes.php`, `inc/`

## شرح
مدیریت کلیدهای متای پروژه، FAQ/thumbnail برای product_cat، hreflang محتوا/ترم و شمارنده بازدید.

## Pass Conditions
- [x] کلیدهای `second-image`…`six-image` با `foure-image`، `finish-pro-time` و `mortabet_mahsole_2` دقیقاً حفظ شوند؛
- [x] خواندن/نوشتن `faq`, `thumbnail`, hreflang و `number2-viewc`؛
- [x] امنیت nonce/capability و escaping خروجی؛
- [x] ترم‌های taxonomy و پست‌ها هر دو پوشش داده شوند.

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
