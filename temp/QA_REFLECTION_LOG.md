# QA / اجرا و تصمیمات مستند

> این فایل گزارش قابل ممیزیِ کارها و نتایج تست است؛ شامل زنجیرهٔ تفکر خصوصی نیست.

## Stage 0 — اسکن و بوت‌استرپ
- ZIP پیوست با magic header مربوط به ZIP شناسایی شد، با وجود پسوند `.png`، و به `static-mirror/` استخراج شد.
- بررسی امنیتی مسیرهای آرشیو: مسیر absolute و `..` رد شد؛ استخراج فقط در پوشهٔ ورودی انجام شد.
- موجودی استخراجی: 128 HTML، 1 CSS، 1 JS، 3 فونت WOFF2 و مجموعهٔ تصاویر/ویدئو.
- ناسازگاری مستندشده: README تعداد صفحات را هم 37 و هم 128 ذکر می‌کند؛ شمارش فیزیکی فایل‌ها 128 HTML است.
- ابزارها: Node و curl موجود؛ PHP, WP-CLI, PHPUnit, PHPCS و xmllint در محیط پیدا نشدند. بنابراین فعلاً آزمون اجرایی PHP/WordPress ممکن نیست.
- فایل‌های مرجع اولیه ایجاد شدند و نگاشت شامل عنوان و شمار header/main/section/footer و شناسه‌های سکشن هر فایل است.

## پاسخ‌های کاربر
- صفحات `product/` و `products/`: مقایسه و ادغام تفاوت‌ها.
- خدمات: CPT مستقل.
- سکشن‌های منتخب: همه محتوا و رسانه قابل ویرایش.
- فیلترها: AJAX.

## Stage 1 — ساخت برنامهٔ آزمون
- نقشهٔ 8 فاز و 22 تسک ایجاد شد؛ برای هر تسک task brief، شروط پذیرش و PHP contract-test scaffold و برای هر فاز runner شل تولید شد.
- تست‌ها در این مرحله skeleton/قراردادند و تا پیاده‌سازی فایل‌های قالب انتظار fail دارند.
- نتیجهٔ اجرای تست: اجرا نشد؛ هنوز قالب WordPress ساخته نشده و PHP در محیط موجود نیست.
- وضعیت خطاهای پیاده‌سازی: موردی وجود ندارد؛ کدنویسی قالب بنا بر گیت تأیید هنوز آغاز نشده است.


## Phase 1 / Task 1.1 — اسکلت پایه
- پیاده‌سازی: `style.css`, `functions.php`, `index.php`, `inc/Autoloader.php`, `inc/Theme.php`.
- اجرای lint: PHP WASM CLI 8.5.12؛ همهٔ 26 فایل PHP در checkout بدون خطای syntax.
- اجرای contract test تسک 1.1: 12 assertion پاس.
- اجرای bootstrap smoke test با stubهای WordPress: autoloader اجرا شد و چهار theme support (`title-tag`, `post-thumbnails`, `woocommerce`, `html5`) ثبت شدند.
- راهکار محیط: چون native PHP/MySQL موجود نبود، PHP WASM 8.5.12 و WordPress 7.2-beta3 از مخزن رسمی GitHub در `/tmp` مستقر شدند؛ SQLite driver تستی نیز استفاده شد. هیچ‌کدام به Git اضافه نشدند.
- فعال‌سازی: WordPress نصب شد، `switch_theme` انجام شد و بارگذاری بعدی theme را با `wp_get_theme()` و `current_theme_supports()` تأیید کرد؛ fallback قالب با درخواست خانه نیز HTML ساختاری معتبر رندر کرد (21,857 بایت).
- خطاها: WP_DEBUG log خالی؛ PHP lint روی 26 فایل PHP و contract test تسک پاس.
- نتیجه: Pass Conditions تسک 1.1 پاس؛ وضعیت `[PASSED]`. نصب تستی در `/tmp/ese-wp-test/` برای تسک‌های بعدی قابل استفاده است. اسکریپت بازتولید محیط: `temp/setup-wordpress-test.sh` (Node/npm + PHP WASM + WordPress + SQLite؛ هیچ دیتابیس/باینری حجیمی داخل مخزن نگه نمی‌دارد).


