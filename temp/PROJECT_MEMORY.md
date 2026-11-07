# حافظه زنده پروژه

- وضعیت: `Phases 1–7 regression PASSED; Phase 8 Task 8.1 PASSED; Tasks 8.2–8.3 pending separate approval.`
- شاخه فعال: `arena/8fc7b5b7-erfansanat-theme-ai`.
- میرور پیوست در `static-mirror/`؛ 128 فایل HTML و assets محلی.
- محیط: Node و curl موجود؛ PHP native/WP-CLI/PHPUnit/PHPCS/xmllint موجود نیستند؛ PHP WASM 8.5.12 به‌صورت محلی در `node_modules/` فراهم شد و برای lint و smoke tests قابل استفاده است. WordPress production/native دائمی نیست؛ برای آزمون، WordPress 7.2-beta4 (build 65250) و SQLite با PHP WASM در `node_modules/.cache/ese-wp-test/` نصب/فعال است؛ این مسیر در Git نادیده گرفته می‌شود.
- اسناد مرجع: `PROJECT_GOAL.md`, `DATA_BLUEPRINT.md`, `STATIC_TEMPLATE_MAP.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md`.
- تصمیم‌های کاربر: صفحات هم‌نام دو مسیر محصول مقایسه/ادغام شوند؛ خدمات یک CPT مستقل؛ تمام سکشن‌های منتخب قابل ویرایش؛ فیلترها AJAX.
- Stage 1: درخت 8 فاز و 22 تسک ساخته شد؛ task briefها، 22 contract tests، 8 phase runners و 1 all-phases runner تولید شدند.
- آخرین تسک‌های پاس‌شده: Task 8.1؛ Phases 1–7 تکمیل و regression شده‌اند. قالب‌های پروژه، بلاگ، جستجو، 404، برگه‌های ویژه و landingها نیز آماده‌اند.
- Task 8.1 پاس شد؛ اجرای Task 8.2 (فرم‌ها) و Task 8.3 (ممیزی/E2E) همچنان خارج از محدوده است و پیش از شروع نیاز به تأیید کاربر دارد. تست هدفمند Task 8.1: `PHP_BIN="$PWD/node_modules/.bin/php-wasm-cli" PHASE_TASKS=01 sh temp/phase-08-ajax-filters-and-audit/tests/run-phase-tests.sh`. Regression فازهای 1–7 نیز پاس است. Harness: `temp/setup-wordpress-test.sh`.
- توجه: اسلاگ نهایی خدمات باید با URLهای زنده تطبیق داده شود؛ برای محصولات URL canonical مطابق بلوپرینت حفظ می‌شود.

- Task 1.1 QA: PHP WASM 8.5.12 روی 26 فایل PHP lint پاس؛ contract test پاس؛ WordPress integration test واقعی (WordPress 7.2-beta3 + SQLite) و render fallback پاس؛ WP_DEBUG log خالی است.
- Task 1.1 `[PASSED]`: PHP lint (26 فایل)، contract test و فعال‌سازی/بارگذاری در WordPress/SQLite تستی پاس شدند؛ WP_DEBUG log خالی بود.

- Task 1.3: 27 asset mirror copied to `assets/`; CSS/JS enqueued with filemtime; all 27 paths returned HTTP 200.
- Task 1.2: frontend DeBloat verified in WordPress; admin hooks intact; WP_DEBUG log empty.

## Phase 2 نتیجه
- Task 2.1 پاس: `ThemeOptions` صفحه RTL، AJAX با capability+nonce، schema validation/sanitization، option `erfansanat_theme_options` و helper `es_option()` با cache invalidation.
- Task 2.2 پاس: تب‌های عمومی، هدر/مگامنو، تماس، فوتر، شبکه‌های اجتماعی، رنگ‌ها، کدهای سفارشی (ذخیره فقط با `unfiltered_html`)، و پیش‌فرض آرشیو؛ CSS variables/scripts و WooCommerce/archive hooks متصل شدند.
- Task 2.3 پاس: `SectionManager` متای ساخت‌یافته و خصوصی صفحات، nonce+capability، schemaهای صفحه‌های منتخب، media/link/icon/video/repeaters، مرتب‌سازی/visibility و fallbackهای استاتیک را پشتیبانی می‌کند.
- تست کامل: `sh temp/setup-wordpress-test.sh` پاس (Phase 1 + WordPress 7.2-beta3/SQLite، WP_DEBUG خالی)؛ `sh temp/phase-02-custom-admin-dashboard/tests/run-phase-tests.sh` پاس (سه integration test + PHP WASM lint).

