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
- Task 7.20 test verifies configured landing sections, the `landing` navigation location, contact CTA, and real post-content fallback; Tasks 7.1–7.20 pass. Phase 7 suite 20/20. Regression Phase 1–7 suites pass with WooCommerce 11.2.1 active. Stop at the Phase 7 approval gate; no Phase 8 work started.
- User supplied a built WooCommerce 11.2.1 ZIP (uploaded filename ended in `.png`, but ZIP signature confirmed); installed and activated it in the ephemeral WordPress testbed. Phase 6 suite now passes 20/20. Live integration exposed two defects: returned Woo quantity HTML was not echoed (and a WC-only helper was called with a test double), and the empty mini-cart fragment lacked its wrapper while `wp_kses_post` stripped its `data-*` selector. Fixed rendering/fallback and preserved only the required data attributes with a scoped KSES allowlist. Full regression suites Phase 1–7 all pass.
- Phase 8 Task 8.1: replaced the empty skeleton test with RED/GREEN coverage, added `AjaxController` and registered it as a theme service. It provides the shared logged-in/public endpoint, exact `erfan_sanat_ajax` nonce, safe recursive input sanitation, a route-filter dispatch point, standardized JSON envelopes, and additive runtime nonce data. Targeted test and PHP lint passed; Phase 8 continues.
- Phase 8 Task 8.2: replaced the skeleton with live WordPress/WooCommerce fixtures. Added the `product_search` route; it uses an escaped title-or-native-`_sku` query, bounds query length/results, and renders the existing product-card template to preserve its established markup. SKU, title, empty/short, content-only exclusion, and inquiry-state tests pass; Task 8.1 and Phase 1 regression remain green.
- Phase 8 Task 8.3: added grouped live search for published `projects` and `post` records using the existing project/post card templates. The existing modal markup uses presence-only attributes, while its JS selectors incorrectly required `="true"`; corrected selectors without changing the DOM, then connected both search endpoints with the dedicated nonce, 250 ms debounce, and stale-response protection. Targeted search tests, Tasks 8.1–8.2 regressions, Phase 1 regression, JS syntax check, and diff whitespace check pass. The static mirror is absent; reused current theme card components and existing modal hooks.
- Phase 8 Task 8.4: completed the formerly empty test harness with live product/category fixtures, then implemented a whitelisted WP_Query builder. Search, `product_cat`, exact `_price` numeric ranges, allowed sorting (price/rating/legacy `number2-viewc`), invalid sort fallback, and invalid/negative price bounds are covered. Targeted tests passed. The test uses unique temporary category slugs and cleans up its records.
- Phase 8 Task 8.5: added live query tests for all/online/call modes and missing `true_sale` metadata. The `online` branch includes legacy missing-meta products because the existing card treats anything other than exact `false` as online; the `call` branch matches exact `true_sale=false`. Invalid modes fall back to all. Task test passes.
- Phase 8 Task 8.6: implemented a allow-listed attribute-taxonomy query modifier for all 18 exact `pa_*` slugs from `FilterRegistry`. Each has an independent assertion; unsafe/unregistered keys are ignored, enabled_filters disables clauses, selections are sanitized/deduped, and clauses AND-merge with existing category filtering. Targeted test passed.
- Phase 8 Task 8.7: added the product-filter route composing Tasks 8.4–8.6, paged WP_Query results, existing product-card HTML, item IDs/count, WP pagination links and the existing empty state. Pagination hrefs are fragments for the later JS handler to prevent navigation; no query-string filters are generated. Target tests cover page count, numeric sort, sale filter, and empty results.
- Phase 8 Task 8.8: connected existing product archive controls to the AJAX endpoint with debounce, stale-response protection, JSON nonce/action, multi-category and all-attribute serialization, page interception, count/empty/pagination updates, and no history/query-string mutation. Strengthened server-side enforcement for disabled search/category/price/sort/sale controls; adapted category query building to multiple slugs. Tests assert the 18 registered attributes and disabled-control behavior. JS syntax and targeted tests pass.
- Phase 8 Task 8.9: implemented `ProjectFilterQuery` and `ProjectFilterEndpoint` for exact `project_type`/`city` taxonomies, published projects, search, sort, pagination, existing project cards and empty state. Wired the existing project archive controls to the shared nonce-protected route (search debounce, stale-response protection, paging button delegation), removed prior URL-parameter reads from that archive, and preserved its static DOM. Targeted query/client tests and JS syntax pass.
- Phase 8 Task 8.10: implemented the blog AJAX route and wired existing blog search/category/reset/pagination controls to it. It uses published posts, safe category resolution, existing post cards, count/paging/empty state, and request race protection. A nonexistent term initially behaved like no category filter with a negative term ID; changed invalid category/type values to an explicit `post__in=[0]` constraint and tested the empty state.
- Phase 8 Task 8.11 investigation: expanded `initArchiveDrawer` with shared control synchronization and clear/apply/close behavior against existing data-hook contracts. The repository scan found no archive `data-archive-drawer` markup, so the UI cannot be activated or browser-verified without inventing DOM/CSS; task is recorded BLOCKED (not passed) under the strict static-to-dynamic rule. Continue with independent Task 8.12.
- Phase 8 Task 8.12: added private `es_form_message` CPT with admin list, type and read/unread columns, and capability/nonce-protected status toggling. `FormManager` validates the dedicated form nonce, honeypot and 3-per-10-minute IP transient, sanitizes field keys/values, and saves message records. Live WP tests cover valid/invalid nonce, honeypot, persistence, sanitation, read status and rate limiting.
- Phase 8 Task 8.13: added contact and product-quote form routes. Existing contact form is AJAX-intercepted without modifying its DOM; a valid product context from its current contact CTA selects the product-inquiry handler. Both sanitize/validate name, phone and message, call the shared nonce/honeypot/rate-limited storage manager, and send `wp_mail` notifications; the existing paragraph becomes the success/error live region. Added a dedicated form nonce to the frontend runtime and standardized route `WP_Error` JSON responses. Tests use `pre_wp_mail` to avoid real mail.
- Phase 8 Task 8.14: added the `recruitment_submission` route, file MIME/extension/5 MB validation, private resume storage and email attachment. Tests reject fake PDF content, oversized files and disallowed extensions. Repository scan confirms the recruitment template has only a mailto CTA and no file input/form, so end-to-end upload UI remains BLOCKED; no new form DOM/CSS was fabricated.
- Phase 8 Task 8.15: implemented a backend client-feedback route with name/message/rating validation, safe storage in the admin submissions CPT and notification email; range/persistence tests pass. Repository audit found no client-feedback markup or styling, so UI is explicitly BLOCKED pending static source and was not fabricated.
- Phase 8 Task 8.16: added registered SEO fallback service for `_rank_math_*` and unprefixed legacy fields only when SEOPress/Rank Math are inactive; active plugin title/canonical/description outputs pass through unchanged. Escaped description fallback and canonical filter tested. Existing `HreflangTags` exact `hrefmeta_fa/en/ar` mapping and FAQ template JSON-LD remain untouched and no duplicate JSON-LD was introduced.
- Phase 8 Task 8.17 final gate: the full `temp/run-all-phases-e2e.sh` executed all Phase 01–08 suites, all PHP lint, `node --check`, and TODO/FIXME scan; every automated check passed. The runner then reported `[E2E BLOCKED]` and exited 2 because task-11/task-14/task-15 lack static drawer/resume/feedback markup. Kept task 8.17 and the Phase 8 suite BLOCKED instead of claiming release readiness.
- Phase 8 continuation: after user selected the native theme approach (not Elementor), implemented a shared responsive archive filter drawer for products/projects/blogs using existing utility styles and exact JS data hooks. Static source checks, `node --check`, and npm `php-parser` syntax checks passed. PHP/WordPress integration test could not run because `php` and `/opt/wp-testbed` are absent in this sandbox; Task 8.11 is kept TESTING. Updated Task 8.17 readiness gate to consider every non-PASSED task, not merely `[BLOCKED]` labels.
- Phase 8 Task 8.14 native recruitment form: replaced the mailto-only slot with a validated multipart UI and AJAX integration, preserving the existing private resume storage/notification route. Added mobile-friendly fields and accepted extension messaging; Persian/Arabic phone digits normalize server-side. PHP/WP live test unavailable in current sandbox (no `php`, no `/opt/wp-testbed`), so status remains TESTING.
- Phase 8 Task 8.15 native client-feedback UI: placed the form after the existing homepage client names and wired accessible status feedback to the established `client_feedback` route; no new persistence contract or database key was introduced. Static source assertions/JS syntax/PHP parser will be checked; real WordPress submit/browser tests unavailable in current sandbox, so status TESTING.
- Phase 8 continuation verification (2026-11-04): resolved the earlier PHP runtime blocker using isolated WordPress Playground PHP 8.5 with WordPress 7.2-beta4 and WooCommerce 11.2.1. Phase 8 Tasks 8.1–8.16 passed; Phase 6 WooCommerce regressions passed 20/20. Task 8.17 gate assertions passed under Playground PHP after making its shell syntax check portable; host `bash -n` separately passes. An actual WordPress HTTP/DOM harness ran 33 assertions against rendered theme pages and `main.js`, including client-feedback AJAX/CPT storage and successful real multipart PDF recruitment upload. `WP_DEBUG=true` exposed deprecated `finfo_close()` in PHP 8.5; removed the redundant close and reran targeted tests cleanly. A true native-browser visual check is still blocked because Chromium cannot load without compatible NSPR/NSS system libraries; jsdom results are not claimed as pixel verification. Tasks 8.11/8.14/8.15 remain TESTING and Task 8.17 remains BLOCKED until native-browser review.

