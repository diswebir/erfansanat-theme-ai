# DATA BLUEPRINT — مشخصات فنی دیتابیس زنده erfansanat.com

> ⚠️ منبع حقیقت (Source of Truth) برای تمام کلیدهای متا، تکسونومی‌ها و Permalinkها. مو‌به‌مو رعایت شود — برخی کلیدها املای تاریخی خاص/غلط دارند (`foure-image`, `galerry_produ`, `english_titl`, `vaule_of_variable`, `usage_countr`) و باید **دقیقاً با همان املای دیتابیس فعلی** خوانده/نوشته شوند.

## ۱. محیط
- WordPress `7.1+`، PHP `8.4+`، WooCommerce `10.9+` — پول: `IRT` — RTL
- حذف می‌شوند: `Elementor`, `Elementor Pro`, `JetEngine`, `JetSearch`, `JetSmartFilters`, `JetTabs`
- باقی می‌مانند (قالب باید سازگار باشد): `WooCommerce`, `Variation Swatches for WooCommerce`, `WPC Product Quantity for WooCommerce`, `SEOPress` & `SEOPress PRO` (+ پشتیبانی متاهای قدیمی `rank_math_*`), `LiteSpeed Cache`, `WP-Parsidate`, `voorodak` (ورود پیامکی), `fl-panel` (پنل کاربری), `hreflangpch`

## ۲. پست‌تایپ‌ها و Permalinks
### 2.1. `projects` (ثبت Native توسط قالب)
- `register_post_type('projects', ...)`: `'public' => true`, `'has_archive' => true`, `'rewrite' => ['slug' => 'projects', 'with_front' => false]`, `'hierarchical' => false`, `'menu_icon' => 'dashicons-screenoptions'`, `'supports' => ['title','editor','thumbnail','comments','custom-fields','revisions']`, `'show_in_rest' => true`

### 2.2. `product` (ووکامرس)
- آرشیو اصلی: برگه با اسلاگ `shop` (ID `8574`)
- سینگل: `'rewrite' => ['slug' => '/product', 'with_front' => false]`

### 2.3. `post` / `page`
- برگه صفحه اصلی (`front_page_id`): `8146` (اسلاگ `home`)
- برگه وبلاگ (`page_for_posts`): `7426` (اسلاگ `blog`)

## ۳. تکسونومی‌ها و اسلاگ‌های حیاتی
| تکسونومی | متصل به | تنظیمات Rewrite | نکته |
|---|---|---|---|
| `project_type` (نوع پروژه) | `projects` | `'slug'=>'project_type','with_front'=>false,'hierarchical'=>false` | Hierarchical=true، ثبت Native + فیلتر دراپ‌داون در لیست ادمین |
| `city` (محل پروژه) | `projects` | `'slug'=>'city','with_front'=>false,'hierarchical'=>false` | Hierarchical=true، ثبت Native + فیلتر دراپ‌داون در لیست ادمین |
| `product_cat` | `product` | **`with_front => true`** و اسلاگ `product_cat` (نه `product-category`!) | Hierarchical. لینک زنده نمونه: `https://erfansanat.com/product_cat/strings-and-lighting-decorations/` |
| `product_tag` | `product` | اسلاگ `product-tag` با `with_front => false` | — |
| `product_brand` | `product` | سلسله‌مراتبی `hierarchical: true` | — |
| ۱۸ ویژگی `pa_*` | `product` | استاندارد ووکامرس | فهرست پایین |

### ۱۸ ویژگی فنی محصولات (`pa_*`):
`pa_after-sales-support` (خدمات پس از فروش)، `pa_beam-angle` (زاویه تابش)، `pa_body-material` (جنس بدنه)، `pa_brand` (برند)، `pa_bulb-type` (جنس حباب)، `pa_cable-material` (جنس سیم)، `pa_certifications` (گواهینامه‌ها)، `pa_color` (رنگ نور)، `pa_diameter` (قطر)، `pa_diffuser-color` (رنگ حباب)، `pa_input-voltage` (ولتاژ ورودی)، `pa_intensity` (شدت نور)، `pa_ip-rating` (درجه حفاظت IP)، `pa_led-chip-type` (نوع چیپ LED)، `pa_lifespan` (طول عمر مفید)، `pa_size` (طول/تراکم)، `pa_warranty` (گارانتی)، `pa_wattage` (توان مصرفی وات)

