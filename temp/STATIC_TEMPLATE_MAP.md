# STATIC TEMPLATE MAP — گزارش جامع اسکن فایل‌های استاتیک و جدول نگاشت به وردپرس

این سند حاصل اسکن خودکار و کالبدشکافی دقیق تمام **۱۵۶ فایل** موجود در پوشه `static-mirror/` است و مرجع مستقیم برش قالب در فازهای ۱ تا ۸ می‌باشد.

---

## ۱. خلاصه آماری دارایی‌های اسکن‌شده در `static-mirror/`

| نوع فایل | تعداد | مسیرها و جزئیات فنی |
|---|---:|---|
| **فایل‌های HTML** | **۱۲۸** | ۱۴ فایل در ریشه + ۵ فایل در `services/` + ۲۰ فایل در `product/` + ۲۷ فایل در `products/` + ۱۵ فایل در `product_cat/` + ۲۰ فایل در `projects/` + ۸ فایل در `project_type/` + ۱۰ فایل در `city/` + ۷ فایل در `blog/` + ۲ فایل در `category/` |
| **فایل CSS** | **۱** | `static-mirror/assets/css/styles.css` (`190,867 bytes` — شامل استایل‌های کامپایل‌شده Tailwind، قوانین سفارشی و `@font-face` محلی؛ **صفر تگ `<style>` خطی** در کل ۱۲۸ فایل HTML) |
| **فایل JavaScript** | **۱** | `static-mirror/assets/js/main.js` (`109,472 bytes` / `2,814 lines` — شامل ۱۶ ماژول تعاملی Vanilla JS بدون هیچ وابستگی خارجی؛ تنها تگ `<script>` داخل صفحات HTML از نوع `application/ld+json` برای اسکیما است) |
| **فونت‌های محلی** | **۳** | `assets/fonts/vazirmatn-arabic.woff2`, `vazirmatn-latin.woff2`, `vazirmatn-latin-ext.woff2` (فراخوانی‌شده با مسیر نسبی `../fonts/...` از درون `styles.css`) |
| **تصاویر و ویدیو** | **۲۲** | ۱۱ فایل در `images/` (`ese-logo.svg`, `hero-video.mp4`, `hero.jpg`, `factory.jpg`, `chandelier.jpg`, `light-element.jpg`, `light-sphere.jpg`, `light-tree.jpg`, `light-tunnel.jpg`, `string-lights.jpg`, `sun-light.jpg`) + ۱۱ تصویر در `images/pexels/pexels-*.jpg` |
| **مستندات** | **۱** | `static-mirror/README.md` |
| **جمع کل** | **۱۵۶** | حجم کل استخراج‌شده: `38.16 MB` |

---

## ۲. کالبدشکافی پوسته سراسری (Global Shell Anatomy: Header, Drawers & Footer)

در تمام ۱۲۸ فایل HTML، ساختار ریشه داخل `<body>` کاملاً یکسان است و به این صورت به `header.php` و `footer.php` برش می‌خورد:

1. **لایه پس‌زمینه محیطی (در `header.php` بعد از `wp_body_open()`):**
   - `<div id="root"><div id="home" class="relative min-h-screen overflow-x-clip ...">`
   - `<div class="pointer-events-none fixed inset-0 z-0 ...">` (گرادیان و بافت نوری پس‌زمینه)
