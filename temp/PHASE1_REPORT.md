# گزارش فاز ۱ — زیرساخت قالب، پاک‌سازی و دارایی‌ها

تاریخ: 2026-11-03 · شاخه: `arena/de593948-erfansanat-theme-ai` · دامنه: فقط محیط آزمایشی محلی

## خلاصه

فاز ۱ با سه تسک اتمیک کامل شد. هر تسک تست قراردادی، بررسی HTTP، ثبت گزارش و commit مستقل دارد. هیچ تغییری روی سایت زنده اعمال نشده و دادهٔ تولیدی خوانده یا نوشته نشده است.

| تسک | وضعیت | commit | PHPUnit | بررسی HTTP |
|---|---|---|---|---|
| 1.1 اسکلت قالب، Autoloader، پشتیبانی ووکامرس | PASSED | `c94d296` | ۹ تست / ۵۳ assertion | ۱۴ |
| 1.2 پاک‌سازی فرانت‌اند (DeBloat) | PASSED | `27b7222` | ۱۱ تست / ۴۵ assertion | ۳۲ |
| 1.3 انتقال Assets و بارگذاری شرطی | PASSED | `e782285` | ۱۲ تست / ۶۹۰ assertion | ۳۷ |

مجموع فاز: **۳۲ تست PHPUnit با ۷۸۸ assertion** و **۸۳ بررسی HTTP**، همه بدون خطا. `tools/test-local.sh` و `tools/test-stage0.sh` هم دوباره پاس شدند.

## آنچه ساخته شد

### Task 1.1 — اسکلت و معماری
- قالب مستقل در `erfansanat/` با نام «Erfan Sanat Esfahan»، namespace `ErfanSanat\Theme` و ساختار `inc/` (نه در ریشهٔ مخزن).
- `inc/Autoloader.php`: PSR-4 با `realpath` و بررسی containment؛ نام کلاس‌های بیگانه، خالی، ناموجود و دارای `..` هیچ فایلی را include نمی‌کنند (تست اختصاصی).
- `inc/Theme.php`: سینگلتون، `title-tag`، `post-thumbnails`، `automatic-feed-links`، `responsive-embeds`، `html5` (هفت گروه)، `woocommerce` و سه گالری ووکامرس، دو سایز تصویر و `content_width`.
- `header.php`/`footer.php`/`sidebar.php` سازگاری تا هشدارهای `theme-compat` هسته حذف شوند؛ `index.php` حلقهٔ موقت و مستند تا فازهای ۴–۷.
- `phpcs.xml.dist` با استاندارد WordPress و معافیت مستند PSR-4.

### Task 1.2 — DeBloat
- حذف از فرانت‌اند: `wp-block-library`, `global-styles`, `classic-theme-styles`, ایموجی، `wp-embed`، تگ‌های `generator`، `EditURI`، `shortlink`، کشف REST و oEmbed.
- معاف و دست‌نخورده: پیشخوان و ویرایشگر بلوک، REST، AJAX، feed، embed، preview، دارایی‌های ووکامرس (`wc-blocks-style`, `woocommerce-js`, `add-to-cart`, variations)، `canonical`، `robots` و feed autodiscovery.
- handleهای افزونه‌های باقی‌مانده (سواچ، ورود پیامکی، پنل کاربری) هرگز deregister نمی‌شوند؛ فیلتر `erfansanat_debloat_front_end` برای خروج اضطراری وجود دارد.

### Task 1.3 — Assets
- انتقال بایت‌به‌بایت: `styles.css` (190,867 B)، `main.js` (112,885 B)، سه WOFF2، ۲۱ تصویر و `hero-video.mp4` (7,025,235 B). همه با SHA-256 یکسان با مرجع.
- `assets/manifest.json` (۲۷ فایل) + `tools/build-asset-manifest.py --check` برای بازبینی خودکار.
- `inc/AssetManager.php`: CSS در head، JS در فوتر، بدون وابستگی به jQuery، نسخهٔ `filemtime`، preload فقط دو فونت با `crossorigin`، حذف dashicons برای مهمان، رد هر مسیری خارج از `assets/`.
- ویدیوی هیرو هیچ‌گاه enqueue یا preload نمی‌شود؛ فقط `has()`/`media()` برای چاپ در سکشن هیرو در فاز ۵.

## تصمیم فنی قابل توجه

در منبع استاتیک، هر ۱۲۸ صفحه دقیقاً یک CSS و یک JS مشترک دارند؛ بنابراین در این فاز همان دو فایل سراسری بارگذاری می‌شوند و بارگذاری شرطی به‌ازای نما، همراه با تولید واقعی قالب‌های هر صفحه در فازهای ۴ تا ۷ انجام می‌شود. ویدیو و تصاویر از صف دارایی بیرون نگه داشته شدند تا صفحات غیرمرتبط سنگین نشوند.

## اشکالات ابزار/محیط که در این فاز رفع شد

1. `tools/phpunit` با وجود شکست تست‌ها کد خروج ۰ برمی‌گرداند → wrapper اکنون کد خروج و متن FAILURES را می‌سنجد.
2. PHPUnit 12 نام کلاس را از نام فایل می‌سازد و فایل `test-task-0N.php` هرگز لود نمی‌شد → `tools/run-php-tests.sh` قرارداد را زیر نام کلاس در کش کپی می‌کند.
3. PHP-Wasm به درخواست HTTP از درون پروسهٔ خودش پاسخ نمی‌دهد → همهٔ شرط‌های HTTP به `check-http.py` (curl بیرونی) منتقل شد.
4. روتر محلی دارایی‌های قالب symlink‌شده را ۳۰۱/۴۰۴ می‌کرد → سرو امن فایل غیر PHP داخل `wp-content/themes|plugins`، ۴۰۴ صریح برای دارایی ناموجود، و اسکریپت `tools/preview-wordpress.sh`.
5. هشدار `Undefined array key "SERVER_NAME"` از هسته هنگام رندر درون‌فرآیندی → شبیه‌سازی `$_SERVER` در bootstrap تست.

## محدودیت‌های صادقانه

- همهٔ شواهد از SQLite + PHP-Wasm می‌آیند؛ MySQL/MariaDB، LiteSpeed، کش/CDN و درگاه پرداخت آزمون نشده‌اند.
- افزونه‌های سواچ و ورود پیامکی نصب نیستند؛ رفتارشان با handle مصنوعی و بازبینی کد سنجیده شد، نه با افزونهٔ واقعی.
- `filemtime` برای استقرار چندسروره مناسب نیست و پیش از تولید باید با نسخهٔ ثابت یا همگام‌سازی جایگزین شود.
- ظاهر صفحات در این فاز عمداً «اسکلت» است؛ DOM استاتیک در فازهای ۴ تا ۷ منتقل می‌شود.

## پیش‌نمایش

- وردپرس محلی: پورت `8080` (پروسهٔ «WordPress preview»).
- مرجع استاتیک: پورت `8000`.
- اعتبارنامه فقط در `.local/credentials.json` و خارج از Git.

## دروازهٔ بعدی

فاز ۲ (داشبورد اختصاصی ادمین و Section Manager) آغاز نمی‌شود تا تأیید شما برسد.
