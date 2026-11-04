# HEADER_MAP — نقشه برش هدر/فوتر/سایدبار موبایل از میرور

> منبع: `uploads/static-mirror/static-mirror/index.html` — refهای byte-exact در همین پوشه.
> refها دقیقاً بلوک‌های میرور هستند (بدون هیچ تغییری). تست‌های 4.8 ساختار خروجی داینامیک را با همین refها مقایسه می‌کنند.

## ۱) بلوک‌های `<header data-navbar-header="true">` (header-ref.html — 69,345 بایت)

| # | بلوک | نشانی DOM | داینامیک‌سازی |
|---|------|-----------|----------------|
| 1 | نوار بالایی (فقط lg+) | `div.hidden.border-b.border-emerald-100/70...` | متن ثابت شرکت + ۳ لینک (licenses-and-honors / refund_returns / recruitment) → `es_page_url()` |
| 2 | ردیف اصلی — لوگو | `a[href=index.html] > div(ESE box) + div(دو خط متن)` | `es_logo_url()`: custom-logo → آپشن `header.logo` → fallback متن ESE عین میرور؛ alt از آپشن |
| 3 | ناو دسکتاپ | `nav[aria-label="منوی اصلی"].hidden.lg\:flex` | موقعیت `main-mega-menu` → `MegaMenu::output()` (تسک 4.4)؛ fallback تاکسونومی‌محور |
| 4 | مگامنو محصولات | `div.group.static[data-mega-menu-products] > button[data-desktop-mega-trigger=products] + div[data-desktop-mega-panel=products]` | تب‌ها: آیتم منو با کلاس `es-mega-tab` (آیکون: `es-icon-{lucide}`) یا تاپ‌لول product_cat؛ پنل: فرزندان تب = لینک دسته‌ها؛ هدر پنل «مشاهده همه» |
| 5 | مگامنو پروژه‌ها | `div.group.static[data-mega-menu-projects]` با تب‌های type/city | پنل type از project_type (grid-cols-2)، پنل city از city (grid-cols-3) + CTA «همه پروژه‌ها» |
| 6 | مگامنو خدمات | `div.group.relative > button[data-desktop-mega-trigger=services] + div[data-desktop-mega-panel=services].w-[340px]` | لینک‌ها: فرزندان آیتم منو یا آخرین services؛ هدر «همه خدمات ←» |
| 7 | مگامنو درباره شرکت | `div.group.static[data-mega-menu-corporate]` (max-w-740، ستون راست گرادیانی + آمار) | متن‌ها ثابت میرور (تاسیس ۱۳۸۰، +۸۵۰ پروژه، ۲۴ ماه) + لینک‌های ستون دوم از فرزندان آیتم منو |
| 8 | اکشن‌ها | `div.flex.shrink-0` : جستجو `[data-search-modal-open]`، سبد `[data-cart-modal-open]` + badge `[data-header-cart-badge]`، CTA «استعلام قیمت» | badge: تعداد سبد WC (فقط وقتی WC فعال، ارقام فارسی) وگرنه ۲ میرور؛ CTA از آپشن با fallback میرور |
| 9 | دکمه منوی موبایل | `button[data-mobile-toggle]` (lg:hidden) | ثابت — JS میرور |
| 10 | مودال جستجو (داخل هدر) | `div[data-search-modal] > input[data-global-search-input] + [data-search-modal-close]` + کانتینر نتایج `[data-global-search-products]` و `[data-global-search-projects]` + ۷ آیتم نمونه `data-search-item` | اسکلت ثابت؛ پر کردن نتایج = فاز ۸ (LiveSearch) |

## ۲) بلوک منوی موبایل (mobile-sidebar-ref.html — بعد از `</header>`)

`div[data-mobile-menu]` (hidden lg:hidden) → `div[data-mobile-backdrop]` + `aside[data-mobile-sidebar]`:
- ردیف لوگو + `[data-mobile-close]`
- دکمه جستجو `[data-search-modal-open]`
- کارت «صفحه اصلی»
- ۴ آکاردئون: `data-mobile-acc-btn/panel` = products (۴ تب `data-mobile-prod-tab` 0..3 + پنل‌ها)، projects (۲ تب type/city + «همه پروژه‌ها»)، services، corporate
- دکمه سبد `[data-cart-modal-open]`
- → تسک 4.5: `template-parts/header/mobile-sidebar.php` + `MobileWalker`
- **مودال سبد خرید** (`data-cart-modal` بعد از سایدبار) → فاز ۶ (WooCommerce/PurchaseFlow) — در فاز ۴ برش نمی‌شود.