## Phase 3 نتیجه
- Task 3.1 پاس: projects/services CPT، project_type/city taxonomy، خدمات با URLهای استاتیک `.html` و product/product_cat/product_tag rewriteهای WooCommerce؛ dropdownهای پروژه و ستون ترجمه و flush فقط هنگام تغییر پوسته.
- Task 3.2 پاس: metabox محصولات برای تمام کلیدهای blueprint با املای دقیق؛ خواندن/نوشتن array, JSON و serialized بدون تخریب shape؛ گالری legacy fallback و nonce/capability.
- Task 3.3 پاس: project gallery modern/legacy sync، `finish-pro-time`, `mortabet_mahsole_2`; taxonomy thumbnail/FAQ و hreflang; post hreflang و `number2-viewc` با fallback به `number-viewc`.
- Phase 3 runner: PHP WASM lint و سه WordPress integration tests پاس؛ WP_DEBUG خالی. Regression نهایی: همهٔ PHP/JS/shell syntax checks و setup/Phase 1 smoke + Phase 2 + Phase 3 runners پاس؛ WP_DEBUG خالی. Phase 4 approval gate حفظ شود.


## Phase 4 / Task 4.1 نتیجه
- کاربر Phase 4 را با «ادامه بده» مجاز کرد؛ اجرای task-by-task و approval gate برای Task 4.2 حفظ شد.
- `header.php` اسکلت سند و هدر واقعی میرور شامل desktop menus, mobile drawer, search/cart overlays را حفظ می‌کند؛ `wp_head`, `body_class`, `wp_body_open` اجرا می‌شوند. مسیرهای static mirror به URLهای WordPress تبدیل و محصولات `products/` به canonical `/product/` هدایت می‌شوند.
- تنظیمات پوسته به branding/logo، topbar/message/links، announcement، sticky header، mega-menu headings/CTA و footer description/badges/quick-links/contact/copyright متصل شدند. دسته‌های footer از `product_cat` می‌آیند و در نبودشان fallback نمایش داده می‌شود.
- hreflang legacy برای post و term با self URL فارسی و en/ar metadata خروجی می‌شود؛ هنگام فعال بودن SEOPress خروجی قالب متوقف می‌شود تا duplicate alternates ایجاد نشود.
- Test fail-first ابتدا نبودن header/footer/renderer را گزارش کرد. Integration test نهایی header/footer options, selectors/hooks, URL mapping, unsafe scheme rejection, post/term hreflang و SEOPress guard را پاس کرد.
- Regression نهایی: PHP/JS/shell syntax checks و Phases 1–3 runners و Task 4.1 پاس؛ WP_DEBUG خالی. Task 4.2 pending و نیازمند تأیید کاربر است.


## Phase 4 / Task 4.2 نتیجه
- کاربر اجرای Task 4.2 را با «اوکیه ادامه بده» تأیید کرد؛ Task 4.2 به‌تنهایی پیاده‌سازی شد؛ در همان مقطع Phase 5 منتظر approval بود (بعداً مجاز و تکمیل شد).
- چهار WordPress menu location در `Navigation::register_menus`: `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing`.
- Context-aware `Walker_Nav_Menu` desktop mega triggers/panels, product/project inner tabs, recursive mobile accordions, product lists و landing anchors را رندر می‌کند؛ تمام data selectors لازم برای `main.js` حفظ شدند.
- Header منوهای desktop/mobile را از locationهای نصب‌شده می‌گیرد؛ نبود mobile location به main menu fallback می‌کند؛ نبود هر دو به markup کامل static mirror برمی‌گردد. `tolidat-sherkat` در فوتر مصرف می‌شود و `landing` برای برگه‌های Arya/SmartElement قابل رندر است.
- Task 4.2 integration test منوها را با ساختار nested WordPress menus ساخت، خروجی header/footer را بررسی کرد، locationهای خالی و fallback را آزمود و fixtureها را cleanup کرد.
- Regression نهایی: PHP/JS/shell syntax checks، setup/Phase 1 smoke test، Phase 2, Phase 3, Phase 4 runners همگی پاس؛ WP_DEBUG خالی.

