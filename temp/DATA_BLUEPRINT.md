# DATA_BLUEPRINT — ساختار دیتابیس سایت زنده عرفان صنعت (مرجع الزام‌آور)

> استخراج‌شده از دیتابیس زنده `erfansanat.com` (منبع: `agent.md`). املای کلیدها **مو‌به‌مو** رعایت شود.

## ۱) محیط و افزونه‌ها
- WordPress `7.1+`، PHP `8.4+`، WooCommerce `10.9+` (پول: `IRT`، `is_rtl: true`).
- **حذف می‌شوند (قالب جایگزینشان می‌شود):** `Elementor`, `Elementor Pro`, `JetEngine`, `JetSearch`, `JetSmartFilters`, `JetTabs`.
- **باقی می‌مانند (سازگاری الزامی):** `WooCommerce`, `Variation Swatches for WooCommerce`, `WPC Product Quantity for WooCommerce`, `SEOPress` + `SEOPress PRO` (+ پشتیبانی متاهای قدیمی `rank_math_*`), `LiteSpeed Cache`, `WP-Parsidate`, `voorodak` (ورود پیامکی), `fl-panel` (پنل کاربری), `hreflangpch`.

## ۲) CPTها و Permalinkها
| Type | ثبت | تنظیمات دقیق |
|---|---|---|
| `projects` | Native در قالب (جای JetEngine) | `public=true`, `has_archive=true`, `rewrite=['slug'=>'projects','with_front'=>false]`, `hierarchical=false`, `menu_icon='dashicons-screenoptions'`, `supports=['title','editor','thumbnail','comments','custom-fields','revisions']`, `show_in_rest=true` |
| `product` (Woo) | ووکامرس | آرشیو: برگه `shop` (ID **8574**) · سینگل: `rewrite=['slug'=>'/product','with_front'=>false]` |
| `post` / `page` | هسته | صفحه اصلی: ID **8146** (slug `home`) · برگه وبلاگ: ID **7426** (slug `blog`) |

## ۳) تکسونومی‌ها
| Taxonomy | متصل به | تنظیمات |
|---|---|---|
| `project_type` | `projects` | Native، `hierarchical=true`, `rewrite=['slug'=>'project_type','with_front'=>false,'hierarchical'=>false]`, `show_in_rest=true` + فیلتر دراپ‌داون ادمین |
| `city` | `projects` | Native، `hierarchical=true`, `rewrite=['slug'=>'city','with_front'=>false,'hierarchical'=>false]`, `show_in_rest=true` + فیلتر دراپ‌داون ادمین |
| `product_cat` | `product` | ⚠️ اسلاگ **`product_cat`** (نه `product-category`)، `with_front=true`, `hierarchical=true` · متا: `thumbnail` (Media) + `faq` (Repeater: `name_question`, `answer_question`) |
| `product_tag` | `product` | اسلاگ `product-tag`, `with_front=false` |
| `product_brand` | `product` | `hierarchical=true` |

### ۱۸ ویژگی فنی (`pa_*`)
`pa_after-sales-support` (خدمات پس از فروش) · `pa_beam-angle` (زاویه تابش) · `pa_body-material` (جنس بدنه) · `pa_brand` (برند) · `pa_bulb-type` (جنس حباب) · `pa_cable-material` (جنس سیم) · `pa_certifications` (گواهینامه‌ها) · `pa_color` (رنگ نور — نوع color) · `pa_diameter` (قطر) · `pa_diffuser-color` (رنگ حباب) · `pa_input-voltage` (ولتاژ ورودی) · `pa_intensity` (شدت نور) · `pa_ip-rating` (درجه حفاظت IP) · `pa_led-chip-type` (نوع چیپ LED) · `pa_lifespan` (طول عمر مفید) · `pa_size` (طول/تراکم) · `pa_warranty` (گارانتی) · `pa_wattage` (توان مصرفی وات)

## ۴) نقشه کلیدهای متا (املای دقیق دیتابیس)