- Phase 8 final acceptance update: user approved continuing after reviewing the live preview. Tasks 8.11/8.14/8.15 are closed PASSED based on that acceptance plus the WordPress runtime and 33 DOM/live-HTTP interaction checks. Task 8.17 now evaluates explicit prerequisite statuses; final readiness status scan will be rerun. A direct attempt to run `run-all-phases-e2e.sh` exited 127 at Phase 1 (`php: command not found`) because host PHP CLI is absent. Record the individual Playground suite results and host shell checks accurately; do not claim the full runner passed.

- Phase 8 final gate (2026-11-04): after recording the user’s preview approval, marked Tasks 8.11, 8.14, and 8.15 PASSED; reran `test-task-17.php` and the host status scan. Every Phase 8 prerequisite is explicitly PASSED and the Phase 8 final readiness gate passes. Full `run-all-phases-e2e.sh` itself remains unexecuted due absent native PHP CLI; this limitation is explicit in MASTER_PLAN/task-17 and is not reported as a full-runner pass.

## Whole-system regression refresh — 2026-11-04
- Refreshed Phase 2 Tasks 2.1–2.15 (15/15), Phase 3 (17/17), Phase 4 (11/11), Phase 5 (20/20), and Phase 7 (20/20) under WordPress Playground PHP 8.5. Phases 6 (20/20) and 8 (17/17, including final gate) retain their successful earlier runs. Total task coverage across Phases 1–8: 123/123.
- Phase 1 Tasks 1.1–1.3 passed through temporary copies; native `php -l`, WP-CLI activation, and the test's PHP built-in server/cURL segment cannot run in PHP-WASM. The theme runtime assertions passed; all 279 PHP files parsed independently, and the 10 live homepage/archive/static-asset HTTP endpoints returned 200 via Node fetch. Phase 2 Tasks 2.1–2.2 likewise used temporary copies to replace only external `php -l`; runtime assertions passed and syntax parsing was performed separately.
- Phase 7 Task 7.20 first exposed an environment-specific assertion hard-coded to `http://localhost:8080/contact/`. The template correctly preserves the configured `/contact/` relative URL. Updated only the test expectation to assert the relative contract and reran the task successfully. No production code changed.
- The unmodified `temp/run-all-phases-e2e.sh` remains blocked at Phase 1 with exit 127 because host `php` is unavailable. Therefore, this is a manually orchestrated, phase-by-phase refresh—not a successful run of the native all-phase script. Native Chromium visual verification also remains unavailable; do not describe jsdom checks as browser-engine verification.

