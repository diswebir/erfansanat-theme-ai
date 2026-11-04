# LIVE SITE DATA BLUEPRINT (مشخصات فنی استخراج‌شده از سایت زنده عرفان صنعت)
این اطلاعات از دیتابیس زنده `erfansanat.com` استخراج شده‌اند و باید در `temp/DATA_BLUEPRINT.md` ثبت و در کدنویسی **مو‌به‌مو** رعایت شوند:

### ۱. محیط سیستم و افزونه‌های همکار
- **محیط هدف:** WordPress `7.1+`، PHP `8.4+`، WooCommerce `10.9+` (واحد پول: `IRT`، راست‌چین `is_rtl: true`).
- **افزونه‌هایی که حذف می‌شوند (و قالب باید وظایفشان را بگیرد):** `Elementor`, `Elementor Pro`, `JetEngine`, `JetSearch`, `JetSmartFilters`, `JetTabs`.
- **افزونه‌هایی که باقی می‌مانند (و قالب باید با آن‌ها سازگار باشد):** `WooCommerce`, `Variation Swatches for WooCommerce`, `WPC Product Quantity for WooCommerce`, `SEOPress` & `SEOPress PRO` (به همراه پشتیبانی از متاهای قدیمی `rank_math_*`), `LiteSpeed Cache`, `WP-Parsidate`, `voorodak` (ورود پیامکی), `fl-panel` (پنل کاربری), `hreflangpch`.

### ۲. پست‌تایپ‌ها (CPTs) و ساختار Permalinks (حفظ ۱۰۰٪ سئو)
1. **پست‌تایپ پروژه‌ها (`projects`):**
   - باید توسط قالب به صورت Native با `register_post_type('projects', ...)` ثبت شود (چون JetEngine غیرفعال می‌شود).
   - تنظیمات دقیق: `'public' => true`, `'has_archive' => true`, `'rewrite' => ['slug' => 'projects', 'with_front' => false]`, `'hierarchical' => false`, `'menu_icon' => 'dashicons-screenoptions'`, `'supports' => ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions']`, `'show_in_rest' => true`.
2. **پست‌تایپ محصولات (`product` - ووکامرس):**
   - آرشیو اصلی: اسلاگ `shop` (برگه با آیدی `8574`).
   - سینگل محصول: `'rewrite' => ['slug' => '/product', 'with_front' => false]`.
3. **نوشته‌ها (`post`) و برگه‌ها (`page`):**
   - برگه صفحه اصلی (`front_page_id`): آیدی `8146` (اسلاگ `home`).
   - برگه وبلاگ (`blog_page_id`): آیدی `7426` (اسلاگ `blog`).

### ۳. تکسونومی‌ها (Taxonomies) و اسلاگ‌های حیاتی
1. **`project_type` (نوع پروژه - متصل به `projects`):**
   - ثبت Native در قالب با `register_taxonomy`: `'hierarchical' => true`, `'rewrite' => ['slug' => 'project_type', 'with_front' => false, 'hierarchical' => false]`, `'show_in_rest' => true`.
   - همراه با فیلتر دراپ‌داون در لیست ادمین پروژه‌ها.
2. **`city` (محل پروژه - متصل به `projects`):**
   - ثبت Native در قالب با `register_taxonomy`: `'hierarchical' => true`, `'rewrite' => ['slug' => 'city', 'with_front' => false, 'hierarchical' => false]`, `'show_in_rest' => true`.
   - همراه با فیلتر دراپ‌داون در لیست ادمین پروژه‌ها.
3. **`product_cat` (دسته‌بندی محصولات ووکامرس):**
   - **نکته بسیار مهم سئو:** در سایت عرفان صنعت، اسلاگ دسته‌بندی محصولات `product_cat` است (نه `product-category`)، با `'with_front' => true` و `'hierarchical' => true` (مثال لینک زنده: `https://erfansanat.com/product_cat/strings-and-lighting-decorations/`).
   - **متاهای تکسونومی `product_cat`:**
     - `thumbnail`: تصویر بندانگشتی دسته (Media).
     - `faq`: فیلد تکرارشونده (Repeater) سوالات متداول دسته‌بندی شامل زیرفیلدهای `name_question` (عنوان سوال) و `answer_question` (جواب سوال).
