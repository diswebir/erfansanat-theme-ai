# STATIC TEMPLATE MAP — گزارش اسکن و نگاشت میرور استاتیک → وردپرس

> تاریخ اسکن: 2026-11-03 · میرور: `uploads/static-mirror/` (۳۹MB · ۱۲۸ HTML · ۱ CSS · ۱ JS · ۳ فونت woff2 · ۲۱ تصویر + ۱ ویدیو)
> ⚠️ این پوشه **فقط مرجع** است (در Git کامیت نمی‌شود). دارایی‌های نهایی در فاز ۱ به `assets/` قالب کپی می‌شوند.

## ۱. خلاصه دارایی‌ها
| مورد | مسیر میرور | اندازه | مقصد در قالب |
|---|---|---|---|
| CSS واحد (کامپایل‌شده، minified) | `assets/css/styles.css` | ۱۸۶KB (۲ خط) | `assets/css/styles.css` — Enqueue سراسری در `AssetManager` |
| JS واحد (Vanilla) | `assets/js/main.js` | ۱۰۶KB · ۲۶۳ data-hook | `assets/js/main.js` — Enqueue شرطی/سراسری |
| فونت Vazirmatn (woff2) | `assets/fonts/vazirmatn-{arabic,latin,latin-ext}.woff2` | ۱۰۸KB | `assets/fonts/` (حفظ مسیر نسبی `../fonts/` در CSS) |
| تصاویر محلی + ویدیو هیرو | `images/*.jpg`, `images/hero-video.mp4`, `images/ese-logo.svg` | ~۱۲MB | `assets/images/` (+ ویدیو در `assets/media/`) |
| تصاویر Pexels | `images/pexels/` | بخشی از ۱۲MB | `assets/images/pexels/` |

نکته CSS: تمام `url(...)`ها نسبی‌اند (`../fonts/`, `data:image/svg+xml`) → با انتقال هم‌ساختار پوشه‌ای هیچ مسیری نمی‌شکند.
نکته JS: هیچ `fetch()` وجود ندارد؛ همه چیز (فیلتر، جستجو، سبد) سمت کلاینت با data-hook کار می‌کند → در فازهای ۵-۸ به WP_Query/AJAX/ووکامرس متصل می‌شود و data-hookها حفظ می‌شوند.

## ۲. ساختار مشترک همه صفحات (Shared Shell)
- `<header data-navbar-header="true">` (~۶۷KB): لوگو، ناوبری دسکتاپ `nav[aria-label="منوی اصلی"]`، مگامنو تب‌دار (`desktop-mega-trigger/panel`, `mega-prod-tab/panel`, `mega-proj-tab/panel`)، دکمه‌های جستجو/سبد (`header-cart-badge`, `search-modal-open`)، سایدبار موبایل (`mobile-toggle/sidebar/backdrop/close`, `mobile-prod/proj-tab/panel`, `mobile-acc-*`)، مودال جستجوی زنده (`search-modal`, `global-search-input`, `search-item`).
- `<footer>` (~۷KB): سه ستون «دسترسی سریع / دسته‌بندی محصولات / اطلاعات تماس کارخانه» + `data-scroll-top`.
- `<head>`: `<link assets/css/styles.css>` + `<script assets/js/main.js defer>` + JSON-LD Organization + BreadcrumbList + متاهای SEO.
- → نگاشت: `header.php` + `footer.php` + `inc/Nav/MegaMenuWalker.php` و بقیه Walkerها (فاز ۴).

## ۳. جدول نگاشت کامل ۱۲۸ فایل HTML → سلسله‌مراتب وردپرس

