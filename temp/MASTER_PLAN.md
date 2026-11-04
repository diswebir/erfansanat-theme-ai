# MASTER_PLAN — نقشه راه کامل پروژه

**8 فاز · 137 تسک اتمیک** (اندازه: S=16 · M=68 · L=53)

> وضعیت‌ها: `[PENDING]` · `[IN_PROGRESS]` · `[TESTING]` · `[PASSED]`  
> قانون: هیچ تسکی بدون `100% PASS` در تست خودکار بسته نمی‌شود؛ هیچ فازی بدون پاس‌شدن همه تسک‌هایش وارد فاز بعد نمی‌شود.


## تصمیم‌های Gate 0 (تأیید کاربر — 2026-11-03)

| # | موضوع | تصمیم |
|---|---|---|
| G1 | واریانت‌های هیرو | فقط `home1` رندر می‌شود؛ `home2`/`home3` به‌صورت فایل آماده و قابل فعال‌سازی از داشبورد (Task 5.20) |
| G2 | لندینگ `arya`/`smartelement` | فعلاً مسکوت؛ بعداً با Section Builder |
| G3 | سبد/پرداخت/حساب | Override قالب‌های ووکامرس با UI استاتیک + حفظ هوک‌ها |
| G4 | محتوا | کاملاً داینامیک از دیتابیس، صفر هاردکد |
| G5 | منوها | ساختار + آیتم + فیلدهای مگامنو در Appearance → Menus؛ فقط تنظیمات نمایشی در داشبورد |
| G6 | تحویل | Stage 1 → Gate 1 |

---


## Phase 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک

`temp/phase-01-core-and-assets/`  
**هدف:** اسکلت قالب، معماری OOP بدون Composer، DeBloat کامل، و مدیر دارایی‌های شرطی با نسخه‌گذاری filemtime.

**12 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 1.1 | هدر قالب در `style.css` و متادیتای نسخه | `S` | — | `[PASSED]` | `style.css` +1 |
| 1.2 | Autoloader PSR-4 دستی و Namespace `ErfanSanat\Theme` | `M` | `1.1` | `[PASSED]` | `inc/Autoloader.php` +1 |
| 1.3 | Kernel قالب و ثبت `add_theme_support`ها | `M` | `1.2` | `[PASSED]` | `inc/Theme.php` +2 |
| 1.4 | `functions.php` سبک + `index.php` + `screenshot.png` | `S` | `1.3` | `[PASSED]` | `functions.php` +2 |
| 1.5 | هارنس رندر کامل قالب (`tools/harness/render.php`) | `M` | `1.4` | `[PASSED]` | `tools/harness/render.php` +1 |
| 1.6 | DeBloat: حذف کامل CSS/JS بلاک‌ها، ایموجی، oEmbed و متاتگ‌های اضافی | `L` | `1.5` | `[PASSED]` | `inc/Performance/DeBloat.php` |
| 1.7 | انتقال بایت‌به‌بایت Assets استاتیک (CSS/JS/فونت/لوگو) | `L` | `1.6` | `[PASSED]` | `assets/css/styles.css` +5 |
| 1.8 | انتقال دارایی‌های تصویری + ویدیو + پیش‌compress | `L` | `1.7` | `[PASSED]` | `assets/img/**/*.jpg` +4 |
| 1.9 | `AssetManager` با Enqueue شرطی و نسخه‌گذاری filemtime | `L` | `1.8` | `[PASSED]` | `inc/Assets/AssetManager.php` +1 |
| 1.10 | Preload/Preconnect فونت و LCP (شرطی) | `M` | `1.9` | `[PASSED]` | `inc/Assets/Preload.php` |
| 1.11 | ثبت جایگاه‌های منو و `load_theme_textdomain` | `S` | `1.3` | `[PASSED]` | `inc/Setup/Menus.php` +1 |
| 1.12 | گیت فاز ۱: اجرای کامل تست‌ها + بودجه عملکرد | `S` | `1.6`، `1.9`، `1.10` | `[PASSED]` | `temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh` |

**گیت فاز:** `tests/run-phase-01-tests.sh`


## Phase 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder

`temp/phase-02-custom-admin-dashboard/`  
**هدف:** پنل مدیریت اختصاصی RTL با ذخیره امن AJAX، لایه آپشن با کش، فیلدهای استاندارد، مدیا، Repeater، Section Manager و همگام‌سازی متای قدیمی.