4. **`product_tag` (برچسب محصولات ووکامرس):** اسلاگ `product-tag` (`'with_front' => false`).
5. **`product_brand` (برند محصولات):** سلسله‌مراتبی (`hierarchical: true`).
6. **۱۸ ویژگی فنی محصولات ووکامرس (`pa_*` Attributes):**
   - `pa_after-sales-support` (خدمات پس از فروش)، `pa_beam-angle` (زاویه تابش)، `pa_body-material` (جنس بدنه)، `pa_brand` (برند)، `pa_bulb-type` (جنس حباب)، `pa_cable-material` (جنس سیم)، `pa_certifications` (گواهینامه‌ها)، `pa_color` (رنگ نور - نوع color)، `pa_diameter` (قطر)، `pa_diffuser-color` (رنگ حباب)، `pa_input-voltage` (ولتاژ ورودی)، `pa_intensity` (شدت نور)، `pa_ip-rating` (درجه حفاظت IP)، `pa_led-chip-type` (نوع چیپ LED)، `pa_lifespan` (طول عمر مفید)، `pa_size` (طول / تراکم)، `pa_warranty` (گارانتی)، `pa_wattage` (توان مصرفی وات).

### ۴. نقشه دقیق کلیدهای متا (Exact Database Meta Keys)
⚠️ **هشدار حیاتی:** برخی کلیدهای متا در دیتابیس فعلی دارای املای خاص یا غلط املایی تاریخی هستند (مثل `foure-image` یا `galerry_produ`). تو **مجبور هستی دقیقاً با همین املا** آن‌ها را در متاباکس‌های اختصاصی قالب بخوانی و بنویسی تا دیتای موجود سایت از دست نرود:

* **الف) متاباکس «اطلاعات محصولات» (روی پست‌تایپ `product`):**
  - شناسه‌های صنعتی و قانونی (همگی از نوع `text` با قابلیت Quick Edit):
    - `gs1_id`: شناسه GS1 (برابر با GTIN)
    - `national_code`: کد ملی کالا
    - `hs_id`: شناسه HS
    - `isic_id`: شناسه ISIC
    - `cpc_id`: شناسه CPC
    - `product_id`: شناسه محصول
  - عناوین و وضعیت فروش:
    - `zireonvan`: زیرعنوان محصول (Text)
    - `english_titl`: عنوان انگلیسی محصول (Text - دقت به املای `english_titl`)
    - `before-sale-c`: اطلاعیه قبل از خرید (در پنل اطلاعات محصول ووکامرس)
    - `true_sale`: وضعیت خرید آنلاین (`"true"` = قابل خرید آنلاین، `"false"` = نیازمند استعلام/تماس)
  - فایل و گالری:
    - `product-catalog-file`: فایل کاتالوگ محصول (Media - ذخیره به صورت `url`)
    - `galerry_produ`: گالری محصول (لیست آیدی‌های تصویر جداشده با کاما - دقت به املای `galerry_produ`؛ همچنین از `_product_image_gallery` استاندارد ووکامرس و کلیدهای قدیمی `gallery-filed` و `image-2` تا `image-6` به عنوان Fallback پشتیبانی کن).
  - روابط (Relations):
    - `project-ids`: پروژه‌های مرتبط با این محصول (آرایه/JSON از آیدی‌های پست‌تایپ `projects`).
    - `usage_countr`: کاربردهای این محصول (آرایه/JSON از `term_id`های تکسونومی `project_type` - دقت به املای `usage_countr`).
  - فیلدهای تکرارشونده (Repeaters - ذخیره به فرمت آرایه/JSON سازگار با ساختار فعلی `{"item-0": {...}}`):
    - `product-feature`: مشخصات محصول داخل محصول تکی -> زیرفیلد: `add-new-feature` (متن ویژگی).
    - `product-feature2_copy`: ویژگی‌های کلیدی دو بخشی -> زیرفیلدها: `add-new-feature` (عنوان مثل گارانتی) و `vaule_of_variable` (مقدار مثل ۲۴ ماهه - دقت به املای `vaule_of_variable`).
    - `usage_product`: کاربردها و موارد مصرف محصول -> زیرفیلد: `add-new-usage`.
    - `faq`: سوالات متداول محصول -> زیرفیلدها: `name_question` (عنوان سوال) و `answer_question` (جواب سوال).