## Phase 5 / Task 5.1 نتیجه
- کاربر Phase 5 را تأیید کرد؛ Task 5.1 ابتدا به‌تنهایی انجام شد. در پایان آن گام، Task 5.2 نیازمند تأیید بود؛ سپس کاربر آن را مجاز کرد و Task 5.2 نیز پاس شد.
- `front-page.php` از SectionManager برای خروجی `<main>` استفاده می‌کند. ۱۵ سکشن در `static-mirror/index.html` به partialهای مستقل در `template-parts/home/` منتقل شده‌اند: `hero`, `projects`, `project-archive`, `about`, `company-identity`, `history`, `products`, `catalog`, `services`, `benefits`, `after-sales`, `clients`, `blog-preview`, `contact`, `location`.
- برای برگهٔ انتخاب‌شده به‌عنوان `page_on_front`، SectionManager schema خانه و مسیر partialهای home را استفاده می‌کند؛ سکشن legacy `gallery` برای sanitization سازگاری حفظ شده ولی در پیش‌فرض صفحهٔ خانه نمایش داده نمی‌شود.
- یک ایراد قدیمی در ترتیب پیش‌فرض SectionManager اصلاح شد: schema با کلید ID بود، درحالی‌که order به‌صورت کلید رشته‌ای ذخیره و به صفر تبدیل می‌شد؛ اکنون ترتیب عددی متوالی است.
- Task 5.1 در زمان تحویل، هر ۱۵ partial را بایت‌به‌بایت با سکشن میرور تطبیق داد. Task 5.2 کارت‌های داده‌محور را جایگزین بخش‌های تکرارشونده کرد؛ تست Task 5.1 اکنون ریشه‌ها/ویژگی‌های section، بخش‌های ثابت، ترتیب/visibility و ارتقای تنظیمات قدیمی صفحهٔ خانه را بررسی می‌کند.
- Task 5.1 و Phases 1–4 regression پاس شدند؛ lint تمام فایل‌های PHP و WP_DEBUG خالی بودند.

## Phase 5 / Task 5.2 نتیجه
- کاربر با «ادامه بده» Task 5.2 را پس از پایان Task 5.1 مجاز کرد؛ فقط همین تسک اجرا شد.
- `HomepageData` فهرست محدود و cache/transient-versioned برای `projects`, `posts`, `product` و دسته‌های `product_cat`/`project_type` می‌سازد؛ تغییر پست/تکسونومی cache version را invalidate می‌کند. محصولات از `wc_get_products` (featured، سپس اخیر) و در نبود WooCommerce از `WP_Query` می‌آیند.
- Hero/video settings و slide projectها، دسته‌های زنده و تصاویر term؛ محصولات منتخب؛ پروژه‌های شاخص و آرشیو دسته‌بندی‌شده؛ مقالات اخیر؛ آمار پروژه/محصول و مقدارهای تنظیم‌شده؛ و کارت‌های مشتریان از repeater قابل‌ویرایش به قالب وصل شدند. Hero/product/category media به مدیای WP/legacy متا متصل است.
- سه کارت اشتراکی `template-parts/cards/card-{product,project,post}.php` از داده‌های WP/WooCommerce استفاده می‌شوند. تکرارهای بدون داده خروجی خالی و بدون fatal/placeholder دارند؛ کارت سفارش سفارشی محصول و داده‌های مشتریان به Section Builder وصل‌اند.
- Test integration تعداد/محدودیت queryها، taxonomy/ترم، invalidation بعد از انتشار پروژه، مقادیر مشتری/آمار editable، مسیر Woo API (stub در صورت نبود Woo در harness) و خروجی واقعی homepage را آزمایش می‌کند. Task 5.1 و 5.2، regression Phases 1–4، PHP lint، JavaScript/shell syntax و WP_DEBUG خالی پاس شدند.
- Phase 5 کامل شد؛ پس از تأیید کاربر Task 6.1 اجرا و پاس شد؛ یادداشت نتایج آن در بخش Phase 6 این فایل آمده است.

## Phase 6 / Task 6.1 result — Single-product page
- User explicitly approved Task 6.1 after the open PR; work stayed limited to this task. Task 6.1 is `[PASSED]`; approval is still required before Task 6.2.
- `single-product.php` renders WooCommerce product details, identifier metas, subtitle/English title, pre-sale notice, catalog link, product features/usages, 18 exact `pa_*` attributes, project/type relations, FAQs, reviews, and a bounded set of related products.
- `true_sale=true` uses WooCommerce's standard price/add-to-cart templates, preserving simple and variable-product behavior. `true_sale=false` hides checkout purchase UI and shows configured phone/email inquiry links.
- Gallery order begins with the featured image and uses `ProductMetaboxes::get_product_gallery_ids()` for exact `galerry_produ`, WooCommerce gallery and legacy `gallery-filed` / `image-2`…`image-6`; gallery, lightbox, tabs and FAQ selectors match `assets/js/main.js`.
- Empty content sections are omitted (gallery has a clear missing-image state); output is escaped, with post description passed through the normal WordPress content filters. Product reviews use `wc_get_template('single-product-reviews.php')` when WooCommerce is active, with a WordPress comments fallback.
- Task 6.1 WordPress integration test covers online/inquiry branches, gallery priority/fallbacks, attributes, identifiers, relations, FAQ visibility, escaped content, WooCommerce review template integration and related products. The disposable WordPress harness does not install WooCommerce, so Woo APIs are exercised with test doubles.
- Task 6.1 test, regression Phases 1–4, Task 5.1, PHP syntax checks, and WP_DEBUG log all passed. Phase 6 runner supports `PHASE_TASKS=01` to run only the authorized task.

