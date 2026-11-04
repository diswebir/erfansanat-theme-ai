# STATIC_TEMPLATE_MAP — نقشه نگاشت قالب استاتیک به سلسله‌مراتب وردپرس

> تولیدشده به‌صورت خودکار توسط `tools/gen_template_map.py` از اسکن `reference/static-mirror`.

> برای بازتولید: `python3 tools/scan_static.py reference/static-mirror temp/_scan && python3 tools/dedupe_pages.py && python3 tools/section_inventory.py && python3 tools/gen_template_map.py`


## ۱. خلاصه اسکن

- تعداد فایل HTML: **128**
- بدنه یکتا (پس از حذف تکراری‌های نسبی): **92**
- گروه‌های تکراری: **36**
- خانواده چیدمان (`<main>`): **6**
- مجموع سکشن‌ها: **528**
- CSS: `assets/css/styles.css` — یک فایل کامپایل‌شده Tailwind + لایه‌های سفارشی (`card-light`, ...) با ۳ `@font-face` محلی Vazirmatn
- JS: `assets/js/main.js` — ۲۸۱۳ خط Vanilla JS، 16 ماژول `init*` (ناوبری، مگامنو+جستجو، اسلایدر هیرو، شمارنده، گالری/لایت‌باکس، فیلتر آرشیو، تب محصول، سبد خرید، فرم، اشتراک‌گذاری)
- تصاویر: ۲۲ فایل (۱۰ تصویر اختصاصی + ۱۱ Pexels + لوگو SVG) + ۱ ویدیو `hero-video.mp4` (۶.۸MB)
- هدر: **یک** قالب مشترک در هر ۱۲۸ صفحه (تفاوت‌ها فقط `../` و کلاس حالت فعال)
- فوتر: **یک** قالب مشترک در هر ۱۲۸ صفحه (تفاوت‌ها فقط پیشوند مسیر نسبی)


## ۲. جدول نگاشت فایل → قالب وردپرس

