# QA REFLECTION LOG — لاگ زنجیره تفکر، تست‌های خودکار و خوداصلاحی

این فایل گزارش مستمر مهندس تضمین کیفیت (QA Lead) در طول اجرای تسک‌به‌تسک پروژه است. در هر تسک، خروجی اجرای تست‌ها، خطاهای احتمالی کشف‌شده و نحوه اصلاح ریشه‌ای آن‌ها ثبت می‌شود.

---

## [STAGE 0] راه‌اندازی محیط هارنس و کالبدشکافی استاتیک
- **تاریخ:** `2026-11-03`
- **یافته‌ها و اصلاحات محیطی:**
  1. در بررسی اولیه محیط، باینری‌های `php`, `wp`, `composer`, `phpunit`, `phpcs`, `xmllint` نصب نبودند و دسترسی شبکه محدود به `github.com`, `api.github.com`, `codeload.github.com`, `registry.npmjs.org` و `pypi.org` بود.
  2. با استفاده از `git sparse-checkout` از مخازن رسمی، PHP `8.4.21`، WP-CLI، Composer `2.9.8`، PHPCS `4.0.5`، PHPUnit Runner، و `xmllint` نصب شدند.
  3. هنگام تست `phpcs --version`، وابستگی به اکستنشن `xmlwriter` برای گزارش‌های XML بررسی شد و با اصلاح `requirements.php` ابزار `phpcs` بدون خطا فعال گردید.
  4. برای اجرای تست‌های واقعی وردپرس (نه صرفاً تست استاتیک رشته‌ای)، هسته **WordPress `7.2-beta3`** به همراه درایور رسمی **`sqlite-database-integration` (`3.1.2`)** در `/opt/wp-testbed` راه‌اندازی شد تا تمامی هوک‌ها، کوئری‌ها و قالب‌ها روی دیتابیس واقعی و `WP_DEBUG = true` تست شوند.
  5. در اسکن ۱۲۸ فایل HTML مشخص شد فایل `category/magazine.html` دارای `<title>undefined</title>` است و تگ‌های `<link rel="preload">` در `index.html` درون `#root` قرار گرفته‌اند؛ هر دو مورد برای اصلاح استاندارد در `wp_head` ثبت شدند.

---

## [STAGE 1] ثبت تصمیمات کارفرما و تولید درخت کامل تست‌ها
- **تاریخ:** `2026-11-03`
- **نکات کلیدی اعمال‌شده در شروط پاس شدن (Pass Conditions):**
  - **قانون سئوی فیلترها (Task 2.2 & Task 8.1):** تست خودکار فاز ۸ صراحتاً بررسی خواهد کرد که اسکریپت فیلتر ایجکس از `history.pushState` / `history.replaceState` برای پارامترهای فیلتر استفاده نکند و تنها فیلترهای فعال‌شده در پنل ادمین رندر شوند.
  - **ریدایرکت ۳۰۱ برگه `/project/` (Task 3.1):** تست خودکار فاز ۳ بررسی خواهد کرد که درخواست به اسلاگ `project` (یا آیدی `8366`) با کد وضعیت `301` به `/projects/` هدایت شود.

---

## [Task 1.1] اسکلت هسته قالب، PSR-4 Autoloader و ثبت قابلیت‌های وردپرس/ووکامرس
- **نتیجه تست (`test-task-01.php`):** `29/29 PASSED` روی WordPress `7.2-beta3` + PHP `8.4.21`.
- **نکات فنی و پیشگیری از باگ:**
  - برای جلوگیری از هشدار منسوخ شدن `theme-compat` در وردپرس ۷ پیش از ساخته شدن `header.php` و `footer.php` در فاز ۴، در `index.php` بررسی هوشمند `locate_template('header.php')` تعبیه شد تا هم اکنون با صفر هشدار در `WP_DEBUG = true` اجرا شود و هم در فاز ۴ به‌صورت خودکار به `get_header()` / `get_footer()` متصل گردد.
  - کلاس‌های پایه بدنه استاتیک (`bg-[#f6f8f7]`, `text-[#1e293b]`, `antialiased`) و جهت `dir="rtl"` به صورت سراسری در فیلترهای `body_class` و `language_attributes` ثبت شدند.

