# تسک 6.1: صفحه تک‌محصول

- فاز: قالب‌های WooCommerce و محصول
- وضعیت: `[PASSED]`
- پیش‌نیاز: تسک‌های پیشین فاز و فازهای موردنیاز تکمیل و تست شده باشند.
- فایل‌ها/مناطق هدف: `single-product.php`, `woocommerce/`, `template-parts/cards/`

## شرح
پیاده‌سازی صفحهٔ محصول تکی بر اساس میرور؛ اتصال گالری، کلیدهای متای قدیمی، ۱۸ ویژگی محصول، روابط، پرسش‌های متداول، محصولات مرتبط، دیدگاه‌ها و منطق خرید/استعلام.

## Pass Conditions
- [x] `true_sale=true` مسیر استاندارد افزودن به سبد خرید WooCommerce را اجرا می‌کند؛ `false` فقط مسیر استعلام/تماس را نشان می‌دهد.
- [x] گالری دقیق `galerry_produ`، گالری استاندارد WooCommerce، و کلیدهای قدیمی `gallery-filed` و `image-2` تا `image-6` پشتیبانی می‌شوند.
- [x] شناسه‌های بلوپرینت، repeaters/روابط/FAQ و تمام ۱۸ ویژگی `pa_*` با املای اصلی خوانده و نمایش داده می‌شوند.
- [x] روابط پروژه و نوع پروژه، محصولات مرتبط، محتوای دیدگاه WooCommerce و فایل کاتالوگ متصل‌اند.
- [x] قسمت‌های فاقد داده پنهان یا دارای empty state امن‌اند؛ متن/نشانی‌ها escape و محتوای توضیحات با فیلتر WordPress نمایش داده می‌شود.
- [x] selectorهای گالری، lightbox، تب‌های محصول و FAQ برای `assets/js/main.js` حفظ شده‌اند.

## پیاده‌سازی و آزمون
- قالب `single-product.php` گالری و fallbackهای legacy/Woo را می‌خواند؛ خرید متغیر/ساده را به template استاندارد WooCommerce می‌سپارد و محصولات استعلامی را به تلفن/ایمیل هدایت می‌کند.
- شناسه‌های صنعتی، زیرعنوان/عنوان انگلیسی، اطلاعیه پیش از خرید، کاتالوگ، مشخصات و کاربردها، `project-ids`, `usage_countr`, FAQ، ویژگی‌های `pa_*`، دیدگاه‌ها و محصولات مرتبط بر اساس داده‌های محصول رندر می‌شوند.
- آزمون WordPress integration هر دو شاخهٔ `true_sale`, escape، گالری و fallbackهای چهارگانه، ۱۸ attribute keys، روابط، FAQ خالی/پر، Woo review template و related product output را بررسی می‌کند. WooCommerce در harness نصب نیست؛ مسیرهای API استاندارد با test doubles آزموده شدند.
- آزمون مستقل: `PHP_BIN=node_modules/.bin/php-wasm-cli PHASE_TASKS=01 sh temp/phase-06-woocommerce-and-products/tests/run-phase-tests.sh`.
- Regression Phases 1–4 و Task 5.1، Task 6.1 integration، PHP syntax checks و WP_DEBUG log خالی پاس شدند.

## روال اجرا
1. پیش از تغییر، حافظه پروژه، نقشه استاتیک و این تسک را بخوان.
2. تست‌های این تسک را قبل/هم‌زمان با پیاده‌سازی تکمیل کن.
3. فقط همین تسک را انجام بده؛ پس از اجرای تست و ثبت نتیجه، حافظه/برنامه را به‌روز کن و برای تسک بعدی تأیید بگیر.
