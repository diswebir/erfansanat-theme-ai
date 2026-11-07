# نقشه میرور استاتیک و نگاشت به وردپرس

> اسکن خودکار فایل‌های استخراج‌شده؛ **128 فایل HTML** پیدا شد. عنوان، تعداد عناصر اصلی و نمونه شناسه/کلاس سکشن‌ها از هر فایل خوانده شده است. نگاشت، پیشنهاد اولیه است و موارد مبهم برای تصمیم کاربر علامت‌گذاری شده‌اند.

## موجودی دارایی‌ها

- **CSS:** `assets/css/styles.css`
- **JavaScript:** `assets/js/main.js`
- **فونت WOFF2 (3):** `assets/fonts/vazirmatn-arabic.woff2`, `assets/fonts/vazirmatn-latin-ext.woff2`, `assets/fonts/vazirmatn-latin.woff2`
- **JPEG (20):** `images/chandelier.jpg`, `images/factory.jpg`, `images/hero.jpg`, `images/light-element.jpg`, `images/light-sphere.jpg`, `images/light-tree.jpg`, `images/light-tunnel.jpg`, `images/pexels/pexels-10951256.jpg`, `images/pexels/pexels-14677080.jpg`, `images/pexels/pexels-14720175.jpg`, `images/pexels/pexels-14822163.jpg`, `images/pexels/pexels-17789372.jpg`, `images/pexels/pexels-19112845.jpg`, `images/pexels/pexels-29570545.jpg`, `images/pexels/pexels-29689925.jpg`, `images/pexels/pexels-34993337.jpg`, `images/pexels/pexels-35318900.jpg`, `images/pexels/pexels-36052966.jpg`, `images/string-lights.jpg`, `images/sun-light.jpg`
- **SVG (1):** `images/ese-logo.svg`
- **ویدئو (1):** `images/hero-video.mp4`

## مسیر دارایی‌ها در پوسته

- CSS، JavaScript و فونت‌ها بدون تغییر باینری از `static-mirror/assets/` به `assets/` منتقل شده‌اند.
- تصاویر، SVG و ویدئو از `static-mirror/images/` به `assets/images/` منتقل شده‌اند؛ ساختار زیرپوشهٔ `pexels/` حفظ شده است.
- مسیرهای نسبی فونت در CSS (`../fonts/*.woff2`) سالم مانده‌اند؛ تصاویر/ویدئو در قالب‌های وردپرس باید با URL پوسته و مسیر `assets/images/` ارجاع داده شوند.

## نگاشت صفحه‌به‌صفحه

