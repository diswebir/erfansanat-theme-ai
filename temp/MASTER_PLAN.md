# MASTER PLAN — نقشه راه ۸ فاز پروژه

وضعیت کلی: `STAGE 1 PLAN BUILT — ALL TASKS PENDING`

| فاز | عنوان | تسک‌ها | وضعیت |
|---|---|---|---|
| `phase-01-core-and-assets` | Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک | 1.1 [PENDING], 1.2 [PENDING], 1.3 [PENDING] | [PENDING] |
| `phase-02-custom-admin-dashboard` | Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder | 2.1 [PENDING], 2.2 [PENDING], 2.3 [PENDING] | [PENDING] |
| `phase-03-cpt-and-legacy-meta` | Phase 3: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس live | 3.1 [PENDING], 3.2 [PENDING], 3.3 [PENDING] | [PENDING] |
| `phase-04-header-footer-navigation` | Phase 4: تبدیل هدر، مگامنو، منوی موبایل و فوتر | 4.1 [PENDING], 4.2 [PENDING] | [PENDING] |
| `phase-05-homepage-and-sections` | Phase 5: صفحه اصلی و کامپوننت‌های ماژولار | 5.1 [PENDING], 5.2 [PENDING] | [PENDING] |
| `phase-06-woocommerce-and-products` | Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه | 6.1 [PENDING], 6.2 [PENDING], 6.3 [PENDING] | [PENDING] |
| `phase-07-projects-blog-and-pages` | Phase 7: پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها | 7.1 [PENDING], 7.2 [PENDING], 7.3 [PENDING] | [PENDING] |
| `phase-08-ajax-filters-and-audit` | Phase 8: جایگزینی Native فیلتر/جستجو/فرم‌ها + ممیزی نهایی | 8.1 [PENDING], 8.2 [PENDING], 8.3 [PENDING] | [PENDING] |

---

## Phase 1: زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک  (`temp/phase-01-core-and-assets/`)
### Task 1.1 [PASSED]
- **شرح:** اسکلت پایه قالب (style.css, functions.php, index.php) + معماری OOP در inc/ با Namespace ErfanSanat\Theme
- **Pass Conditions:** فعال‌سازی بدون خطا؛ php -l؛ add_theme_support برای title-tag, post-thumbnails, woocommerce, html5
- **فایل‌ها:** `temp/phase-01-core-and-assets/tasks/task-1-1.md` | `temp/phase-01-core-and-assets/tests/test-task-1-1.php`
### Task 1.2 [PASSED]
- **شرح:** ماژول DeBloat.php — حذف استایل/اسکریپت‌های گوتنبرگ، ایموجی و تگ‌های اضافی wp_head
- **Pass Conditions:** صفر بودن فایل‌های CSS/JS بلوک‌های وردپرس در سورس خروجی
- **فایل‌ها:** `temp/phase-01-core-and-assets/tasks/task-1-2.md` | `temp/phase-01-core-and-assets/tests/test-task-1-2.php`
### Task 1.3 [PASSED]
- **شرح:** انتقال CSS/JS/فونت/آیکون به assets/ + AssetManager.php با Enqueue شرطی per-page و نسخه filemtime
- **Pass Conditions:** لود 200 تمام assetها؛ عدم شکستگی url(...) داخل CSS
- **فایل‌ها:** `temp/phase-01-core-and-assets/tasks/task-1-3.md` | `temp/phase-01-core-and-assets/tests/test-task-1-3.php`

## Phase 2: فریم‌ورک داشبورد اختصاصی ادمین و Section Builder  (`temp/phase-02-custom-admin-dashboard/`)
### Task 2.1 [PASSED]
- **شرح:** پنل داشبورد اختصاصی (inc/Admin/ThemeOptions.php) — RTL، تب‌بندی، AJAX Save، بدون افزونه
- **Pass Conditions:** current_user_can + Nonce؛ ذخیره در option واحد erfansanat_theme_options؛ کش در es_option()
- **فایل‌ها:** `temp/phase-02-custom-admin-dashboard/tasks/task-2-1.md` | `temp/phase-02-custom-admin-dashboard/tests/test-task-2-1.php`
### Task 2.2 [PASSED]
- **شرح:** تب‌های مدیریتی سراسری (هدر، مگامنو، لوگو، فوتر، تماس، شبکه‌ها، رنگ برند، اسکریپت‌ها)
- **Pass Conditions:** مقدار پیش‌فرض = محتوای استاتیک؛ اعمال آنی در فرانت‌اند
- **فایل‌ها:** `temp/phase-02-custom-admin-dashboard/tasks/task-2-2.md` | `temp/phase-02-custom-admin-dashboard/tests/test-task-2-2.php`
### Task 2.3 [PASSED]
- **شرح:** موتور SectionManager.php — روشن/خاموش، Drag&Drop ترتیب، ویرایش کامل contenido سکشن‌ها
- **Pass Conditions:** ذخیره متادیتای ساختاریافته؛ Media/Link/Icon/Repeater؛ رندر پویا بر اساس ترتیب
- **فایل‌ها:** `temp/phase-02-custom-admin-dashboard/tasks/task-2-3.md` | `temp/phase-02-custom-admin-dashboard/tests/test-task-2-3.php`