2. **هدر ثابت دسکتاپ و موبایل (`<header data-navbar-header class="fixed inset-x-0 top-0 z-50 ...">`):**
   - لوگو (`images/ese-logo.svg`) + برندینگ «عرفان صنعت اصفهان».
   - مگامنوی دسکتاپ (`main-mega-menu`) با اتریبیوت‌های تعاملی:
     - `data-desktop-mega-trigger` / `data-desktop-mega-panel`
     - پنل محصولات تب‌دار: `data-mega-menu-products`, `data-mega-prod-tab`, `data-mega-prod-panel`
     - پنل پروژه‌ها تب‌دار (بر اساس نوع پروژه و شهر): `data-mega-menu-projects`, `data-mega-proj-tab`, `data-mega-proj-panel`
     - پنل شرکتی (درباره ما، خدمات، مجوزها، استخدام، سوالات متداول، شرایط گارانتی): `data-mega-menu-corporate`
   - دکمه باز کردن مودال جستجوی سراسری (`[data-search-modal-open]`) و خود مودال جستجو (`[data-search-modal]`, `[data-global-search-input]`, `[data-global-search-products]`, `[data-global-search-projects]`).
   - دکمه و نشانگر سبد خرید (`[data-cart-modal-open]`, `[data-header-cart-badge]`) و دکمه تماس سریع/مشاوره + همبرگر موبایل (`[data-mobile-toggle]`).
3. **دراور منوی موبایل (`<div data-mobile-menu class="fixed inset-0 z-[90] lg:hidden ...">`):**
   - متصل به موقعیت منوی `mobile-menu` با آکاردئون‌ها و تب‌های `[data-mobile-acc-btn]`, `[data-mobile-prod-tab]`, `[data-mobile-proj-tab]`.
4. **مودال سبد خرید / پیش‌فاکتور سریع (`<div data-cart-modal class="fixed inset-0 z-[95] ...">`):**
   - شامل لیست آیتم‌ها (`[data-cart-modal-items-list]`), کنترل تعداد (`inc`/`dec`/`remove`), جمع کل (`[data-cart-modal-total]`) و لینک به سبد خرید و تسویه‌حساب.
5. **محتوای اصلی صفحه (`<main>`):**
   - محل قرارگیری سکشن‌های اختصاصی هر قالب.
6. **فوتر سراسری (`<footer>` در `footer.php`):**
   - معرفی کوتاه کارخانه دانش‌بنیان + شبکه‌های اجتماعی + ۳ ستون («دسترسی سریع»، «دسته‌بندی محصولات» متصل به `tolidat-sherkat`، «اطلاعات تماس کارخانه») + نوار کپی‌رایت و نمادها + دکمه بازگشت به بالا (`<button data-scroll-top>`).

---

## ۳. ماژول‌های ۱۶گانه جاوااسکریپت در `assets/js/main.js` (قراردادهای `data-*`)

1. `initNavbar()` — تغییر استایل هدر هنگام اسکرول (`[data-navbar-header]`) و باز/بستن دراور موبایل (`[data-mobile-menu]`).
2. `initMegaMenuAndSearch()` — مگامنوی تب‌دار دسکتاپ + مودال جستجوی زنده سراسری (`[data-search-modal]`).
3. `initHeroVideo()` & `initHomeHeroVariants()` — کنترل ۵ صحنه هیرو صفحه اصلی (`[data-home1-scene-btn]`), پخش ویدیوی درون‌خطی (`[data-home1-inline-video]`) و کنترل صدا/بستن.
4. `initScrollTop()` — دکمه شناور اسکرول به بالا (`[data-scroll-top]`).
5. `initCounters()` — انیمیشن شمارنده‌های آماری هنگام ورود به دید کاربر.
6. `initHomeServices()` — تب‌های تعاملی سکشن خدمات در صفحه اصلی.
7. `initGalleries()` — اسلایدر گالری تصاویر و لایت‌باکس تمام‌صفحه در تک‌محصول و تک‌پروژه (`[data-gallery-root]`, `[data-gallery-lightbox]`).
8. `initArchiveDrawer()` — دراور فیلتر موبایل و صفحه‌بندی پویا در آرشیوها.
9. `initProjectsArchive()` — فیلتر پروژه‌ها بر اساس نوع، شهر و جستجو.
10. `initProductsArchive()` — فیلتر هوشمند محصولات (جستجو، دسته‌بندی، وضعیت خرید آنلاین/استعلامی، ویژگی‌ها و مرتب‌سازی).
11. `initProductDetailAndFaqs()` — تب‌های صفحه تک‌محصول (`[data-prod-tab-btn]`), انتخابگر متغیرها/سوآچ‌ها (`[data-opt-group]`), آکاردئون FAQ (`[data-faq-toggle]`) و فرم ثبت دیدگاه و امتیاز ستاره‌ای (`[data-review-form]`).
12. `initPurchaseJourney()` — مدیریت سبد خرید، مینی‌کارت، تغییر تعداد با رعایت حداقل خرید و گام افزایشی (`data-product-min`, `data-product-step`) و تسویه‌حساب.
13. `initBlogArchive()` — فیلتر دسته‌بندی و جستجوی مقالات وبلاگ.
14. `initRecruitmentTabs()` — تب‌های ۴ واحد سازمانی در صفحه استخدام (`[data-recruit-tab-btn]`).
15. `initShareButtons()` — کپی لینک و اشتراک‌گذاری در مقالات و صفحات جزئیات (`[data-share-btn]`).
16. `initContactForm()` — اعتبارسنجی و ارسال فرم‌های مشاوره، تماس و استخدام (`[data-contact-form]`).