| فایل HTML | عنوان سند | ساختار (`header/main/section/footer`) | سکشن‌های شاخص (id؛ class در صورت نبود id) | نگاشت پیشنهادی وردپرس |
|---|---|---:|---|---|
| `about.html` | درباره ما \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, .border-y, #contact | page-templates/about.php |
| `blog.html` | وبلاگ و مقالات تخصصی نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/2/1 | .relative, #articles | home.php / archive.php (آرشیو وبلاگ) |
| `blog/baloti-seven-color-string.html` | ریسه بلوطی هفت رنگ \| مشخصات فنی، قیمت و کاربردها در نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `blog/frame-holder-plt.html` | حضور قاب نگهدارنده PLT عرفان صنعت در پروژه‌های نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `blog/led-wall-washer-guide.html` | والواشر LED چیست؟ راهنمای جامع نورپردازی نمای ساختمان با والواشر RGB \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `blog/point-light-pixel-led.html` | پوینت لایت پیکسل LED چیست و چه کاربردی در نورپردازی شهری دارد؟ \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `blog/sun-display-32-vs-48.html` | بررسی و مقایسه خورشیدنما ۳۲ و ۴۸ پره \| کدام گزینه برای پروژه شما مناسب‌تر است؟ \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `blog/types-of-light-tunnel.html` | آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `blog/urban-lighting-elements.html` | المان‌های نورپردازی شهری \| راهنمای جامع خرید و استانداردهای فنی \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, .border-t | single.php (مقاله) |
| `cart.html` | سبد خرید و لیست پیش‌فاکتور \| عرفان صنعت اصفهان (ESE) | 1/1/2/1 | .relative, .mx-auto | قالب/صفحه WooCommerce (cart) |
| `category/magazine.html` | undefined | 1/1/2/1 | .relative, #articles | category.php |
| `category/news-and-events.html` | undefined | 1/1/2/1 | .relative, #articles | category.php |
| `checkout.html` | ثبت نهایی سفارش و صدور پیش‌فاکتور رسمی \| عرفان صنعت اصفهان (ESE) | 1/1/2/1 | .relative, .mx-auto | قالب/صفحه WooCommerce (checkout) |
| `city/ahvaz.html` | پروژه‌های نورپردازی شهری و نیم هرم نوری در اهواز \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/alborz.html` | پروژه‌های نورپردازی شهری و تونل نوری در استان البرز و کرج \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/birjand.html` | پروژه‌های نورپردازی شهری و درخت نوری در بیرجند \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/esfahan.html` | پروژه‌های نورپردازی شهری در اصفهان \| شرکت دانش‌بنیان عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/iraq.html` | پروژه‌های صادراتی نورپردازی شهری در کشور عراق \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/kerman.html` | پروژه‌های نورپردازی شهری و تونل نوری در کرمان \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/khorsand.html` | پروژه‌های نورپردازی شهری و تونل قلبی در شهر خورسند \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/mashhad.html` | پروژه‌های نورپردازی شهری و خورشیدنما در مشهد \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/qom.html` | پروژه‌های نورپردازی شهری و نورپردازی پل در قم \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `city/tehran.html` | پروژه‌های نورپردازی نمای ساختمان و المان شهری در تهران \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | taxonomy-city.php |
| `contact.html` | تماس با ما و استعلام قیمت \| عرفان صنعت اصفهان (ESE) | 1/1/2/1 | .relative, .mx-auto | page-templates/contact.php |
| `faq.html` | سوالات متداول نورپردازی شهری و خرید محصولات \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, #contact | page-templates/faq.php |
| `index.html` | عرفان صنعت اصفهان (ESE) \| نورپردازی شهری و المان‌های نوری | 1/1/15/1 | #home, #projects, .relative, #about, .mx-auto, #products, #services, #blog-preview | front-page.php (صفحه خانه) |
| `licenses-and-honors.html` | مجوزها، گواهینامه‌ها و افتخارات \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, #contact | page-templates/licenses-and-honors.php |
| `product/artam-light-tree.html` | درخت نوری آرتام دو طبقه هوشمند \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/baloti-string.html` | ریسه بلوطی هفت رنگ و تک‌رنگ (IP67) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/delsa-light-tree.html` | درخت نوری دلسا \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/fandoghi-string.html` | ریسه فندقی فول‌کالر هوشمند \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/finger-string-lights.html` | ریسه LED انگشتی ضدآب شهری \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/foroogh-chandelier.html` | لوستر نوری فروغ (سه‌طبقه) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/illumina.html` | سازه حجمی ایلومینا (۲۴ خروجی) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/industrial-led-bulb.html` | لامپ LED رنگی و دکوراتیو ضدآب (سرپیچ E27) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/lighting-ball.html` | گوی نورانی هوشمند ۳۶۰ درجه \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/linear-light-facade.html` | لینیر لایت نما (خطی دفنی و روکار IP68) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/needle-string-lights.html` | ریسه سوزنی شهری ضد آب (IP68) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/pixel-point-string.html` | ریسه پوینت لایت پیکسلی آویز و پرده‌ای \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/point-light-pixel-6cm.html` | پوینت لایت پیکسل ۶ سانتی پرقدرت \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/point-light-pixel.html` | پوینت لایت پیکسل ۴ سانتی فول‌کالر \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/shamse-chandelier.html` | لوستر نوری شمسه (شش‌پر) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/smart-control-box.html` | باکس کنترل هوشمند مرکزی نورپردازی (NRF / WiFi) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفه | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/star-lamp-string.html` | ریسه لامپ ستاره‌ای دکوراتیو فضای باز \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/sun-light-32.html` | خورشیدنما ۳۲ پره هوشمند (WiFi) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/sun-light-48.html` | خورشیدنما ۴۸ پره فول‌کالر (NRF) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product/wall-washer.html` | والواشر RGB و تک‌رنگ صنعتی (IP68) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php (صفحه محصول؛ میرور legacy) |
| `product_cat/acorn-thread.html` | قیمت و خرید ریسه بلوطی LED هفت رنگ و تک رنگ (IP67) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/control-box.html` | قیمت و خرید باکس کنترل هوشمند نورپردازی شهری (WiFi و NRF) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/finger-thread.html` | قیمت و خرید ریسه LED انگشتی ضد آب شهری (IP67) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/hazelnut-thread.html` | قیمت و خرید ریسه فندقی فول‌کالر و هوشمند شهری (IP67) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/led-lamp.html` | قیمت و خرید لامپ LED رنگی و دکوراتیو ضد آب شهری \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/light-chandelier.html` | قیمت و خرید لوستر نوری فضای باز و شهری (فروغ و شمسه) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/light-tree.html` | قیمت و خرید درخت نوری هوشمند شهری (دلسا، آرتام و نخل) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/needle-thread.html` | قیمت و خرید ریسه ال ای دی مدل سوزنی شهری ضد آب (IP68) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/point-light.html` | قیمت و خرید پوینت لایت پیکسل LED فول‌کالر ۴ و ۶ سانتی (IP68) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/rise-point-lite-pixel.html` | قیمت و خرید ریسه پوینت لایت پیکسل هوشمند (IP68) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/star-lamp-thread.html` | قیمت و خرید ریسه لامپ ستاره ای ضد آب شهری \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/strings-and-lighting-decorations.html` | قیمت و خرید ریسه و تزئینات نورپردازی شهری (بلوطی، فندقی، سوزنی IP68) \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/sun-light-elements.html` | قیمت و خرید المان نوری خورشیدنما شهری ۳۲ و ۴۸ پره فول‌کالر \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/urban-lighting-elements.html` | قیمت و خرید المان های نورپردازی شهری، خورشیدنما و گوی نورانی \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `product_cat/wall-washer.html` | قیمت و خرید لینیرلایت و والواشر LED و RGB نمای ساختمان (IP68) \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | taxonomy-product_cat.php |
| `products.html` | محصولات نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #archive, #contact | page-templates/catalog.php یا archive-product.php (نیازمند تعیین مرجع) |
| `products/artam-light-tree.html` | درخت نوری آرتام دو طبقه هوشمند \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/baloti-string.html` | ریسه بلوطی هفت رنگ و تک‌رنگ (IP67) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-led-string-lights.html` | قیمت و خرید ریسه و تزئینات نورپردازی شهری (بلوطی، فندقی، سوزنی IP68) \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-led-wall-washer.html` | قیمت و خرید لینیرلایت و والواشر LED و RGB نمای ساختمان (IP68) \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-point-light-pixel.html` | قیمت و خرید پوینت لایت پیکسل LED فول‌کالر ۴ و ۶ سانتی (IP68) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-smart-light-trees.html` | قیمت و خرید درخت نوری هوشمند شهری (دلسا، آرتام و نخل) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-urban-chandeliers.html` | قیمت و خرید لوستر نوری فضای باز و شهری (فروغ و شمسه) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-urban-light-elements.html` | قیمت و خرید المان های نورپردازی شهری، خورشیدنما و گوی نورانی \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/category-urban-sunbursts.html` | قیمت و خرید المان نوری خورشیدنما شهری ۳۲ و ۴۸ پره فول‌کالر \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/delsa-light-tree.html` | درخت نوری دلسا \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/fandoghi-string.html` | ریسه فندقی فول‌کالر هوشمند \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/finger-string-lights.html` | ریسه LED انگشتی ضدآب شهری \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/foroogh-chandelier.html` | لوستر نوری فروغ (سه‌طبقه) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/illumina.html` | سازه حجمی ایلومینا (۲۴ خروجی) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/industrial-led-bulb.html` | لامپ LED رنگی و دکوراتیو ضدآب (سرپیچ E27) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/lighting-ball.html` | گوی نورانی هوشمند ۳۶۰ درجه \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/linear-light-facade.html` | لینیر لایت نما (خطی دفنی و روکار IP68) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/needle-string-lights.html` | ریسه سوزنی شهری ضد آب (IP68) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/pixel-point-string.html` | ریسه پوینت لایت پیکسلی آویز و پرده‌ای \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/point-light-pixel-6cm.html` | پوینت لایت پیکسل ۶ سانتی پرقدرت \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/point-light-pixel.html` | پوینت لایت پیکسل ۴ سانتی فول‌کالر \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/shamse-chandelier.html` | لوستر نوری شمسه (شش‌پر) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/smart-control-box.html` | باکس کنترل هوشمند مرکزی نورپردازی (NRF / WiFi) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفه | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/star-lamp-string.html` | ریسه لامپ ستاره‌ای دکوراتیو فضای باز \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/5/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/sun-light-32.html` | خورشیدنما ۳۲ پره هوشمند (WiFi) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/sun-light-48.html` | خورشیدنما ۴۸ پره فول‌کالر (NRF) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `products/wall-washer.html` | والواشر RGB و تک‌رنگ صنعتی (IP68) \| خرید، مشخصات فنی و قیمت کارخانه \| عرفان صنعت اصفهان | 1/1/6/1 | .relative, .mx-auto, #product-details, .border-t | single-product.php یا taxonomy-product_cat.php (بر اساس نام category-*) |
| `project_type/ball-of-light.html` | پروژه‌های اجرایی گوی نورانی و توپ نورانی شهری \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/bridge-lighting.html` | پروژه‌های نورپردازی پل‌های شهری و عابر پیاده \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/light-tree.html` | پروژه‌های اجرایی درخت نوری هوشمند در شهرهای ایران \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/light-tunnel.html` | پروژه‌های تونل نوری شهری (کمانی، نیم‌آرک و قلبی) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/lighting-building.html` | پروژه‌های نورپردازی ساختمان و نمای مجتمع‌های تجاری \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/luminous-elements.html` | پروژه‌های المان‌های نورانی و سازه‌های حجمی شهری \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/square-lighting.html` | پروژه‌های نورپردازی میدان‌های شهری \| نمونه کارهای عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `project_type/urban-lighting.html` | پروژه‌های نورپردازی شهری و زیباسازی معابر \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | taxonomy-project_type.php |
| `projects.html` | پروژه‌های نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #archive, #contact | archive-projects.php |
| `projects/ahvaz-semi-pyramid-light.html` | اجرای پروژه نیم هرم نوری اهواز (اهواز) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/artam-tree-midan.html` | المان درخت نوری آرتام دو طبقه (اصفهان) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/birjand-smart-light-trees.html` | نصب درختان نوری هوشمند دلسا و خورشیدنما — بیرجند (بیرجند) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/bridge-fullcolor-qom.html` | نورپردازی پل با ریسه فول‌کالر — قم (قم) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/city-alborz.html` | پروژه‌های نورپردازی شهری و تونل نوری در استان البرز و کرج \| عرفان صنعت | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/city-esfahan.html` | پروژه‌های نورپردازی شهری در اصفهان \| شرکت دانش‌بنیان عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/city-khorsand.html` | پروژه‌های نورپردازی شهری و تونل قلبی در شهر خورسند \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/city-qom.html` | پروژه‌های نورپردازی شهری و نورپردازی پل در قم \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/esfahan-shohada-square.html` | نورپردازی میدان شهدای جهان اسلام اصفهان (اصفهان) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/half-arch-tunnel-alborz.html` | تونل نوری نیم‌آرک استان البرز (استان البرز) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/heart-tunnel-khorsand.html` | تونل قلبی شهدای خورسند (خورسند) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/iraq-karbala-urban-lighting.html` | پروژه صادراتی نورپردازی مسیر زائران و موکب‌های مرکزی — عراق (عراق) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/kerman-arch-light-tunnel.html` | احداث تونل نوری کمانی و گوی‌های نورانی — کرمان (کرمان) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/mashhad-pilgrim-boulevard-lighting.html` | آذین‌بندی و نورپردازی بلوارهای منتهی به حرم مطهر — مشهد (مشهد) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/tehran-commercial-facade-lighting.html` | نورپردازی مالتی‌مدیا نمای مجتمع تجاری و اداری — تهران (تهران) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/tunnel-90-alborz.html` | تونل نوری ۹۰ درجه استان البرز (استان البرز) \| عرفان صنعت اصفهان (ESE) | 1/1/5/1 | .relative, .mx-auto, #project-details, #used-products, .border-t | single-projects.php |
| `projects/type-bridge-lighting.html` | پروژه‌های نورپردازی پل‌های شهری و عابر پیاده \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/type-light-element.html` | پروژه‌های المان‌های نورانی و سازه‌های حجمی شهری \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/type-light-tunnel.html` | پروژه‌های تونل نوری شهری (کمانی، نیم‌آرک و قلبی) \| عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `projects/type-square-lighting.html` | پروژه‌های نورپردازی میدان‌های شهری \| نمونه کارهای عرفان صنعت اصفهان | 1/1/3/1 | .relative, #archive, #contact | single-projects.php |
| `recruitment.html` | فرصت‌های شغلی و استخدام \| عرفان صنعت اصفهان (ESE) | 1/1/2/1 | .relative, .mx-auto | page-templates/recruitment.php |
| `refund_returns.html` | شرایط گارانتی، خدمات پس از فروش و بازگشت کالا \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, .mx-auto, #contact | page-templates/refund_returns.php |
| `services.html` | خدمات نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/4/1 | .relative, #services-list, .border-y, #contact | page-templates/services.php |
| `services/bridge-lighting.html` | نورپردازی پل \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #service-details, #contact | single-services.php / page template (تعیین نوع محتوا لازم است) |
| `services/building-lighting.html` | نورپردازی ساختمان \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #service-details, #contact | single-services.php / page template (تعیین نوع محتوا لازم است) |
| `services/element-lighting.html` | نورپردازی المان‌های شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #service-details, #contact | single-services.php / page template (تعیین نوع محتوا لازم است) |
| `services/square-lighting.html` | نورپردازی میدان \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #service-details, #contact | single-services.php / page template (تعیین نوع محتوا لازم است) |
| `services/street-lighting.html` | نورپردازی خیابان و بلوار \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #service-details, #contact | single-services.php / page template (تعیین نوع محتوا لازم است) |
| `shop.html` | فروشگاه و کاتالوگ محصولات نورپردازی شهری \| عرفان صنعت اصفهان (ESE) | 1/1/3/1 | .relative, #archive, #contact | archive-product.php (آرشیو ووکامرس) |

