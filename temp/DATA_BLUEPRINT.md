# DATA BLUEPRINT — مشخصات فنی و ساختار دیتابیس سایت زنده «عرفان صنعت اصفهان»

این سند مرجع رسمی ساختار داده‌های سایت زنده `https://erfansanat.com` است. تمامی CPTها، تکسونومی‌ها، قوانین Permalink و کلیدهای متا در قالب جدید باید **مو‌به‌مو و با املای دقیق ثبت‌شده در این سند** پیاده‌سازی شوند تا پس از غیرفعال‌سازی `Elementor` و `JetEngine` هیچ داده‌ای از دست نرود و هیچ لینک ایندکس‌شده‌ای با خطای `404` مواجه نشود.

---

## ۱. محیط سیستم و ماتریس افزونه‌ها

### الف) مشخصات پایه محیط
- **WordPress:** `7.1+`
- **PHP:** `8.4+`
- **WooCommerce:** `10.9+`
- **واحد پول:** `IRT` (تومان)
- **جهت زبان:** راست‌چین (`is_rtl: true`, `fa_IR`)

### ب) افزونه‌های حذفی (وظایف منتقل‌شده به هسته قالب)
- `Elementor` & `Elementor Pro` -> جایگزین با قالب‌های PHP اختصاصی + `ThemeOptions` + `SectionManager`
- `JetEngine` -> جایگزین با ثبت Native پست‌تایپ `projects`، تکسونومی‌ها و موتور متاباکس‌های اختصاصی سازگار با ساختار دیتای JetEngine
- `JetSearch` -> جایگزین با موتور جستجوی زنده ایجکس قالب (`Native Live AJAX Search`)
- `JetSmartFilters` -> جایگزین با موتور فیلتر هوشمند ایجکس محصولات و پروژه‌ها (`Native Smart Filters`)
- `JetTabs` -> جایگزین با سیستم تب‌ها و آکاردئون‌های بومی Vanilla JS قالب استاتیک

### ج) افزونه‌های باقی‌مانده (الزام سازگاری ۱۰۰٪)
- `WooCommerce`
- `Variation Swatches for WooCommerce`
- `WPC Product Quantity for WooCommerce`
- `SEOPress` & `SEOPress PRO` (+ پشتیبانی Fallback از متاهای قدیمی `rank_math_*`)
- `LiteSpeed Cache`
- `WP-Parsidate`
- `voorodak` (سامانه ورود و ثبت‌نام پیامکی)
- `fl-panel` (پنل کاربری اختصاصی)
- `hreflangpch`

---

## ۲. پست‌تایپ‌ها (CPTs) و قوانین Permalink

| عنوان | نام فنی (`post_type`) | منبع ثبت | تنظیمات دقیق Rewrite و پشتیبانی |
|---|---|---|---|
| **پروژه‌ها** | `projects` | ثبت Native در قالب (`register_post_type`) | `'public' => true`, `'has_archive' => true`, `'rewrite' => ['slug' => 'projects', 'with_front' => false]`, `'hierarchical' => false`, `'menu_icon' => 'dashicons-screenoptions'`, `'supports' => ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions']`, `'show_in_rest' => true` |
| **محصولات** | `product` | ووکامرس | آرشیو اصلی: اسلاگ `shop` (برگه آیدی `8574`) \| تک‌محصول: `'rewrite' => ['slug' => '/product', 'with_front' => false]` |
| **نوشته‌ها** | `post` | هسته وردپرس | برگه وبلاگ (`page_for_posts`): آیدی `7426` (اسلاگ `blog`) |
| **برگه‌ها** | `page` | هسته وردپرس | برگه نخست (`page_on_front`): آیدی `8146` (اسلاگ `home`) |

---

## ۳. تکسونومی‌ها (Taxonomies) و ویژگی‌های فنی

### الف) تکسونومی‌های پروژه‌ها و محصولات

| عنوان | نام فنی (`taxonomy`) | متصل به | تنظیمات دقیق ثبت و Rewrite | متاهای ترم (Term Meta) |
|---|---|---|---|---|
| **نوع پروژه** | `project_type` | `projects` | `'hierarchical' => true`, `'rewrite' => ['slug' => 'project_type', 'with_front' => false, 'hierarchical' => false]`, `'show_in_rest' => true` + فیلتر دراپ‌داون در لیست ادمین | فیلدهای Hreflang (`hrefmeta_fa`, `hrefmeta_en`, `hrefmeta_ar`, `same_key_hrefl`) |
| **محل پروژه (شهر)** | `city` | `projects` | `'hierarchical' => true`, `'rewrite' => ['slug' => 'city', 'with_front' => false, 'hierarchical' => false]`, `'show_in_rest' => true` + فیلتر دراپ‌داون در لیست ادمین | فیلدهای Hreflang |
| **دسته‌بندی محصولات** | `product_cat` | `product` | ⚠️ **حیاتی سئو:** اسلاگ دقیقاً `product_cat` (نه `product-category`) با `'with_front' => true` و `'hierarchical' => true` (مثال: `/product_cat/strings-and-lighting-decorations/`) | `thumbnail` (آیدی/مدیا تصویر دسته) + `faq` (Repeater شامل `name_question` و `answer_question`) + Hreflang |
| **برچسب محصولات** | `product_tag` | `product` | اسلاگ `product-tag` (`'with_front' => false`) | Hreflang |
| **برند محصولات** | `product_brand` | `product` | سلسله‌مراتبی (`'hierarchical' => true`) | Hreflang |