## ۴. کلیدهای متا — الف) متاباکس «اطلاعات محصولات» (روی `product`)
### شناسه‌های صنعتی و قانونی (text + Quick Edit):
`gs1_id` (برابر GTIN) · `national_code` (کد ملی کالا) · `hs_id` · `isic_id` · `cpc_id` · `product_id`
### عناوین و وضعیت فروش:
- `zireonvan` زیرعنوان (Text) · `english_titl` عنوان انگلیسی (Text — دقت به املای بدون e آخر!)
- `before-sale-c` اطلاعیه قبل از خرید (در پنل اطلاعات محصول ووکامرس)
- `true_sale` وضعیت خرید آنلاین: `"true"` = قابل خرید آنلاین / `"false"` = نیازمند استعلام/تماس
### فایل و گالری:
- `product-catalog-file` فایل کاتالوگ (Media، ذخیره به صورت `url`)
- `galerry_produ` گالری محصول (آیدی‌های تصویر جداشده با کاما — املای دقیق!) + Fallback: `_product_image_gallery` استاندارد ووکامرس و کلیدهای قدیمی `gallery-filed`, `image-2` … `image-6`
### روابط:
- `project-ids` پروژه‌های مرتبط (آرایه/JSON از IDهای `projects`)
- `usage_countr` کاربردها (آرایه/JSON از `term_id`های `project_type` — املای دقیق!)
### فیلدهای تکرارشونده (Repeater — فرمت آرایه/JSON سازگار با ساختار فعلی `{"item-0": {...}}`):
- `product-feature` → زیرفیلد: `add-new-feature`
- `product-feature2_copy` → زیرفیلدها: `add-new-feature` (عنوان مثل «گارانتی») + `vaule_of_variable` (مقدار مثل «۲۴ ماهه» — املای دقیق!)
- `usage_product` → زیرفیلد: `add-new-usage`
- `faq` → زیرفیلدها: `name_question` + `answer_question`

## ۵. کلیدهای متا — ب) متاباکس «اطلاعات پروژه» (روی `projects`)
- تصاویر گالری (آیدی Media): `second-image`, `three-image`, `foure-image` (املای دقیق!), `five-image`, `six-image` + فیلد گالری مدرن همگام با همین کلیدها
- `finish-pro-time` تاریخ تکمیل پروژه (Text — مثال `1404/01/01`)
- `mortabet_mahsole_2` محصولات استفاده‌شده در پروژه (آرایه/JSON از IDهای `product`)

## ۶. کلیدهای متا — ج) متاباکس «ترجمه‌های این محتوا / Hreflang» (روی `post`,`page`,`product`,`projects` + تکسونومی‌های اصلی)
`hrefmeta_en` (لینک EN) · `hrefmeta_ar` (لینک AR) · `hrefmeta_fa` (لینک FA) · `same_key_hrefl` (کلید مشترک — قابل مرتب‌سازی در ستون ادمین)

## ۷. کلیدهای متا — د) «میزان بازدید» (روی `post`,`projects`,`product`)
- `number2-viewc` تعداد بازدید (Number) + Fallback خواندن از `number-viewc`

## ۸. متاهای تکسونومی `product_cat`
- `thumbnail` تصویر بندانگشتی دسته (Media)
- `faq` فیلد تکرارشونده سوالات متداول: `name_question` + `answer_question`

## ۹. برگه‌های ثابت و لندینگ‌ها
| اسلاگ | ID | عنوان/نقش |
|---|---|---|
| `home` | 8146 | صفحه اصلی (front page) |
| `shop` | 8574 | آرشیو محصولات (ووکامرس) |
| `project` | 8366 | پروژه‌های برگزیده |
| `blog` | 7426 | وبلاگ (page_for_posts) |
| `تماس-با-ما` | 7666 | تماس با ما |
| `درباره-ما` | 7578 | درباره ما |
| `licenses-and-honors` | 7583 | مجوزها و افتخارات |
| `faq` | 10195 | سوالات متداول |
| `recruitment` | 17898 | فرصت‌های شغلی (فرم IT/حسابداری/پروژه/R&D) |
| `refund_returns` | 14922 | شرایط گارانتی و خدمات پس از فروش |
| `arya` | 16947 | لندینگ درب بازکن هوشمند «آریا» |
| `smartelement` | 11984 | خدمات شرکت عرفان صنعت |
| `cart` / `checkout` / `my-account` / `auth` | — | برگه‌های ووکامرس / ورود پیامکی |