### ۳.۱. صفحات ریشه (۱۲ فایل)
| # | فایل استاتیک | H1 / نقش | سکشن‌های اصلی | فایل وردپرس معادل |
|---|---|---|---|---|
| 1 | `index.html` (۳۴۹KB) | صفحه اصلی — «وقتی کیفیت و ماندگاری مهمه» | ۱۴ سکشن: `#home` هیرو طرح ۱ (نوتچ معماری + `home1-scene-*` + ویدیو `home1-inline-video`)، `#projects` گالری پروژه‌ها، `#about` معرفی، `data-history-scroll-track` تایم‌لاین تاریخچه (۶ مرحله)، `#products` دسته‌بندی‌ها، کاتالوگ دانلود، `#services` تب خدمات، خدمات پس از فروش، مشتریان، `#blog-preview` مقالات، `#contact` فرم تماس، نقشه کارخانه | `front-page.php` + `template-parts/home/section-*.php` (رندر پویا با SectionManager — فاز ۵) |
| 2 | `shop.html` ⭐canonical آرشیو | «فروشگاه محصولات نورپردازی شهری و صنعتی» | هیرو آرشیو، نوار فیلتر (`product-search`, `product-category`, `product-ip-filter`, `product-voltage-filter`, `product-purchase-mode`, `product-price-range`, `product-sort`, `per-page`)، گرید ۲۰ کارت (`product-item`: `product-image/title/price/sku/purchasable/rating/views`)، صفحه‌بندی (`pagination-*`)، `archive-drawer` فیلتر موبایل | `archive-product.php` (+ `template-parts/cards/card-product.php`، فاز ۶ — فیلترها در فاز ۸ به AJAX وصل می‌شود) |
| 3 | `products.html` | واریانت آرشیو `/products` (og:url متفاوت، بقیه تقریباً یکسان) | مانند shop | واریانت — تصمیم در پرسش Stage 0 (پیش‌فرض: نادیده گرفته می‌شود) |
| 4 | `projects.html` | «پروژه‌های نورپردازی شهری» | هیرو، فیلتر (`project-search`, `project-type`, `project-province`, `project-sort`, `project-count`)، گرید کارت پروژه (`project-item`: `project-title/location/date/rating/views`)، صفحه‌بندی | `archive-projects.php` + `card-project.php` (فاز ۷) |
| 5 | `blog.html` | «وبلاگ و مقالات تخصصی نورپردازی شهری» | هیرو، فیلتر دسته/جستجو (`blog-cat-btn`, `blog-search`, `blog-grid`, `blog-count`) | `home.php` (page_for_posts=7426) + `card-post.php` (فاز ۷) |
| 6 | `about.html` | «درباره شرکت عرفان صنعت اصفهان» | هیرو، معرفی، آمار، تایم‌لاین، ویدیو (`home3-about-video` در main.js)، ارزش‌ها | قالب برگه `page-templates/about.php` + SectionManager (فاز ۷) |
| 7 | `contact.html` | «تماس با ما؛ پاسخگوی شما هستیم» | هیرو، اطلاعات تماس، فرم (`contact-form/reset/success`)، نقشه | `page-templates/contact.php` (فرم در فاز ۸ — سیستم فرم اختصاصی) |
| 8 | `faq.html` | «سوالات متداول نورپردازی شهری و خرید محصولات» | هیرو، آکاردئون `faq-list/faq-toggle/faq-body/faq-icon` | `page-templates/faq.php` (فاز ۷) |
| 9 | `licenses-and-honors.html` | «مجوزها و افتخارات» | هیرو، گرید گواهینامه‌ها | `page-templates/licenses-honors.php` (فاز ۷) |
| 10 | `recruitment.html` | «فرصت‌های شغلی و استخدام در کارخانه دانش‌بنیان» | هیرو، تب‌های واحدها (`recruit-tabs/panel/btn`)، فرم | `page-templates/recruitment.php` (فرم در فاز ۸) |
| 11 | `refund_returns.html` | «شرایط گارانتی و خدمات پس از فروش» | هیرو، متن قانونی | `page-templates/refund-returns.php` (فاز ۷) |
| 12 | `services.html` | «خدمات نورپردازی شهری» | هیرو، سرویس‌های تعاملی (`service-row/preview/counter`)، CTA | `page-templates/services.php` (تصمیم معماری: پرسش Stage 0 — فاز ۷) |

