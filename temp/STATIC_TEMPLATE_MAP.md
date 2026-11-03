# STATIC_TEMPLATE_MAP — گزارش اسکن و نگاشت قالب استاتیک به وردپرس

> تاریخ اسکن: 2026-11-03 · منبع: `static-mirror/` (استخراج از `erfansanat-static-mirror.zip.png`)
> وضعیت: **تاییدشده توسط کاربر (2026-11-03)** — تصمیمات در §۸

## ۱) موجودی کامل فایل‌ها (Inventory)

| نوع | تعداد | مسیر | توضیح |
|---|---|---|---|
| HTML | **128** | ریشه + ۹ زیرپوشه | ۳۹MB کل؛ هر صفحه شامل هدر/فوتر/مودال‌های مشترک تکراری |
| CSS | 1 | `assets/css/styles.css` (190KB) | Tailwind کامپایل‌شده + `@font-face` محلی؛ کلاس‌های Utility |
| JS | 1 | `assets/js/main.js` (112KB) | Vanilla JS؛ ~۲۰ ماژول (منو، مگامنو، جستجو، سبد، فیلتر آرشیو، گالری/لایت‌باکس، FAQ، هیرو ویدیویی، فرم‌ها، شمارنده‌ها) |
| فونت | 3 | `assets/fonts/vazirmatn-{arabic,latin,latin-ext}.woff2` | وزیرمتن |
| تصویر | 21 | `images/` (10 محلی + 11 Pexels) + `images/ese-logo.svg` | لوگو SVG + هیرو + محصولات/پروژه‌ها |
| ویدیو | 1 | `images/hero-video.mp4` (7MB) | هیرو صفحه اصلی |

⚠️ نکته: `README.md` میرور قدیمی است (می‌گوید ۳۷ صفحه) — تعداد واقعی **۱۲۸** فایل HTML است.

### تفکیک ۱۲۸ صفحه HTML
| گروه | تعداد | فایل‌ها |
|---|---|---|
| صفحات ریشه | 14 | `index`, `shop`, `products`, `projects`, `services`, `blog`, `about`, `contact`, `faq`, `licenses-and-honors`, `recruitment`, `refund_returns`, `cart`, `checkout` |
| `product/` | 20 | تک‌محصول (delsa-light-tree, artam-light-tree, sun-light-32, sun-light-48, foroogh-chandelier, shamse-chandelier, fandoghi-string, baloti-string, finger-string-lights, needle-string-lights, star-lamp-string, pixel-point-string, point-light-pixel, point-light-pixel-6cm, lighting-ball, linear-light-facade, wall-washer, illumina, industrial-led-bulb, smart-control-box) |
| `products/` | 27 | **Alias** ۲۰ تک‌محصول + ۷ آرشیو دسته قدیمی (`category-*`) — فقط canonical/og/hreflang متفاوت |
| `product_cat/` | 15 | آرشیو دسته‌بندی محصولات (اسلاگ واقعی سایت زنده) |
| `projects/` | 20 | ۱۲ تک‌پروژه + **Alias** ۴ شهر (`city-*`) + ۴ نوع (`type-*`) |
| `city/` | 10 | آرشیو شهر (ahvaz, alborz, birjand, esfahan, iraq, kerman, khorsand, mashhad, qom, tehran) |
| `project_type/` | 8 | آرشیو نوع پروژه (ball-of-light, bridge-lighting, light-tree, light-tunnel, lighting-building, luminous-elements, square-lighting, urban-lighting) |
| `blog/` | 7 | تک‌مقاله (types-of-light-tunnel, urban-lighting-elements, sun-display-32-vs-48, led-wall-washer-guide, point-light-pixel-led, frame-holder-plt, baloti-seven-color-string) |
| `category/` | 2 | آرشیو دسته وبلاگ (magazine=مجله علمی, news-and-events=اخبار و رویدادها) |
| `services/` | 5 | جزئیات خدمات (square-lighting, street-lighting, bridge-lighting, element-lighting, building-lighting) |

### گروه‌های Alias (محتوای یکسان، فقط URL متاها متفاوت)
1. `products/<slug>.html` ≡ `product/<slug>.html` (۲۰ جفت)
2. `products/category-*.html` ≡ `product_cat/*.html` (۷ جفت؛ مثال: `category-led-string-lights` ≡ `strings-and-lighting-decorations`)
3. `projects/city-<slug>.html` ≡ `city/<slug>.html` (۴ جفت)
4. `projects/type-<slug>.html` ≡ `project_type/<slug>.html` (۴ جفت)
5. `products.html` ≈ `shop.html` (فقط عنوان/متاها؛ هر دو آرشیو فروشگاه)