**15 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 2.1 | لایه آپشن: `es_option()` / `es_update_option()` با کش و fallback | `M` | `1.3` | `[PASSED]` | `inc/Helpers/Options.php` +1 |
| 2.2 | منوی ادمین سطح‌بالا + پوسته RTL داشبورد | `L` | `2.1` | `[PASSED]` | `inc/Admin/AdminMenu.php` +2 |
| 2.3 | موتور فیلدها: Renderer، Sanitizer، Schema | `L` | `2.2` | `[PASSED]` | `inc/Admin/Fields/Schema.php` +3 |
| 2.4 | ذخیره AJAX امن (Nonce + Capability + rate) | `L` | `2.3` | `[PASSED]` | `inc/Admin/AjaxSave.php` |
| 2.5 | ریفیلد Media Picker (کتابخانه رسانه) | `M` | `2.4` | `[PASSED]` | `assets/admin/media.js` +1 |
| 2.6 | فیلد Repeater با Drag&Drop (بدون jQuery UI) | `L` | `2.5` | `[PASSED]` | `inc/Admin/Fields/types/repeater.php` +2 |
| 2.7 | تب «هدر» و «فوتر» (نوار اعلان، تلفن‌ها، شبکه‌ها، کپی‌رایت) | `L` | `2.4` | `[PASSED]` | `inc/Admin/Tabs/HeaderTab.php` +1 |
| 2.8 | تب «برند و رنگ» با متغیرهای CSS | `M` | `2.7` | `[PASSED]` | `inc/Admin/Tabs/BrandTab.php` +1 |
| 2.9 | تب «آرشیوها» (تعداد ستون، تعداد آیتم، ترتیب، فیلترها) | `M` | `2.4` | `[PASSED]` | `inc/Admin/Tabs/ArchiveTab.php` +1 |
| 2.10 | تب «اسکریپت‌ها و ابزار» (هدر/فوتر/کدهای سفارشی) | `M` | `2.4` | `[PASSED]` | `inc/Admin/Tabs/ScriptsTab.php` +1 |
| 2.11 | Section Manager: Schema سکشن‌ها و متای `_es_sections` | `L` | `2.3` | `[PASSED]` | `inc/Sections/Registry.php` +8 |
| 2.12 | ویرایشگر سکشن‌ها در ادمین + Drag&Drop | `L` | `2.11`، `2.6` | `[PASSED]` | `inc/Admin/SectionsPage.php` +6 |
| 2.13 | Renderer سکشن‌ها در فرانت + fallback | `M` | `2.11` | `[PASSED]` | `inc/Sections/Renderer.php` +1 |
| 2.14 | صادرات/واردات تنظیمات و `Reset` ایمن | `M` | `2.1` | `[PASSED]` | `inc/Admin/ImportExport.php` +2 |
| 2.15 | گیت فاز ۲: تست امنیتی + عملکرد پنل | `S` | `2.1`، `2.2`، `2.3`، `2.4`، `2.5`، `2.6`، `2.7`، `2.8`، `2.9`، `2.10`، `2.11`، `2.12`، `2.13`، `2.14` | `[PASSED]` | `temp/phase-02-custom-admin-dashboard/tests/run-phase-02-tests.sh` |

**گیت فاز:** `tests/run-phase-02-tests.sh`


## Phase 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده

`temp/phase-03-cpt-and-legacy-meta/`  
**هدف:** ثبت Native تایپ‌ها/تکسونومی‌ها با URL یکسان، و خواندن/نوشتن بی‌نقص هر ۱۸ کلید متا و ۵ Repeater فیلد JetEngine.