## ۳) فوتر (footer-ref.html — 7,475 بایت)

| بلوک | DOM | داینامیک |
|------|-----|-----------|
| برند | `div.lg:col-span-4` (لوگو متنی + توضیح + ۴ چیپ گواهی) | توضیح/چیپ‌ها ثابت میرور |
| دسترسی سریع | `div.lg:col-span-2 > ul` ۸ لینک | منوی `footer` یا fallback آپشن `footer.quick_links` (۸ لینک فاز ۲) |
| دسته‌بندی محصولات | `div.lg:col-span-3 > ul` ۸ لینک product_cat | منوی `tolidat-sherkat` یا `wp_list_categories` سبک از product_cat |
| اطلاعات تماس | `div.lg:col-span-3` (آدرس، ۲ تلفن، ایمیل، ساعات + آیکون‌های lucide) | `es_contact_args()` فاز ۲ — مقادیر پیش‌فرض = دقیقاً میرور |
| نوار پایانی | کپی‌رایت + `button[data-scroll-top]` «بازگشت به بالا» | کپی‌رایت از آپشن؛ `wp_footer()` بعد از فوتر |

## ۴) کلاس‌های CSS کلیدی آیتم‌های منو (برای CSS Classes در ادمین وردپرس)

- سطح اول ساده: `rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700`
- فعال (current): `bg-emerald-50 text-emerald-800`
- کلاس‌های کنوانسیون منو: `es-mega-products` `es-mega-projects` `es-mega-services` `es-mega-corporate` (سطح اول) · `es-mega-tab` + `es-icon-{cpu|sparkles|trees|waves|...}` (تب‌های محصولات) · `es-mega-proj-type` / `es-mega-proj-city` (تب‌های پروژه)

## ۵) جدول جایگزینی (نقاط داینامیک → منبع داده)

| نقطه | منبع | fallback میرور |
|------|------|----------------|
| href پارامترهای داخلی | `es_page_url(slug)` | صفحه میرور |
| لوگو | custom-logo → `header.logo` | باکس متنی ESE |
| ناو دسکتاپ | موقعیت `main-mega-menu` | `MegaMenu::fallback()` از product_cat/project_type/city |
| سایدبار موبایل | موقعیت `mobile-menu` | `MobileMenu::fallback()` تاکسونومی‌محور |
| badge سبد | `WC()->cart->get_cart_contents_count()` | `۲` |
| CTA هدر | آپشن CTA | «استعلام قیمت» → contact |
| دسترسی سریع فوتر | منوی `footer` | آپشن `footer.quick_links` |
| دسته‌بندی فوتر | منوی `tolidat-sherkat` | product_cat |
| تماس فوتر | `es_contact_args()` | مقادیر میرور |
| کپی‌رایت | آپشن `footer.copyright` | متن میرور |
| hreflang | متاهای `hrefmeta_*` (3.13) | — |

## ۶) فهرست کامل data-hookها (مرجع تست 4.1)

- `data-cart-modal-open`
- `data-desktop-mega-panel`
- `data-desktop-mega-trigger`
- `data-global-search-input`
- `data-global-search-products`
- `data-global-search-projects`
- `data-header-cart-badge`
- `data-mega-menu-corporate`
- `data-mega-menu-products`
- `data-mega-menu-projects`
- `data-mega-prod-panel`
- `data-mega-prod-tab`
- `data-mega-proj-panel`
- `data-mega-proj-tab`
- `data-mobile-acc-btn`
- `data-mobile-acc-icon`
- `data-mobile-acc-panel`
- `data-mobile-backdrop`
- `data-mobile-close`
- `data-mobile-menu`
- `data-mobile-prod-panel`
- `data-mobile-prod-tab`
- `data-mobile-proj-panel`
- `data-mobile-proj-tab`
- `data-mobile-sidebar`
- `data-mobile-toggle`
- `data-navbar-header`
- `data-scroll-top`
- `data-search-item`
- `data-search-modal`
- `data-search-modal-close`
- `data-search-modal-open`