---

## [Task 1.2] ماژول DeBloat و پاکسازی کامل زوائد وردپرس ۷
- **نتیجه تست (`test-task-02.php`):** `18/18 PASSED`.
- **نکات فنی کشف‌شده در تست واقعی روی WordPress 7.2:**
  - در وردپرس ۷.x علاوه بر هوک‌های کلاسیک گوتنبرگ، هوک‌های جدید `wp_hoist_late_printed_styles` (تولیدکننده `wp-block-styles-placeholder-inline-css` و `wp-global-styles-placeholder-inline-css`)، `wp_enqueue_emoji_styles` و `wp_enqueue_img_auto_sizes_contain_css_fix` نیز فعال بودند که همگی به صورت دقیق در `DeBloat.php` خنثی شدند تا خروجی `wp_head` به ۴ خط خالص استاندارد برسد.

---

## [Task 1.3 & Phase 1 Complete] انتقال Assets استاتیک، AssetManager و تست زنده HTTP 200
- **نتیجه تست (`test-task-03.php`):** `41/41 PASSED` | **نتیجه کل فاز ۱ (`run-phase-01-tests.sh`):** `91/91 PASSED`.
- **لاگ خوداصلاحی (Self-Correction):**
  1. در اجرای نخست `test-task-03.php`، یک مرجع SVG درونی به صورت `filter='url(%23n)'` داخل رشته `data:image/svg+xml` در `styles.css` توسط Regex به عنوان فایل نسبی `%23n` شناسایی شد؛ با افزودن `rawurldecode()`، مقدار `%23n` به `#n` (ارجاع به فیلتر SVG درون‌خطی) تبدیل و هر ۳ مسیر واقعی فونت `../fonts/vazirmatn-*.woff2` با دقت ۱۰۰٪ تایید شدند.
  2. در حلقه انتظار راه‌اندازی سرور موقت `php -S`، اولین پینگ `@fsockopen` در PHP 8+ توسط `set_error_handler` سفارشی ثبت شده بود؛ با رعایت استاندارد `!(error_reporting() & $errno)` و جداسازی مسیر `temp/` از فایل‌های اجرایی قالب، بررسی خطاهای واقعی قالب کاملاً دقیق شد.
  3. تست زنده `cURL` روی سرور محلی وردپرس برای مسیرهای `/`، `styles.css`، `main.js`، هر ۳ فونت `woff2` و لوگوی `ese-logo.svg` وضعیت `HTTP 200 OK` برگرداند.


## [PLAN v2] اتمیک‌سازی تسک‌ها (تصمیم کارفرما)
- فاز ۱ بدون هیچ تغییری فریز شد (۳ تسک، ۹۱/۹۱ پاس، کامیت‌های `6b85da5`/`f0a04e0`/`45b2f36`).
- فازهای ۲ تا ۸: از ۱۹ تسک فشرده به **۱۲۰ تسک اتمیک** (فاز۲: ۱۵، فاز۳: ۱۷، فاز۴: ۱۱، فاز۵: ۲۰، فاز۶: ۲۰، فاز۷: ۲۰، فاز۸: ۱۷)؛ هر تسک فایل مشخصات کامل (Goal/Scope/Dependencies/Target Files/Static & Data Contract/Steps/Pass Conditions/Test Scenarios/DoD/Commit) و فایل تست مستقل دارد.
- Runner هر فاز به‌صورت خودکار همه `test-task-*.php` همان فاز را اجرا می‌کند.