## ادغام فونت Vazirmatn در پنل پیشخوان — 2026-11-04
- RED/GREEN در Task 2.4: ابتدا assertionهای سه فایل فونت محلی، scope داشبورد و مدیال رسانه اضافه شد و روی کد موجود شکست خورد؛ سپس سه `@font-face` با unicode-range صحیح و استایل scope‌شده برای متن/فرم‌ها/مدیال افزوده شد. استثناهای Dashicons و آیکن‌های رسانه حفظ شدند.
- اجرای دوباره Phase 2: **15/15 پاس** تحت Playground PHP 8.5. CSS با Prettier parse شد؛ stylesheet مدیریت و سه فایل WOFF2 از سرور زنده HTTP 200 دادند.
- محدودیت: در این محیط Chromium بومی نصب نیست؛ صحت selectorها و دریافت فایل‌ها تست شد، اما ادعای مرور بصری بومی نمی‌شود.
- دامنه تکمیل‌شده: علاوه بر صفحه داشبورد، enqueue مشروط برای متاباکس‌های theme در post/page/product/projects، taxonomyهای دسته/برند/شهر/نوع پروژه، و فهرست محصولات/پروژه‌ها اضافه شد. Task 2.4 با `WP_Screen` ساختگی enqueue را در متاباکس محصول، taxonomy و فهرست پروژه به‌صورت runtime تأیید می‌کند و هم‌زمان عدم enqueue در CPT نامرتبط را می‌سنجد؛ تست وجود فایل‌های فونت روی دیسک و signature برابر `wOF2` را نیز بررسی می‌کند. سوئیت Phase 2 پس از این گسترش 15/15 پاس است.
- پوشش آخرین بخش باقی‌مانده: چون `DeBloat` قالب Gutenberg را غیرفعال کرده و ویرایشگر کلاسیک فعال است، همان stylesheet از طریق `add_editor_style()` به TinyMCE معرفی شد؛ selectorهای بوم بصری و فیلدهای title/content/excerpt در حالت متنی هم تست می‌شوند. Task 2.4 در حالت runtime وجود stylesheet در `get_editor_stylesheets()` و URL محلی آن را تأیید کرد.
- پس از تغییر editor، سوئیت کامل Phase 2 دوباره اجرا شد: 15/15 پاس؛ رگرسیون Phase 1 در سه تست موقت نیز بعد از تغییر `ThemeSetup` سبز ماند (فقط اجرای PHP built-in server/cURL در Task 1.3 به fetch زنده Node واگذار شد).