## یافته‌های ساختاری و تصمیمات ثبت‌شده

- میرور واقعی **128 صفحه HTML** دارد؛ README داخل ZIP درباره تعداد صفحات اعداد متناقض «37» و «128» ذکر می‌کند. شمارش فایل‌ها مبنای این نقشه است.
- صفحه خانه `index.html` دارای هدر، main، فوتر و 15 سکشن است؛ آرشیوها و taxonomyها از ساختار عمومی مشترک header/main/footer و سکشن‌های معرفی، نتایج و CTA بهره می‌برند.
- در هر دو مسیر `product/` (20 فایل محصول) و `products/` (28 فایل شامل محصولات/دسته‌ها) صفحات هم‌نام/مشابه وجود دارد؛ تصمیم کاربر مقایسه و ادغام تفاوت‌های یکتا است و URL canonical مطابق بلوپرینت `/product/` می‌ماند.
- `project_type/` و `city/` فایل‌های آرشیو term هستند؛ `projects/` صفحات جزئیات پروژه را در بر می‌گیرد.
- خدمات شامل صفحه فهرست `services.html` و 5 فایل جزئیات `services/` است؛ به انتخاب کاربر به‌صورت CPT مستقل مدل می‌شود، با حفظ URLهای میرور و reconcile در تسک‌های 3.1/7.3.
- CSS/JS و فونت‌ها محلی‌اند؛ ویدئوی هیرو و تصاویر باید در مسیرهای وردپرسی جدید بدون شکستن URLهای نسبی بارگذاری شوند.
- HTMLها دارای header/footer تکرارشونده و المان‌های سبد/پیش‌فاکتور در ساختارند؛ باید در برش قالب، مارک‌آپ نسخه مرجع حفظ شود.

## پاسخ‌های کاربر و مبانی نگاشت

- صفحات هم‌نام `product/` و `products/`: کاربر خواسته تفاوت‌ها مقایسه و ادغام شوند. مسیرهای واقعی باید همچنان از بلوپرینت سایت (اسلاگ محصول `/product/`) پیروی کنند؛ نگاشت per-page در زمان پیاده‌سازی با حفظ داده/markupهای یکتا تکمیل می‌شود.
- خدمات: طبق انتخاب کاربر به‌صورت CPT مستقل مدل‌سازی می‌شود؛ جزئیات rewrite و ثبت داده در تسک 3.1/7.3 با حفظ اسلاگ‌های میرور انجام می‌شود.
- Section Builder: انتخاب کاربر این است که همهٔ محتوا/رسانه/تکرارشونده‌های سکشن‌های منتخب و ترتیب/visibility قابل ویرایش باشند.
- فیلترها: انتخاب کاربر AJAX است؛ محصولات و پروژه‌ها طبق بلوپرینت فیلتر می‌شوند.