## [Phase 2 Complete] داشبورد ادمین و SectionManager — ۱۵/۱۵ پاس
- هر تسک با تست اختصاصی و کامیت جدا اجرا شد (2.1 منو/پوسته، 2.2 OptionsStore+Defaults، 2.3 es_option+کش، 2.4 AdminAssets مشروط، 2.5 AJAX امن، 2.6 Reset/Export/Import، 2.7-2.8 FieldRenderer/Media/Repeater، 2.9-2.12 تب‌ها با ۲۵ toggle فیلتر، 2.13-2.15 SectionManager).
- نکته WP: در تست عدم دسترسی، `wp_die` پروسه CLI را می‌بندد؛ با فیلتر `wp_die_handler` و پرتاب Exception تست شد.
- رگرسیون فاز ۱ پس از فاز ۲: سبز (91/91).


## [Phase 3 Complete] داده‌ها و متاباکس‌های Legacy — ۱۷/۱۷ پاس
- WooCommerce در تستبد نصب نیست؛ بنابراین product_cat/product_tag فقط با فیلتر `woocommerce_taxonomy_args_*` گارد شدند و تست‌ها روی post/page/projects/category واقعی اجرا شدند.
- همه کلیدهای دارای غلط تاریخی (`english_titl`,`galerry_produ`,`foure-image`,`vaule_of_variable`,`usage_countr`,`mortabet_mahsole_2`,`number2-viewc`) عیناً حفظ و در تست‌ها assert شدند.
- رگرسیون فاز ۱ و ۲ سبز.


## [Phase 5 Complete] صفحه اصلی — ۲۰/۲۰ پاس
- ۱۵ سکشن با DefaultSections روی front page حتی بدون _es_sections رندر می‌شوند؛ ترتیب/خاموشی سفارشی از SectionStorage اعمال شد.
- هیرو همه قراردادهای JS استاتیک (data-home1-scene-btn/img, inline-video, categories-grid) و خدمات همه data-service-*های main.js را حفظ می‌کند.
- محیط: سندباکس ریست شده بود؛ هارنس با setup-harness.sh بازسازی، فاز ۴ بازنویسی (11/11) و سپس فاز ۵ اجرا شد. static-mirror روی دیسک نیست؛ برش‌ها بر اساس STATIC_TEMPLATE_MAP + قراردادهای main.js انجام شد.


## [Phase 6 / Task 6.1] WooCommerce setup — PASSED
- WP/Woo testbed reinstalled after sandbox reset; WooCommerce activated from the allowed GitHub source.
- Theme integration declares WooCommerce and all three gallery supports and replaces the conventional wrapper/sidebar/summary output for the custom presentation.
- Re-ran Phases 1–5 after recovery: all green. Static mirror remains unavailable, so exact source HTML parity cannot be verified.

- Task 6.2 test validates title/subtitle/English title and breadcrumb; no theme warnings.
- Task 6.3 verifies all JS gallery hooks, exact legacy gallery key parsing, trust badges, and no theme warnings.
- Task 6.4 test passed (including negative/mode-switch coverage) with no theme warnings.
- Task 6.5 test passed (including negative/mode-switch coverage) with no theme warnings.
- Task 6.6 test passed (including negative/mode-switch coverage) with no theme warnings.
- Task 6.7 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.8 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.9 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.10 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.11 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.12 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.13 integration test passed; conditional behavior checked and theme warning collector clean.
- Task 6.14 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.
- Task 6.15 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.
- Task 6.16 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.
- Task 6.17 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.
- Task 6.18 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.
- Task 6.19 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.
- Task 6.20 test passed; coverage verified markup contracts, live/fallback data, and no theme warnings.

## [Phase 6 Follow-up] Woo plugin compatibility and archive filters
- Added the native Woo variation dropdown helper path for Variation Swatches integration.
- Expanded archive filter markup to honor admin toggles and available product attribute terms; added serialization of product attribute term slugs on cards.
- Replaced mini-cart client-only quantity changes with nonce-protected AJAX updates against WC cart, and fixed exact `data-*="true"` selectors required by main.js.
- All affected task tests pass. Note the sandbox Woo source checkout lacks the built vendor autoloader, so cart integration uses controlled test doubles; real hosted WooCommerce runtime remains necessary for final live smoke test.