### ب) ۱۸ ویژگی فنی محصولات ووکامرس (`pa_*` Product Attributes)
1. `pa_after-sales-support` — خدمات پس از فروش
2. `pa_beam-angle` — زاویه تابش
3. `pa_body-material` — جنس بدنه
4. `pa_brand` — برند
5. `pa_bulb-type` — جنس حباب
6. `pa_cable-material` — جنس سیم
7. `pa_certifications` — گواهینامه‌ها
8. `pa_color` — رنگ نور (نوع: `color`)
9. `pa_diameter` — قطر
10. `pa_diffuser-color` — رنگ حباب
11. `pa_input-voltage` — ولتاژ ورودی
12. `pa_intensity` — شدت نور
13. `pa_ip-rating` — درجه حفاظت IP
14. `pa_led-chip-type` — نوع چیپ LED
15. `pa_lifespan` — طول عمر مفید
16. `pa_size` — طول / تراکم
17. `pa_warranty` — گارانتی
18. `pa_wattage` — توان مصرفی (وات)

---

## ۴. نقشه دقیق کلیدهای متا (Exact Database Meta Keys)

> ⚠️ **قانون املای دقیق:** املای تمام کلیدهای زیر مستقیماً از دیتابیس زنده استخراج شده و تغییر حتی یک کاراکتر در آن‌ها ممنوع است.

### الف) متاباکس «اطلاعات محصولات» (روی پست‌تایپ `product`)

#### ۱. شناسه‌های صنعتی و قانونی (همگی `text` + پشتیبانی از **Quick Edit** در لیست محصولات):
- `gs1_id`: شناسه بین‌المللی GS1 (برابر با GTIN)
- `national_code`: کد ملی کالا (ایران‌کد)
- `hs_id`: کد تعرفه هماهنگ گمرکی (HS)
- `isic_id`: کد صنعتی آیسیک (ISIC)
- `cpc_id`: کد طبقه‌بندی مرکزی محصول (CPC)
- `product_id`: شناسه اختصاصی محصول

#### ۲. عناوین و وضعیت فروش:
- `zireonvan`: زیرعنوان محصول (`text`)
- `english_titl`: عنوان انگلیسی محصول (`text` — **توجه به املای دقیق بدون e پایانی: `english_titl`**)
- `before-sale-c`: اطلاعیه قبل از خرید (نمایش در پنل اطلاعات محصول ووکامرس و تک‌محصول)
- `true_sale`: وضعیت خرید آنلاین (`"true"` = قابل خرید آنلاین با قیمت و دکمه افزودن به سبد خرید؛ `"false"` = استعلامی / نیازمند تماس و پیش‌فاکتور)

#### ۳. فایل کاتالوگ و گالری تصاویر:
- `product-catalog-file`: فایل کاتالوگ فنی محصول (Media — ذخیره به صورت رشته `url`)
- `galerry_produ`: گالری محصول (رشته آیدی‌های مدیا جداشده با کاما — **توجه به املای دقیق `galerry_produ`**)
  - *زنجیره Fallback خواندن گالری محصول:* `galerry_produ` -> `_product_image_gallery` (استاندارد ووکامرس) -> `gallery-filed` -> `image-2` الی `image-6`.

#### ۴. روابط (Relations):
- `project-ids`: پروژه‌های مرتبط با محصول (آرایه سریالایزشده یا JSON از آیدی‌های پست‌تایپ `projects`)
- `usage_countr`: کاربردهای این محصول بر اساس نوع پروژه (آرایه سریالایزشده یا JSON از `term_id`های تکسونومی `project_type` — **توجه به املای دقیق `usage_countr`**)

#### ۵. فیلدهای تکرارشونده (Repeaters — سازگار با ساختار JetEngine یعنی آرایه سریالایزشده یا JSON با کلیدهای `{"item-0": {...}, "item-1": {...}}` و آرایه اندیس‌دار):
- `product-feature`: مشخصات تک‌ستونی محصول
  - زیرفیلد: `add-new-feature` (متن ویژگی)
- `product-feature2_copy`: ویژگی‌های کلیدی دو بخشی (خلاصه بالای صفحه/کارت مشخصات)
  - زیرفیلدها: `add-new-feature` (عنوان ویژگی، مثل «گارانتی») و `vaule_of_variable` (مقدار ویژگی، مثل «۲۴ ماهه» — **توجه به املای دقیق `vaule_of_variable`**)
- `usage_product`: کاربردها و موارد مصرف محصول
  - زیرفیلد: `add-new-usage` (عنوان/توضیح کاربرد)