### ۳.۲. سینگل محصول — `product/` (۲۰ فایل) ⭐canonical (سازگار با rewrite `/product`)
`artam-light-tree`, `baloti-string`, `delsa-light-tree`, `fandoghi-string`, `finger-string-lights`, `foroogh-chandelier`, `illumina`, `industrial-led-bulb`, `lighting-ball`, `linear-light-facade`, `needle-string-lights`, `pixel-point-string`, `point-light-pixel-6cm`, `point-light-pixel`, `shamse-chandelier`, `smart-control-box`, `star-lamp-string`, `sun-light-32`, `sun-light-48`, `wall-washer`

سکشن‌های سینگل محصول (نمونه `product/wall-washer.html`، ۲۱۶KB):
1. هیرو محصول (breadcrumb + H1 + `zireonvan`-مانند زیرعنوان + badges `product-active-purch-badge`)
2. گالری (`gallery-root/main-img/thumb/next/prev/lightbox/counter/expand`)
3. تب‌های مشخصات (`prod-tabs-container/prod-tab-btn/panel/icon` — جدول ۱۸ ویژگی `pa_*`، وات/ولتاژ/IP)
4. مشخصات کلیدی دو بخشی (`product-feature2_copy` سبک)، ویژگی‌ها (`product-feature`)، کاربردها (`usage_product`)
5. قیمت + دکمه خرید/استعلام (`pdp-add-to-cart`, `pdp-qty-*`, `opt-group/opt-btn` سوئچ رنگ/مدل، `min-qty/step-qty`, `pdp-cart-toast`) — وضعیت استعلامی ↔ `true_sale`
6. اطلاعیه قبل از خرید (`before-sale-c`)، کاتالوگ PDF (`product-catalog-file`)
7. پروژه‌های مرتبط (`project-ids`)، محصولات مرتبط، فرم دیدگاه (`review-form/name/org/rating-input/comment`) و لیست (`reviews-list`)
8. FAQ آکاردئونی (`faq` متا)

→ نگاشت: `single-product.php` (ووکامرس) + `template-parts/single-product/*.php` — اتصال به کلیدهای متای بلوپرینت (فاز ۶).
⚠️ واریانت تکراری `products/` (۲۰ فایل هم‌نام + ۸ فایل `category-*`) — تفاوت فقط og:url/canonical (`/products`).

### ۳.۳. آرشیو دسته محصول — `product_cat/` (۱۴ فایل) ⭐canonical
`acorn-thread`, `control-box`, `finger-thread`, `hazelnut-thread`, `led-lamp`, `light-chandelier`, `light-tree`, `needle-thread`, `point-light`, `rise-point-lite-pixel`, `star-lamp-thread`, `strings-and-lighting-decorations`, `sun-light-elements`, `urban-lighting-elements`, `wall-washer`

ساختار: هیرو دسته (H1 سئوشده «قیمت و خرید ...») + توضیحات + گرید کارت محصول + FAQ دسته (متای `faq` ترم) + breadcrumb.
→ `taxonomy-product_cat.php` (فاز ۶). ⚠️ واریانت تکراری: `products/category-led-string-lights` … `category-urban-sunbursts` (۸ فایل، اسلاگ‌های متفاوت با `product_cat/` — هنگام نگاشت، اسلاگ رسمی از دیتابیس زنده خوانده می‌شود).