* **ب) متاباکس «اطلاعات پروژه» (روی پست‌تایپ `projects`):**
  - تصاویر گالری پروژه (ذخیره آیدی تصویر Media):
    - `second-image` (تصویر 2)، `three-image` (تصویر 3)، `foure-image` (تصویر 4 - دقت به املای `foure-image`)، `five-image` (تصویر 5)، `six-image` (تصویر 6). (به علاوه یک فیلد گالری مدرن در داشبورد جدید که با این کلیدها همگام باشد).
  - `finish-pro-time`: تاریخ تکمیل پروژه (Text - مثال: `1404/01/01`).
  - `mortabet_mahsole_2`: محصولات مرتبط استفاده شده در پروژه (آرایه/JSON از آیدی‌های پست‌تایپ `product`).

* **ج) متاباکس «ترجمه‌های این محتوا / Hreflang» (روی `post`, `page`, `product`, `projects` و تمام تکسونومی‌های اصلی):**
  - `hrefmeta_en`: لینک نسخه انگلیسی
  - `hrefmeta_ar`: لینک نسخه عربی
  - `hrefmeta_fa`: لینک نسخه فارسی
  - `same_key_hrefl`: کلید مشترک (قابل مرتب‌سازی در ستون ادمین نوشته‌ها).

* **د) متاباکس «میزان بازدید» (روی `post`, `projects`, `product`):**
  - `number2-viewc`: تعداد بازدید (Number - همراه با Fallback خواندن از `number-viewc`).

### ۵. برگه‌های ثابت و لندینگ‌های فعال سایت (Pages & Slugs)
قالب باید از برگه‌های فعلی سایت با اسلاگ‌های زیر پشتیبانی کامل داشته باشد:
- `home` (صفحه اصلی - ID: 8146)، `shop` (فروشگاه - ID: 8574)، `project` (پروژه‌های برگزیده - ID: 8366)، `blog` (وبلاگ - ID: 7426)، `تماس-با-ما` (ID: 7666)، `درباره-ما` (ID: 7578)، `licenses-and-honors` (مجوزها و افتخارات - ID: 7583)، `faq` (سوالات متداول - ID: 10195)، `recruitment` (فرصت‌های شغلی و فرم استخدام واحدهای IT، حسابداری، پروژه و تحقیق و توسعه - ID: 17898)، `refund_returns` (شرایط گارانتی و خدمات پس از فروش - ID: 14922)، `arya` (لندینگ اختصاصی درب بازکن هوشمند آریا - ID: 16947)، `smartelement` (خدمات شرکت عرفان صنعت - ID: 11984)، و برگه‌های ووکامرس (`cart`, `checkout`, `my-account`, `auth`).

### ۶. موقعیت‌های منو (Menu Locations)
- `main-mega-menu` (مگامنوی اصلی دسکتاپ شامل محصولات، پروژه‌ها بر اساس نوع و شهر، مجوزها، سوالات متداول، وبلاگ، تماس و درباره ما)
- `mobile-menu` (منوی موبایل چندسطحی - معادل `فهرست موبایل`)
- `tolidat-sherkat` (منوی تولیدات شرکت در فوتر/هدر)
- `landing` (منوی اسکرول صفحات لندینگ مثل آریا)

---