---

## ۴. جدول نگاشت کلان الگوهای استاتیک به سلسله‌مراتب وردپرس (Master Template Mapping)

| ردیف | الگوی استاتیک مرجع در `static-mirror/` | تعداد فایل HTML | فایل هدف در قالب وردپرس | اسلاگ / شرط وردپرس | توضیحات معماری |
|---:|---|---:|---|---|---|
| ۱ | `index.html` | ۱ | `front-page.php` + `template-parts/home/section-01..15.php` | `is_front_page()` (برگه `home` - ID `8146`) | ۱۵ سکشن ماژولار متصل به `SectionManager` با قابلیت Drag & Drop و فعال/غیرفعال‌سازی |
| ۲ | `shop.html` (+ نام مستعار `products.html`) | ۲ | `woocommerce/archive-product.php` | `is_shop()` (برگه `shop` - ID `8574`) | هیرو فروشگاه + سایدبار فیلتر هوشمند ایجکس + گرید محصولات + راهنمای خرید و FAQ فروشگاه + سکشن تماس |
| ۳ | `product_cat/*.html` (+ ۷ نام مستعار `products/category-*.html`) | ۲۲ | `woocommerce/taxonomy-product_cat.php` | `is_product_category()` (`/product_cat/<slug>/`) | اشتراک ساختار با آرشیو فروشگاه + هدر اختصاصی دسته + توضیحات سئو و آکاردئون `faq` متای ترم `product_cat` |
| ۴ | `product/*.html` (+ ۲۰ نام مستعار `products/*.html`) | ۴۰ | `woocommerce/single-product.php` | `is_product()` (`/product/<slug>/`) | پشتیبانی از ۳ حالت (ساده آنلاین، متغیر آنلاین با Swatch، و استعلامی `true_sale=false`) + گالری + شناسه‌های GS1/ایران‌کد/HS/ISIC/CPC + تب‌ها + پروژه‌های مرتبط + FAQ + محصولات مشابه |
| ۵ | `cart.html` | ۱ | `woocommerce/cart/cart.php` + `page-templates/template-cart.php` | `is_cart()` (`/cart/`) | پوسته استاتیک سبد خرید و پیش‌فاکتور متصل به آیتم‌ها و محاسبات واقعی `WC()->cart` |
| ۶ | `checkout.html` | ۱ | `woocommerce/checkout/form-checkout.php` + `page-templates/template-checkout.php` | `is_checkout()` (`/checkout/`) | پوسته استاتیک تسویه‌حساب متصل به هوک‌های استاندارد پرداخت ووکامرس و درگاه بانکی |
| ۷ | `projects.html` | ۱ | `archive-projects.php` (+ قالب برگه `project` - ID `8366`) | `is_post_type_archive('projects')` (`/projects/`) | هیرو پروژه‌ها + فیلتر ایجکس نوع پروژه و شهر + گرید کارت پروژه‌ها + سکشن تماس |
| ۸ | `project_type/*.html` (+ ۴ نام مستعار `projects/type-*.html`) | ۱۲ | `taxonomy-project_type.php` | `is_tax('project_type')` (`/project_type/<slug>/`) | آرشیو پروژه‌های هر نوع سازه نوری + راهنمای تخصصی همان دسته در پایین صفحه |
| ۹ | `city/*.html` (+ ۴ نام مستعار `projects/city-*.html`) | ۱۴ | `taxonomy-city.php` | `is_tax('city')` (`/city/<slug>/`) | آرشیو پروژه‌های هر شهر/کشور + کارنامه اجرایی همان شهر در پایین صفحه |
| ۱۰ | `projects/<single>.html` | ۱۲ | `single-projects.php` | `is_singular('projects')` (`/projects/<slug>/`) | گالری و لایت‌باکس (`second-image` تا `six-image`) + شناسنامه پروژه (`finish-pro-time`, `city`, `project_type`) + محتوا + محصولات استفاده‌شده (`mortabet_mahsole_2`) + پروژه‌های مشابه |
| ۱۱ | `blog.html` | ۱ | `home.php` | `is_home()` (برگه `blog` - ID `7426`) | هیرو و مقالات ویژه + فیلتر دسته‌بندی و جستجوی مقالات + گرید کارت نوشته‌ها |
| ۱۲ | `category/*.html` | ۲ | `category.php` / `archive.php` | `is_category()` / `is_archive()` | هدر دسته‌بندی وبلاگ + لیست مقالات همان دسته + صفحه‌بندی |
| ۱۳ | `blog/*.html` | ۷ | `single.php` | `is_singular('post')` | بردکرامب و متادیتا (زمان مطالعه، تاریخ شمسی، بازدید `number2-viewc`) + سایدبار چسبان فهرست مطالب (TOC) و باکس مشاوره + محتوا و اشتراک‌گذاری + دیدگاه‌ها + مقالات مرتبط |
| ۱۴ | `about.html` | ۱ | `page-templates/template-about.php` | برگه `درباره-ما` (ID `7578`) | ۵ سکشن قابل ویرایش در `SectionManager` (هیرو، داستان ۲۵ سال تجربه، گواهی ISO، اصول بنیادین، تماس) |
| ۱۵ | `contact.html` | ۱ | `page-templates/template-contact.php` | برگه `تماس-با-ما` (ID `7666`) | ۲ سکشن شامل کارت‌های اطلاعات تماس کارخانه/دفتر، نقشه و فرم ایجکس مشاوره رایگان |
| ۱۶ | `faq.html` | ۱ | `page-templates/template-faq.php` | برگه `faq` (ID `10195`) | ۳ سکشن شامل دسته‌بندی پرسش‌ها و آکاردئون‌های قابل مدیریت در ادمین + اسکیمای `FAQPage` |
| ۱۷ | `licenses-and-honors.html` | ۱ | `page-templates/template-licenses.php` | برگه `licenses-and-honors` (ID `7583`) | ۳ سکشن شامل گرید مجوز دانش‌بنیان، گواهینامه‌های ISO و تقدیرنامه‌ها با لایت‌باکس |
| ۱۸ | `recruitment.html` | ۱ | `page-templates/template-recruitment.php` | برگه `recruitment` (ID `17898`) | ۲ سکشن شامل تب‌های ۴ واحد (R&D، پروژه، مالی، IT) + فرم استخدام و آپلود/ثبت رزومه در داشبورد |
| ۱۹ | `refund_returns.html` | ۱ | `page-templates/template-warranty.php` | برگه `refund_returns` (ID `14922`) | ۳ سکشن شامل شرایط ۲۴ ماه گارانتی تعویض، فرآیند خدمات پس از فروش و تماس |
| ۲۰ | `services.html` | ۱ | `page-templates/template-services.php` | برگه `smartelement` (ID `11984`) | ۴ سکشن شامل هیرو، لیست ۵ خدمت تخصصی، مسیر همکاری از ایده تا اجرا و سکشن تماس |
| ۲۱ | `services/*.html` | ۵ | `page-templates/template-service-single.php` | زیربرگه‌های خدمات (یا CPT خدمات) | ۳ سکشن شامل هیرو خدمت، اقلام اجرایی/پروژه‌ها و محصولات مرتبط با آن خدمت و سکشن تماس |