## Phase 6 / Task 6.2 — WooCommerce archives
- Shop, category and tag templates retain WordPress/WooCommerce main queries and archive hooks; `template-parts/woocommerce/product-archive.php` renders dynamic cards, filter controls, pagination, empty states, category thumbnail/description/FAQ metadata and escaped output.
- `assets/js/main.js` handles `menu_order`, price ascending/descending, popularity and inquiry purchase types; the filter UI remains ready for Phase 8 AJAX without adding AJAX behavior in Phase 6.
- Integration fixtures cover shop/category/tag queries and rewrite bases, card data, filters, pagination hooks, term metadata, escaping and empty archives. Task 6.2 test passed.

## Phase 6 / Task 6.3 — WooCommerce Cart, Checkout and My Account
- `page.php` scopes a dedicated wrapper to WooCommerce cart/checkout/account conditional pages and runs the stored WordPress content via `the_content()`; all other regular pages continue through the prior `index.php` fallback.
- No WooCommerce cart, checkout or account transaction template override was added. Core shortcodes/blocks, gateway rendering, lifecycle, and plugin hooks remain in control; before/after main-content actions are preserved.
- RTL/responsive visual-only styling covers classic WooCommerce markup and cart/checkout blocks. On transactional pages, the theme localStorage purchase journey does not initialize; its header cart button routes to the native Woo cart URL, leaving native checkout/cart forms and their JS untouched.
- Task 6.3 integration test covers native shortcode content for all three contexts, wrapper/cart URL, no unsafe template overrides, styling contracts, and no JS interception of native Woo forms. Passed. The Phase 6 runner detects explicit `FAIL` output as well as nonzero statuses because PHP WASM does not always propagate PHP `exit(1)`.
- Phase 6 full runner (Tasks 6.1–6.3) passed; WP_DEBUG log was cleared before run and remained empty. Phase 1–5 regression tests and syntax/diff checks also passed after updating the asset migration test to recognize reviewed Phase 6 stylesheet/JS extensions. Phase 7 was subsequently authorized and passed; Task 8.1 was later authorized and passed, while Tasks 8.2–8.3 remain out of scope pending approval.

## Phase 7 / Tasks 7.1–7.3 result
- WordPress integration tests pass for projects and taxonomies, blog/search/404 and safe view counting, plus all eight special-page templates and their editable SectionManager content/repeaters.
- `page.php` routes canonical and Persian legacy page slugs to named templates while keeping WooCommerce transaction-page routing intact. Arya and SmartElement schemas are registered. Recruitment repeaters include categories and retain the existing `data-recruit-*` filtering hooks; FAQs use native `<details>`; page content and shortcodes remain available for Phase 8's secure form integration.
- Phase 7 runner checks test output for `FAIL` (PHP WASM may return zero for PHP-level failures) and enforces an empty WP_DEBUG log. Tasks 7.1–7.3 plus the Phase 1–6 regression runners passed; targeted PHP lint passed.
- Test harness used disposable WordPress 7.2-beta4/build 65250 with SQLite and PHP WASM. No WooCommerce transaction templates or custom form submission handlers were added.

## Phase 8 / Task 8.1 result — AJAX live search and archive filters
- Implemented `ErfanSanat\Theme\Ajax\Filters` with public and authenticated AJAX hooks for `ese_live_search`, `ese_filter_products`, and `ese_filter_projects`; handlers accept POST only, verify the localized nonce, bound/sanitize inputs, whitelist ordering, escape rendered output, and use WP_Query/prepared search SQL.
- Product archive AJAX supports text/SKU, category, min/max price, `true_sale`, `pa_ip-rating`/`pa_input-voltage`, sort, clear, and pagination. Project archive supports text, `project_type`, city, count, popularity/newest sorting, and pagination. Responses replace results in place; JS debounces and aborts stale requests, updates counts/status/history, and falls back to local UI when AJAX is unavailable.
- `test-task-01.php` uses WordPress fixtures for composable filters, SQL/XSS safety, nonce dispatch, SKU lookup, sort/pagination and endpoint timing. Task test passed at max 2ms PHP WASM fixture time (query/render only; network latency excluded). Regression Phases 1–7 passed; WP_DEBUG empty; `node --check` and `git diff --check` passed.
- Task 8.1 is `[PASSED]`. Do not begin Task 8.2 or 8.3 without additional user authorization.