## Phase 1 / Task 1.2 — DeBloat
- تست fail-first در WordPress نشان داد global styles و emoji در head وجود دارند.
- `inc/DeBloat.php` فقط در frontend اجرا می‌شود؛ WordPress 7.2 برای global styles یک enqueue دیرهنگام در `wp_footer` و placeholderهای CSS نیز دارد، که پس از آشکارشدن در تست به‌صورت هدفمند حذف شدند.
- تست frontend (با enqueue اجباری block styles)، admin isolation، حفظ hook سفارشی `wp_head` و WP_DEBUG خالی — پاس.

## Phase 1 / Task 1.3 — انتقال Assets
- CSS, JS, 3 WOFF2 و تمام 22 تصویر/SVG/MP4 بدون تغییر باینری کپی شدند: 27 فایل در `assets/`.
- `AssetManager` بارگذاری frontend-only CSS/JS را با `filemtime` انجام می‌دهد؛ feed/admin مستثنا هستند. مسیرهای CSS `../fonts` حل می‌شوند.
- تست WordPress تأیید کرد URLهای stylesheet/script با نسخهٔ filemtime تولید می‌شوند؛ تست HTTP روی تمام 27 دارایی وضعیت 200 گرفت.
- PHP WASM lint و WordPress 7.2-beta3/SQLite integration پاس؛ WP_DEBUG خالی.
- وضعیت Task 1.3: `[PASSED]`.


## Phase 2 / Task 2.1 — Theme Options
- تست Task 2.1 قبل از implementation ایجاد و syntax-check شد؛ نبود کلاس/Helper انتظار fail را داد.
- پیاده‌سازی صفحه تنظیمات RTL، action AJAX فقط برای `manage_options`، nonce، validate/sanitize روی whitelist schema، ذخیرهٔ non-autoload و `es_option()` با cache invalidation.
- Integration test: helper defaults, sanitize متن/email, reject unknown keys, nonce/capability, admin menu و UI — پاس.

## Phase 2 / Task 2.2 — Global settings
- تست fail-first با سنجش defaults, cache, CSS output و archive filters نوشته شد؛ سپس schema تکمیل شد.
- مقدارهای اولیه از static mirror: تلفن 031-91091011، فروش 09137976915، ایمیل info@erfansanat.com، social links، footer copy/badges/quick links، header announcement/mega-menu، palette و archive counts.
- CSS custom properties در `wp_head` با validate/escape، script snippets فقط برای کاربر دارای `unfiltered_html`، و pagination/order defaults متصل شدند.
- Integration test defaults, ذخیره/خواندن option، رنگ frontend، سقف تعداد محصولات، fallback مرتب‌سازی و head script — پاس.

## Phase 2 / Task 2.3 — Section Builder
- قبل از implementation test گسترش یافت تا post meta registration، sanitize، nonce-protected save, drag/order, visibility, media/link/icon/video/repeater, rendering و fallback را بررسی کند؛ initial run نبود implementation را نشان داد.
- `SectionManager` برای خانه و برگه‌های منتخب (about/contact/services/FAQ/licenses/recruitment/refund_returns/default) schema ثبت می‌کند؛ متا `_es_page_sections` با capability, nonce و sanitization ثبت می‌شود.
- Editor native شامل media picker، repeater rows، drag/drop، collapse و visibility است. `get_renderable_sections()` فقط سکشن‌های فعال را به ترتیب ذخیره‌شده برمی‌گرداند؛ `render_sections()` از partials، filter یا trusted template fallback پشتیبانی می‌کند.
- یک مورد واقعی هنگام integration: وردپرس sanitize callback را با meta key به‌عنوان آرگومان دوم صدا می‌زند، نه post ID؛ امضای callback اصلاح و schema sanitizer طوری گسترش یافت که union شناخته‌شدهٔ schemaها را نگه دارد؛ تست ذخیرهٔ واقعی و WP_DEBUG هر دو پاس شدند.
- Final verification: setup WordPress + Phase 1 tests پاس؛ Phase 2 runner سه تست و lint پاس؛ WP_DEBUG log خالی. Phase 2 تمام شد؛ گیت تأیید Phase 3 برقرار است.