---

## ۵. کالبدشکافی سکشن‌به‌سکشن صفحات کلیدی

### الف) صفحه اصلی (`index.html` -> `front-page.php`) — ۱۵ سکشن مستقل
1. **سکشن ۱ (`id="home"`):** هیرو تعاملی ۵ صحنه‌ای با تغییر تصویر/عنوان/لینک (`data-home1-scene-btn="0..4"`) + پخش ویدیوی کارخانه/پروژه‌ها (`images/hero-video.mp4`) + نوار گرید دسته‌بندی‌های سریع محصولات (`data-home1-categories-grid`).
2. **سکشن ۲ (`id="projects"`):** سرفصل و معرفی پروژه‌های نورپردازی شهری به همراه دسته‌بندی‌های سریع پروژه‌ها.
3. **سکشن ۳:** ویترین کارت‌های نمونه‌کارها و پروژه‌های شاخص اجراشده در کلان‌شهرها (`WP_Query` از `projects`).
4. **سکشن ۴ (`id="about"`):** معرفی مهندسی «بیش از ۲۵ سال تجربه در تولید سازه‌های نوری شهری» + شمارنده‌های آماری (`initCounters`).
5. **سکشن ۵:** بنر/بلوک «مرجع بومی‌سازی سازه‌های نوری و تجهیزات روشنایی شهری در ایران».
6. **سکشن ۶:** تایم‌لاین تاریخچه و دستاوردهای عرفان صنعت اصفهان (۱۳۸۰ تا ۱۴۰۵).
7. **سکشن ۷ (`id="products"`):** ویترین «منتخب محصولات عرفان صنعت اصفهان» (`WP_Query` از `product` با کارت استاندارد محصول).
8. **سکشن ۸:** بنر دانلود «کاتالوگ جامع محصولات عرفان صنعت اصفهان».
9. **سکشن ۹ (`id="services"`):** سکشن تعاملی تب‌دار «خدمات تخصصی نورپردازی شهری» (`initHomeServices` — میدان، خیابان، پل، المان، نما).
10. **سکشن ۱۰:** سکشن «استانداردهای کیفیت و ماندگاری» (IP68، تست دمایی، بدنه مقاوم، دانش‌بنیان).
11. **سکشن ۱۱:** بنر «پشتیبانی فنی و گارانتی تعویض در سراسر ایران».
12. **سکشن ۱۲:** لوگولایست / معرفی «مشتریان و سازمان‌های همکار عرفان صنعت» (شهرداری‌های کلان‌شهرها).
13. **سکشن ۱۳ (`id="blog-preview"`):** «جدیدترین مقالات و راهنماهای تخصصی نورپردازی» (`WP_Query` از `post`).
14. **سکشن ۱۴ (`id="contact"`):** بنر فراخوان اقدام (CTA) مشاوره و استعلام قیمت پروژه.
15. **سکشن ۱۵:** کارت نشانی کارخانه، دفتر مرکزی اصفهان، شماره‌های تماس و مسیریابی.