## Phase 3: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس live  (`temp/phase-03-cpt-and-legacy-meta/`)
### Task 3.1 [PASSED]
- **شرح:** register_post_type('projects') + register_taxonomy('project_type','city') + rewrite دقیق product_cat + فیلترهای ستون ادمین
- **Pass Conditions:** تطابق ۱۰۰٪ URLها با live؛ بدون 404
- **فایل‌ها:** `temp/phase-03-cpt-and-legacy-meta/tasks/task-3-1.md` | `temp/phase-03-cpt-and-legacy-meta/tests/test-task-3-1.php`
### Task 3.2 [PASSED]
- **شرح:** ProductMetaboxes.php — تمام کلیدهای live (gs1_id...usage_countr) + repeaters
- **Pass Conditions:** خواندن/نوشتن بی‌نقص داده‌های JetEngine (serialized + JSON) با همان فرمت
- **فایل‌ها:** `temp/phase-03-cpt-and-legacy-meta/tasks/task-3-2.md` | `temp/phase-03-cpt-and-legacy-meta/tests/test-task-3-2.php`
### Task 3.3 [PASSED]
- **شرح:** متاباکس پروژه‌ها (second-image تا six-image incl. foure-image) + faq/thumbnail دسته + Hreflang + number2-viewc
- **Pass Conditions:** تست read/write روی تمام کلیدها در پست‌ها و ترم‌های تست
- **فایل‌ها:** `temp/phase-03-cpt-and-legacy-meta/tasks/task-3-3.md` | `temp/phase-03-cpt-and-legacy-meta/tests/test-task-3-3.php`

## Phase 4: تبدیل هدر، مگامنو، منوی موبایل و فوتر  (`temp/phase-04-header-footer-navigation/`)
### Task 4.1 [PASSED]
- **شرح:** برش header/footer استاتیک → header.php و footer.php + Hreflang در <head>
- **Pass Conditions:** حفظ ۱۰۰٪ DOM/CSS + wp_head/body_class/wp_body_open/wp_footer
- **فایل‌ها:** `temp/phase-04-header-footer-navigation/tasks/task-4-1.md` | `temp/phase-04-header-footer-navigation/tests/test-task-4-1.php`
### Task 4.2 [PASSED]
- **شرح:** Custom Nav Walkers برای main-mega-menu, mobile-menu, tolidat-sherkat, landing
- **Pass Conditions:** HTML منو منطبق بر استاتیک؛ JS مگامنو/موبایل کار کند
- **فایل‌ها:** `temp/phase-04-header-footer-navigation/tasks/task-4-2.md` | `temp/phase-04-header-footer-navigation/tests/test-task-4-2.php`

## Phase 5: صفحه اصلی و کامپوننت‌های ماژولار  (`temp/phase-05-homepage-and-sections/`)
### Task 5.1 [PASSED]
- **شرح:** برش سکشن‌های index.html → template-parts/home/section-*.php + front-page.php با SectionManager
- **Pass Conditions:** رندر پیش‌فرض = استاتیک؛ تغییر ترتیب/حذف از ادمین اعمال شود
- **فایل‌ها:** `temp/phase-05-homepage-and-sections/tasks/task-5-1.md` | `temp/phase-05-homepage-and-sections/tests/test-task-5-1.php`
### Task 5.2 [PASSED]
- **شرح:** اتصال سکشن‌ها به WP_Query (دسته‌ها، محصولات برگزیده، پروژه‌ها، مقالات، آمار) + کارت‌های استاندارد
- **Pass Conditions:** صفر محتوای هاردکد؛ card-product/card-project/card-post
- **فایل‌ها:** `temp/phase-05-homepage-and-sections/tasks/task-5-2.md` | `temp/phase-05-homepage-and-sections/tests/test-task-5-2.php`

