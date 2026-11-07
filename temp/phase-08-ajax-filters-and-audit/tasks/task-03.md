# تسک 8.3: ممیزی نهایی و E2E

- فاز: جستجوی AJAX، فیلترها، فرم‌ها و ممیزی
- وضعیت: `[COMPLETE]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `temp/run-all-phases-e2e.sh`, `temp/phase-*/tests/`

## شرح
سازگاری SEOPress/hreflang/schema، عملکرد/امنیت/responsive، اجرای کامل تست‌ها و گزارش آمادگی استقرار.

## Pass Conditions
- [x] تمام اسکریپت‌های فازها پاس و صفر PHP warning با `WP_DEBUG=true`؛
- [x] داده meta/URL/Hreflang/schema و SEO plugin از نظر قراردادهای theme بررسی شده‌اند؛ خروجی plugin واقعی نیازمند تأیید staging است.
- [x] بررسی responsive/accessibility/performance و escaping خروجی‌ها انجام شد؛ screen-reader و performance تولیدی نیازمند تأیید staging هستند.
- [x] محدودیت‌های محیط/تست و rollout/rollback مستند باشند.

نتیجهٔ runner کامل، شامل jsdom و Chromium headless: PASS. جزئیات و محدودیت‌ها در `audit-report.md`.

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
