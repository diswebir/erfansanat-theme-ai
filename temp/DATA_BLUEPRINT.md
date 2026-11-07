# DATA BLUEPRINT — ساختار دیتابیس سایت زنده erfansanat.com
(منبع: agent.md — باید مو‌به‌مو در کدنویسی رعایت شود)

## ۱. محیط سیستم
- WordPress 7.1+، PHP 8.4+، WooCommerce 10.9+ | valuta: IRT | `is_rtl: true`
- حذف می‌شوند: Elementor، Elementor Pro، JetEngine، JetSearch، JetSmartFilters، JetTabs
- باقی می‌مانند: WooCommerce، Variation Swatches for WooCommerce، WPC Product Quantity، SEOPress + SEOPress PRO (سازگاری با متاهای rank_math_*)، LiteSpeed Cache، WP-Parsidate، voorodak، fl-panel، hreflangpch

## ۲. پست‌تایپ‌ها و Permalinkها
| CPT | تنظیمات کلیدی |
|---|---|
| `projects` | register_post_type native؛ public، has_archive، rewrite slug `projects` (with_front=false)، dashicons-screenoptions، supports: title/editor/thumbnail/comments/custom-fields/revisions، show_in_rest |
| `product` (ووکامرس) | آرشیو اسلاگ `shop` (page ID 8574)، سینگل rewrite slug `/product` (with_front=false) |
| `post` / `page` | front_page ID 8146 (slug home)، blog page ID 7426 (slug blog) |

## ۳. تکسونومی‌ها
| Taxonomy | اتصال | تنظیمات |
|---|---|---|
| `project_type` | projects | hierarchical=true، rewrite slug `project_type` (with_front=false, hierarchical=false)، show_in_rest + فیلتر دراپ‌داون ادمین |
| `city` | projects | hierarchical=true، rewrite slug `city` + فیلتر دراپ‌داون ادمین |
| `product_cat` | product | **اسلاگ `product_cat`** (with_front=true, hierarchical=true) — مثال: /product_cat/strings-and-lighting-decorations/ — متاها: `thumbnail` (Media)، `faq` (repeater: name_question, answer_question) |
| `product_tag` | product | اسلاگ `product-tag` (with_front=false) |
| `product_brand` | product | hierarchical=true |

### ۱۸ ویژگی فنی (pa_*): after-sales-support, beam-angle, body-material, brand, bulb-type, cable-material, certifications, color (نوع color), diameter, diffuser-color, input-voltage, intensity, ip-rating, led-chip-type, lifespan, size, warranty, wattage

## ۴. کلیدهای متای دقیق (املای دیتابیس live — بدون تغییر!)
### الف) متاباکس محصول (product)
- IDs: `gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`, `product_id` (text، Quick Edit)
- `zireonvan` (زیرعنوان)، `english_titl` (عنوان انگلیسی)، `before-sale-c` (اطلاعیه)، `true_sale` ("true"=آنلاین / "false"=استعلام)
- `product-catalog-file` (Media→url)، `galerry_produ` (CSV of image IDs + fallback به `_product_image_gallery`, `gallery-filed`, `image-2..6`)
- `project-ids` (array of projects IDs)، `usage_countr` (array of project_type term_ids)
- Repeaters (فرمت `{"item-0": {...}}`): `product-feature` (add-new-feature)، `product-feature2_copy` (add-new-feature + `vaule_of_variable`)، `usage_product` (add-new-usage)، `faq` (name_question + answer_question)

### ب) متاباکس پروژه (projects)
- `second-image`, `three-image`, `foure-image` (!), `five-image`, `six-image` (Media IDs)
- `finish-pro-time` (تاریخ، مثل 1404/01/01)، `mortabet_mahsole_2` (array of product IDs)

### ج) Hreflang (post, page, product, projects + taxonomyهای اصلی)
`hrefmeta_en`, `hrefmeta_ar`, `hrefmeta_fa`, `same_key_hrefl` (مرتب‌سازی ستون ادمین)

### د) شمارنده بازدید (post, projects, product)
`number2-viewc` (number، fallback به `number-viewc`)

## ۵. برگه‌های ثابت live
home(8146), shop(8574), project(8366), blog(7426), تماس-با-ما(7666), درباره-ما(7578), licenses-and-honors(7583), faq(10195), recruitment(17898), refund_returns(14922), arya(16947), smartelement(11984) + woocommerce pages (cart, checkout, my-account, auth)

## ۶. موقعیت‌های منو
`main-mega-menu` (مگامنوی دسکتاپ)، `mobile-menu` (موبایل چندسطحی)، `tolidat-sherkat` (تولیدات شرکت، فوتر/هدر)، `landing` (اسکرول لندینگ مثل آریا)