## [Phase 6 Gate] WooCommerce — 20/20 PASSED; Phase 1–5 regressions green
- `run-phase-06-tests.sh`: 20/20; PHP lint of 26 Woo/theme PHP targets clean; Node syntax check of `assets/js/main.js` clean.
- Assertions cover exact meta keys/typos, gallery fallback chain, sale/inquiry split, variation swatches/quantity helper, 18 attribute rendering and dashboard toggle, taxonomy term FAQ, WC cart markup, nonce-checked live cart AJAX, fragments, checkout billing/shipping/order-review hooks and shortcode integration.
- **Important environment limit:** sandbox reset deleted `/opt/wp-testbed`/PHP and `static-mirror`; `temp/setup-harness.sh` restored WP/PHP. Public GitHub tree has no static HTML mirror, so strict full-DOM parity remains unverifiable. GitHub WooCommerce source is an unbuilt checkout missing `vendor/autoload_packages.php`, so the suite uses controlled test doubles for cart/product APIs and checks standard Woo hooks. Need a production-grade Woo installation for final runtime checkout/gateway plugin smoke test.
- Reset recovery snapshot: `3fe9ed6`; follow-up implementation commits are on active branch `arena/23abd651-erfansanat-theme-ai`.

## Phase 7 / Task 7.1 — PASSED
- RED/green test verifies project title, excerpt, taxonomy terms, and breadcrumb with no theme warnings.
- Task 7.2 test verifies gallery/lightbox JS hooks, `foure-image` typo, project identity metadata and no theme warnings.
- Task 7.3 test covers content, related product/project cards, and conditional omission of empty used-products block.
- Task 7.4 test checks project archive controls, pagination hooks, cards and project taxonomy data; no warnings.
- Task 7.5 verifies both taxonomy routes, term archive content and filter state; no warning/regression.
- Task 7.6 verified category/search card data and all three WordPress archive templates; Tasks 7.1–7.6 regression set passed.
- Task 7.7 dedicated header test passed; the complete Phase 7 task tests through 7.7 pass.
- Task 7.8 initially exposed WordPress `sanitize_title` percent-encoding Persian; switched generated IDs to Unicode-safe letters/numbers/hyphen normalization. Task tests 7.1–7.8 all pass.
- Task 7.9 verified share hooks, related-card output and conditional comments behavior. After restoring the harness and activating the theme, Task tests 7.1–7.9 all pass with no theme warnings.
- Task 7.10 mixed search test passed for product/project/post results and empty state; Tasks 7.1–7.10 regressions pass.
- Task 7.11 tests verify response-template structure, search field, key links and no theme warnings; Tasks 7.1–7.11 pass.
- Task 7.12 tests persisted SectionManager fields and the empty-metadata five-section fallback; Tasks 7.1–7.12 regression set passes. Static mirror unavailable; exact about-page DOM parity remains unverified.
- Task 7.13 verifies all configured contact values, section metadata, Google Maps search URL and the form slot; Tasks 7.1–7.13 regressions pass.
- Task 7.14 verifies three SectionManager sections, both certificate/honor galleries, item labels, lightbox hooks and no warnings; Tasks 7.1–7.14 pass.
- Task 7.15 verifies category accordions, question/answer output, FAQPage schema and warning-free rendering; Tasks 7.1–7.15 regression set passes.
- Task 7.16 verifies terms/process/contact sections and configured repeater values without theme warnings; Tasks 7.1–7.16 pass.
- Task 7.17 verifies all four recruitment tabs/panels, configured content and application slot; `node --check` and PHP lint are clean; Tasks 7.1–7.17 pass.
- Task 7.18 verifies five child page cards, process/contact sections, no warnings and PHP lint; Tasks 7.1–7.18 regressions pass.
- Task 7.19 verifies service breadcrumb, configured details, related project/product cards and CTA; Tasks 7.1–7.19 pass without theme warnings.