## Phase 3 / Task 3.1 — CPTs, taxonomies, URLs
- Test integration قبل از implementation بازنویسی شد: WordPress assertions برای CPT args/supports، taxonomy object types/rewrite، WC filters، admin dropdowns/column و activation-only flush. Fail-first نبودن ContentTypes را ثابت کرد.
- Native `projects` و `services`, `project_type` و `city` ثبت شدند؛ Woo filters single product `/product/`, archive `shop`, product_cat `product_cat` و product-tag `product-tag` را حفظ می‌کنند. خدمات `.html` static-mirror URLs نیز با permalink filters/rewrite rules پشتیبانی می‌شوند.
- Dropdown فیلتر پروژه‌ها و ستون `same_key_hrefl` اضافه شد؛ rewrite flush فقط روی `after_switch_theme`. Test WordPress/PHP WASM پاس.

## Phase 3 / Task 3.2 — Product legacy metadata
- Integration test پیش از کدگذاری گسترش یافت؛ تمام exact blueprint keys، repeater JSON/serialization، nonce/capability، sanitization و gallery fallbacks بررسی شدند.
- `ProductMetaboxes` schema/registry و native editor برای identifiers, true_sale, catalog URL, gallery, project relations, `usage_countr`, چهار repeaters را پیاده کرد؛ `english_titl`, `galerry_produ`, `vaule_of_variable` و سایر املای اصلی حفظ شدند.
- Array, JSON و PHP serialized repeaters در round-trip شکل اولیه را حفظ می‌کنند. Woo gallery و کلیدهای `gallery-filed`, `image-2`…`image-6` fallback هستند.
- Integration test واقعی شامل save معتبر و رد nonce نامعتبر — پاس.

## Phase 3 / Task 3.3 — Project, term, hreflang, views
- Test fail-first شامل پروژه، term و content metadata، گالری مدرن/legacy، FAQ و thumbnail، hreflang و شمارنده بود.
- `ProjectMetaboxes` کلیدهای `second-image`, `three-image`, `foure-image`, `five-image`, `six-image`, `finish-pro-time`, `mortabet_mahsole_2` را حفظ می‌کند و gallery جدید را با پنج کلید قدیمی sync می‌کند.
- `TermMetaboxes` به `product_cat` کلیدهای `thumbnail` و `faq` با زیرکلیدهای دقیق، و به taxonomyهای منتخب hreflang اضافه کرد. `ContentMeta` hreflang را روی post/page/product/projects و views را روی post/projects/product ثبت می‌کند؛ `number2-viewc` مقدم است و در غیابش `number-viewc` خوانده می‌شود.
- nonce/capability و escaping form output با WP integration بررسی شدند؛ سه phase tests + lint پاس، WP_DEBUG خالی. Phase 3 تمام؛ approval پیش از Phase 4 لازم است.


## Phase 4 / Task 4.1 — Header/footer shell
- کاربر پس از approval gate صریحاً «ادامه بده» گفت؛ Phase 4 مجاز شد و Task 4.1 به‌تنهایی انجام شد.
- Contract پیش از implementation از scaffold به WordPress integration test گسترش یافت؛ fail-first نبودن `header.php`, `footer.php`, `TemplateParts` را مشخص کرد.
- Header source به‌صورت دقیق از میرور بازاستفاده شد تا data-* selectors و desktop/mobile/search/cart DOM سالم بمانند؛ body/head/footer shell از WordPress hooks استفاده می‌کند. Image/href URLs به assets/routeهای WordPress تبدیل شدند؛ `products/*.html` به canonical product `/product/` map می‌شود.
- Header options متصل: branding, logo, topbar, primary/additional links, announcement, sticky behavior, mega-menu labels/CTA; footer options متصل: company/description, badges, quick links, live product categories with fallback, phone/sales/email/address/hours, copyright/back-to-top.
- Legacy hreflang از post/term metadata ساخته می‌شود، فارسی به URL فعلی fallback دارد، و SEOPress فعال باعث skip شدن custom output می‌شود تا duplicate نشود. Unsafe URLs رد و dynamic text/attributeها escaped شدند.
- Task 4.1 test پاس، PHP lint, JS/shell syntax checks پاس؛ regressions Phases 1–3 نیز پاس و WP_DEBUG خالی. منتظر تأیید پیش از Task 4.2.