### ب) صفحه تک‌محصول (`product/*.html` -> `woocommerce/single-product.php`) — ۶ سکشن
1. **سکشن ۱:** نوار Breadcrumb + نشان دسته‌بندی اصلی + عنوان `<h1>` محصول + زیرعنوان (`zireonvan`).
2. **سکشن ۲:** گرید دو ستونه بالای صفحه:
   - ستون راست: گالری تصاویر محصول (`galerry_produ` + تصویر شاخص + لایت‌باکس `[data-gallery-root]`) + ۴ بج اعتماد (۲۴ ماه گارانتی، ۵ سال خدمات، ارسال سراسری، فاکتور رسمی).
   - ستون چپ: وضعیت موجودی/فروش (`true_sale`)، امتیاز، کد کالا (`SKU` / `product_id`)، قیمت و حداقل سفارش (در حالت `true_sale == 'true'`) یا باکس استعلام قیمت کارخانه (در حالت `true_sale == 'false'`)، چکیده ویژگی‌های کلیدی دو بخشی (`product-feature2_copy`)، انتخابگر متغیرها (`pa_*` swatches در محصولات متغیر)، و دکمه افزودن به سبد خرید یا درخواست پیش‌فاکتور رسمی.
3. **سکشن ۳ (`id="product-details"`):** باکس تب‌بندی‌شده (`[data-prod-tabs-container]`) شامل ۳ تب:
   - تب ۱ («معرفی و کاربردها»): محتوای کامل محصول (`the_content`) + لیست مشخصات (`product-feature`) + کارت‌های کاربردها (`usage_product` و ترم‌های `usage_countr`).
   - تب ۲ («مشخصات فنی و شناسه‌ها»): جدول ۱۸ ویژگی فنی (`pa_*`) + جدول شناسه‌های رسمی سامانه ستاد و ایران‌کد (`gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`).
   - تب ۳ («نظرات کاربران»): لیست دیدگاه‌های تاییدشده محصول + فرم ثبت نظر و امتیاز ۵ ستاره.