## ۲) ساختار مشترک همه صفحات (Global Parts)
- **`<header data-navbar-header>`**: نوار اعلان بالا (دانش‌بنیان/ISO + لینک مجوزها/گارانتی/استخدام) → ردیف اصلی (لوگو `ese-logo.svg`، ناوبری دسکتاپ `hidden lg:flex`، جستجو، سبد `data-header-cart-badge`، دکمه موبایل `data-mobile-toggle`) → **۴ مگامنوی دسکتاپ** (`data-desktop-mega-trigger/panel`: محصولات با تب `data-mega-prod-tab/panel`، پروژه‌ها با تب نوع/شهر `data-mega-proj-tab/panel`، شرکتی `data-mega-menu-corporate`) — **بدون id، تماماً data-attribute و کلاس Tailwind**.
- **`<aside>` منوی موبایل Off-canvas**: آکاردئون `data-mobile-acc-btn/icon/panel` + تب‌های داخلی محصولات/پروژه‌ها (`data-mobile-prod-tab/panel`, `data-mobile-proj-tab/panel`).
- **مودال جستجوی سراسری** (`data-search-modal`, `data-global-search-input/products/projects`, آیتم‌های `data-search-item`) — جایگزین JetSearch.
- **مودال سبد خرید** (`data-cart-modal-*`: ردیف‌ها، تعداد، حذف، جمع) — Quick-cart.
- **`<section id="contact">`** CTA مشترک «روشنایی و هویت بصری متمایز...» در اکثر صفحات.
- **`<footer>`** + سکشن اطلاعات (نشانی کارخانه/دفتر، دسترسی سریع، دسته‌بندی محصولات، شبکه‌های اجتماعی).
- **`<head>`**: JSON-LD (`ese-jsonld-schema`: Organization/WebSite/...)، og/twitter، `hreflang:fa/en/ar` (به‌صورت meta name)، preload تصاویر هیرو.

## ۳) جدول نگاشت اصلی (HTML → WordPress)

| فایل/گروه استاتیک | قالب وردپرس پیشنهادی | فاز |
|---|---|---|
| هدر + مگامنوها (همه صفحات) | `header.php` + `template-parts/header/{topbar,mega-products,mega-projects,mega-corporate,search-modal,cart-modal}.php` + Walkerهای اختصاصی | 4 |
| منوی موبایل `<aside>` | `template-parts/header/mobile-menu.php` + Walker | 4 |
| فوتر + سکشن اطلاعات + CTA contact | `footer.php` + `template-parts/global/{cta-contact,footer-info}.php` | 4 |
| `index.html` (۱۵ سکشن) | `front-page.php` + `template-parts/home/section-*.php` (جدول §۴) | 5 |
| `shop.html` (+alias `products.html`) | `woocommerce/archive-product.php` + `template-parts/archive/{filters,toolbar,cards}.php` | 6 |
| `product_cat/*.html` (۱۵) | `woocommerce/taxonomy-product_cat.php` (توضیحات + FAQ دسته `faq` + `thumbnail`) | 6 |
| `product/*.html` (۲۰) | `woocommerce/single-product.php` + `woocommerce/content-single-product.php` + `template-parts/product/{gallery,summary,legal-ids,features,tabs,specs-table,usage,related-projects,faq,related-products,reviews}.php` | 6 |
| `cart.html` | `woocommerce/cart/cart.php` (بازنویسی پوسته، حفظ کلاس‌ها) | 6 |
| `checkout.html` | `woocommerce/checkout/form-checkout.php` (+ رسید/پیش‌فاکتور چاپی) | 6 |
| `projects.html` | `archive-projects.php` + فیلتر نوع/شهر | 7 |
| `projects/<12 پروژه>.html` | `single-projects.php` + `template-parts/project/{hero,gallery,details,used-products,related-projects}.php` | 7 |
| `city/*.html` (۱۰) | `taxonomy-city.php` | 7 |
| `project_type/*.html` (۸) | `taxonomy-project_type.php` | 7 |
| `blog.html` | `home.php` (برگه 7426) | 7 |
| `category/*.html` (۲) | `category.php` / `archive.php` | 7 |
| `blog/*.html` (۷) | `single.php` + `template-parts/post/{hero,content,share,views,related}.php` | 7 |
| `about.html` | `page-templates/about.php` → برگه `درباره-ما` (7578) | 7 |
| `contact.html` | `page-templates/contact.php` → برگه `تماس-با-ما` (7666) | 7 |
| `faq.html` | `page-templates/faq.php` → برگه `faq` (10195) | 7 |
| `licenses-and-honors.html` | `page-templates/licenses.php` → برگه 7583 | 7 |
| `recruitment.html` | `page-templates/recruitment.php` → برگه 17898 (۴ تب شغلی + فرم) | 7+8 |
| `refund_returns.html` | `page-templates/warranty.php` → برگه 14922 | 7 |
| `services.html` | `page-templates/services.php` → برگه `smartelement` (11984) ✅ | 7 |
| `services/*.html` (۵) | CPT بومی `services` + `single-services.php` (تصمیم D2) | 7 |
| Aliasهای `products/*`, `projects/city-*`, `projects/type-*` | ریدایرکت 301 به ساختار اصلی؛ اسلاگ لایو فارسی است (D3) | 3+8 |
| — (غایب) `my-account`, `auth` | `woocommerce/myaccount/*` + صفحه `auth` (voorodak/fl-panel) ✅ ساخته شود (D1) | 6 |
| — (غایب) لندینگ `arya` | `page-templates/landing-arya.php` با کامپوننت‌های موجود + منوی `landing` (D1) | 7 |
| — (غایب) `search`, `404` | `search.php`, `404.php` با همان design system ✅ (D1) | 7 |
| — (غایب) برگه `project` (8366) | ریدایرکت 301 به آرشیو `/projects/` (D4) | 3 |