| # | فایل استاتیک | قالب وردپرس | توضیح | سکشن | هدر/فوتر |
|---|---|---|---|---|---|
| 1 | `about.html` | `page-templates/template-about.php` | برگه درباره ما | 5 | f62e4a3f/52915220 |
| 2 | `blog.html` | `home.php` | آرشیو وبلاگ (برگه blog) | 2 | 15c139b7/52915220 |
| 3 | `blog/baloti-seven-color-string.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 4 | `blog/frame-holder-plt.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 5 | `blog/led-wall-washer-guide.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 6 | `blog/point-light-pixel-led.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 7 | `blog/sun-display-32-vs-48.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 8 | `blog/types-of-light-tunnel.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 9 | `blog/urban-lighting-elements.html` | `single.php` | تک‌نوشته | 3 | 21e87344/afa8597d |
| 10 | `cart.html` | `woocommerce/cart/cart.php` | سبد خرید | 2 | 1bd992a7/52915220 |
| 11 | `category/magazine.html` | `category.php` | آرشیو دسته نوشته | 2 | b2f64f33/afa8597d |
| 12 | `category/news-and-events.html` | `category.php` | آرشیو دسته نوشته | 2 | b2f64f33/afa8597d |
| 13 | `checkout.html` | `woocommerce/checkout/form-checkout.php` | تسویه حساب | 2 | 1bd992a7/52915220 |
| 14 | `city/ahvaz.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 15 | `city/alborz.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 16 | `city/birjand.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 17 | `city/esfahan.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 18 | `city/iraq.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 19 | `city/kerman.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 20 | `city/khorsand.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 21 | `city/mashhad.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 22 | `city/qom.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 23 | `city/tehran.html` | `taxonomy-city.php` | آرشیو شهر | 3 | 2e0e568c/afa8597d |
| 24 | `contact.html` | `page-templates/template-contact.php` | برگه تماس با ما | 2 | 1d6d5308/52915220 |
| 25 | `faq.html` | `page-templates/template-faq.php` | برگه سوالات متداول | 3 | ac127641/52915220 |
| 26 | `index.html` | `front-page.php` | صفحه اصلی (۱۵ سکشن) | 15 | 9090bfd4/52915220 |
| 27 | `licenses-and-honors.html` | `page-templates/template-licenses.php` | برگه مجوزها و افتخارات | 3 | a83c1628/52915220 |
| 28 | `product/artam-light-tree.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 29 | `product/baloti-string.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 30 | `product/delsa-light-tree.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 31 | `product/fandoghi-string.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 32 | `product/finger-string-lights.html` | `woocommerce/single-product.php` | صفحه محصول | 5 | f6329fa1/afa8597d |
| 33 | `product/foroogh-chandelier.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 34 | `product/illumina.html` | `woocommerce/single-product.php` | صفحه محصول | 5 | f6329fa1/afa8597d |
| 35 | `product/industrial-led-bulb.html` | `woocommerce/single-product.php` | صفحه محصول | 5 | f6329fa1/afa8597d |
| 36 | `product/lighting-ball.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 37 | `product/linear-light-facade.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 38 | `product/needle-string-lights.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 39 | `product/pixel-point-string.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 40 | `product/point-light-pixel-6cm.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 41 | `product/point-light-pixel.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 42 | `product/shamse-chandelier.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 43 | `product/smart-control-box.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 44 | `product/star-lamp-string.html` | `woocommerce/single-product.php` | صفحه محصول | 5 | f6329fa1/afa8597d |
| 45 | `product/sun-light-32.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 46 | `product/sun-light-48.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 47 | `product/wall-washer.html` | `woocommerce/single-product.php` | صفحه محصول | 6 | f6329fa1/afa8597d |
| 48 | `product_cat/acorn-thread.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 49 | `product_cat/control-box.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 50 | `product_cat/finger-thread.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 51 | `product_cat/hazelnut-thread.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 52 | `product_cat/led-lamp.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 53 | `product_cat/light-chandelier.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 54 | `product_cat/light-tree.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 55 | `product_cat/needle-thread.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 56 | `product_cat/point-light.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 57 | `product_cat/rise-point-lite-pixel.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 58 | `product_cat/star-lamp-thread.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 59 | `product_cat/strings-and-lighting-decorations.html` <br>↳ تکرارِ `products/category-led-string-lights.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 60 | `product_cat/sun-light-elements.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 61 | `product_cat/urban-lighting-elements.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 62 | `product_cat/wall-washer.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول | 3 | f6329fa1/afa8597d |
| 63 | `products.html` <br>↳ تکرارِ `shop.html` | `woocommerce/archive-product.php` | آرشیو فروشگاه /shop/ | 3 | bf6b5cc0/52915220 |
| 64 | `products/artam-light-tree.html` <br>↳ تکرارِ `product/artam-light-tree.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 65 | `products/baloti-string.html` <br>↳ تکرارِ `product/baloti-string.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 66 | `products/category-led-string-lights.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 67 | `products/category-led-wall-washer.html` <br>↳ تکرارِ `product_cat/wall-washer.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 68 | `products/category-point-light-pixel.html` <br>↳ تکرارِ `product_cat/point-light.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 69 | `products/category-smart-light-trees.html` <br>↳ تکرارِ `product_cat/light-tree.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 70 | `products/category-urban-chandeliers.html` <br>↳ تکرارِ `product_cat/light-chandelier.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 71 | `products/category-urban-light-elements.html` <br>↳ تکرارِ `product_cat/urban-lighting-elements.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 72 | `products/category-urban-sunbursts.html` <br>↳ تکرارِ `product_cat/sun-light-elements.html` | `woocommerce/taxonomy-product_cat.php` | آرشیو دسته محصول (نام قدیمی) | 3 | f6329fa1/afa8597d |
| 73 | `products/delsa-light-tree.html` <br>↳ تکرارِ `product/delsa-light-tree.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 74 | `products/fandoghi-string.html` <br>↳ تکرارِ `product/fandoghi-string.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 75 | `products/finger-string-lights.html` <br>↳ تکرارِ `product/finger-string-lights.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 5 | f6329fa1/afa8597d |
| 76 | `products/foroogh-chandelier.html` <br>↳ تکرارِ `product/foroogh-chandelier.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 77 | `products/illumina.html` <br>↳ تکرارِ `product/illumina.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 5 | f6329fa1/afa8597d |
| 78 | `products/industrial-led-bulb.html` <br>↳ تکرارِ `product/industrial-led-bulb.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 5 | f6329fa1/afa8597d |
| 79 | `products/lighting-ball.html` <br>↳ تکرارِ `product/lighting-ball.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 80 | `products/linear-light-facade.html` <br>↳ تکرارِ `product/linear-light-facade.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 81 | `products/needle-string-lights.html` <br>↳ تکرارِ `product/needle-string-lights.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 82 | `products/pixel-point-string.html` <br>↳ تکرارِ `product/pixel-point-string.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 83 | `products/point-light-pixel-6cm.html` <br>↳ تکرارِ `product/point-light-pixel-6cm.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 84 | `products/point-light-pixel.html` <br>↳ تکرارِ `product/point-light-pixel.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 85 | `products/shamse-chandelier.html` <br>↳ تکرارِ `product/shamse-chandelier.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 86 | `products/smart-control-box.html` <br>↳ تکرارِ `product/smart-control-box.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 87 | `products/star-lamp-string.html` <br>↳ تکرارِ `product/star-lamp-string.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 5 | f6329fa1/afa8597d |
| 88 | `products/sun-light-32.html` <br>↳ تکرارِ `product/sun-light-32.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 89 | `products/sun-light-48.html` <br>↳ تکرارِ `product/sun-light-48.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 90 | `products/wall-washer.html` <br>↳ تکرارِ `product/wall-washer.html` | `woocommerce/single-product.php` | صفحه محصول (نام قدیمی) | 6 | f6329fa1/afa8597d |
| 91 | `project_type/ball-of-light.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 92 | `project_type/bridge-lighting.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 93 | `project_type/light-tree.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 94 | `project_type/light-tunnel.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 95 | `project_type/lighting-building.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 96 | `project_type/luminous-elements.html` <br>↳ تکرارِ `projects/type-light-element.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 97 | `project_type/square-lighting.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 98 | `project_type/urban-lighting.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه | 3 | 2e0e568c/afa8597d |
| 99 | `projects.html` | `archive-projects.php` | آرشیو پروژه‌ها | 3 | da652b0a/52915220 |
| 100 | `projects/ahvaz-semi-pyramid-light.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 101 | `projects/artam-tree-midan.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 102 | `projects/birjand-smart-light-trees.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 103 | `projects/bridge-fullcolor-qom.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 104 | `projects/city-alborz.html` <br>↳ تکرارِ `city/alborz.html` | `taxonomy-city.php` | آرشیو شهر (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 105 | `projects/city-esfahan.html` <br>↳ تکرارِ `city/esfahan.html` | `taxonomy-city.php` | آرشیو شهر (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 106 | `projects/city-khorsand.html` <br>↳ تکرارِ `city/khorsand.html` | `taxonomy-city.php` | آرشیو شهر (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 107 | `projects/city-qom.html` <br>↳ تکرارِ `city/qom.html` | `taxonomy-city.php` | آرشیو شهر (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 108 | `projects/esfahan-shohada-square.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 109 | `projects/half-arch-tunnel-alborz.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 110 | `projects/heart-tunnel-khorsand.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 111 | `projects/iraq-karbala-urban-lighting.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 112 | `projects/kerman-arch-light-tunnel.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 113 | `projects/mashhad-pilgrim-boulevard-lighting.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 114 | `projects/tehran-commercial-facade-lighting.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 115 | `projects/tunnel-90-alborz.html` | `single-projects.php` | صفحه پروژه | 5 | 2e0e568c/afa8597d |
| 116 | `projects/type-bridge-lighting.html` <br>↳ تکرارِ `project_type/bridge-lighting.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 117 | `projects/type-light-element.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 118 | `projects/type-light-tunnel.html` <br>↳ تکرارِ `project_type/light-tunnel.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 119 | `projects/type-square-lighting.html` <br>↳ تکرارِ `project_type/square-lighting.html` | `taxonomy-project_type.php` | آرشیو نوع پروژه (نام قدیمی) | 3 | 2e0e568c/afa8597d |
| 120 | `recruitment.html` | `page-templates/template-recruitment.php` | برگه فرصت‌های شغلی | 2 | 1333d674/52915220 |
| 121 | `refund_returns.html` | `page-templates/template-refund.php` | برگه شرایط گارانتی | 3 | 30861e46/52915220 |
| 122 | `services.html` | `page-templates/template-services.php` | برگه فهرست خدمات | 4 | a5a01c79/52915220 |
| 123 | `services/bridge-lighting.html` | `single-service.php (یا قالب برگه فرزند)` | جزئیات خدمت | 3 | 62e5c5c8/afa8597d |
| 124 | `services/building-lighting.html` | `single-service.php (یا قالب برگه فرزند)` | جزئیات خدمت | 3 | 62e5c5c8/afa8597d |
| 125 | `services/element-lighting.html` | `single-service.php (یا قالب برگه فرزند)` | جزئیات خدمت | 3 | 62e5c5c8/afa8597d |
| 126 | `services/square-lighting.html` | `single-service.php (یا قالب برگه فرزند)` | جزئیات خدمت | 3 | 62e5c5c8/afa8597d |
| 127 | `services/street-lighting.html` | `single-service.php (یا قالب برگه فرزند)` | جزئیات خدمت | 3 | 62e5c5c8/afa8597d |
| 128 | `shop.html` | `woocommerce/archive-product.php` | آرشیو فروشگاه /shop/ | 3 | bf6b5cc0/52915220 |

## ۳. سکشن‌های صفحه اصلی (`index.html` → `front-page.php`)

| # | id | aria-label | بایت | tab | form | img | داده‌هوک‌های اصلی |
|---|---|---|---|---|---|---|---|
| 2 | `home` | بخش آغازین - طرح خانه ۱ (نوتچ معماری و دسته‌بندی‌ها) | 28200 | 0 | 0 | 5 | `data-home1-scene-img, data-home1-overlay-item, data-home1-scene-btn, data-scene-title, data-scene-href, data-scene-cta` |
| 3 | `projects` | - | 6205 | 0 | 0 | 3 | `` |
| 4 | `-` | گالری نمونه‌کارها و پروژه‌های اجراشده عرفان صنعت | 25642 | 3 | 0 | 15 | `data-home2-port-tab, data-home2-port-panel` |
| 5 | `about` | - | 7314 | 0 | 0 | 1 | `data-counter-target, data-counter-suffix` |
| 6 | `-` | درباره تاسیس شرکت دانش‌بنیان عرفان صنعت اصفهان | 5758 | 0 | 0 | 3 | `` |
| 7 | `-` | تاریخچه شرکت دانش‌بنیان عرفان صنعت اصفهان | 33507 | 6 | 0 | 18 | `data-home2-history-btn, data-home2-history-panel, data-home2-history-prev, data-home2-history-next, data-history-scroll-track, data-history-total` |
| 8 | `products` | - | 15299 | 0 | 0 | 6 | `` |
| 9 | `-` | دانلود کاتالوگ جامع محصولات عرفان صنعت | 3794 | 0 | 0 | 2 | `` |
| 10 | `services` | - | 15255 | 0 | 0 | 6 | `data-service-row, data-service-title, data-service-desc-text, data-service-img, data-service-counter, data-service-indicator` |
| 11 | `-` | - | 11224 | 0 | 0 | 0 | `` |
| 12 | `-` | خدمات پس از فروش عرفان صنعت در سراسر ایران | 36855 | 0 | 0 | 0 | `` |
| 13 | `-` | مشتریان و سازمان‌های همکار عرفان صنعت | 13942 | 0 | 0 | 0 | `` |
| 14 | `blog-preview` | ویترین مقالات کاربردی عرفان صنعت | 8893 | 0 | 0 | 4 | `` |
| 15 | `contact` | - | 6613 | 0 | 0 | 0 | `` |
| 16 | `-` | موقعیت کارخانه روی نقشه و پشتیبانی فنی عرفان صنعت | 3402 | 0 | 0 | 0 | `` |

## ۴. سکشن‌های صفحه محصول (`single-product.php`) — `product/delsa-light-tree.html`

| # | id | aria-label | بایت | tab | form | img | هوک‌ها |
|---|---|---|---|---|---|---|---|
| 2 | `-` | - | 3681 | 0 | 0 | 0 | `` |
| 3 | `-` | - | 17184 | 0 | 0 | 6 | `data-gallery-thumb, data-gallery-src, data-gallery-thumb-ring, data-gallery-lightbox-dot, data-gallery-root, data-gallery-main-img` |
| 4 | `product-details` | - | 41967 | 0 | 1 | 0 | `data-review-star, data-review-star-svg, data-prod-tab-btn, data-prod-tab-icon, data-prod-tab-panel, data-prod-tabs-container` |
| 5 | `-` | - | 4855 | 0 | 0 | 2 | `` |
| 6 | `-` | - | 4879 | 0 | 0 | 0 | `data-faq-toggle, data-faq-icon, data-faq-body, data-faq-list` |
| 7 | `-` | - | 6439 | 0 | 0 | 3 | `` |

## ۴. سکشن‌های صفحه پروژه (`single-projects.php`) — `projects/esfahan-shohada-square.html`

| # | id | aria-label | بایت | tab | form | img | هوک‌ها |
|---|---|---|---|---|---|---|---|
| 2 | `-` | - | 3752 | 0 | 0 | 0 | `` |
| 3 | `-` | - | 19406 | 0 | 0 | 7 | `data-gallery-thumb, data-gallery-src, data-gallery-thumb-ring, data-gallery-lightbox-dot, data-gallery-root, data-gallery-main-img` |
| 4 | `project-details` | - | 18564 | 0 | 0 | 0 | `data-prod-tab-btn, data-prod-tab-icon, data-prod-tab-panel, data-prod-tabs-container` |
| 5 | `used-products` | - | 8068 | 0 | 0 | 4 | `` |
| 6 | `-` | - | 6374 | 0 | 0 | 3 | `` |

## ۴. سکشن‌های تک‌نوشته (`single.php`) — `blog/types-of-light-tunnel.html`

| # | id | aria-label | بایت | tab | form | img | هوک‌ها |
|---|---|---|---|---|---|---|---|
| 2 | `-` | - | 7167 | 0 | 0 | 1 | `` |
| 3 | `-` | - | 16200 | 0 | 0 | 1 | `data-share-btn, data-share-label` |
| 4 | `-` | - | 5784 | 0 | 0 | 3 | `` |

## ۴. سکشن‌های آرشیو فروشگاه (`archive-product.php`) — `products.html`

| # | id | aria-label | بایت | tab | form | img | هوک‌ها |
|---|---|---|---|---|---|---|---|
| 2 | `-` | - | 9331 | 0 | 0 | 1 | `` |
| 3 | `archive` | - | 130793 | 0 | 0 | 20 | `data-product-item, data-product-category, data-product-categories, data-product-purchasable, data-product-price, data-product-ip` |
| 4 | `contact` | - | 6613 | 0 | 0 | 0 | `` |

## ۴. سکشن‌های آرشیو پروژه (`archive-projects.php`) — `projects.html`

| # | id | aria-label | بایت | tab | form | img | هوک‌ها |
|---|---|---|---|---|---|---|---|
| 2 | `-` | - | 5654 | 0 | 0 | 3 | `` |
| 3 | `archive` | - | 90592 | 0 | 0 | 12 | `data-project-item, data-project-type, data-project-types, data-project-location, data-project-views, data-project-rating` |
| 4 | `contact` | - | 6613 | 0 | 0 | 0 | `` |

## ۴. سکشن‌های آرشیو وبلاگ (`home.php`) — `blog.html`

| # | id | aria-label | بایت | tab | form | img | هوک‌ها |
|---|---|---|---|---|---|---|---|
| 2 | `-` | - | 7709 | 0 | 0 | 1 | `data-blog-cat-btn, data-blog-search` |
| 3 | `articles` | - | 31012 | 0 | 0 | 7 | `data-blog-item, data-blog-category, data-blog-search-text, data-page-num, data-blog-archive, data-items-per-page` |

## ۵. فایل‌های تکراری (باید ادغام شوند، نه دوباره ساخته شوند)

| گروه | فایل اصلی (مرجع) | فایل‌های تکراری |
|---|---|---|
| 2 | `city/alborz.html` | `projects/city-alborz.html` |
| 2 | `city/esfahan.html` | `projects/city-esfahan.html` |
| 2 | `city/khorsand.html` | `projects/city-khorsand.html` |
| 2 | `city/qom.html` | `projects/city-qom.html` |
| 2 | `product/artam-light-tree.html` | `products/artam-light-tree.html` |
| 2 | `product/baloti-string.html` | `products/baloti-string.html` |
| 2 | `product/delsa-light-tree.html` | `products/delsa-light-tree.html` |
| 2 | `product/fandoghi-string.html` | `products/fandoghi-string.html` |
| 2 | `product/finger-string-lights.html` | `products/finger-string-lights.html` |
| 2 | `product/foroogh-chandelier.html` | `products/foroogh-chandelier.html` |
| 2 | `product/illumina.html` | `products/illumina.html` |
| 2 | `product/industrial-led-bulb.html` | `products/industrial-led-bulb.html` |
| 2 | `product/lighting-ball.html` | `products/lighting-ball.html` |
| 2 | `product/linear-light-facade.html` | `products/linear-light-facade.html` |
| 2 | `product/needle-string-lights.html` | `products/needle-string-lights.html` |
| 2 | `product/pixel-point-string.html` | `products/pixel-point-string.html` |
| 2 | `product/point-light-pixel-6cm.html` | `products/point-light-pixel-6cm.html` |
| 2 | `product/point-light-pixel.html` | `products/point-light-pixel.html` |
| 2 | `product/shamse-chandelier.html` | `products/shamse-chandelier.html` |
| 2 | `product/smart-control-box.html` | `products/smart-control-box.html` |
| 2 | `product/star-lamp-string.html` | `products/star-lamp-string.html` |
| 2 | `product/sun-light-32.html` | `products/sun-light-32.html` |
| 2 | `product/sun-light-48.html` | `products/sun-light-48.html` |
| 2 | `product/wall-washer.html` | `products/wall-washer.html` |
| 2 | `product_cat/light-chandelier.html` | `products/category-urban-chandeliers.html` |
| 2 | `product_cat/light-tree.html` | `products/category-smart-light-trees.html` |
| 2 | `product_cat/point-light.html` | `products/category-point-light-pixel.html` |
| 2 | `products/category-led-string-lights.html` | `product_cat/strings-and-lighting-decorations.html` |
| 2 | `product_cat/sun-light-elements.html` | `products/category-urban-sunbursts.html` |
| 2 | `product_cat/urban-lighting-elements.html` | `products/category-urban-light-elements.html` |
| 2 | `product_cat/wall-washer.html` | `products/category-led-wall-washer.html` |
| 2 | `shop.html` | `products.html` |
| 2 | `project_type/bridge-lighting.html` | `projects/type-bridge-lighting.html` |
| 2 | `project_type/light-tunnel.html` | `projects/type-light-tunnel.html` |
| 2 | `projects/type-light-element.html` | `project_type/luminous-elements.html` |
| 2 | `project_type/square-lighting.html` | `projects/type-square-lighting.html` |

## ۶. کاتالوگ دارایی‌ها (Assets)

| فایل | حجم | مقصد در قالب |
|---|---|---|
| `assets/css/styles.css` | - | `assets/css/styles.css` |
| `assets/js/main.js` | - | `assets/js/main.js` |
| `assets/fonts/vazirmatn-arabic.woff2` | - | `assets/fonts/` |
| `assets/fonts/vazirmatn-latin.woff2` | - | `assets/fonts/` |
| `assets/fonts/vazirmatn-latin-ext.woff2` | - | `assets/fonts/` |
| `images/ese-logo.svg` | - | `assets/img/ (لوگو)` |
| `images/hero.jpg … (10 تصویر)` | - | `assets/img/ (اسلایدهای هیرو)` |
| `images/pexels/*.jpg (11)` | - | `assets/img/pexels/` |
| `images/hero-video.mp4` | - | `assets/video/ (۶.۸MB — نیاز به تأیید)` |

## ۷. صفحاتی که در میرور استاتیک وجود ندارند (نیازمند تصمیم)

- `arya` — لندینگ درب بازکن هوشمند (ID 16947)
- `smartelement` — لندینگ خدمات (ID 11984)
- `my-account` / `auth` — حساب کاربری و ورود (WooCommerce + voorodak)
- `project` — برگه پروژه‌های برگزیده (ID 8366)
- `search.php` — نتایج جستجو، `404.php`، `page.php` عمومی، `single.php` برای برگه‌های ووکامرس خالی


## یادداشت دامنه برای Task 5.17 (پس از اسکن)

static mirror همچنان دقیقاً ۱۵ سکشن homepage دارد. با تصمیم صریح کاربر برای Task 5.17، theme یک سکشن شانزدهم مستقل `why-ese` اضافه می‌کند که از اطلاعات موجود شرکت ساخته شده و static-reference-derived نیست. این یادداشت به اسکن خودکار نسبت داده نمی‌شود و جدول استخراج‌شده را تغییر نمی‌دهد.