### ۳.۴. آرشیوهای پروژه — `city/` (۱۱) و `project_type/` (۸) ⭐canonical
- `city/`: `ahvaz`, `alborz`, `birjand`, `esfahan`, `iraq`, `kerman`, `khorsand`, `mashhad`, `qom`, `tehran` → `taxonomy-city.php`
- `project_type/`: `ball-of-light`, `bridge-lighting`, `light-tree`, `light-tunnel`, `lighting-building`, `luminous-elements`, `square-lighting`, `urban-lighting` → `taxonomy-project_type.php`
- ساختار هر دو: هیرو آرشیو + گرید کارت پروژه + فیلتر + صفحه‌بندی (مانند projects.html).
⚠️ واریانت تکراری: `projects/city-{alborz,esfahan,khorsand,qom}.html` و `projects/type-{bridge-lighting,light-element,light-tunnel,square-lighting}.html` (۸ فایل).

### ۳.۵. سینگل پروژه — `projects/` (۱۲ فایل سینگل واقعی)
`ahvaz-semi-pyramid-light`, `artam-tree-midan`, `birjand-smart-light-trees`, `bridge-fullcolor-qom`, `esfahan-shohada-square`, `half-arch-tunnel-alborz`, `heart-tunnel-khorsand`, `iraq-karbala-urban-lighting`, `kerman-arch-light-tunnel`, `mashhad-pilgrim-boulevard-lighting`, `tehran-commercial-facade-lighting`, `tunnel-90-alborz`

سکشن‌های سینگل پروژه (نمونه `esfahan-shohada-square.html`، ۱۸۸KB): هیرو (H1 + شهر در عنوان)، گالری لایت‌باکس (معادل `second-image` تا `six-image`)، توضیحات، مشخصات پروژه (تاریخ ↔ `finish-pro-time`، شهر ↔ `city`، نوع ↔ `project_type`)، محصولات استفاده‌شده (↔ `mortabet_mahsole_2`)، پروژه‌های مرتبط.
→ `single-projects.php` + `template-parts/single-project/*.php` (فاز ۷).

### ۳.۶. وبلاگ — سینگل `blog/` (۷ فایل) و آرشیو دسته `category/` (۲ فایل)
- سینگل‌ها: `baloti-seven-color-string`, `frame-holder-plt`, `led-wall-washer-guide`, `point-light-pixel-led`, `sun-display-32-vs-48`, `types-of-light-tunnel`, `urban-lighting-elements` → `single.php`
  - ساختار: هیرو + بدنه مقاله + دکمه اشتراک‌گذاری (`share-btn`) + CTA محصولات مرتبط. (TOC/بازدید/دیدگاه در میرور رندر نشده — طبق بلوپرینت در فاز ۷ با سبک کامپوننت‌های موجود اضافه می‌شود: `number2-viewc`, TOC, comments)
- آرشیوها: `category/magazine.html` (H1: «مجله علمی نورپردازی شهری» — title باگ ژنراتور: `undefined`) و `category/news-and-events.html` (اخبار و رویدادها) → `category.php`

### ۳.۷. خدمات — `services/` (۵ فایل) — ✅ تصمیم کارفرما: CPT اختصاصی Native `services`
`bridge-lighting`, `building-lighting`, `element-lighting`, `square-lighting`, `street-lighting`
ساختار: هیرو خدمت + توضیح + CTA استعلام.
→ CPT `services` در تسک 3.1 ثبت می‌شود (`public`, `rewrite slug 'services'`, `has_archive => false` تا با برگه فهرست خدمات تداخل نکند؛ `show_in_rest`, پشتیبانی thumbnail/editor) + متاباکس Hreflang/بازدید.
→ `single-services.php` (فاز ۷) · فهرست خدمات = برگه با قالب `page-templates/services-listing.php` (کوئری CPT + کارت‌های `card-service.php`) · صفحه `services.html` مرجع ظاهر فهرست است.