### الف) متاباکس «اطلاعات محصولات» (`product`)
**شناسه‌های صنعتی/قانونی** (text + Quick Edit): `gs1_id` (=GTIN) · `national_code` · `hs_id` · `isic_id` · `cpc_id` · `product_id`
**عناوین/فروش:** `zireonvan` (زیرعنوان) · `english_titl` (عنوان انگلیسی ⚠️ املا) · `before-sale-c` (اطلاعیه قبل از خرید، پنل ووکامرس) · `true_sale` (`"true"`=خرید آنلاین / `"false"`=استعلامی)
**فایل/گالری:** `product-catalog-file` (Media → `url`) · `galerry_produ` (⚠️ املا؛ لیست آیدی تصاویر جداشده با کاما؛ Fallback: `_product_image_gallery` استاندارد + کلیدهای قدیمی `gallery-filed`, `image-2`..`image-6`)
**روابط:** `project-ids` (آرایه/JSON آیدی `projects`) · `usage_countr` (⚠️ املا؛ آرایه/JSON `term_id`های `project_type`)
**Repeaterها** (فرمت سازگار `{"item-0": {...}}`):
- `product-feature` → `add-new-feature`
- `product-feature2_copy` → `add-new-feature` + `vaule_of_variable` (⚠️ املا)
- `usage_product` → `add-new-usage`
- `faq` → `name_question` + `answer_question`

### ب) متاباکس «اطلاعات پروژه» (`projects`)
- گالری: `second-image`, `three-image`, `foure-image` (⚠️ املا), `five-image`, `six-image` (آیدی Media) + فیلد گالری مدرن همگام با این کلیدها
- `finish-pro-time` (Text، مثال `1404/01/01`)
- `mortabet_mahsole_2` (آرایه/JSON آیدی `product`)

### ج) متاباکس Hreflang (`post`, `page`, `product`, `projects` + تکسونومی‌های اصلی)
`hrefmeta_en` · `hrefmeta_ar` · `hrefmeta_fa` · `same_key_hrefl` (کلید مشترک، ستون sortable ادمین)

### د) شمارنده بازدید (`post`, `projects`, `product`)
`number2-viewc` (Number) + Fallback خواندن از `number-viewc`

## ۵) برگه‌های ثابت و لندینگ‌ها
| Slug | ID | توضیح |
|---|---|---|
| `home` | 8146 | صفحه اصلی |
| `shop` | 8574 | فروشگاه |
| `project` | 8366 | پروژه‌های برگزیده |
| `blog` | 7426 | وبلاگ |
| `تماس-با-ما` | 7666 | تماس با ما |
| `درباره-ما` | 7578 | درباره ما |
| `licenses-and-honors` | 7583 | مجوزها و افتخارات |
| `faq` | 10195 | سوالات متداول |
| `recruitment` | 17898 | استخدام (IT، حسابداری، پروژه، R&D) |
| `refund_returns` | 14922 | گارانتی و خدمات پس از فروش |
| `arya` | 16947 | لندینگ درب بازکن هوشمند آریا |
| `smartelement` | 11984 | خدمات شرکت |
| `cart`, `checkout`, `my-account`, `auth` | — | برگه‌های ووکامرس |

## ۶) موقعیت‌های منو
`main-mega-menu` (مگامنوی دسکتاپ) · `mobile-menu` (فهرست موبایل) · `tolidat-sherkat` (تولیدات شرکت) · `landing` (منوی اسکرول لندینگ‌ها مثل آریا)

## ۷) تصمیمات تکمیلی کاربر (2026-11-03)
- **CPT جدید `services` (D2):** ۵ تک‌خدمت (نورپردازی میدان/خیابان/پل/المان/ساختمان). ثبت بومی: `public=true`, `has_archive=false` (آرشیو = برگه `smartelement` 11984), `rewrite=['slug'=>'services','with_front'=>false]`, `supports=['title','editor','thumbnail','excerpt','custom-fields','revisions']`, `show_in_rest=true`. ⚠️ این CPT در دیتابیس لایو وجود ندارد → هنگام استقرار، محتوای ۵ صفحه از میرور/کارفرما مهاجرت داده می‌شود.
- **اسلاگ ترم‌ها فارسی است (D3):** مثال لایو `/city/اصفهان/`. هیچ کوئری/لینکی به اسلاگ انگلیسی میرور هاردکد نشود.
- **برگه `project` (8366) → 301 به `/projects/` (D4).**
- **هیرو (D5):** ویدیوی اصلی = فیلد Media جدا در تنظیمات قالب؛ اسلایدهای دیگر = Repeater انتخاب پروژه.
- **وبلاگ (D6):** TOC + دیدگاه با همان سبک اضافه می‌شود.