## Phase 4 / Task 4.2 — Menu walkers and responsive navigation
- پس از approval کاربر، contract scaffold به WordPress integration test گسترش یافت؛ fail-first `Walker.php` و `Navigation.php` نبودند.
- منوهای `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing` ثبت شدند؛ Walker context-sensitive و `Navigation` renderer desktop mega, mobile accordion/off-canvas, footer products و landing sections را به WP menu APIs وصل می‌کنند.
- Product/project tabs ساختارهای data-tab/data-panel هم‌نام با JS دارند؛ پروژه‌ها برای `project_type`/`city` کلیدهای type/city می‌گیرند. Mobile nested groups از `data-mobile-acc-btn/panel` استفاده می‌کنند.
- Dynamic header/mobile/footer integration و menu-location registrations با nested test menus آزمایش شد؛ no-menu static mirror fallbacks و main-menu-to-mobile fallback پاس شدند. Fixtures/options به حالت قبل برگردانده شدند.
- Final regression Phases 1–4 and all syntax checks passed; WP_DEBUG empty. Phase 4 complete; request approval before Phase 5.


## Phase 5 / Task 5.1 — Homepage sections
- کاربر Phase 5 را مجاز کرد؛ اجرای task-by-task رعایت شد و Task 5.2 شروع نشد.
- Test scaffold با WordPress integration تقویت شد: استخراج ۱۵ سکشن top-level میرور، تطبیق اولیهٔ partialها بایت‌به‌بایت، بررسی شناسه‌ها/ترتیب پیش‌فرض و تست visibility/reorder. پس از Task 5.2، regression این تسک ریشه‌های سکشن و attributes را مقایسه می‌کند و DOM کامل بخش‌های ثابت را کنترل می‌کند؛ کارت‌های تکرارشونده به‌صورت داده‌محور رندر می‌شوند.
- پیاده‌سازی: `front-page.php` فقط پوستهٔ `<main>` را می‌سازد و render را به `SectionManager::render_sections` می‌سپارد. ۱۵ سکشن `static-mirror/index.html` بدون تغییر markup به `template-parts/home/{id}.php` منتقل شدند.
- `SectionManager` اکنون برای برگهٔ تنظیم‌شده در `page_on_front` schema/path خانه را انتخاب می‌کند؛ schema خانه به ۱۵ سکشن واقعی گسترش یافت و `gallery` به‌عنوان ID سازگار با متادیتای قبلی حفظ شد، اما جزو سکشن‌های پیش‌فرض نیست.
- تست fail-first، ایراد ترتیب پیش‌فرض را آشکار کرد: schema keyed-by-ID بود، اما کلید رشته‌ای به عنوان order ثبت می‌شد و به صفر sanitize می‌شد. order پیش‌فرض به شمارندهٔ ترتیبی تبدیل شد؛ تست مرحلهٔ 2.3 fallback نیز پس از محدود کردن مسیر home به front page واقعی پاس ماند.
- نتایج نهایی در پایان Task 5.1: integration و Phase 1–4 regression و lint پاس؛ `WP_DEBUG` خالی. Phase 5 runner از `PHP WASM` و اجرای انتخابی `PHASE_TASKS=01` پشتیبانی کرد. در همان نقطه Task 5.2 منتظر approval بود؛ طبق بخش بعدی، بعداً تأیید شد و پاس شد.