4. **سکشن ۴ (مشروط به پر بودن `project-ids`):** اسلایدر/گرید «پروژه‌های اجراشده با این محصول» (در ۴ فایل استاتیک که پروژه مرتبط ندارند، این سکشن به‌درستی حذف شده است).
5. **سکشن ۵ (مشروط به پر بودن `faq`):** آکاردئون «سوالات متداول درباره محصول».
6. **سکشن ۶:** «محصولات مشابه و مکمل».

### ج) صفحه تک‌پروژه (`projects/<slug>.html` -> `single-projects.php`) — ۵ سکشن
1. **سکشن ۱:** Breadcrumb + بج نوع پروژه (`project_type`) و شهر (`city`) + عنوان `<h1>` پروژه + خلاصه.
2. **سکشن ۲:** گالری تصاویر پروژه (تصویر شاخص + `second-image`, `three-image`, `foure-image`, `five-image`, `six-image` با لایت‌باکس) + کارت شناسنامه پروژه (سال تکمیل `finish-pro-time`، محل اجرا `city`، نوع پروژه `project_type`، دکمه‌های مشاوره پروژه مشابه).
3. **سکشن ۳ (`id="project-details"`):** شرح فنی و داستانی اجرای پروژه (`the_content`).
4. **سکشن ۴ (`id="used-products"` - مشروط به `mortabet_mahsole_2`):** کارت‌های «محصولات و تجهیزات استفاده‌شده در این پروژه».
5. **سکشن ۵:** «سایر پروژه‌های شاخص عرفان صنعت».

---

## ۶. فهرست کامل ۱۲۸ فایل HTML موجود در `static-mirror/`