## ۴) نگاشت ۱۵ سکشن صفحه اصلی (`index.html`)

| # | id | محتوا | فایل پیشنهادی `template-parts/home/` | منبع داینامیک |
|---|---|---|---|---|
| 1 | `home` | هیرو: ویدیو/اسلایدر چندصحنه‌ای (`data-scene-*`)، متن دایره‌ای `eseCircleTextPath`, دسته‌بندی‌ها `home1-categories` | `section-hero.php` | تنظیمات قالب: ویدیوی اصلی جدا + اسلایدهای انتخابی از پروژه‌ها (D5) |
| 2 | `projects` | کارت انواع پروژه (تونل، گوی، درخت...) | `section-project-types.php` | ترم‌های `project_type` |
| 3 | — | نمونه‌کارهای اجراشده در کلان‌شهرها | `section-featured-projects.php` | `WP_Query projects` |
| 4 | `about` | معرفی + آمار ۲۵ سال تجربه (شمارنده) | `section-about.php` | Options + counters |
| 5 | — | مرجع بومی‌سازی سازه‌های نوری | `section-localization.php` | Section Manager |
| 6 | — | تاریخچه و دستاوردها (تایم‌لاین ۱۳۸۰–۱۴۰۵) | `section-history.php` | Repeater |
| 7 | `products` | منتخب محصولات (درختان، خورشیدنما...) | `section-products.php` | `WP_Query product` + `card-product.php` |
| 8 | — | CTA کاتالوگ جامع ۱۴۰۴ | `section-catalog.php` | Options (فایل کاتالوگ) |
| 9 | `services` | تب‌های خدمات تخصصی | `section-services.php` | `WP_Query services` (CPT مصوب D2) |
| 10 | — | استانداردهای کیفیت (ISO، تزریق مستقیم...) | `section-standards.php` | Repeater |
| 11 | — | پشتیبانی فنی و گارانتی تعویض | `section-support.php` | Options |
| 12 | — | مشتریان و سازمان‌های همکار (لوگوها) | `section-clients.php` | Repeater (Media) |
| 13 | `blog-preview` | جدیدترین مقالات | `section-blog.php` | `WP_Query post` + `card-post.php` |
| 14 | `contact` | CTA تماس (مشترک) | `template-parts/global/cta-contact.php` | Options |
| 15 | — | نشانی/دسترسی سریع/دسته‌بندی‌ها | بخشی از `footer.php` | Options + منوها |