## Phase 5 / Task 5.2 — Dynamic homepage data
- کاربر پس از Task 5.1 با «ادامه بده» اجرای Task 5.2 را تأیید کرد؛ محدودهٔ کار به همین تسک محدود ماند.
- Contract scaffold قبل از پیاده‌سازی به WP integration تبدیل شد و آزمون‌های query limit/empty, cache invalidation, taxonomy terms, Section Builder repeaters, stats, shared-card output و front-page واقعی اضافه شدند.
- `inc/HomepageData.php` از `WP_Query` برای پروژه‌ها/مقالات/محصول fallback، و `wc_get_products` برای محصولات Woo (featured و سپس اخیر) استفاده می‌کند؛ queryها محدود، transient cache شده و با versioning هنگام ذخیره/حذف محتوا و تغییر termها invalidate می‌شوند.
- `template-parts/cards/card-product.php`, `card-project.php`, `card-post.php` ساخته شدند. Hero slides از پروژه‌ها و تنظیمات رسانه/متن، دسته‌بندی‌های پرمحصول از `product_cat`/thumbnail متا، محصولات از Woo، پروژه‌ها و تب‌های taxonomy، مقالات از `post`, آمار از post counts و repeaters، و کارت مشتریان از repeater قابل‌ویرایش می‌آیند.
- محتوای نمونهٔ مشتریان/آمار در schema به‌صورت defaults قابل‌ویرایش نگه داشته شده؛ مقادیر آماری وابسته به پروژه/محصول شمارش واقعی دارند و مشتریان می‌توانند از repeater شمارش شوند یا عدد دستی داشته باشند. CTA سفارش اختصاصی نیز به فیلدهای Section Builder منتقل شد.
- در front-page tests دادهٔ خالی برای queryها بدون fatal/placeholder مدیریت شد. چون harness وردپرس WooCommerce نصب ندارد، مسیر `wc_get_products` با test stub پوشش داده شد و WP_Query fallback واقعی نیز اجرا شد.
- Final regression Phases 1–4 + Phase 5 Tasks 5.1–5.2 پاس؛ همهٔ PHPها lint شدند؛ `node --check assets/js/main.js` و `sh -n` runner پاس؛ WP_DEBUG log خالی.


## Phase 6 / Task 6.1 — Single-product WooCommerce
- کاربر پس از ایجاد PR، شروع Task 6.1 را صریحاً تأیید کرد؛ فقط همین Task اجرا شد و گیت تأیید Task 6.2 حفظ شد.
- Test scaffold به integration test وردپرس تبدیل شد و پیش از implementation fail شد چون `single-product.php` وجود نداشت. آزمون fixtures برای حالت خرید/استعلام، title/meta escaping، gallery/fallbackها، شناسه‌ها، ویژگی‌های `pa_*`، project relations، repeaters/FAQ، reviews و related products دارد.
- `single-product.php` از APIهای استاندارد خرید، قیمت و review WooCommerce استفاده می‌کند؛ `true_sale=false` به لینک‌های contact/settings می‌رود. گالری legacy و استاندارد، ۱۸ attribute exact، شناسه‌ها/ویژگی‌های فنی، FAQ، پروژه‌ها/کاربردها، products related و WordPress/Woo reviews data-driven رندر می‌شوند. selectorهای gallery/lightbox/tabs/FAQ با `main.js` حفظ شده‌اند.
- چون test harness افزونهٔ WooCommerce نصب نمی‌کند، hookهای واقعی WooCommerce با test doubles پوشش داده شدند؛ مسیر WordPress comments fallback نیز قابل استفاده باقی ماند.
- نتایج: Task 6.1 integration PASS؛ regression Phases 1–4 و Task 5.1 PASS؛ syntax PHPهای تغییرکرده PASS؛ WP_DEBUG خالی. Phase 6 runner از `PHASE_TASKS=01` برای اجرای تسک تأییدشده پشتیبانی می‌کند.