**18 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 3.1 | ثبت پست‌تایپ `projects` (Native) | `M` | `1.3` | `[PASSED]` | `inc/Content/PostTypes.php` |
| 3.2 | ثبت تکسونومی `project_type` | `M` | `3.1` | `[PASSED]` | `inc/Content/Taxonomies.php` |
| 3.3 | ثبت تکسونومی `city` | `M` | `3.1` | `[PASSED]` | `inc/Content/Taxonomies.php` |
| 3.4 | تصحیح Rewriteهای ووکامرس (`product_cat`, `product-tag`, سینگل) | `L` | `1.3` | `[PASSED]` | `inc/Content/Rewrites.php` +1 |
| 3.5 | Flush نسخه‌دار + مانیتور ۴۰۴ پس از فعال‌سازی | `M` | `3.4` | `[PASSED]` | `inc/Content/Rewrites.php` +3 |
| 3.6 | ستون‌های ادمین + فیلترهای دراپ‌داون پروژه‌ها | `M` | `3.1`، `3.2`، `3.3` | `[PASSED]` | `inc/Admin/ListTables.php` |
| 3.7 | ستون‌های ادمین نوشته‌ها/محصولات/دسته‌ها | `M` | `3.4` | `[PASSED]` | `inc/Admin/ListTables.php` |
| 3.8 | `MetaRepository` + `RepeaterCodec` (سازگاری دوطرفه JetEngine) | `L` | `1.3` | `[PASSED]` | `inc/Meta/MetaRepository.php` +2 |
| 3.9 | متاباکس «اطلاعات محصولات» — تب شناسه‌ها و عناوین | `L` | `2.3`، `3.8` | `[PASSED]` | `inc/Admin/Metaboxes/ProductMeta.php` +2 |
| 3.10 | تب فایل و گالری محصول + همگام‌سازی | `L` | `3.9`، `2.5` | `[PASSED]` | `inc/Admin/Metaboxes/Tabs/ProductMediaTab.php` +1 |
| 3.11 | تب روابط محصول (`project-ids`, `usage_countr`) | `L` | `3.8` | `[PASSED]` | `inc/Admin/Metaboxes/Tabs/ProductRelationsTab.php` +1 |
| 3.12 | Repeaterهای محصول: `product-feature`, `product-feature2_copy`, `usage_product`, `faq` | `L` | `3.8`، `2.6` | `[PASSED]` | `inc/Admin/Metaboxes/Tabs/ProductRepeatersTab.php` |
| 3.13 | Quick Edit برای شناسه‌ها و وضعیت فروش | `M` | `3.9` | `[PASSED]` | `inc/Admin/QuickEdit.php` |
| 3.14 | متاباکس پروژه (`second-image` تا `six-image`, تاریخ، محصولات مرتبط) | `L` | `3.1`، `3.8` | `[PASSED]` | `inc/Admin/Metaboxes/ProjectMeta.php` |
| 3.15 | ترم‌متای `product_cat`: تصویر و FAQ | `M` | `3.4` | `[PASSED]` | `inc/Admin/Metaboxes/TermMeta.php` |
| 3.16 | Hreflang (`hrefmeta_fa/en/ar`, `same_key_hrefl`) | `M` | `3.8` | `[PASSED]` | `inc/Admin/Metaboxes/Hreflang.php` |
| 3.17 | شمارنده بازدید `number2-viewc` (با fallback `number-viewc`) | `M` | `3.8` | `[PASSED]` | `inc/Meta/Views.php` |
| 3.18 | گیت فاز ۳: تست یکپارچگی داده | `S` | `3.1`، `3.2`، `3.3`، `3.4`، `3.5`، `3.6`، `3.7`، `3.8`، `3.9`، `3.10`، `3.11`، `3.12`، `3.13`، `3.14`، `3.15`، `3.16`، `3.17` | `[PASSED]` | `temp/phase-03-cpt-and-legacy-meta/tests/run-phase-03-tests.sh` +1 |

**نتیجه گیت فاز ۳ (2026-11-04):** 18 فایل تست، **66 PASS / 0 FAIL / 0 PENDING**.

**گیت فاز:** `tests/run-phase-03-tests.sh`


## Phase 4 — هدر، مگامنو، منوی موبایل و فوتر

`temp/phase-04-header-footer-navigation/`  
**هدف:** یک هدر/فوتر مشترک با حفظ ۱۰۰٪ DOM، Walkers اختصاصی و مدیریت کامل منو و مگامنو از Appearance → Menus.

