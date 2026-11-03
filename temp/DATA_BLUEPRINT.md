# DATA_BLUEPRINT — ساختار دیتابیس سایت زنده عرفان صنعت

> منبع: بخش «LIVE SITE DATA BLUEPRINT» در `agent.md`. این فایل مرجع کدنویسی است؛ هر کلید متا باید **مو‌به‌مو با همین املا** پیاده شود.

## ۱. محیط سیستم
| مورد | مقدار |
|---|---|
| WordPress | `7.1+` |
| PHP | `8.4+` |
| WooCommerce | `10.9+` |
| واحد پول | `IRT` (تومان) |
| زبان | فارسی، `is_rtl: true` |

**افزونه‌های حذف‌شده:** Elementor, Elementor Pro, JetEngine, JetSearch, JetSmartFilters, JetTabs — قالب باید وظایف آن‌ها را Native بر عهده بگیرد.

**افزونه‌های باقی‌مانده (سازگاری الزامی):** WooCommerce, Variation Swatches for WooCommerce, WPC Product Quantity for WooCommerce, SEOPress (+PRO، و متاهای قدیمی `rank_math_*`), LiteSpeed Cache, WP-Parsidate, voorodak (ورود پیامکی), fl-panel (پنل کاربری), hreflangpch.

## ۲. پست‌تایپ‌ها و Permalink
### ۲.۱ `projects` (باید Native در قالب ثبت شود)
```php
register_post_type('projects', [
  'public' => true, 'has_archive' => true,
  'rewrite' => ['slug' => 'projects', 'with_front' => false],
  'hierarchical' => false,
  'menu_icon' => 'dashicons-screenoptions',
  'supports' => ['title','editor','thumbnail','comments','custom-fields','revisions'],
  'show_in_rest' => true,
]);
```
### ۲.۲ `product` (WooCommerce)
- آرشیو: اسلاگ `shop` — برگه ID `8574`
- سینگل: `rewrite['slug'] = '/product'`, `with_front => false`

### ۲.۳ `post` / `page`
- `page_on_front` = `8146` (اسلاگ `home`)
- `page_for_posts` = `7426` (اسلاگ `blog`)

## ۳. تکسونومی‌ها
| تکسونومی | اتصال | تنظیمات حیاتی |
|---|---|---|
| `project_type` | `projects` | `hierarchical=true`, `rewrite=['slug'=>'project_type','with_front'=>false,'hierarchical'=>false]`, `show_in_rest=true`, فیلتر دراپ‌داون در لیست ادمین |
| `city` | `projects` | `hierarchical=true`, `rewrite=['slug'=>'city','with_front'=>false,'hierarchical'=>false]`, `show_in_rest=true`, فیلتر دراپ‌داون در لیست ادمین |
| `product_cat` | `product` | ⚠️ اسلاگ `product_cat` (نه `product-category`), `with_front=true`, `hierarchical=true` — نمونه: `/product_cat/strings-and-lighting-decorations/` |
| `product_tag` | `product` | اسلاگ `product-tag`, `with_front=false` |
| `product_brand` | `product` | `hierarchical=true` |

**متاهای تکسونومی `product_cat`:** `thumbnail` (تصویر، Media)، `faq` (Repeater با زیرفیلدهای `name_question`, `answer_question`)

### ۳.۱ ویژگی‌های فنی (`pa_*`) — ۱۸ عدد
`pa_after-sales-support` (خدمات پس از فروش) · `pa_beam-angle` (زاویه تابش) · `pa_body-material` (جنس بدنه) · `pa_brand` (برند) · `pa_bulb-type` (جنس حباب) · `pa_cable-material` (جنس سیم) · `pa_certifications` (گواهینامه‌ها) · `pa_color` (رنگ نور، نوع color) · `pa_diameter` (قطر) · `pa_diffuser-color` (رنگ حباب) · `pa_input-voltage` (ولتاژ ورودی) · `pa_intensity` (شدت نور) · `pa_ip-rating` (درجه حفاظت IP) · `pa_led-chip-type` (نوع چیپ LED) · `pa_lifespan` (طول عمر مفید) · `pa_size` (طول/تراکم) · `pa_warranty` (گارانتی) · `pa_wattage` (توان مصرفی وات)

## ۴. نقشه دقیق کلیدهای متا
> ⚠️ املای تاریخی باید **عیناً** حفظ شود.

