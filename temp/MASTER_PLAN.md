# MASTER PLAN — نقشه راه کامل فازها و تسک‌ها

> وضعیت‌ها: `[PENDING]` · `[IN_PROGRESS]` · `[TESTING]` · `[PASSED]` · `[BLOCKED]`
> چرخه ۶ گامی هر تسک: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & CHECK → UPDATE MEMORY + Git Commit

## STAGE 0 — تجهیز هارنس و اسکن ✅
- [x] نصب ابزارها: Node 22 · PHP 8.4.27 (wasm شیم `/home/user/tools/php`) · curl · git · unzip
- [x] استخراج و اسکن میرور استاتیک (۱۲۸ HTML)
- [x] ساخت ۴ فایل حافظه اولیه + `.gitignore` + کامیت `94d4986`
- [x] پرسش‌نامه ابهام‌زدایی (Q1-Q4) → پاسخ کارفرما دریافت شد

## STAGE 1 — ساخت درخت فازها ✅
- [x] آپدیت `PROJECT_MEMORY.md` و `STATIC_TEMPLATE_MAP.md` با پاسخ‌های کارفرما
- [x] ساخت `MASTER_PLAN.md` و `QA_REFLECTION_LOG.md`
- [x] ساخت ۸ پوشه فاز با تسک‌ها، شروط پاس شدن و اسکلت تست‌ها

## Phase 1 — زیرساخت هسته قالب، Autoloader و انتقال Assets `[PENDING]` → `temp/phase-01-core-and-assets/`
- Task 1.1 `[PENDING]` اسکلت پایه قالب (`style.css`,`functions.php`,`index.php`) + معماری OOP در `inc/` با `ErfanSanat\Theme`
- Task 1.2 `[PENDING]` `DeBloat.php` — حذف گوتنبرگ/ایموجی/تگ‌های زائد `wp_head`
- Task 1.3 `[PENDING]` انتقال Assets + `AssetManager.php` (Enqueue شرطی + `filemtime`)
- 🔬 `tests/run-phase-01-tests.sh`

## Phase 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder `[PENDING]` → `temp/phase-02-custom-admin-dashboard/`
- Task 2.1 `[PENDING]` پنل «داشبورد اختصاصی عرفان صنعت» (`inc/Admin/ThemeOptions.php` — RTL، تب‌بندی، ذخیره AJAX، nonce/capability)
- Task 2.2 `[PENDING]` تب‌های سراسری (هدر، مگامنو، لوگو، نوار اعلان، فوتر، تماس، سوشال، برند، اسکریپت سفارشی، آرشیوها) + Fallback برابر میرور
- Task 2.3 `[PENDING]` موتور `SectionManager.php` (روشن/خاموش، Drag & Drop ترتیب، فیلد Media/لینک/آیکون/Repeater، رندر پویا)
- 🔬 `tests/run-phase-02-tests.sh`

## Phase 3 — CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده `[PENDING]` → `temp/phase-03-cpt-and-legacy-meta/`
- Task 3.1 `[PENDING]` CPT `projects` + تکسونومی `project_type`,`city` + **CPT `services` (تصمیم Q2)** + rewrite دقیق `product_cat` + فیلترهای ستون ادمین
- Task 3.2 `[PENDING]` `ProductMetaboxes.php` — تمام کلیدهای متا (gs1_id…usage_countr) + ۴ Repeater با فرمت `{"item-0":{…}}`
- Task 3.3 `[PENDING]` متاباکس پروژه‌ها (`second-image`…`six-image`,`finish-pro-time`,`mortabet_mahsole_2`) + `product_cat` FAQ/thumbnail + Hreflang + بازدید `number2-viewc`
- 🔬 `tests/run-phase-03-tests.sh`

## Phase 4 — هدر، مگامنو، منوی موبایل و فوتر `[PENDING]` → `temp/phase-04-header-footer-navigation/`
- Task 4.1 `[PENDING]` `header.php`/`footer.php` از برش میرور + wp_head/wp_footer/body_class/wp_body_open + Hreflang head
- Task 4.2 `[PENDING]` Nav Walkers: `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing`
- 🔬 `tests/run-phase-04-tests.sh` (مقایسه DOM با مرجع استاتیک)

## Phase 5 — صفحه اصلی و کامپوننت‌های ماژولار `[PENDING]` → `temp/phase-05-homepage-and-sections/`
- Task 5.1 `[PENDING]` برش ۱۴ سکشن `index.html` به `template-parts/home/section-*.php` + `front-page.php` با رندر پویای SectionManager
- Task 5.2 `[PENDING]` اتصال سکشن‌ها به WP_Query و داشبورد (کارت‌ها: `card-product/card-project/card-post/card-service`)
- 🔬 `tests/run-phase-05-tests.sh`

## Phase 6 — ووکامرس، محصول تکی و آرشیو فروشگاه `[PENDING]` → `temp/phase-06-woocommerce-and-products/`
- Task 6.1 `[PENDING]` `single-product.php` — گالری، تمام متاها، ۱۸ `pa_*`، Repeaterها، true_sale، دیدگاه‌ها
- Task 6.2 `[PENDING]` آرشیوها (`archive-product`, `taxonomy-product_cat` + FAQ دسته, `taxonomy-product_tag`) + صفحه‌بندی
- Task 6.3 `[PENDING]` استایل cart/checkout/my-account (+auth/voorodak) با پوسته میرور — ووکامرس کامل (تصمیم Q4)
- 🔬 `tests/run-phase-06-tests.sh`

## Phase 7 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها `[PENDING]` → `temp/phase-07-projects-blog-and-pages/`
- Task 7.1 `[PENDING]` `single-projects.php` + `archive-projects.php` + `taxonomy-project_type.php` + `taxonomy-city.php`
- Task 7.2 `[PENDING]` وبلاگ (`home.php`/`archive.php`/`category.php`)، `single.php` (TOC/Share/بازدید/مرتبط/دیدگاه)، `search.php`، `404.php`
- Task 7.3 `[PENDING]` قالب برگه‌ها (about/contact/licenses/faq/refund/recruitment) + CPT `services` (single + listing) + لندینگ‌ها (arya/smartelement) از کامپوننت‌های میرور (تصمیم Q3)
- 🔬 `tests/run-phase-07-tests.sh`

## Phase 8 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی `[PENDING]` → `temp/phase-08-ajax-filters-and-audit/`
- Task 8.1 `[PENDING]` AJAX Live Search + Smart Filters محصولات (متنی، دسته، قیمت، true_sale، `pa_*`، مرتب‌سازی) و پروژه‌ها (type/city + شمارنده) — هدف <۲۰۰ms
- Task 8.2 `[PENDING]` سیستم فرم‌ها (تماس، استعلام، استخدام، بازخورد) + ذخیره در ادمین + Honeypot/Nonce + ایمیل
- Task 8.3 `[PENDING]` ممیزی سئو/پرفورمنس/امنیت + `run-all-phases-e2e.sh` — صفر خطا با WP_DEBUG
- 🔬 `tests/run-phase-08-tests.sh`

## دروازه‌های تایید کارفرما (Gates)
1. ✅ پایان Stage 0 (پاسخ Q1-Q4 دریافت شد)
2. ⏳ **پایان Stage 1 — در انتظار تایید شروع Phase 1**
3. پس از هر تسک/فاز طبق دستور کارفرما
4. پایان Phase 8 (ممیزی نهایی و آمادگی استقرار)