## ۱۰. موقعیت‌های منو (Menu Locations)
1. `main-mega-menu` — مگامنوی اصلی دسکتاپ (محصولات، پروژه‌ها بر اساس نوع/شهر، مجوزها، FAQ، وبلاگ، تماس، درباره ما)
2. `mobile-menu` — منوی موبایل چندسطحی (معادل «فهرست موبایل»)
3. `tolidat-sherkat` — منوی تولیدات شرکت (فوتر/هدر)
4. `landing` — منوی اسکرول صفحات لندینگ (مثل آریا)

## ۱۱. نتایج تکمیلی از اسکن میرور استاتیک (Stage 0)
- میرور شامل ۱۲۸ HTML است؛ واریانت‌های تکراری با URLهای متفاوت وجود دارد (تحلیل کامل در `STATIC_TEMPLATE_MAP.md`):
  - سینگل محصول: `product/*.html` (canonical، سازگار با rewrite `/product`) + واریانت تکراری `products/*.html`
  - آرشیو محصول: `shop.html` (canonical سازگار با برگه shop) + واریانت `products.html`
  - آرشیو دسته محصول: `product_cat/*.html` (canonical) + واریانت `products/category-*.html`
  - آرشیو شهر: `city/*.html` (canonical) + واریانت `projects/city-*.html`
  - آرشیو نوع پروژه: `project_type/*.html` (canonical) + واریانت `projects/type-*.html`
- دو فایل `category/magazine.html` و `category/news-and-events.html` در `<title>` مقدار `undefined` دارند (باگ ژنراتور میرور) اما H1 درست دارند: «مجله علمی نورپردازی شهری» و (news) دسته اخبار و رویدادها.
- قیمت‌ها در میرور با واحد «تومان» نمایش داده می‌شوند (IRT ✓).
- سینگل محصول میرور شامل: هیرو، تب‌های مشخصات (`prod-tab-*`)، گالری لایت‌باکس (`gallery-*`)، FAQ آکاردئونی (`faq-*`)، فرم دیدگاه (`review-*`)، دکمه سبد (`pdp-*`)، محصولات و پروژه‌های مرتبط، کاتالوگ و وضعیت استعلام (`true_sale`).
- سینگل مقاله میرور: share دکمه دارد؛ TOC/بازدید/دیدگاه در میرور رندر نشده (باید بر اساس بلوپرینت و سبک کامپوننت‌های موجود پیاده شود — در تسک 7.2).
- هدر/فوتر در هر ۱۲۸ صفحه یکسان هستند (هدر ۶۷KB شامل مگامنو تب‌دار محصولات/پروژه‌ها + سایدبار موبایل + مودال جستجو؛ فوتر ۷KB با سه ستون: دسترسی سریع، دسته‌بندی محصولات، اطلاعات تماس کارخانه + دکمه scroll-top).
- `assets/css/styles.css` = ۱۸۶KB کامپایل‌شده (Tailwind-like utility classes، ۲ خط، minified) با `@font-face` سه‌گانه Vazirmatn و مسیرهای نسبی `../fonts/`.
- `assets/js/main.js` = ۱۰۶KB Vanilla با **۲۶۳ data-hook** (مگامنو، سایدبار موبایل، مودال جستجوی زنده، فیلتر/جستجو/مرتب‌سازی/صفحه‌بندی سمت کلاینت آرشیوها، سبد خرید localStorage، لایت‌باکس گالری، اسلایدر هیرو، تایم‌لاین تاریخچه، شمارنده‌ها، آکاردئون، تب‌ها، فرم‌ها).
- تصاویر: `images/` (۱۰ تصویر محلی + `hero-video.mp4`) و `images/pexels/` (۱۱ تصویر).
- JSON-LD Organization در `index.html` اینلاین است (Schema.org) — در قالب باید داینامیک تولید شود.