## Phase 6: قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه  (`temp/phase-06-woocommerce-and-products/`)
### Task 6.1 [PASSED]
- **شرح:** single-product.php — گالری galerry_produ، zireonvan، english_titl، IDهای قانونی، pa_* (۱۸)، repeaters، پروژه‌های مرتبط، faq
- **Pass Conditions:** true_sale true/false korrekt؛ عدم نمایش سکشن خالی
- **فایل‌ها:** `temp/phase-06-woocommerce-and-products/tasks/task-6-1.md` | `temp/phase-06-woocommerce-and-products/tests/test-task-6-1.php`
### Task 6.2 [PASSED]
- **شرح:** archive-product.php + taxonomy-product_cat/tag — توضیحات و faq دسته + صفحه‌بندی
- **Pass Conditions:** کارکرد /shop/ و /product_cat/... با کارت‌های استاتیک
- **فایل‌ها:** `temp/phase-06-woocommerce-and-products/tasks/task-6-2.md` | `temp/phase-06-woocommerce-and-products/tests/test-task-6-2.php`
### Task 6.3 [PASSED]
- **شرح:** استایل cart/checkout/my-account منطبق بر پوسته + سازگاری درگاه
- **Pass Conditions:** چرخه خرید کامل بدون تداخل اسکریپت
- **فایل‌ها:** `temp/phase-06-woocommerce-and-products/tasks/task-6-3.md` | `temp/phase-06-woocommerce-and-products/tests/test-task-6-3.php`

## Phase 7: پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها  (`temp/phase-07-projects-blog-and-pages/`)
### Task 7.1 [PENDING]
- **شرح:** single-projects.php + archive-projects + taxonomy-project_type/city — گالری second..six-image، finish-pro-time، mortabet_mahsole_2
- **Pass Conditions:** رندر منطبق بر استاتیک
- **فایل‌ها:** `temp/phase-07-projects-blog-and-pages/tasks/task-7-1.md` | `temp/phase-07-projects-blog-and-pages/tests/test-task-7-1.php`
### Task 7.2 [PENDING]
- **شرح:** home.php/archive/category/single (TOC، اشتراک‌گذاری، number2-viewc، مرتبط‌ها) + search.php + 404.php
- **Pass Conditions:** ثبت خودکار بازدید در number2-viewc
- **فایل‌ها:** `temp/phase-07-projects-blog-and-pages/tasks/task-7-2.md` | `temp/phase-07-projects-blog-and-pages/tests/test-task-7-2.php`
### Task 7.3 [PENDING]
- **شرح:** page-templates برای about/contact/licenses/faq/refund/recruitment/arya/smartelement — قابل ویرایش از ادمین
- **Pass Conditions:** تمام متون/تصاویر/تب‌ها/آکاردئون‌ها از پنل ادمین
- **فایل‌ها:** `temp/phase-07-projects-blog-and-pages/tasks/task-7-3.md` | `temp/phase-07-projects-blog-and-pages/tests/test-task-7-3.php`

## Phase 8: جایگزینی Native فیلتر/جستجو/فرم‌ها + ممیزی نهایی  (`temp/phase-08-ajax-filters-and-audit/`)
### Task 8.1 [PENDING]
- **شرح:** Live AJAX Search + Smart Filters (محصولات: متن/دسته/قیمت/true_sale/pa_*؛ پروژه‌ها: type/city) — جایگزین JetSearch/JetSmartFilters
- **Pass Conditions:** <200ms؛ امن در برابر SQLi/XSS؛ آپدیت کارت‌ها بدون رفرش
- **فایل‌ها:** `temp/phase-08-ajax-filters-and-audit/tasks/task-8-1.md` | `temp/phase-08-ajax-filters-and-audit/tests/test-task-8-1.php`
### Task 8.2 [PENDING]
- **شرح:** سیستم مدیریت فرم‌ها (تماس، استعلام، استخدام، بازخورد) + ذخیره در ادمین + Honeypot/Nonce + ایمیل
- **Pass Conditions:** ثبت امن + لیست پیام‌ها در ادمین
- **فایل‌ها:** `temp/phase-08-ajax-filters-and-audit/tasks/task-8-2.md` | `temp/phase-08-ajax-filters-and-audit/tests/test-task-8-2.php`
### Task 8.3 [PENDING]
- **شرح:** ممیزی نهایی سئو/پرفورمنس + run-all-phases-e2e.sh (SEOPress، Hreflang، JSON-LD، escaping، ریسپانسیو)
- **Pass Conditions:** ۱۰۰٪ پاس با صفر خطا و صفر Warning در WP_DEBUG
- **فایل‌ها:** `temp/phase-08-ajax-filters-and-audit/tasks/task-8-3.md` | `temp/phase-08-ajax-filters-and-audit/tests/test-task-8-3.php`