| # | مسیر فایل در `static-mirror/` | تعداد سکشن | نوع مسیر | قالب وردپرس متناظر |
|---:|---|---:|---|---|
| 1 | `index.html` | 15 | اصلی | `front-page.php` |
| 2 | `shop.html` | 3 | اصلی | `woocommerce/archive-product.php` |
| 3 | `products.html` | 3 | نام مستعار `shop.html` | `woocommerce/archive-product.php` |
| 4 | `cart.html` | 2 | اصلی | `woocommerce/cart/cart.php` |
| 5 | `checkout.html` | 2 | اصلی | `woocommerce/checkout/form-checkout.php` |
| 6 | `projects.html` | 3 | اصلی | `archive-projects.php` |
| 7 | `blog.html` | 2 | اصلی | `home.php` |
| 8 | `about.html` | 5 | اصلی | `page-templates/template-about.php` |
| 9 | `contact.html` | 2 | اصلی | `page-templates/template-contact.php` |
| 10 | `faq.html` | 3 | اصلی | `page-templates/template-faq.php` |
| 11 | `licenses-and-honors.html` | 3 | اصلی | `page-templates/template-licenses.php` |
| 12 | `recruitment.html` | 2 | اصلی | `page-templates/template-recruitment.php` |
| 13 | `refund_returns.html` | 3 | اصلی | `page-templates/template-warranty.php` |
| 14 | `services.html` | 4 | اصلی | `page-templates/template-services.php` |
| 15–19 | `services/{bridge,building,element,square,street}-lighting.html` (۵ فایل) | 3 | اصلی | `page-templates/template-service-single.php` |
| 20–39 | `product/{artam-light-tree,baloti-string,delsa-light-tree,fandoghi-string,finger-string-lights,foroogh-chandelier,illumina,industrial-led-bulb,lighting-ball,linear-light-facade,needle-string-lights,pixel-point-string,point-light-pixel-6cm,point-light-pixel,shamse-chandelier,smart-control-box,star-lamp-string,sun-light-32,sun-light-48,wall-washer}.html` (۲۰ فایل) | 5–6 | اصلی (`/product/<slug>/`) | `woocommerce/single-product.php` |
| 40–59 | `products/{همان ۲۰ اسلاگ محصول بالا}.html` (۲۰ فایل) | 5–6 | نام مستعار میرور | `woocommerce/single-product.php` |
| 60–74 | `product_cat/{acorn-thread,control-box,finger-thread,hazelnut-thread,led-lamp,light-chandelier,light-tree,needle-thread,point-light,rise-point-lite-pixel,star-lamp-thread,strings-and-lighting-decorations,sun-light-elements,urban-lighting-elements,wall-washer}.html` (۱۵ فایل) | 3 | اصلی (`/product_cat/<slug>/`) | `woocommerce/taxonomy-product_cat.php` |
| 75–81 | `products/category-{led-string-lights,led-wall-washer,point-light-pixel,smart-light-trees,urban-chandeliers,urban-light-elements,urban-sunbursts}.html` (۷ فایل) | 3 | نام مستعار میرور | `woocommerce/taxonomy-product_cat.php` |
| 82–93 | `projects/{ahvaz-semi-pyramid-light,artam-tree-midan,birjand-smart-light-trees,bridge-fullcolor-qom,esfahan-shohada-square,half-arch-tunnel-alborz,heart-tunnel-khorsand,iraq-karbala-urban-lighting,kerman-arch-light-tunnel,mashhad-pilgrim-boulevard-lighting,tehran-commercial-facade-lighting,tunnel-90-alborz}.html` (۱۲ فایل) | 5 | اصلی (`/projects/<slug>/`) | `single-projects.php` |
| 94–101 | `project_type/{ball-of-light,bridge-lighting,light-tree,light-tunnel,lighting-building,luminous-elements,square-lighting,urban-lighting}.html` (۸ فایل) | 3 | اصلی (`/project_type/<slug>/`) | `taxonomy-project_type.php` |
| 102–105 | `projects/type-{bridge-lighting,light-element,light-tunnel,square-lighting}.html` (۴ فایل) | 3 | نام مستعار میرور | `taxonomy-project_type.php` |
| 106–115 | `city/{ahvaz,alborz,birjand,esfahan,iraq,kerman,khorsand,mashhad,qom,tehran}.html` (۱۰ فایل) | 3 | اصلی (`/city/<slug>/`) | `taxonomy-city.php` |
| 116–119 | `projects/city-{alborz,esfahan,khorsand,qom}.html` (۴ فایل) | 3 | نام مستعار میرور | `taxonomy-city.php` |
| 120–126 | `blog/{baloti-seven-color-string,frame-holder-plt,led-wall-washer-guide,point-light-pixel-led,sun-display-32-vs-48,types-of-light-tunnel,urban-lighting-elements}.html` (۷ فایل) | 3 | اصلی | `single.php` |
| 127–128 | `category/{magazine,news-and-events}.html` (۲ فایل) | 2 | اصلی | `category.php` / `archive.php` |

