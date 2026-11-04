# تسک 6.2: آرشیو فروشگاه و دسته محصول

- فاز: قالب‌های WooCommerce و محصول
- وضعیت: `[PENDING]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php`

## شرح
پیاده‌سازی `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php` مطابق میرور، FAQ/term description و pagination.

## Pass Conditions
- [ ] `/shop/` و `/product_cat/.../` با rewrite پایدار پاسخ معتبر دهند؛
- [ ] کارت‌ها و pagination ساختاری مطابق میرور باشند؛
- [ ] توضیح و FAQ term بر اساس meta متصل شوند؛
- [ ] فیلترهای HTML حفظ و به منطق AJAX فاز 8 متصل شوند.

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
