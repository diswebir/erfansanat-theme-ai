# Task 5.21 — گیت فاز ۵: تست رندر، تعامل و بودجه mock

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی؛ ۱۵ سکشن مرجع + سکشن افزوده‌شده با مجوز کاربر |
| اندازه | `S` |
| وابستگی | Tasks `5.2`–`5.20` |
| وضعیت | `[PASSED — PHP-WASM/jsdom mock gate; live WordPress and SQL instrumentation not run]` |
| تست | `tests/test-task-21.php` |

> تفسیر سنجه: query report، درخواست‌های mock سطح `WP_Query` و `get_terms` را می‌شمارد؛ شمارش SQL واقعی `$wpdb` نیست. کاربر این سنجه را برای این فاز پذیرفته است. Static mirror همچنان ۱۵ سکشن دارد؛ هر ۱۵ مقایسه‌ی مرجع ۱۰۰٪ هستند و سکشن شانزدهم جداگانه در Registry/تست خودش بررسی می‌شود.

## شروط پذیرش

- [x] هر ۱۵ سکشن مرجع با امتیاز ≥۹۹٪ حفظ شده و سکشن شانزدهم مجازشده حضور دارد.
- [x] `main.js` و adapter لینک خدمات روی خروجی PHP-WASM در jsdom اجرا می‌شوند؛ صفر runtime error.
- [x] بودجه mock ≤۳۰ درخواست، cache hit در رندر دوم، و صفر warning.
- [x] ۲۰ فایل پیشین پیش از اجرای Task 5.21 سبز هستند؛ runner و final gate نتیجه‌ی کامل ۲۱ فایل را اعتبارسنجی می‌کنند.

## آخرین نتیجه

`npm run phase5:tests`: **129 PASS / 0 FAIL / 0 PENDING در 21 فایل — PHASE RESULT: OK**. PHP-WASM report: 16 سکشن، 15/15 structural similarities = 100٪، 6 mock درخواست سطح `WP_Query`/`get_terms`، cache hit، 0 warning، jsdom runtime errors = 0. این نتیجه به‌تنهایی اثبات اجرای WordPress زنده، مرورگر سایت یا SQL `$wpdb` نیست.