## داشبورد ادمین: تکمیل UX و قابلیت‌ها — 2026-11-04
- RED: تست‌ها خلأ ToolsTab/SectionsTab، عدم handler برای media/repeater، نداشتن template در repeater خالی، و عدم بررسی binary/validation import را آشکار کردند. GREEN: دو تب ثبت شدند؛ داشبورد سربرگ/ناوبری/فرم responsive و دسترس‌پذیر گرفت؛ save status/error handling، media picker، repeater reorder/reindex، JSON tools و sections overview تکمیل شدند.
- رفع باگ import: decode associative دوباره حفظ شد تا `enabled_filters` nested map از backup برگردد؛ import object-only و server cap برابر 1 MiB شد. `shop_per_page`, `projects_per_page`, `blog_per_page` به بازه 1–100 clamp می‌شوند. فیلد کد سفارشی LTR و number fields دارای min/max شدند.
- سوئیت Phase 2 پس از تغییرات 15/15 پاس؛ تست DOM jsdom مسیرهای save و خطای سرور، nonce/status، انتخاب/حذف media، add/move/reindex/remove repeater، export، import معتبر/نامعتبر و لغو reset را گذراند. تمام 281 PHP بدون خطای parser؛ node syntax و CSS parse پاس.
- دو تست قدیمی Phase 2 شامل `exec php -l` هستند؛ فقط نسخه‌های موقت آن دو در PHP-WASM با placeholder shell اجرا شدند، و syntax واقعی همه فایل‌ها جداگانه parser شد. مرور بصری Chromium-native هنوز در دسترس نیست.