### ۴.۱ محصول (`product`) — متاباکس «اطلاعات محصولات»
| کلید | نوع | توضیح |
|---|---|---|
| `gs1_id` | text (Quick Edit) | شناسه GS1 = GTIN |
| `national_code` | text | کد ملی کالا |
| `hs_id` | text | شناسه HS |
| `isic_id` | text | شناسه ISIC |
| `cpc_id` | text | شناسه CPC |
| `product_id` | text | شناسه محصول |
| `zireonvan` | text | زیرعنوان محصول |
| `english_titl` | text | ⚠️ عنوان انگلیسی (بدون `e` آخر) |
| `before-sale-c` | text | اطلاعیه قبل از خرید |
| `true_sale` | `"true"`/`"false"` | true = خرید آنلاین، false = استعلام/تماس |
| `product-catalog-file` | Media URL | فایل کاتالوگ |
| `galerry_produ` | CSV of attachment IDs | ⚠️ گالری محصول — Fallback به `_product_image_gallery`, `gallery-filed`, `image-2`…`image-6` |
| `project-ids` | آرایه/JSON از ID `projects` | پروژه‌های مرتبط |
| `usage_countr` | آرایه/JSON از `term_id`های `project_type` | ⚠️ کاربردها |
| `product-feature` | Repeater `{"item-0":{...}}` | زیرفیلد `add-new-feature` |
| `product-feature2_copy` | Repeater | زیرفیلدها `add-new-feature` (عنوان) و `vaule_of_variable` (مقدار) ⚠️ |
| `usage_product` | Repeater | زیرفیلد `add-new-usage` |
| `faq` | Repeater | زیرفیلدها `name_question`, `answer_question` |

### ۴.۲ پروژه (`projects`) — متاباکس «اطلاعات پروژه»
| کلید | نوع | توضیح |
|---|---|---|
| `second-image` | attachment ID | تصویر ۲ |
| `three-image` | attachment ID | تصویر ۳ |
| `foure-image` | attachment ID | ⚠️ تصویر ۴ |
| `five-image` | attachment ID | تصویر ۵ |
| `six-image` | attachment ID | تصویر ۶ |
| `finish-pro-time` | text | تاریخ تکمیل، مثال `1404/01/01` |
| `mortabet_mahsole_2` | آرایه/JSON از ID `product` | محصولات مرتبط پروژه |

### ۴.۳ چندزبانه / Hreflang
روی `post`, `page`, `product`, `projects` و تکسونومی‌های اصلی:
| کلید | توضیح |
|---|---|
| `hrefmeta_en` | لینک نسخه انگلیسی |
| `hrefmeta_ar` | لینک نسخه عربی |
| `hrefmeta_fa` | لینک نسخه فارسی |
| `same_key_hrefl` | کلید مشترک (ستون مرتب‌سازی در لیست ادمین نوشته‌ها) |

### ۴.۴ شمارنده بازدید
| کلید | روی | توضیح |
|---|---|---|
| `number2-viewc` | `post`, `projects`, `product` | تعداد بازدید (Fallback خواندن: `number-viewc`) |

## ۵. برگه‌ها و اسلاگ‌های ثابت
| اسلاگ | ID | نقش |
|---|---|---|
| `home` | 8146 | صفحه اصلی (front page) |
| `shop` | 8574 | آرشیو فروشگاه |
| `project` | 8366 | پروژه‌های برگزیده |
| `blog` | 7426 | وبلاگ (page_for_posts) |
| `تماس-با-ما` | 7666 | تماس با ما |
| `درباره-ما` | 7578 | درباره ما |
| `licenses-and-honors` | 7583 | مجوزها و افتخارات |
| `faq` | 10195 | سوالات متداول |
| `recruitment` | 17898 | فرصت‌های شغلی |
| `refund_returns` | 14922 | شرایط گارانتی و خدمات پس از فروش |
| `arya` | 16947 | لندینگ درب بازکن هوشمند آریا |
| `smartelement` | 11984 | لندینگ خدمات شرکت |
| `cart`, `checkout`, `my-account`, `auth` | — | برگه‌های ووکامرس |

## ۶. جایگاه‌های منو (Menu Locations)
| موقعیت | توضیح |
|---|---|
| `main-mega-menu` | مگامنوی اصلی دسکتاپ (محصولات، پروژه‌ها بر اساس نوع و شهر، مجوزها، سوالات متداول، وبلاگ، تماس، درباره ما) |
| `mobile-menu` | منوی موبایل چندسطحی (معادل «فهرست موبایل») |
| `tolidat-sherkat` | منوی تولیدات شرکت (فوتر/هدر) |
| `landing` | منوی اسکرول صفحات لندینگ (مثل آریا) |

## ۷. نکات سازگاری با دیتای فعلی
1. Repeaterهای JetEngine هم به‌صورت **آرایه سریالایز** و هم **JSON** ممکن است ذخیره شده باشند → لایه خواندن باید هر دو را نرمال کند و در ذخیره، فرمت غالب قبلی را حفظ کند.
2. `true_sale` مقادیر رشته‌ای `"true"`/`"false"` دارد (نه بولین) → مقایسه دقیق رشته‌ای.
3. `galerry_produ` لیست CSV از **attachment ID** است، نه URL.
4. متاهای سئو: اولویت SEOPress (`_seopress_*`)، سپس `rank_math_*` قدیمی.
5. بازدید: خواندن از `number2-viewc` با Fallback به `number-viewc`؛ نوشتن فقط روی `number2-viewc`.
