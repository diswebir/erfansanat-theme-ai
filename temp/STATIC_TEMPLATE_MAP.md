# STATIC TEMPLATE MAP — نگاشت فایل‌های استاتیک به سلسله‌مراتب قالب وردپرس

📍 منبع استاتیک: `/home/user/static-mirror/static-mirror/` (۱۲۸ فایل HTML — استخراج‌شده از `erfansanat-static-mirror.zip.png`)

## ۱. دارایی‌ها (Assets)
| فایل | مقصد در قالب |
|---|---|
| `assets/css/styles.css` (190KB — Tailwind کامپایل‌شده) | `assets/css/styles.css` (Enqueue سراسری) |
| `assets/js/main.js` (112KB — Vanilla JS: منوها، فیلتر، جستجو، لایت‌باکس، آکاردiong، فرم، شمارنده) | `assets/js/main.js` (Enqueue سراسری، defer) |
| `assets/fonts/vazirmatn-*.woff2` (۳ فایل) | `assets/fonts/` |
| `images/` (20 jpg + ese-logo.svg + hero-video.mp4 + pexels/*) | `assets/images/` |

> تمام لینک‌ها نسبی (relative) هستند؛ با حفظ ساختار پوشه‌ها، `url(...)`های داخل CSS بدون شکستگی لود می‌شوند.

## ۲. صفحات سطح بالا (۱۴ فایل) → Templateهای وردپرس
| فایل استاتیک | صفحه live | Template وردپرس |
|---|---|---|
| `index.html` (379KB — ۱۲ سکشن) | home (ID 8146) | `front-page.php` + `template-parts/home/section-*.php` |
| `shop.html` (303KB) | shop (ID 8574) | `archive-product.php` (ووکامرس) |
| `products.html` (303KB) | کاتالوگ محصولات | `archive-product.php` / template اختصاصی — **سوال ۲** |
| `projects.html` (250KB) | project (ID 8366) | `archive-projects.php` |
| `blog.html` (185KB) | blog (ID 7426) | `home.php` |
| `about.html` | درباره-ما (7578) | `page-templates/about.php` |
| `contact.html` | تماس-با-ما (7666) | `page-templates/contact.php` |
| `faq.html` | faq (10195) | `page-templates/faq.php` |
| `licenses-and-honors.html` | licenses-and-honors (7583) | `page-templates/licenses.php` |
| `recruitment.html` | recruitment (17898) | `page-templates/recruitment.php` |
| `refund_returns.html` | refund_returns (14922) | `page-templates/refund.php` |
| `services.html` (186KB) | smartelement (11984) | `page-templates/services.php` |
| `cart.html` | cart | `woocommerce/cart.php` |
| `checkout.html` | checkout | `woocommerce/checkout.php` |

## ۳. صفحات detail (۱۱۴ فایل در ۹ پوشه)
| پوشه استاتیک | تعداد | نوع محتوا | Template وردپرس |
|---|---|---|---|
| `product/` | 20 | صفحه details محصول (datasheet) | `single-product.php` (اسلاگ live: `/product/...`) |
| `products/` | 27 | 20 details تکراری + 7 فایل `category-*` | `single-product.php` + `taxonomy-product_cat.php` — **سوال ۲** |
| `projects/` | 20 | صفحه details پروژه (case study) | `single-projects.php` |
| `project_type/` | 8 | آرشیو نوع پروژه | `taxonomy-project_type.php` |
| `city/` | 10 | آرشیو شهر | `taxonomy-city.php` |
| `blog/` | 7 | تک‌نوشته | `single.php` |
| `category/` | 2 | آرشیو دسته‌بندی وبلاگ | `category.php` |
| `services/` | 5 | صفحات details خدمات | `page-templates/service-*.php` — **سوال ۳** |
| `product_cat/` | 15 | آرشیو دسته‌بندی محصول | `taxonomy-product_cat.php` |

## ۴. ساختار مشترک صفحات
- هر ۱۲۸ فایل دارای `<header>`، `<main>`، `<footer>` مشترک هستند → `header.php` / `footer.php`
- لینک‌های asset: `<link rel="stylesheet" href="assets/css/styles.css">` + `<script src="assets/js/main.js" defer>`
- هوک‌های JS: `data-home` (۵۹)، `data-search-item`، `data-service-*`، `data-unit-price`، `data-step-qty` و...
- منوی دسکتاپ: لینک به تمام شهرها (`city/*`)، انواع پروژه، محصولات، وبلاگ — معادل مگامنو