### ۳.۸. ووکامرس — `cart.html` و `checkout.html`
- `cart.html` (`data-cart-page`): جدول سبد (`cart-items-list/item-row/qty-inc/dec/remove/clear/subtotal/tax/grandtotal`) + `buyer-type-radio` (حقیقی/حقوقی) — در میرور با localStorage کار می‌کند.
- `checkout.html` (`data-checkout-page`): فرم (`checkout-form`) + رسید/پیش‌فاکتور (`checkout-receipt`, `receipt-code/customer/phone/method`).
→ فاز ۶ تسک 6.3: `cart/checkout` ووکامرس با پوسته همین مارک‌آپ استایل می‌شود (چرخه خرید واقعی + درگاه). سازگاری WPC Product Quantity (min/step ↔ `min-qty/step-qty`) و Variation Swatches (↔ `opt-group`).

### ۳.۹. فایل‌های بدون مرجع استاتیک (نیاز به تصمیم — پرسش Stage 0)
- لندینگ `arya` (ID 16947) و `smartelement` (ID 11984): در میرور وجود ندارند.
- `my-account`, `auth` (voorodak): در میرور وجود ندارند.
- پیش‌فرض پیشنهادی: ساخت با ترکیب کامپوننت‌های موجود میرور (هیرو/کارت/فرم/تب) و استایل فرم‌های استاندارد افزونه‌ها.

## ۴. نقشه data-hookهای اصلی main.js → پیاده‌سازی وردپرس
| خانواده hook | صفحات | جایگزین داینامیک |
|---|---|---|
| `desktop-mega-*`, `mega-prod/proj-*`, `mobile-*`, `navbar-header` | هدر | Nav Walkers + `wp_nav_menu` (فاز ۴) |
| `search-modal*`, `global-search-input`, `search-item` | هدر | AJAX Live Search Native (فاز ۸) |
| `product-*` (search/category/ip/voltage/purchase-mode/price-range/sort/per-page/count/empty/grid/item) | آرشیو محصولات | WP_Query + AJAX Smart Filters (فاز ۶+۸) |
| `project-*` (search/type/province/sort/count/item) | آرشیو پروژه‌ها | WP_Query + AJAX (فاز ۷+۸) |
| `blog-*` | آرشیو وبلاگ | WP_Query + فیلتر دسته (فاز ۷) |
| `cart-*`, `pdp-*`, `buyer-type-radio`, `checkout-*`, `receipt-*` | سبد/چک‌اوت/محصول | ووکامرس واقعی (فاز ۶) — localStorage حذف می‌شود |
| `gallery-*` | سینگل محصول/پروژه | حفظ Vanilla + رندر داینامیک آیدی‌ها |
| `faq-*` | FAQ/محصول/دسته | متای Repeater `faq` |
| `prod-tab-*`, `recruit-*`, `service-*`, `home1-*`, `home2-history-*`, `home3-*`, `hero-*`, `history-*`, `counter-*` | سکشن‌های صفحه‌ای | SectionManager + حفظ JS |
| `review-*`, `rv-*` | سینگل محصول | دیدگاه‌های ووکامرس |
| `contact-form`, `contact-reset/success` | تماس | سیستم فرم اختصاصی (فاز ۸) |
| `pagination-*`, `page-btn/next/prev` | آرشیوها | paginate_links + AJAX |
| `scroll-top`, `counted`, `share-btn` | سراسری | حفظ Vanilla |

## ۵. جمع‌آوری آمار نهایی اسکن
- **۱۲۸ HTML** = ۱۲ ریشه + ۲۰ سینگل محصول (canonical) + ۲۰ واریانت products/ + ۸ products/category-* + ۱۴ product_cat + ۱۱ city + ۸ project_type + ۱۲ سینگل پروژه + ۸ واریانت projects/city|type + ۷ سینگل بلاگ + ۲ category + ۵ services + cart + checkout
- همه صفحات: breadcrumb JSON-LD + og/twitter meta + هدر/فوتر مشترک ✓
- فونت/آیکون: بدون CDN خارجی (۱۰۰٪ آفلاین) ✓ — آیکون‌ها inline SVG در HTML