- `faq`: سوالات متداول اختصاصی محصول
  - زیرفیلدها: `name_question` (عنوان سوال) و `answer_question` (متن پاسخ)

---

### ب) متاباکس «اطلاعات پروژه» (روی پست‌تایپ `projects`)
- **تصاویر گالری پروژه (ذخیره آیدی پیوست Media):**
  - `second-image` (تصویر ۲)
  - `three-image` (تصویر ۳)
  - `foure-image` (تصویر ۴ — **توجه به املای دقیق `foure-image`**)
  - `five-image` (تصویر ۵)
  - `six-image` (تصویر ۶)
  - *(در پنل ادمین جدید یک انتخابگر گالری مدرن نیز تعبیه می‌شود که مستقیماً با همین ۵ کلید همگام‌سازی می‌شود).*
- `finish-pro-time`: تاریخ/سال تکمیل پروژه (`text` — مثال: `1404/01/01`)
- `mortabet_mahsole_2`: محصولات استفاده‌شده در پروژه (آرایه سریالایزشده یا JSON از آیدی‌های پست‌تایپ `product`)

---

### ج) متاباکس چندزبانه «ترجمه‌های این محتوا / Hreflang»
قابل استفاده روی پست‌تایپ‌های `post`, `page`, `product`, `projects` و تکسونومی‌های اصلی (`category`, `product_cat`, `product_tag`, `product_brand`, `project_type`, `city`):
- `hrefmeta_fa`: URL کامل نسخه فارسی
- `hrefmeta_en`: URL کامل نسخه انگلیسی
- `hrefmeta_ar`: URL کامل نسخه عربی
- `same_key_hrefl`: کلید مشترک ترجمه (به همراه ستون قابل مرتب‌سازی `Sortable Admin Column` در لیست ادمین نوشته‌ها)

---

### د) متاباکس «میزان بازدید»
قابل استفاده روی `post`, `projects`, `product`:
- `number2-viewc`: تعداد بازدید (`number` — ذخیره و افزایش خودکار روی این کلید؛ با Fallback خواندن اولیه از کلید قدیمی `number-viewc` در صورت خالی بودن `number2-viewc`).

---

## ۵. برگه‌های ثابت و لندینگ‌های فعال سایت زنده

| عنوان برگه | اسلاگ (`post_name`) | آیدی زنده (`ID`) | نقش در سیستم |
|---|---|---|---|
| صفحه اصلی | `home` | `8146` | `front-page.php` (`page_on_front`) |
| فروشگاه | `shop` | `8574` | آرشیو اصلی محصولات ووکامرس (`woocommerce_shop_page_id`) |
| پروژه‌های برگزیده | `project` | `8366` | برگه معرفی/لیست پروژه‌ها (همراه با آرشیو CPT در `/projects/`) |
| وبلاگ | `blog` | `7426` | آرشیو نوشته‌ها (`page_for_posts`) |
| تماس با ما | `تماس-با-ما` | `7666` | برگه تماس، نقشه و فرم مشاوره |
| درباره ما | `درباره-ما` | `7578` | برگه معرفی شرکت، تاریخچه و اصول بنیادین |
| مجوزها و افتخارات | `licenses-and-honors` | `7583` | گواهینامه‌های دانش‌بنیان، ISO و تقدیرنامه‌ها |
| سوالات متداول | `faq` | `10195` | بانک سوالات متداول دسته‌بندی‌شده |
| فرصت‌های شغلی و استخدام | `recruitment` | `17898` | معرفی ۴ واحد سازمانی (R&D، پروژه، مالی، IT) و فرم استخدام |
| شرایط گارانتی و بازگشت کالا | `refund_returns` | `14922` | شرایط ۲۴ ماه گارانتی تعویض و خدمات پس از فروش |
| درب بازکن هوشمند آریا | `arya` | `16947` | لندینگ اختصاصی محصول آریا (متصل به منوی `landing`) |
| خدمات شرکت عرفان صنعت | `smartelement` | `11984` | لندینگ خدمات مهندسی و اجرایی نورپردازی شهری |
| برگه‌های عملیاتی ووکامرس/کاربری | `cart`, `checkout`, `my-account`, `auth` | — | سبد خرید، تسویه‌حساب، پنل کاربری (`fl-panel`) و ورود پیامکی (`voorodak`) |

---

## ۶. موقعیت‌های فهرست (Menu Locations)
1. `main-mega-menu`: مگامنوی اصلی دسکتاپ (محصولات تب‌بندی‌شده، پروژه‌ها بر اساس نوع و شهر، корпораت/شرکتی شامل مجوزها، سوالات متداول، استخدام، گارانتی، وبلاگ، خدمات، درباره ما و تماس با ما).
2. `mobile-menu`: منوی موبایل چندسطحی کشویی (Off-canvas معادل «فهرست موبایل»).
3. `tolidat-sherkat`: فهرست «تولیدات شرکت / دسته‌بندی محصولات» در فوتر و بخش‌های دسترسی سریع.
4. `landing`: منوی اسکرول تک‌صفحه‌ای ویژه برگه‌های لندینگ (مانند لندینگ آریا `arya`).