**12 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 4.1 | `head` سفارشی: `wp_head`، متا، Hreflang و JSON-LD پایه | `M` | `1.9`، `2.7` | `[PENDING]` | `inc/Frontend/Head.php` +1 |
| 4.2 | `header.php`: نوار اعلان + لوگو + نوار ناوبری | `L` | `4.1`، `2.7` | `[PENDING]` | `header.php` +3 |
| 4.3 | `footer.php` + بازگشت به بالا + اطلاعات تماس | `M` | `4.2` | `[PENDING]` | `footer.php` +3 |
| 4.4 | Walker مگامنو محصولات (تب‌ها + ستون‌ها) | `L` | `4.2`، `1.11` | `[PENDING]` | `inc/Nav/MegaMenuWalker.php` +1 |
| 4.5 | Walker مگامنو پروژه‌ها (تب نوع/شهر) | `L` | `4.2`، `1.11` | `[PENDING]` | `inc/Nav/ProjectsMegaWalker.php` +1 |
| 4.6 | فیلدهای آیتم منو در Appearance → Menus | `L` | `2.7` | `[PENDING]` | `inc/Nav/MenuItemFields.php` +2 |
| 4.7 | تنظیمات نمایشی منو در داشبورد (بدون مدیریت ساختار) | `M` | `4.6` | `[PENDING]` | `inc/Admin/Tabs/NavTab.php` +1 |
| 4.8 | منوی موبایل off-canvas (`aside[data-mobile-sidebar]`) | `M` | `4.2` | `[PENDING]` | `template-parts/header/mobile-menu.php` |
| 4.9 | `template-parts/header/search-modal.php` (مارک‌آپ مودال جستجو) | `M` | `4.1` | `[PENDING]` | `template-parts/header/search-modal.php` +1 |
| 4.10 | شمارنده سبد خرید هدر (WooCommerce Fragment) | `M` | `4.2` | `[PENDING]` | `inc/Woo/HeaderCart.php` |
| 4.11 | سازگاری کامل با `main.js` (بدون تغییر فایل JS) | `M` | `4.4`، `4.5`، `4.8` | `[PENDING]` | `inc/Frontend/InlineData.php` |
| 4.12 | گیت فاز ۴: مقایسه DOM هدر/فوتر با مرجع | `S` | `4.1`، `4.2`، `4.3`، `4.4`، `4.5`، `4.6`، `4.7`، `4.8`، `4.9`، `4.10`، `4.11` | `[PENDING]` | `temp/phase-04-header-footer-navigation/tests/run-phase-04-tests.sh` +1 |

**گیت فاز:** `tests/run-phase-04-tests.sh`


## Phase 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار

`temp/phase-05-homepage-and-sections/`  
**هدف:** هر سکشن صفحه اصلی یک تسک مستقل؛ اتصال کامل به WP_Query و کارت‌های استاندارد؛ صفر محتوای هاردکد.

