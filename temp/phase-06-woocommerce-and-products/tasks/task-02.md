# تسک 6.2: آرشیو فروشگاه و دسته محصول

- فاز: قالب‌های WooCommerce و محصول
- وضعیت: `[PASSED]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php`

## شرح
پیاده‌سازی `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php` مطابق میرور، FAQ/term description و pagination.

## Pass Conditions
- [x] مسیرهای shop، product category و product tag از queryهای اصلی و rewriteهای WooCommerce استفاده می‌کنند؛
- [x] کارت‌های پویا، مرتب‌سازی، pagination و empty state بدون جایگزینی loop و hookهای WooCommerce رندر می‌شوند؛
- [x] تصویر، توضیح و FAQ دسته از term meta امن خوانده می‌شوند؛
- [x] فیلترها و selectorهای سازگار با اتصال AJAX فاز ۸ حفظ شده‌اند (اتصال AJAX خارج از Phase 6 است).

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