## ۵) نگاشت ماژول‌های `main.js` → منطق وردپرس
| ماژول JS | معادل داینامیک |
|---|---|
| `initMegaMenuAndSearch` + مودال جستجو (`data-global-search-*`) | Native Live AJAX Search (فاز 8) — حفظ DOM و data-attrها |
| `initProductsArchive` / `initProjectsArchive` / `initBlogArchive` (فیلتر کلاینت‌ساید با `data-product-*` / `data-project-*`) | Native Smart Filters ایجکس (فاز 8)؛ کارت‌ها همان data-attrها را از متا/ترم پر می‌کنند |
| `initPurchaseJourney` + مودال سبد + `cart/checkout` (localStorage) | WooCommerce Cart/Checkout واقعی + AJAX (`wc-ajax=add_to_cart`...) — فاز 6 |
| `initProductDetailAndFaqs` (تب‌ها، آکاردئون، انتخاب گزینه، ستاره امتیاز) | حفظ JS + اتصال به متاها/کامنت‌های ووکامرس — فاز 6 |
| `initGalleries` + لایت‌باکس | گالری محصول (`galerry_produ`) / گالری پروژه (`second-image`..`six-image`) |
| `initContactForm` / فرم استخدام (بدون `name` روی inputها) | فرم‌های اختصاصی فاز 8 (name + nonce + honeypot اضافه می‌شود) |
| `initHeroVideo` / `initHomeHeroVariants` | هیرو از Section Manager |
| `initCounters`, `initScrollTop`, `initNavbar`, `initRecruitmentTabs`, `initShareButtons` | بدون تغییر (JS استاتیک حفظ می‌شود) |

### کلیدهای data کارت‌ها (باید داینامیک پر شوند)
- محصول: `data-product-item`, `data-product-category`, `data-product-categories`, `data-product-purchasable`, `data-product-price`, `data-product-ip`, `data-product-voltage`, `data-product-views`, `data-product-rating`, `data-product-search-text`, `data-product-purchase-mode`
- پروژه: `data-project-item`, `data-project-type`, `data-project-types`, `data-project-location`, `data-project-views`, `data-project-rating`, `data-project-search-text`, `data-project-count`

## ۶) صفحات غایب در میرور (نیاز به تعیین تکلیف)
`arya` (لندینگ آریا) · `my-account` · `auth` · `search` · `404` · برگه `project` (8366) · تک‌خدمت‌ها به‌عنوان CPT؟ → سوالات Stage 0.

## ۷) یادداشت‌های فنی اسکن
- هدر/فوتر/مودال‌ها در هر ۱۲۸ فایل **تکرار** شده‌اند (~۱۵۰KB پایه هر صفحه) → یکبار برش به `header.php`/`footer.php`.
- `category/magazine.html` و `category/news-and-events.html` تگ `<title>undefined</title>` دارند (باگ میرور) → در وردپرس از `wp_title`/SEOPress استفاده می‌شود.
- تک‌مقاله‌های وبلاگ در میرور **TOC و بخش دیدگاه ندارند** (اما MASTER_PLAN فاز 7 آنها را می‌خواهد) → سوال ۶.
- فونت‌ها و همه Assetها محلی‌اند؛ هیچ وابستگی خارجی (CDN) وجود ندارد. ✅
- تصاویر میرور placeholder هستند (Pexels)؛ در قالب نهایی از Media Library سایت زنده استفاده می‌شود و تصاویر میرور فقط fallback پیش‌فرض Options خواهند بود.

## ۸) تصمیمات نهایی کاربر (Stage 0 → Stage 1)
- **D1:** صفحات غایب (`arya`, `my-account`, `auth`, `search`, `404`) با همان design system ساخته می‌شوند.
- **D2:** `services.html` = برگه `smartelement` (11984)؛ ۵ تک‌خدمت = CPT بومی `services` (اسلاگ `services`، بدون آرشیو مستقل).
- **D3:** ساختار اصلی تاکسونومی‌ها حفظ؛ اسلاگ ترم‌های لایو **فارسی** است (`/city/اصفهان/`) → قالب‌ها slug-agnostic؛ aliasهای قدیمی → 301.
- **D4:** برگه `project` (8366) → ریدایرکت 301 به `/projects/`.
- **D5:** هیرو در تنظیمات قالب: ویدیوی اصلی جداگانه + اسلایدهای دیگر قابل انتخاب از بین پروژه‌ها.
- **D6:** TOC + دیدگاه وبلاگ با همان سبک طراحی اضافه می‌شود.
- **D7:** تست ووکامرس با shim در سندباکس + نهایی‌سازی روی استیجینگ.
- **D8:** سورس قالب در `erfansanat/` (زیپ نهایی فقط همین پوشه).