**21 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 5.1 | `front-page.php` + حلقه رندر ۱۵ سکشن | `M` | `2.11`، `2.13` | `[PENDING]` | `front-page.php` +2 |
| 5.2 | هیرو واریانت home1 (اسلایدر صحنه + ویدیو + نوار دسته‌بندی) | `L` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-hero.php` +1 |
| 5.3 | معرفی پروژه‌های نورپردازی شهری | `S` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-projects-intro.php` +1 |
| 5.4 | گالری نمونه‌کارها (تب اسلایدر نمونه‌کارها) | `L` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-gallery.php` +1 |
| 5.5 | درباره + شمارنده‌های آماری | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-about.php` +1 |
| 5.6 | درباره تاسیس شرکت (بلوک متنی + تصویر) | `S` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-about-establishment.php` +1 |
| 5.7 | تاریخچه شرکت (۱۳۸۰ تا ۱۴۰۵) با اسکرول تعاملی | `L` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-timeline.php` +1 |
| 5.8 | محصولات منتخب (۴ کارت دسته) | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-products.php` +1 |
| 5.9 | بنر دانلود کاتالوگ جامع | `S` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-catalog.php` +1 |
| 5.10 | خدمات تخصصی (ردیف‌های تعاملی) | `L` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-services.php` +1 |
| 5.11 | استانداردهای کیفیت و ماندگاری (۴ کارت) | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-quality.php` +1 |
| 5.12 | پشتیبانی فنی و گارانتی (نقشه/ویژگی‌ها) | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-support.php` +1 |
| 5.13 | مشتریان و سازمان‌های همکار | `S` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-clients.php` +1 |
| 5.14 | ویترین مقالات (۴ کارت) | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-blog-preview.php` +1 |
| 5.15 | CTA تماس/استعلام قیمت | `S` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-contact-cta.php` +1 |
| 5.16 | نقشه موقعیت کارخانه + اطلاعات تماس | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-map-contact.php` +1 |
| 5.17 | چرا عرفان صنعت (تمایزها و گواهینامه‌ها) | `M` | `2.11`، `2.13` | `[PENDING]` | `template-parts/home/section-why-ese.php` +1 |
| 5.18 | کارت‌های استاندارد: `card-product`, `card-project`, `card-post` | `L` | `5.8`، `5.14` | `[PENDING]` | `template-parts/cards/card-product.php` +2 |
| 5.19 | لایه کوئری بهینه صفحه اصلی (`HomeQueries`) | `L` | `5.18` | `[PENDING]` | `inc/Query/HomeQueries.php` |
| 5.20 | واریانت‌های آماده `home2` و `home3` (غیرفعال، بدون حذف) | `M` | `5.1` | `[PENDING]` | `template-parts/home/hero-variants/hero-home2.php` +1 |
| 5.21 | گیت فاز ۵: تست رندر و کوئری صفحه اصلی | `S` | `5.2`، `5.3`، `5.4`، `5.5`، `5.6`، `5.7`، `5.8`، `5.9`، `5.10`، `5.11`، `5.12`، `5.13`، `5.14`، `5.15`، `5.16`، `5.17`، `5.18`، `5.19`، `5.20` | `[PENDING]` | `temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh` +1 |

**گیت فاز:** `tests/run-phase-05-tests.sh`


## Phase 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه

`temp/phase-06-woocommerce-and-products/`  
**هدف:** Override کامل قالب‌های ووکامرس با همان UI استاتیک، اتصال همه متاهای محصول و حفظ سازگاری درگاه/افزونه‌ها.

**18 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 6.1 | راه‌اندازی پوشه `woocommerce/` و `declare_compatibility` | `M` | `1.9` | `[PENDING]` | `inc/Woo/WooSupport.php` +1 |
| 6.2 | هیرو محصول (`template-parts/product/hero.php`) | `L` | `6.1`، `5.18` | `[PENDING]` | `woocommerce/single-product.php` +1 |
| 6.3 | گالری محصول + لایت‌باکس | `L` | `6.2`، `3.10` | `[PENDING]` | `template-parts/product/gallery.php` +1 |
| 6.4 | جعبه خرید: قیمت، وضعیت `true_sale`، افزودن به سبد | `L` | `6.2` | `[PENDING]` | `template-parts/product/purchase-box.php` |
| 6.5 | جدول مشخصات فنی (۱۸ ویژگی `pa_*`) | `L` | `6.2` | `[PENDING]` | `template-parts/product/specs.php` +1 |
| 6.6 | Repeaterهای محصول در فرانت (مشخصات، ویژگی‌های کلیدی، کاربردها، FAQ) | `L` | `3.12` | `[PENDING]` | `template-parts/product/features.php` +2 |
| 6.7 | شناسه‌های قانونی (`gs1_id`, `national_code`, `hs_id`, `isic_id`, `cpc_id`, `product_id`) | `M` | `6.2` | `[PENDING]` | `template-parts/product/legal-ids.php` |
| 6.8 | دانلود کاتالوگ محصول (`product-catalog-file`) | `M` | `3.10` | `[PENDING]` | `template-parts/product/catalog.php` |
| 6.9 | پروژه‌های مرتبط محصول (`project-ids`) + کاربردها (`usage_countr`) | `M` | `3.11`، `5.18` | `[PENDING]` | `template-parts/product/related-projects.php` |
| 6.10 | تب‌های صفحه محصول (`data-prod-tab-*`) و نظرات | `L` | `6.2` | `[PENDING]` | `template-parts/product/tabs.php` +1 |
| 6.11 | محصولات مشابه و مکمل | `M` | `5.18` | `[PENDING]` | `template-parts/product/related.php` |
| 6.12 | آرشیو فروشگاه و دسته‌ها (`archive-product.php`, `taxonomy-product_cat.php`) | `L` | `6.1`، `5.18` | `[PENDING]` | `woocommerce/archive-product.php` +3 |
| 6.13 | آرشیو برچسب و برند (`taxonomy-product_tag.php`, `taxonomy-product_brand.php`) | `M` | `6.12` | `[PENDING]` | `woocommerce/taxonomy-product_tag.php` +1 |
| 6.14 | سبد خرید (`woocommerce/cart/*`) با UI استاتیک | `L` | `6.1` | `[PENDING]` | `woocommerce/cart/cart.php` +3 |
| 6.15 | تسویه حساب (`woocommerce/checkout/*`) | `L` | `6.1` | `[PENDING]` | `woocommerce/checkout/form-checkout.php` +2 |
| 6.16 | حساب کاربری (`woocommerce/myaccount/*`) + ورود پیامکی | `M` | `6.1` | `[PENDING]` | `woocommerce/myaccount/my-account.php` +3 |
| 6.17 | صفحه‌های ووکامرس بدون قالب استاتیک (`search`, `404`, `page`) | `M` | `6.12` | `[PENDING]` | `woocommerce/404.php` +1 |
| 6.18 | گیت فاز ۶: تست سرتاسری فروشگاه | `S` | `6.2`، `6.3`، `6.4`، `6.5`، `6.6`، `6.7`، `6.8`، `6.9`، `6.10`، `6.11`، `6.12`، `6.13`، `6.14`، `6.15`، `6.16`، `6.17` | `[PENDING]` | `temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh` +1 |

**گیت فاز:** `tests/run-phase-06-tests.sh`


## Phase 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی

`temp/phase-07-projects-blog-and-pages/`  
**هدف:** تک‌پروژه، آرشیو نوع/شهر، وبلاگ/تک‌نوشته/جستجو/۴۰۴، صفحه خدمات و ۶ برگه ثابت — همه با داده داینامیک.

**23 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 7.1 | `single-projects.php`: هیرو + گالری + شناسنامه | `L` | `3.1`، `3.2`، `3.3`، `5.18` | `[PENDING]` | `single-projects.php` +3 |
| 7.2 | تب‌های جزئیات پروژه + فرآیند اجرا | `L` | `7.1` | `[PENDING]` | `template-parts/project/tabs.php` |
| 7.3 | محصولات استفاده‌شده در پروژه (`mortabet_mahsole_2`) | `M` | `7.1`، `5.18` | `[PENDING]` | `template-parts/project/used-products.php` |
| 7.4 | پروژه‌های شاخص مرتبط (سایر پروژه‌های هم‌نوع) | `M` | `5.18` | `[PENDING]` | `template-parts/project/related.php` |
| 7.5 | آرشیو پروژه‌ها (`archive-projects.php`) | `L` | `7.1` | `[PENDING]` | `archive-projects.php` +2 |
| 7.6 | آرشیو نوع پروژه (`taxonomy-project_type.php`) | `M` | `3.2` | `[PENDING]` | `taxonomy-project_type.php` +1 |
| 7.7 | آرشیو شهر (`taxonomy-city.php`) | `M` | `3.3` | `[PENDING]` | `taxonomy-city.php` |
| 7.8 | آرشیو/فهرست خدمات (`services.html` → برگه «خدمات») | `M` | `2.11`، `5.18` | `[PENDING]` | `page-templates/template-services.php` +2 |
| 7.9 | صفحه جزئیات خدمت (۵ صفحه: میدان، خیابان، پل، المان، ساختمان) | `M` | `7.8` | `[PENDING]` | `page-templates/template-service-detail.php` +2 |
| 7.10 | آرشیو وبلاگ (`home.php`) با فیلتر دسته و جستجو | `L` | `5.18` | `[PENDING]` | `home.php` +3 |
| 7.11 | آرشیو دسته نوشته (`category.php`) + برچسب/نویسنده | `M` | `7.10` | `[PENDING]` | `category.php` +3 |
| 7.12 | تک‌نوشته (`single.php`): محتوا + TOC + اشتراک‌گذاری | `L` | `7.10`، `3.17` | `[PENDING]` | `single.php` +4 |
| 7.13 | نظرات تک‌نوشته + مقالات مرتبط | `L` | `7.12` | `[PENDING]` | `comments.php` +2 |
| 7.14 | نتایج جستجو (`search.php`) | `M` | `5.18` | `[PENDING]` | `search.php` +1 |
| 7.15 | صفحه ۴۰۴ اختصاصی | `M` | `4.2`، `4.3` | `[PENDING]` | `404.php` |
| 7.16 | برگه «درباره ما» (`template-about.php`) | `M` | `2.11` | `[PENDING]` | `page-templates/template-about.php` +1 |
| 7.17 | برگه «تماس با ما» (`template-contact.php`) + فرم | `M` | `2.11` | `[PENDING]` | `page-templates/template-contact.php` +1 |
| 7.18 | برگه «مجوزها و افتخارات» (`template-licenses.php`) | `M` | `2.11` | `[PENDING]` | `page-templates/template-licenses.php` +1 |
| 7.19 | برگه «سوالات متداول» (`template-faq.php`) | `L` | `2.11` | `[PENDING]` | `page-templates/template-faq.php` +2 |
| 7.20 | برگه «شرایط گارانتی» (`template-refund.php`) | `M` | `2.11` | `[PENDING]` | `page-templates/template-refund.php` +1 |
| 7.21 | برگه «فرصت‌های شغلی» (`template-recruitment.php`) | `L` | `2.11` | `[PENDING]` | `page-templates/template-recruitment.php` +1 |
| 7.22 | `page.php` عمومی + نگاشت خودکار قالب‌های برگه | `M` | `7.16`، `7.17`، `7.18`، `7.19`، `7.20`، `7.21` | `[PENDING]` | `page.php` +1 |
| 7.23 | گیت فاز ۷: رندر همه قالب‌ها + جستجوی ۴۰۴ | `S` | `7.1`، `7.2`، `7.3`، `7.4`، `7.5`، `7.6`، `7.7`، `7.8`، `7.9`، `7.10`، `7.11`، `7.12`، `7.13`، `7.14`، `7.15`، `7.16`، `7.17`، `7.18`، `7.19`، `7.20`، `7.21`، `7.22` | `[PENDING]` | `temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh` +1 |

**گیت فاز:** `tests/run-phase-07-tests.sh`


## Phase 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی

`temp/phase-08-ajax-filters-and-audit/`  
**هدف:** جستجوی زنده و فیلترهای هوشمند ایجکس، سیستم فرم اختصاصی با ذخیره/ایمیل، و ممیزی سئو/امنیت/عملکرد.

**18 تسک**

| # | تسک | اندازه | وابستگی | وضعیت | فایل‌های کلیدی |
|---|---|---|---|---|---|
| 8.1 | اندپوینت جستجوی زنده (`SearchEndpoint`) | `L` | `4.9` | `[PENDING]` | `inc/Ajax/SearchEndpoint.php` +1 |
| 8.2 | UI جستجوی زنده در مودال هدر (Debounce + حالت خالی) | `L` | `8.1`، `4.9` | `[PENDING]` | `assets/js/search.js` +1 |
| 8.3 | فیلتر ایجکس محصولات (دسته، قیمت، IP، ولتاژ، وضعیت فروش، مرتب‌سازی) | `L` | `6.12` | `[PENDING]` | `inc/Ajax/FilterEndpoint.php` +2 |
| 8.4 | فیلتر ایجکس پروژه‌ها (نوع/شهر + جستجو + مرتب‌سازی) | `L` | `5.5`، `5.6`، `7.6` | `[PENDING]` | `inc/Ajax/ProjectFilterEndpoint.php` +1 |
| 8.5 | جدول فرم‌ها + `FormRepository` + migration نسخه‌دار | `L` | `2.4` | `[PENDING]` | `inc/Forms/FormRepository.php` +1 |
| 8.6 | فرم تماس/مشاوره: پردازش، Honeypot، Nonce، ایمیل | `L` | `8.5`، `7.17` | `[PENDING]` | `inc/Forms/FormHandler.php` +2 |
| 8.7 | فرم استعلام قیمت محصول (پاپ‌آپ) | `M` | `8.6`، `6.4` | `[PENDING]` | `template-parts/product/inquiry-modal.php` +1 |
| 8.8 | فرم استخدام با آپلود رزومه | `M` | `8.6`، `7.21` | `[PENDING]` | `inc/Forms/RecruitmentHandler.php` |
| 8.9 | فرم بازخورد مشتریان (`client-feedback`) | `M` | `8.6` | `[PENDING]` | `inc/Forms/FeedbackHandler.php` |
| 8.10 | لیست پیام‌ها در داشبورد (فیلتر، وضعیت، جزئیات، دانلود فایل) | `L` | `8.5`، `8.6` | `[PENDING]` | `inc/Admin/FormsListTable.php` +1 |
| 8.11 | تنظیمات فرم‌ها (ایمیل مقصد، موضوع، autoresponder، reCAPTCHA اختیاری) | `M` | `8.10` | `[PENDING]` | `inc/Admin/Tabs/FormsTab.php` |
| 8.12 | ممیزی Hreflang و چندزبانه | `M` | `4.1` | `[PENDING]` | `inc/SEO/Hreflang.php` |
| 8.13 | ممیزی JSON-LD (Organization, WebSite, Breadcrumb, Product, Article, FAQPage) | `M` | `4.1` | `[PENDING]` | `inc/SEO/JsonLd.php` |
| 8.14 | ممیزی امنیتی: escaping، capability، nonce، sanitize، آپلود | `M` | `2.4`، `8.10` | `[PENDING]` | `temp/phase-08-ajax-filters-and-audit/tests/security-scan.php` |
| 8.15 | ممیزی عملکرد: Core Web Vitals و بودجه بارگذاری | `L` | `1.6`، `5.18`، `6.12`، `7.5` | `[PENDING]` | `tools/harness/perf-report.php` |
| 8.16 | ممیزی ریسپانسیو و RTL (بدون تغییر CSS) | `M` | `8.2`، `8.3`، `8.4`، `8.6`، `8.10` | `[PENDING]` | `tools/harness/responsive-check.md` |
| 8.17 | ممیزی سازگاری با افزونه‌های باقی‌مانده | `M` | `8.15` | `[PENDING]` | `temp/phase-08-ajax-filters-and-audit/tests/plugin-compat.php` |
| 8.18 | گیت فاز ۸ + E2E نهایی (`run-all-phases-e2e.sh`) | `S` | `8.1`، `8.2`، `8.3`، `8.4`، `8.5`، `8.6`، `8.7`، `8.8`، `8.9`، `8.10`، `8.11`، `8.12`، `8.13`، `8.14`، `8.15`، `8.16`، `8.17` | `[PENDING]` | `temp/phase-08-ajax-filters-and-audit/tests/run-all-phases-e2e.sh` +1 |

**گیت فاز:** `tests/run-phase-08-tests.sh`


---

## Gateها

| دروازه | شرط عبور | وضعیت |
|---|---|---|
| Gate 0 | تأیید اسکن و پاسخ سؤالات ابهام | ✅ PASSED |
| Gate 1 | پایان فاز ۱ — 170/170 assertion سبز، بودجه عملکرد OK | ✅ PASSED |
| Gate 2 | پایان Phase 2: 104 PASS / 0 FAIL / 0 PENDING | ✅ PASSED |
| Gate 3 | پایان Phase 3: 66 PASS / 0 FAIL / 0 PENDING | ✅ PASSED |
| Gate 4..8 | پایان هر فاز: گزارش تست + تأیید صریح کاربر | — |

## وابستگی‌های بحرانی (زنجیره مسیر)

```text
1.1 → 1.2 → 1.3 → 1.4 → 1.5 (هارنس رندر: پیش‌نیاز همه تست‌های ساختاری)
                      1.6 → 1.9 → 1.10 → 1.12
2.1 → 2.3 → 2.4 → {2.7, 2.9, 2.10, 2.14}
2.3 → 2.6 → 2.11 → 2.12 → 2.13  (Section Builder)
3.8 → {3.9..3.17} → 3.18
1.11 → 4.2 → {4.4, 4.5, 4.8} ; 4.6 (Appearance) → 4.7
2.11+2.13 → 5.1 → {5.2..5.16} ; 5.17 (کارت‌ها) → 5.18 → 5.20
6.1 → 6.2 → {6.3..6.11} ; 5.17 → 6.12 → {6.13, 6.14, 6.15, 6.16}
7.1 → {7.2,7.3,7.4} ; 7.5 → {7.6,7.7} ; 2.11 → {7.16..7.21} → 7.22
8.5 → {8.6,8.7,8.8,8.9} → 8.10 → 8.11 ; 8.1 → 8.2 ; 8.3/8.4 → 8.18
```