---

## ۷. تصمیمات نهایی کارفرما برای شکاف‌ها و قوانین سئو (تاییدشده در پایان Stage 0)

1. **لندینگ «درب بازکن هوشمند آریا» (`arya` - ID `16947`) و منوی `landing`:** با استفاده ۱۰۰٪ از کلاس‌ها و کامپوننت‌های موجود در `styles.css` به صورت قالب لندینگ ماژولار (`page-templates/template-landing-arya.php`) متصل به `SectionManager` و موقعیت منوی `landing` پیاده‌سازی می‌شود.
2. **صفحات `my-account`، `auth`، `search.php`، `404.php`، `taxonomy-product_tag.php`، `taxonomy-product_brand.php` و فرم `client-feedback`:** با ۱۰۰٪ کلاس‌ها و کارت‌های موجود در `styles.css` پیاده‌سازی می‌شوند؛ برگه‌های `my-account` و `auth` خروجی/شورت‌کد افزونه‌های فعال `fl-panel` و `voorodak` را درون پوسته استاندارد هم‌خانواده با `cart.html` رندر می‌کنند.
3. **مدل داده‌ای زیرصفحات خدمات (`services/*.html` و `smartelement` - ID `11984`):** برگه مادر خدمات (`smartelement`) از `page-templates/template-services.php` و ۵ خدمت تخصصی از قالب برگه فرزند `page-templates/template-service-single.php` (مجهز به `SectionManager`) استفاده می‌کنند (بدون نیاز به ثبت CPT اضافه).
4. **اتصال سبد خرید و تسویه‌حساب به ووکامرس:** حفظ ۱۰۰٪ DOM و کلاس‌های `[data-cart-modal]`، `cart.html` و `checkout.html` و اتصال کامل عملیات پشت‌صحنه به `WC()->cart`، درگاه‌های پرداخت فعال ووکامرس، `WPC Product Quantity` و `Variation Swatches`.
5. **نمایش مشروط متاهای دیتابیس زنده:** فیلدهای `english_titl`، `before-sale-c` و `product-catalog-file` در `single-product.php` و شمارنده بازدید `number2-viewc` + دیدگاه‌ها در `single.php` به صورت مشروط (فقط در صورت پر بودن) با کامپوننت‌های بصری `styles.css` نمایش داده می‌شوند.
6. **ریدایرکت سئوی برگه `project` (ID `8366`):** برگه با اسلاگ `project` (و آیدی `8366`) با وضعیت `301 Permanent Redirect` به آرشیو اصلی `/projects/` هدایت می‌شود تا از محتوای تکراری جلوگیری شود.
7. **قانون حیاتی فیلترهای ایجکس (بدون تغییر URL + قابل انتخاب در داشبورد ادمین):**
   - اعمال فیلترها **به هیچ عنوان نباید URL مرورگر را تغییر دهد** (ممنوعیت `pushState` و Query Stringهای فیلتر) تا از ایندکس شدن صفحات فیلتر در گوگل، خزش ربات‌ها و پر شدن کش LiteSpeed و فضای سرور جلوگیری شود.
   - تمام فیلترها (جستجو، دسته‌بندی، بازه قیمت، وضعیت خرید آنلاین `true_sale` و **هر ۱۸ ویژگی فنی `pa_*`** + فیلترهای پروژه‌ها) پیاده‌سازی می‌شوند و در داشبورد اختصاصی ادمین (`ThemeOptions`) امکان **فعال/غیرفعال کردن تک‌تک فیلترها** در اختیار مدیر سایت قرار می‌گیرد.
