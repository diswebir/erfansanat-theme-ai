# تسک 8.2: فرم‌های اختصاصی

- فاز: جستجوی AJAX، فیلترها، فرم‌ها و ممیزی
- وضعیت: `[COMPLETE]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `inc/Admin/Forms.php`, `inc/Forms/`, `template-parts/forms/`

## شرح
ساخت پنل/ذخیره inbox برای contact, quote, recruitment, client-feedback با nonce, honeypot, email notification.

## Pass Conditions
- [x] درخواست‌ها validate/sanitize/escape و capability-bound باشند؛
- [x] honeypot و nonce فعال؛
- [x] ورودی در ادمین قابل مرور و وضعیت‌پذیر باشد؛
- [x] ایمیل اعلان اختیاری/پیکربندی‌پذیر و خطاها بدون افشای داده باشند.

نتیجهٔ تست یکپارچه: PASS برای contact، quote، recruitment و client-feedback؛ گزارش نهایی در `audit-report.md`.

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
