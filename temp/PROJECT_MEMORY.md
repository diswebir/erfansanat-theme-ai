# PROJECT MEMORY — حافظه زنده (Active State)

> ⚠️ قبل از شروع هر تسک این فایل خوانده شود و بلافاصله پس از اتمام تسک آپدیت شود.

## وضعیت فعلی
**PHASE 6 COMPLETE (11/11 TASKS · 62/62 فاز ۶ · PHASE 5 COMPLETE 12/12) — فاز بعدی: 7 (پروژه‌ها/وبلاگ/برگه‌ها)**

- D-035: قرارداد فاز ۵ — HomeSections (inc/Sections/) رجیستری ۱۵ سکشن صفحه اصلی؛ front-page.php با es_render_sections؛ wrapper در template-parts/sections/{id}.php و مارک‌آپ در template-parts/home/section-{id}.php؛ فرگمنت‌های nowdoc با توکن {ES_*} در generatorهای temp/gen_*5.py؛ فیلتر es_{section}_args برای هر سکشن.
- D-036: سکشن‌های انجام‌شده — hero (5.2)، projects (5.3، فقط دستی post__in)، about (5.4)، history (5.5، رپیتر ۶ مرحله ۱۷فیلد)، categories (5.6، ۶ کارت + CTA)، catalog (5.7، فایل PDF opt-in)، services (5.7، رپیتر ۵ ردیفی تب‌دار با data-service-*؛ JS میرور تب‌ها را می‌راند).
- D-037: Icons::svg(name, classes, stroke='2', attrs='') — ضخامت قلم پارامتر (سکشن خدمات ۱٫۷)؛ attrs برای data-service-row-icon؛ آیکون‌های ۲۱گانه شامل download/landmark/milestone/waypoints/building2 (inner building2 از سرویس خدمات: دو path M10+M14).
- D-038: refs بایت‌دقیق ۱۵ سکشن در temp/phase-05-homepage-and-sections/refs/ (همگی به «</section» ختم می‌شوند بدون «>» نهایی).
- شکاف فاز ۳: inc/Admin/CategoryTermMeta.php هرگز کامیت نشد (تست test-task-13.php در واقع HreflangMeta را تست می‌کند) — UI ادمین termmetaهای thumbnail/faq وجود ندارد؛ فقط هلپرهای es_get_category_thumb/faq (فاز ۵) هستنند. در گزارش فاز ۵ به کارفرما پیگیری شود.

- D-027: refهای برش در `temp/phase-04-header-footer-navigation/refs/` — header-ref (69,345B)، footer-ref (7,475B)، mobile-sidebar-ref (29,459B) همگی byte-exact از میرور؛ HEADER_MAP فهرست ۳۲ data-hook + جدول جایگزینی. میرور flat در `uploads/static-mirror/` (زیپ یک لایه تو در تو دارد — استخراج باید flatten شود).
- D-028: هدر = `header.php` (shell: language_attributes/body_class/wp_body_open/wp_head) + `template-parts/header/header-top.php` (نوار بالا + لوگو + nav مگا + اکشن‌ها + مودال جستجو، برش عین میرور) + `template-parts/header/mobile-sidebar.php`.
- D-029: `MegaMenu` (inc/Nav/) — فرگمنت‌های byte-exact به‌صورت nowdoc constant (F_HOME/F_P_HEAD/F_TAB_ACTIVE/F_TAB_IDLE/F_PANEL/F_CAT/F_P_BOTTOM/…) با توکن‌های {ES_*}؛ بازسازی با مقادیر میرور = بلوک مرجع (تست byte-exact). منابع: منو (کنوانسیون کلاس es-mega-products/es-mega-tab/es-icon-{lucide}) → fallback تاکسونومی (۴ دسته سطح‌اول product_cat با آیکون‌های cpu/sparkles/trees/waves) → fallback نهایی آرشیو (هدر هرگز خالی نمی‌ماند). فیلتر es_mega_products_tabs. TAB_IDLE تک‌تب است (تب ۱؛ نه دنباله). کلاس‌های active تب در انتهای attr میرورند.
- D-030: `MobileWalker` — آکاردئون ۴گانه products (۴ تب)/projects (type+city)/services/corporate با همان منابع MegaMenu + سبد/تلفن از contact_args؛ بازسازی byte-exact آکاردئون کامل محصولات در تست.
- D-031: `Footer` — فرگمنت‌های ۵ ستونه؛ دسترسی سریع: منوی footer → آپشن footer.quick_links (لینک نسبی / → home_url)؛ دسته‌بندی: منوی tolidat-sherkat → product_cat؛ تماس: contact_args (address fallback میرور)؛ کپی‌رایت از آپشن؛ بازسازی byte-exact کامل فوتر در تست (بدون append تکراری </footer> — F_BAR خودش دارد).
- D-032: `SimpleWalker` (تک‌سطح، کلاس‌های میرور فوتر) + `LandingWalker` (انکر #es-sec-{slug} + data-landing-scroll + is-active؛ resolve از منوی landing با regex انکر).
- D-033: `es_page_url()` نگاشت اسلاگ‌های میرور به URL داینامیک (shop/cart/checkout/my-account از wc_get_page_id؛ projects/services/refund_returns…؛ فیلتر es_page_urls)؛ `es_cart_badge()` (WC → ارقام فارسی؛ fallback «۲»)؛ `es_logo_url()` سه‌مرحله‌ای (custom-logo → آپشن → باکس متنی ESE میرور؛ مقدار placeholder «assets/images/ese-logo.svg» = باکس متنی)؛ `es_cta_text/es_cta_url` (fallback میرور)؛ `es_fa_num`.
- D-034: hreflang قالب با فیلتر `es_disable_builtin_hreflang` برای هماهنگی با افزونه hreflangpch.
- نکته تست: هدر test-02 رندر واقعی header.php را include می‌کند؛ نوار اعلان فقط با ann_enabled=1؛ مودال جستجو اسکلت ثابت با ۷ data-search-item نمونه (پر شدن = فاز ۸).

**QA فاز ۴ (خوداصلاحی‌ها):** TAB_IDLE کل دنباله ۳ تب بریده شده بود؛ کلاس active تب موبایل وسط attr نبود (انتها)؛ MP_TAIL شامل CTA فروشگاه بین پنل‌ها و بستن wrapper؛ PANEL_TPL پر از لینک concrete بود (inner گرید → {ES_CATS})؛ شاب wnome فرگمنت HOME با آفست مطلق/نسبی؛ فوتر </footer> دوبار append می‌شد؛ لینک‌های نسبی quick_links به home_url نیاز داشتند؛ شیم PHP نسخه ۵ (loadNodeRuntime → runtimeId → new PHP + emscriptenOptions.processId، پاسخ: bytes/errors/exitCode، خطا: e.response PHPExecutionFailureError).

**PHASE 3 COMPLETED (14/14 TASKS, 221/221 CONDITIONS, TESTS 01-14)**

- D-014: `PostTypes::init()` روی init — projects (rewrite `projects` with_front=false, archive, icon screenoptions) / services (has_archive FALSE — بدون صفحه آرشیو، D-005) / تاکسونومی‌های city و project_type روی projects (hierarchical, with_front=false, show_admin_column).
- D-015: `WooRewrite` — product_cat slug=`product_cat` با with_front=TRUE (سئو-بحرانی: /product_cat/…)؛ product_tag `product-tag` with_front=false؛ product_brand hierarchical.
- D-016: `AdminListFilters` — دراپ‌داون city/project_type در لیست پروژه‌ها (GET sanitize_key + parse_query tax_query)، ستون‌های es-city/es-project_type/es-hrefkey + سورت متا.
- D-017: `RepeaterEngine` — dual-mode: `parse()` → {format: json|serial|array|empty, rows[]}؛ ذخیره با حفظ فرمت قبلی (json_encode UNESCAPED_UNICODE|SLASHES یا serialize یا آرایه item-N)؛ read/read_term/write/write_term.
- D-018: `ProductMetaboxes` — متاباکس es-product-info (۵ تب)، nonce es_product_meta، پریفیکس opt_meta؛ کلیدها با املای دقیق دیتابیس زنده: gs1_id/national_code/hs_id/isic_id/cpc_id/product_id، zireonvan، `english_titl` (بدون e پایانی!)، `before-sale-c`، true_sale (radio: 'false' استعلامی/else آنلاین)، `product-catalog-file` (esc_url_raw یا آیدی)، `galerry_produ` (با typo!)؛ repeaterها product-feature (add-new-feature)، product-feature2_copy (add-new-feature + `vaule_of_variable`!)، usage_product (add-new-usage)، faq (name_question/answer_question) — sanitize ردیف‌ها با Sanitizer::field نوع repeater + نوشتن با فرمت قدیمی (خالی→آرایه)؛ ستون es-identifiers (span.es-quickedit-data با data-*) + es-sale-mode؛ اکشن es_product_meta_saved.
- D-019: `ProductGallery::sync` — منابع به ترتیب: galerry_produ → _product_image_gallery → gallery-filed → image-2..6؛ normalize فقط ارقام مثبت (ctype_digit — absint منفی را مثبت می‌کند!)؛ sync رشته کاما در هر دو کلید galerry_produ و _product_image_gallery.
- D-020: `QuickEdit` — quick_edit_custom_box روی ستون es-identifiers فقط product؛ فیلدهای identifiers با data-es-qe؛ اسکریپت admin_footer-edit.php پر می‌کند/ذخیره با AJAX موجود.
- D-021: `RelatedPicker` — AJAX es_related_search (nonce es_related، cap edit_posts، فیلتر es_related_search_results، fallback get_terms name__like)؛ چیپ‌ها + اینپوت مخفی کاما؛ current_ids مقاوم (آرایه/JSON/کاما).
- D-022: `Purchasable` — is_online = true_sale !== 'false'؛ برچسب آنلاین/استعلامی؛ پایه فاز ۶ (is_purchasable filter + CTA استعلام).
- D-023: `ProjectMetaboxes` — متاباکس es-project-info روی projects، nonce es_project_meta؛ گالری: ۵ کلید تاریخی second-image/three-image/**foure-image** (typo دقیق)/five-image/six-image + کلید مدرن _es_project_gallery؛ ذخیره: ۵ آیدی اول گالری مدرن → کلیدهای تاریخی sync می‌شوند (بدون پاک‌کردن data قدیمی مگر مقدار جدید)؛ finish-pro-time (متن شمسی)؛ mortabet_mahsole_2 (محصولات پروژه، حفظ فرمت JSON/آرایه)؛ ستون es-finish-time.
- D-024: `ProjectGallery` (Frontend) — get_gallery: ادغام ۵ کلید تاریخی + مدرن، dedup ترتیب‌دار، URL قدیمی → آیدی از فیلتر es_attachment_id_from_url؛ get_products: mortabet_mahsole_2 مقاوم (آرایه/JSON/کاما).
- D-025: `HreflangMeta` — متاباکس es-hreflang-meta روی [post,page,product,projects,services] (فیلتر es_hreflang_post_types) + فرم ترم روی [product_cat,city,project_type]؛ کلیدها hrefmeta_fa/en/ar + same_key_hrefl (پریفیکس فرم opt_href)؛ خروجی wp_head (pri 5): fa/en/ar موجود + x-default=same_key_hrefl (fallback fa)؛ fa و x-default با URL یکسان هر دو چاپ می‌شوند (دستورالعمل مجزا)؛ dedupe per-hreflang با استاتیک $emitted؛ بدون متا → بدون خروجی.
- D-026: `ViewCounter` — شمارش روی wp (pri 20) برای [product,projects,post]؛ کلید number2-viewc (fallback number-viewc)؛ کوکی es_vc_{id} (TTL DAY_IN_SECONDS، فیلتر es_view_counter_cookie_ttl)؛ رد: ادمین/پیش‌نمایش/فید/بدون-UA/بات (لیست فیلتر es_view_counter_bots)؛ ستون es-views محصولات.

**QA فاز ۳ (خوداصلاحی‌ها):** sanitize ردیف‌های repeater با اسکیم تخت خطا می‌داد → Sanitizer::field نوع repeater؛ absint('-5')=5 → فیلتر با ctype_digit؛ array_unique روی URL حذف x-default می‌کرد → dedupe فقط per-hreflang؛ استاب add_meta_box با id تکراری overwrite می‌کرد → تجمیع screens؛ تست 3.6 ترتیب override دستی true_sale را اصلاح کرد.

**PHASE 2 COMPLETED (9/9 TASKS PASSED) — AWAITING USER APPROVAL TO START PHASE 3**
فاز ۱ (۸/۸) و فاز ۲ (۹/۹) کامل شدند. تسک بعدی: فاز ۳ (CPTها و متاباکس‌ها) — Task 3.1.

## آخرین رویداد
- **2026-11-03 — Stage 0 انجام شد:** تجهیز محیط (PHP 8.4 wasm + Node 22)، استخراج میرور (۱۲۸ HTML، ۳۹MB)، اسکن کامل و ساخت ۴ فایل حافظه. کامیت `94d4986`.
- **2026-11-03 — پاسخ‌های کارفرما به سوالات Stage 0 دریافت شد:**
  - **Q1 → اسلاگ‌های سایت زنده** ملاک است: `product/`, `shop`, `product_cat/`, `city/`, `project_type/`. واریانت‌های تکراری میرور (`products/*`, `products.html`, `products/category-*`, `projects/city-*`, `projects/type-*`) نادیده گرفته می‌شوند.
  - **Q2 → خدمات = CPT اختصاصی Native `services`** (افزوده‌شدن به بلوپرینت؛ رجیستر در تسک 3.1، قالب‌ها در فاز ۷).
  - **Q3 → صفحات بدون مرجع (`arya`, `smartelement`, `my-account`, `auth`)** از کامپوننت‌های موجود میرور ساخته می‌شوند (فاز ۷ تسک 7.3 + استایل فرم‌های ووکامرس/voorodak در 6.3).
  - **Q4 → ووکامرس کامل** برای cart/checkout؛ محصول استعلامی (`true_sale=false`) دکمه استعلام/تماس می‌گیرد.
- **2026-11-03 — Stage 1 انجام شد:** ساخت `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` و درخت اولیه ۲۲ تسکی.
- **2026-11-03 — Stage 1-R (بازطراحی اتمیک) انجام شد:** درخواست کارفرما: «تسک‌ها کم هستند؛ اتمیک‌تر، ریزتر، جزئی‌تر و جامع‌تر شوند». درخت کاملاً بازتولید شد:
  - فاز ۱: ۸ تسک (اسکلت/اتولودر/supports/DeBloat×۲/انتقال assets/AssetManager/زیرساخت تست wp-stubs)
  - فاز ۲: ۹ تسک (هسته پنل/موتور فیلد+Sanitizer/تب‌های هدر|فوتر|برند|اسکریپت/SectionManager×۳)
  - فاز ۳: ۱۴ تسک (CPTها و rewrite×۴/متاباکس محصول×۶/RepeaterEngine/پروژه×۲/ترم‌متا/Hreflang+بازدید)
  - فاز ۴: ۸ تسک (ref-extract/header.php×۲/MegaWalker/MobileWalker/footer/Walkerهای ساده/تست DOM)
  - فاز ۵: ۱۲ تسک (front-page + ۹ سکشن جدا + کارت‌ها + تست جامع)
  - فاز ۶: ۱۱ تسک (wrapper/سینگل×۵/آرشیو×۲/cart/checkout/my-account)
  - فاز ۷: ۱۰ تسک (پروژه×۳/وبلاگ×۳/برگه‌ها×۲/services/لندینگ)
  - فاز ۸: ۱۰ تسک (AJAX×۴/فرم×۲/سئو/پرفورمنس/امنیت/E2E)
  - هر تسک شامل: هدف، وابستگی‌ها، مراجع دقیق فایل میرور، فایل‌های خروجی، مراحل گام‌به‌گام، خط‌قرمزها، Pass Conditions + اسکلت test-task-XX.php با شرط‌های pre-seeded (check commented).
  - Task 1.8 جدید: زیرساخت wp-stubs (تست بدون وردپرس روی PHP wasm).

## تصمیمات فنی ثبت‌شده
- **D-001:** PHP runtime = `@php-wasm/node` 8.4 (شیم `/home/user/tools/php`). بوت هر اجرا ~۱۵-۳۰s → تست‌های هر تسک در یک اسکریپت واحد اجرا شوند.
- **D-002:** میرور استاتیک فقط مرجع (gitignored). دارایی‌ها در فاز ۱ کپی می‌شوند.
- **D-003 (نهایی با تایید کارفرما):** canonical = اسلاگ‌های سایت زنده؛ واریانت‌های تکراری میرور دور ریخته می‌شوند.
- **D-004:** `category/magazine|news-and-events` باگ title دارند؛ عنوان واقعی از دیتابیس رندر می‌شود.
- **D-005 (نهایی):** خدمات → CPT Native `services` (سینگل: `/services/{slug}`؛ فهرست: برگه/آرشیو با کارت‌های میرور). هنگام register، اولویت rewrite طوری تنظیم شود که تداخلی با برگه‌های موجود نداشته باشد.
- **D-006 (نهایی):** `arya`/`smartelement`/`my-account`/`auth` از کامپوننت‌های میرور ساخته می‌شوند.
- **D-007 (نهایی):** سبد/چک‌اوت = ووکامرس کامل با پوسته میرور؛ `true_sale=false` → دکمه استعلام.
- **D-008:** قالب خدمات جدید (CPT `services`) در فاز ۳ ثبت و پشتیبانی متاباکس Hreflang/بازدید روی آن هم اضافه می‌شود.

## نکات به‌یادماندنی برای تسک‌های بعدی
- **تولیدکننده درخت:** `/home/user/tools/build_tasks.php` (+tasks_part1/2.php) — هر تغییر ساختاری از همین اسکریپت (تسک‌ها دستی ویرایش نشوند که با regen از بین نروند؛ به‌جز وضعیت‌ها که در MASTER_PLAN آپدیت می‌شود).
- **اجرا:** هر تسک = ۱ فایل تست؛ `bash temp/phase-XX*/tests/run-phase-XX-tests.sh` · کل: `bash temp/e2e/run-all-phases-e2e.sh`.
- اسکلت تست‌ها عمداً FAIL می‌دهند (PLACEHOLDER) تا WRITE TEST هر تسک واقعاً انجام شود.
- هدر/فوتر در هر ۱۲۸ صفحه یکسان → برش در فاز ۴.
- CSS واحد minified (۲ خط) — دست نزنیم، فقط Enqueue. مسیر فونت نسبی `../fonts/` → ساختار `assets/` مثل میرور.
- main.js بدون fetch؛ ۲۶۳ data-hook → فاز ۸ اتصال AJAX با حفظ hookها.
- قیمت‌ها «تومان» (IRT)؛ RTL؛ JSON-LDها داینامیک شوند.
- `page_for_posts`=7426 و `front_page_id`=8146 — قالب‌ها باید با برگه‌های موجود کار کنند.
- متاهای legacy با املای دقیق (`galerry_produ`, `foure-image`, `english_titl`, `vaule_of_variable`, `usage_countr`).

## رویدادهای فاز ۷ (2026-11-07)

### ۷.۱ (تسک اول فاز ۷) — single-projects (2026-11-07)
- خروجی‌ها: `single-projects.php` (شل: ترم‌های city/project_type + خلاصه چکیده→برش محتوا + ۵ فراخوان es_get_template + کوئری مرتبط tax_query OR + سکشن‌های مخفی وقتی داده خالی) + `template-parts/single-project/{hero,gallery,tabs,product-card,related-card,products,related}.php` — هر پارشیال nowdoc بایت‌دقیق میرور projects/esfahan-shohada-square.html با توکن {ES_*}؛ ژنراتور `temp/gen_p71.py` (برش ۶ رگیون + assert بازسازی هر فرگمنت + raw_sum مرزها).
- کلیدهای متا: `es_client` / `es_duration` (شناسنامه سایدبار + تب specs) / `es_standard` / `finish-pro-time` (تاریخ تکمیل تب) / `mortabet_mahsole_2` (محصولات از ProjectGallery::get_products) — گالری از ProjectGallery::get_gallery (کلیدهای legacy second..six-image) → ۵ اسلات با fallback میرور light-sphere؛ شمارنده es_fa_num.
- نکات ساختاری: اتریبیوت design میرور custom با مقادیر 0/0.06/0.12/0.18 لیست ثابت در شل است (نه هاردکد در پارتیال)؛ lightbox گالری یک img هاردکد دارد → sweep توکن {ES_Gn} بر اساس نام فایل؛ {ES_CONTENT} فقط ردیف پاراگراف‌هاست و بستن کول گرید static در قالب می‌ماند (برش DESC تا lg:col-span-5 منهای بستن آخر)؛ hero خلاصه = post_excerpt → wp_trim_words(content, 24).
- **الگوی مهم اسلات‌ها:** وقتی اسلات توکنی وسط فرگمنت است ({ES_PRODUCT_CARDS}/{ES_RELATED_CARDS})، کارت‌های ۲ به بعد باید از دم static حذف شوند (برش تا انتهای کارت آخر) — وگرنه کارت‌های هاردکد میرور به خروجی داینامیک می‌چسبند.
- مارکر «مخفی شدن سکشن» باید سکشن‌محور باشد: هدر (باکس global-search) لینک پروژه‌های ahvaz/half-arch را همیشه دارد؛ مارکر امن = tunnel-90-alborz / تیتر سکشن.
- استاب هارنس: get_posts اکنون field=slug/name را در tax_query می‌فهمد (پیش‌فرض term_id — سازگار با قبل).
- تست ۷.۱: ۲۵/۲۵ (۵ قیاس بایت‌دقیق + ترکیب main شل + متا + lightbox + مخفی‌ها + args کوئری) · رگرسیون: ۶۳ فایل پیاده‌شده = **۱۱۶۸ پاس / ۰ رد**؛ ۱۹ رد فقط placeholder طراحی‌شده فاز ۷/۸ (هر کدام check(false)). عدد مستند قبلی «۱۱۶۴» شامل شمارش دوباره یک فایل بود — مبنا با هر دو نسخه استاب ۱۱۴۳ (فاز ۱–۶) تأیید شد.

## رویدادهای فاز ۶ (2026-11-04)

### ۷.۲ — archive-projects (2026-11-08)
- خروجی‌ها: `archive-projects.php` (شل: هیرو ESP72H + sec#archive + فیلترها + topbar + گرید ۶ کارت نستد + nav مشترک + empty/دراور/مقاله استاتیک + CTA) · `template-parts/archive/project-filters.php` (۹ ردیف نوع get_terms(project_type) + ۱۰ chip شهر get_terms(city)؛ فعال با مقایسه slug) · `template-parts/archive/pagination.php` مشترک (پیش‌فرض products؛ projects با root='projects'/count_attr/unit/label؛ early-return total≤1؛ data-href بعد از class طبق قرارداد ۶.۷) · `inc/Frontend/ProjectArchive.php` (pre_get_posts @20: type/location→tax_query slug، sort popular→number2-viewc / rating→es_rating، per-page clamp ۱..۱۰۰) — boot در Theme.php.
- ژنراتور `temp/gen_p72.py`: ۹ ریجن از projects.html با assert بازسازی بایت‌دقیق + nav_norm(projects)≡nav_norm(shop)؛ توکن‌های ROW_ON/ROW_OFF/ROW_CITY از ردیف‌های یکتای میرور.
- تست ۷.۲: 20/20 — قیاس کل main شل ≡ میرور (۱۲ کارت دم → ۶ کارت رندر per-page=۶؛ custom یک‌درمیان 0.04/0.1) + empty toggle + selected مرتب‌سازی + query_args/apply/sanitize/کلامپ. رگرسیون کامل: **۱۱۸۸/۰** (۶۴ فایل پیاده‌شده + ۱۸ placeholder).
- تغییرات جانبی مجاز: دیفالت `archives.projects_per_page` اسکیما ۱۲→۶ (+تست ۲.۵) چون میرور data-items-per-page="6" و ناو دوصفحه‌ای دارد؛ card-project فلگ `nested` (آرشیو محتوا را داخل data-project-item می‌گیرد برخلاف صفحه اصلی) + `es_client` به data-project-search-text.

### ۷.۳ — taxonomy-project_type / taxonomy-city (2026-11-08)
- خروجی‌ها: `taxonomy-project_type.php` + `taxonomy-city.php` (دوقلوی توکن‌مشترک؛ heredocهای جدا برای هیرو/تاپ‌بار/مقاله) + `template-parts/archive/project-drawer.php` (دراور پویا با ESP73_DALL_ON/OFF، DROW/DROW_ON، DCITY/DCITY_ON) · ژنراتور `temp/gen_p73.py` (فرگمنت‌ها assert-دار؛ بازسازی کامل دراور هر دو میرور).
- متاهای ترم با fallback میرور: es_archive_crumb/badge/title_a/title_b/caption/top_title/chip/image_id (+term_description).
- تفاوت ترم از آرشیو اصلی: ناو همیشه رندر (hidden در تک‌صفحه‌ای) + disabled قبلی/بعدی؛ بلوک بازنشانی؛ «همه» غیرفعال با نقطه slate-300 و شمارش کل (wp_count_posts→__es_stub_post_counts)؛ deactivate_all در سایدبارِ شهر؛ چیپ شهر فعال = واریانت emerald-800 (سایدبار و دراور).
- pagination مشترک: always_show + nav_cls + {ES_NEXT_TAG} (enabled: class+data-href بعد از class طبق ۶.۷ / disabled: disabled قبل از class) — هر دو چیدمان میرور با یک توکن.
- ProjectArchive::apply روی is_tax(['project_type','city']) هم فعال.
- تست ۷.۳: 25/25 (قیاس کل main هر دو شل + ناو/فیلتر/دراور/خالی/چندصفحه‌ای/apply). رگرسیون کامل: **۱۲۱۳/۰** (۶۵ فایل پیاده‌شده + ۱۷ placeholder).

### ۷.۴ — home / archive / category (وبلاگ) (2026-11-08)
- خروجی‌ها: `home.php`/`archive.php`/`category.php` (نازک — همه از `template-parts/archive/blog-shell.php` با mode=home|category) + `inc/Frontend/BlogArchive.php` (pre_get_posts @20 فقط روی is_home/is_category: cat/s از GET، posts_per_page=100 برای رندر کامل DOM (فیلتر/صفحه‌بندی JS میرور data-items-per-page=4)، ignore_sticky_posts) — boot در Theme.php.
- ژنراتور `temp/gen_p74.py`: هیرو/سکشن/featured/دکمه‌های JS/لینک‌های سکشن از blog.html + magazine.html با assert بازسازی بایت‌دقیق هر دو حالت؛ توکن {ES_CRUMB3_SPAN} (بلاگ: پیل «وبلاگ»؛ کت: لینک وبلاگ + پیل ترم) و {ES_FEATURED} (کت همان فرگمنت با ../ prefix).
- card-post (۵.۱۱) سه فلگ جدید گرفت: nested (WRAP باز + بستن div custom+wrap بعد از BODY — چیدمان تودرتوی آرشیو)، visible (block/hidden — ۴ اولِ per-page)، search_chip=false چون search-text میرور chip ندارد (= title+excerpt+متا es_keywords)؛ پیش‌فرض‌ها سازگار ۵.۱۱.
- sticky: sticky_posts چندگانه [۷۰۱,۷۰۲,۷۰۳] = ۳ نشان «★ ویژه» گرید؛ کارت هیرو «★ منتخب سردبیر» = نخستین sticky وگرنه نخستین مقاله؛ «بدون تکرار» = کارت sticky هم داخل گرید می‌ماند (مطابق میرور هر ۷ کارت).
- pagination واریانت بلاگ از قالب مشترک: box_cls=bg-slate-50/70، navbtn_cls=bg-white px-3 py-2، cur_label=صفحه، count_seg='' (بدون «مجموعاً»)، page_btn (غیرفعال‌ها دکمه JS نه a)، next_href=false (بدون data-href)؛ pages=ceil(total/per) با per=es_option('archives.posts_per_page').
- home فقط برای صفحه نوشته‌ها (page_for_posts=7426)؛ متاهای برگه/ترم با fallback میرور: es_blog_crumb/badge/title_a/title_b/desc/heading (+term_description برای desc کت)؛ بج کت شمارش کل دسته (۷) را می‌دهد نه صفحه (۵)؛ شمارنده سکشن = تعداد رندر.
- تست ۷.۴: 26/26 (بایت‌دقیق home ≡ blog.html و category ≡ magazine.html + خالی + sticky + GET cat/s + هوک es_blog_archive_query_args + apply + سازگاری کارت مستقل). رگرسیون کامل: **۱۲۳۹/۰** (۶۶ فایل پیاده‌شده + ۱۶ placeholder).

### ۷.۵ — single.php سینگل مقاله (2026-11-08)
- خروجی‌ها: `single.php` (نازک: main + شل با get_queried_object) + `template-parts/single/article-shell.php` (هیرو/محتوا/تگ‌وعرSEO/سایدبار/مرتبط — ژنراتور `temp/gen_p75.py` با assert بازسازی دو میرور led-wall-washer-guide و point-light-pixel-led) + `inc/Frontend/TOC.php` (کلاس TOC: process هر h2 را در `<div id="sec-N" class="scroll-mt-24">` می‌پوشاند و آیتم‌ها را جمع می‌کند؛ init فیلتر the_content @20) + `template-parts/cards/card-related.php` (کارت فشرده h-44/h4/line-clamp-2 — متفاوت از card-post) + `es_related_posts()` در Helpers.
- قاعده مرتبط میرور = «۳ مقاله جدیدتر به‌جز جاری» (تاریخ نزولی) — با هفت میرور راستی‌آزمایی شد (تاریخ‌ها: تونل ۱۲م، المان ۵م، ریسه ۲۸ت، خورشیدنما ۲۱ت، والواشر ۱۴ت، پوینت ۷ت، قاب ۲خ).
- توکن‌های فرگمنت بدون پیشوند ES_ را جا ندهید: CRUMB_LINK={URL}/{X}، TAG_PILL={TAG} — PHP باید همان توکن‌ها را strtr کند (دو ردِ تست همین بود).
- محتوای پست: raw = رندر میرور منهای wrapperها؛ تصاویر درون‌محتوایی در وردپرس مطلق‌اند (فیکسچر src="../images/" → uploads).
- بازدید: ViewCounter فاز ۳.۱۴ (number2-viewc) — میرور نمایش بازدید ندارد؛ فقط شمارش سمت سرور (maybe_track با UA/is_singular/cookie dedupe). TOC حتی با ۲ سرفصل رندر می‌شود (برخلاف حدس «≥۳» spec — میرور حاکم است)؛ دیدگاه‌ها در میرور نیست → رندر نمی‌شود.
- تست ۷.۵: 20/20 (بایت‌دقیق دو سینگل + TOC واحد + h3 دست‌نخورده + هوک the_content + قاعده مرتبط ×۳ + increment/maybe_track/بات + کارت مرتبط مستقل + share). رگرسیون کامل: **۱۲۵۹/۰** (۶۷ فایل پیاده‌شده + ۱۵ placeholder).

### ۷.۶ — search / 404 / searchform (2026-11-08)
- خروجی‌ها: `search.php` (main + شل) + `template-parts/search/search-shell.php` (هیرو جستجو + سکشن نتایج + تب‌ها + ناو) + `searchform.php` (فرم GET با کلاس‌های اینپوت بایت‌دقیق بلاگ) + `404.php` (backdrop/کرامب/۴۰۴/فرم/قرص‌های دسترسی سریع) + `inc/Frontend/SearchArchive.php` (pre_get_posts فقط روی is_search: type GET whitelist → post_type، per_page=9 فیلترپذیر es_search_per_page) — boot در Theme.php.
- صفحه جستجو/۴۰۴ مرجع میرور ندارد (Q3) — از کامپوننت‌های بایت‌دقیق ساخته شد: backdrop/حالت‌خالی/کلاس اینپوت از blog.html؛ کرامب از ۷.۵؛ قرص‌های لینک از ۷.۴ (بایت‌دقیق با regex تأییدی در gen_p76.py)؛ گرید `grid gap-6 sm:grid-cols-2 xl:grid-cols-3` از ۶.۷؛ ناو مشترک (products پیش‌فرض، root=search، unit=نتیجه).
- تب‌ها: همه/محصولات/پروژه‌ها/مقالات با `?s=…&type=…`؛ بدون s → بدون تب‌ها و H1 ساده. حالت خالی همیشه رندر (الگوی میرور بلاگ: hidden وقتی نتیجه هست) با متن عمومی. فیلتر type با sanitize_key — نامعتبر → همه.
- کارت‌ها بر اساس post_type: product→card-product (custom 0/0.06/0.12)، projects→card-project (0.04/0.1)، post→card-post (0.05/0.12/0.19) — همه standalone.
- تک‌صفحه‌ای بدون ناو (early-return مشترک) — برخلاف بلاگ که always_show دارد.
- تست ۷.۶: 34/34 (سه کارت سه‌نوع + فیلتر type + حالت خالی + apply/query_args/هوک + searchform کلاس‌بایت‌دقیق + 404 کامل + جستجوی خالی + ناو دوصفحه‌ای). رگرسیون کامل: **۱۲۹۳/۰** (۶۸ فایل پیاده‌شده + ۱۴ placeholder).

### ۷.۷ — قالب برگه‌های about و contact با SectionManager (2026-11-03)
- خروجی‌ها: `page-templates/page-about.php` (Template Name: درباره ما) + `page-templates/page-contact.php` (Template Name: تماس با ما) — بدنه نازک `es_render_sections((int) get_queried_object_id())` + `inc/Sections/AboutSections.php` (گروه about: page-about-hero / page-about-intro / page-about-iso / page-about-values + contact مشترک) + `inc/Sections/ContactSections.php` (contact-hero + contact-form) + ۶ مارک‌آپ `template-parts/about/section-{hero,intro,iso,values}.php` و `template-parts/contact/section-{hero,form}.php` + ۶ رپر `template-parts/sections/*.php` — همه از `temp/gen_p77.py` با assert بازسازی «رشته کامل» (۶/۶ فرگمنت سبز؛ about 32839 و contact 25648 کاراکتر بایت‌دقیق).
- **انحراف مستند از spec تسک:** میرور about.html سکشن «ویدیو معرفی/تایم‌لاین» ندارد و contact.html سکشن «نقشه» ندارد (خط‌قرمز: پیش‌فرض = میرور) → گروه about دقیقاً ۵ سکشن میرور؛ CTA پایانی about بایت‌مساوی سکشن contact ایندکس است → همان id `contact` با `HomeSections::contact_schema()` (استخراج‌شده به متد مشترک) reuse شد.
- **SectionManager (توسعه ۲.۹):** `TEMPLATE_GROUPS` + `group_for_post()` (نگاشت slug قالب → گروه؛ فیلتر `es_sections_template_groups`؛ پیش‌فرض page) + `get_sections` بدون متا فقط سکشن‌های گروه قالب + متاباکس فقط گروه قالب را لیست می‌کند. save() با سفیدلیست کل گروه‌ها دست‌نخورده. رفتار ۵.۱ (برگه معمولی → ۱۵ سکشن) حفظ شد.
- **درس کلیدی — id سکشن سراسری است:** `schema_of(id)` بین همه گروه‌ها جستجو می‌کند و گروه page اول است؛ idهای «about/about-intro/...» ایندکس با گروه about تداخل داشتند → پیشوند `page-about-*`. (تست ۵.۱۲ با حذف اشتباهی رپر ۵.x `sections/about-intro.php` در حین تغییر نام، ۲ رد خورد — از git بازگردانی شد؛ رپرهای هوم به نام‌های قدیمی‌شان برمی‌گردند.)
- **فرم تماس (تا ۸.۵ نمایشی):** توکن `{ES_TF_FORM_EXTRA}` در تگ form + فیلتر `es_contact_form_extra` (پیش‌فرض '' → بایت‌دقیق میرور؛ فاز ۸.۵ action/nonce را با همین فیلتر تزریق می‌کند)؛ hookهای میرور data-contact-form/success/reset حفظ؛ فیلدها: نام/تلفن(rtl)/سازمان/توضیحات + placeholderها توکن.
- **باگ ۵.x که در پروب پیدا شد:** `MobileWalker::F_HEAD` داخل nowdoc بلوک‌های `<?php if (es_logo_url())…` داشت که خام به خروجی می‌نشیت → توکن `{ES_LOGO_HTML}` در render() (ESE پیش‌فرض عین میرور / img با لوگوی سفارشی). تست ۷.۷ گارد `!str_contains($mw, '<?php')` دارد.
- هارنس: استاب `get_page_template_slug` (__es_stub_template_slug) اضافه شد؛ استاب `remove_filter` حالا واقعاً حذف می‌کند (spl_object_id برای کلژر) — قبلاً no-op بود.
- تست ۷.۷: **58/58** (رجیستری/عدم‌تداخل/اسکیم مشترک/نگاشت قالب/پیش‌فرض گروهی/متاباکس گروهی/بایت‌دقیق ×۲/هوک‌های فرم/فیلتر form_extra/ویرایش فیلد/toggle/reorder/قالب کامل ×۲/MobileWalker/فایل‌ها). رگرسیون کامل: **۱۳۵۳/۰** (۶۹ فایل پیاده‌شده + ۱۳ placeholder). e2e فقط placeholderها را رد می‌کند (وضعیت شناخته‌شده).
- به‌روزرسانی تست‌های قدیمی با معناشناسی جدید (حفظ قصد): 2.7 (پیش‌فرض ۱۵ + چک نگاشت قالب نمونه)، 2.8/2.9 (نگاشت template-300/template-demo → گروه home نمونه). محیط: رانر php-wasm بازسازی شد (`/home/user/tools/php` → `tools/phpwasm/php.mjs`؛ processId=7 الزامی؛ resolveHost برای مسیر نسبی؛ شیم php -l فقط lex).

### ۷.۸ — قالب برگه‌های licenses/faq/refund/recruitment با SectionManager (2026-11-03)
- خروجی‌ها: `page-templates/page-{licenses,faq,refund,recruitment}.php` + `inc/Sections/LandingSections.php` (۴ گروه جدید) + ۸ مارک‌آپ `template-parts/landing/section-*.php` + ۸ رپر `template-parts/sections/*.php`؛ ژنراتور `temp/gen_p78.py` (Tok position-based/mask + assert بازسازی «رشته کامل» بایت‌دقیق برای هر ۴ سکشن اصلی)؛ فیکسچر `temp/p78_expect_*.html`.
- گروه‌ها (ترتیب عین میرور): licenses=[licenses-hero, licenses-certs, contact]، faq=[faq-hero, faq-list, contact]، refund=[refund-hero, refund-policy, contact]، recruitment=[recruit-hero, recruit-jobs] — contact سومین reuse (بایت‌مساوی سکشن ۱۴ ایندکس)؛ recruitment طبق میرور CTA ندارد.
- Repeaterها (پیش‌فرض = میرور): badges×4 / certs[cat,code,title,desc,org,period]×6 / s_items×4 · cats×3 / items[cat,question,answer,open]×6 · cards×3 / covered×5 / excluded×4 / steps×3 · tabs[key,btn,open,badge,title,desc]×4 / jobs[tab,type,exp,title,loc,skills چندخطی→li]×7. پرفیکس توکن‌ها: LH/FH/RH/KH (هیروها) و LC/FL/RP/RJ؛ custom دوره‌ای (certs 0.04/0.1/0.16؛ cards 0/0.06/0.12؛ faq 0..0.15 گام 0.03؛ شماره steps = es_fa_num از ایندکس).
- فرم recruitment: `data-contact-form` + لنگر id=apply-form + option واحدهای سازمانی از tabs.btn (بدون esc_attr — پیش‌فرض میرور `&amp;` دارد و esc_attr دوبار escape می‌کرد) + فیلتر `es_recruit_form_extra` برای اتصال ۸.۶؛ تا ۸.۶ نمایشی بدون action.
- پچ‌ها: SectionManager::TEMPLATE_GROUPS +۴ نگاشت؛ Theme.php بوت LandingSections؛ تست 2.7 به ۸ گروه به‌روز شد (به‌روزرسانی پیش‌بینی‌شده).
- تست ۷.۸: **78/78** (رجیستری ۴گروه/reuse اسکیم contact/نگاشت + سازگاری ۵.۱/پیش‌فرض گروهی/بایت‌دقیق ×۴/هوک‌های data-faq-*/فرم + فیلتر/es_fa_num/ویرایش repeater×۲/toggle/reorder/قالب کامل ×۴/عدم نشتی متا). رگرسیون کامل: **۱۴۵۲/۰** (۷۱ فایل پیاده‌شده + ۱۲ placeholder شناخته‌شده: ۷.۹/۷.۱۰/فاز ۸). e2e همان وضع قبل.
- **باگ کلیدی که تست گرفت:** اسلاگ مارک‌آپ هیروی recruit «recruitment-hero» تولید شده بود ولی شناسه ثبت‌شده «recruit-hero» — es_get_template بی‌سروصدا return می‌کند و رندر هیرو تهی می‌شد (licenses/faq/refund چون هم‌نام بودند می‌گذشتند). قاعده: slug مارک‌آپ باید عین شناسه سکشن ثبت‌شده باشد.
- محیط: رانر php-wasm نسخه ۹ بازسازی شد (`@php-wasm/universal` + `loadNodeRuntime('8.2',{emscriptenOptions:{processId:1000}})`؛ مونت کل مخزن متنی بدون .git/zip؛ ES_SCRIPT_PATH با define+require؛ PHPExecutionFailureError شکافته می‌شود؛ process.exit صریح چون event-loop واسم تعطیلی ندارد) — کل رگرسیون ~۶۰ ثانیه.

### ۶.۱۱ (تسک یازدهم — پایانی فاز ۶) — my-account (2026-11-07)
- خروجی‌ها: `woocommerce/myaccount/form-login.php` (ورود/ثبت‌نام با کامپوننت‌های فرم میرور — کلاس اینپوت/لیبل checkout + btn-primary؛ نام فیلدها/nonce/هوک‌های استاندارد WC: username/password/rememberme/login/register + woocommerce-login-nonce/register-nonce + do_action login/register_form_*)؛ `assets/css/wc-forms-override.css` (نگاشت کلاس‌های WC به ظاهر میرور: input/label/button/navigation endpoints/orders-table/messages/dashboard)؛ Setup::enqueue_account_styles (wp_enqueue_scripts + is_account_page، ورژن filemtime با ERFANSANAT_URI).
- گزینه‌های WC رعایت شد: generate_username/password=yes → فیلدهای ثبت‌نام حذف.
- فیکسچر: wp_enqueue_style استاب هارنس از قبل هست (به __wp_hooks[enqueued_styles] می‌نویسد) — تکراری تعریف نکنید؛ متد checkout/payment_gateways در فیکسچر WC متدند.
- تست ۶.۱۱: ۲۲/۲۲ · رگرسیون کامل ۱۱۶۴/۰ (۸۲ فایل فعال).

### ۶.۱۰ (تسک دهم) — checkout + thank-you (2026-11-07)
- خروجی‌ها: `woocommerce/checkout/form-checkout.php` (شل دوبل: فرم checkout میرور / رسید thank-you) + `inc/Woo/CheckoutFields.php` (buyer_type + es_tax_id → متای سفارش).
- شکل کار: هیرو میرور (توکن home/shop)؛ حالت order-received → رسید receipt میرور (توکن {ES_CODE}=#شماره سفارش / {ES_CUSTOMER} / {ES_METHOD} / {ES_SHOP_URL}) + بستن؛ وگرنه فرم.
- انحراف‌های مستند از میرور (استاندارد WC): form → name="checkout" class="checkout woocommerce-checkout grid..." action استاندارد (گرید میرور روی خود form)؛ نام فیلدها customer_* → billing_first_name/last_name(جدید اجباری)/phone/city/address_1/order_comments + billing_es_tax_id اختیاری؛ radioهای payment value → اسلاگ درگاه واقعی (WC()->payment_gateways) با حفظ name=payment_method؛ بلوک استاندارد woocommerce_checkout_payment داخل باکس ۳ + nonce process_checkout.
- فیلدها با کلاس میرور ولی name/value استاندارد (value از WC()->checkout()->get_value).
- **داک‌بلاک: `billing_*/` کامنت را می‌بندد** (*/ داخل متن) — فاصله بگذارید.
- تست ۶.۱۰: ۲۱/۲۱ (هیرو/فرم/radio/فیلدهای استاندارد/thank-you بایت‌دقیق/متا fixture).

### ۶.۹ (تسک نهم) — سبد خرید ووکامرس (2026-11-07)
- خروجی‌ها: `woocommerce/cart/cart.php` (رگیون‌های nowdoc از cart.html: HERO/HEAD/ROW/MID/QA/ASIDE/FOOT/EMPTY با توکن {ES_*}) + بسط `inc/Woo/Setup.php` (main_attrs برای <main> شل: cart/checkout/account با کلاس میرور + هندلر template_redirect ?empty-cart=1) + woocommerce.php (main_attrs تزریق شد).
- **سه انحراف مستند از میرور** (الزام فرم استاندارد ووکامرس — بدون JS دستی): ۱) span[data-cart-row-qty] → input number با name="cart[key][qty]" + دکمه update_cart مخفی؛ ۲) دکمه‌های حذف/بازنشانی/افزودن سریع → لینک‌های استاندارد (wc_get_cart_remove_url / ?empty-cart=1 / ?add-to-cart=N) با حفظ data-hookها؛ ۳) فرم استاندارد woocommerce-cart-form + nonce (nonce قبل از دکمه update چاپ می‌شود).
- ژنراتور temp/gen_p69.py: assert بازسازی با اعمال انحراف‌ها روی برش مرجع میرور (m_row_swaps/m_head_swaps/m_qa_swaps) — خروجی == میرور+انحراف‌ها؛ خودِ میرور未经‌دست‌کاری با raw_sum چک می‌شود.
- quick-add: cross-sells → fallback ۳ محصول (post__not_in سبد)؛ ES_MID توکن {ES_QA_ITEMS} (آیتم سوم، بستن پایانی‌اش داخل run پنج‌تایی `<div` است — مرز برش +۱ بستن).
- نکته: `array_values` روی get_cart() ممنوع (کلیدهای سبد = نام فیلد input)؛ esc_url روی & می‌گذارد &amp; (مرجع تست هم همان)؛ apply_filters استاب ارگ دوم واقعی می‌دهد → فیلتر step باید مقاوم باشد.
- تست ۶.۹: ۲۳/۲۳ (رندر کامل بایت‌دقیق با انحراف‌ها، خالی، اعلان error با استایل رز، Setup، wrapper).

### ۶.۸ (تسک هشتم) — آرشیو دسته/تگ محصول (2026-11-07)
- خروجی‌ها: `taxonomy-product_cat.php` (شل: archive-open میرور با data-initial-category داینامیک + hero-cat + product-filters ۶.۷ + topbar + گرید card-product + pagination + product-empty + category-faq با رپر mt-16؛ بدون ترم → get_template_part('archive-product'))، `taxonomy-product_tag.php` (گرید ساده + pagination)، `template-parts/archive/{hero-cat,category-faq,topbar,product-empty}.php`.
- hero-cat.php: فرگمنت ۶.۵KB میرور (breadcrumb aria-label="مسیر صفحه" با {ES_HOME_URL}/{ES_SHOP_URL} داینامیک + H1 دونیمه سئو + desc + تصویر) — **archive-open (`<section id="archive">`) داخل این فرگمنت نیست**؛ شل چاپش می‌کند (مرز تمیز بین پارتیال و شل). توکن {ES_TERM_NAME} در breadcrumb + fallback تصویر light-element.jpg.
- category-faq.php: WRAP (data-faq-list + h3 «سوالات متداول درباره {ES_LABEL}») + ROW (data-faq-toggle{N}/icon/body)؛ آیکون باز rotate-45 bg-emerald-800 text-white، بسته bg-emerald-100 text-emerald-800؛ ردیف اول باز؛ rows خالی → return (مخفی).
- topbar/product-empty: مشترک با shop — **topbar برش از خودش شروع شود** (`<div class="mb-6 flex flex-wrap items-center justify-between...">` تا `data-product-grid`) نه از wrapper col-span (دوبله div)؛ empty فرگمنت **دو بستن ساختاری (col-span + grid-12) را داخل خودش دارد** → دم شل فقط `</section>`.
- باگ باز ۶.۷ که اینجا فیکس شد: REGION nowdoc‌ها در gen_p67 ساخته می‌شد ولی به SHELL نمی‌چسبید → `$ES_HERO` undefined در رندر واقعی (تست‌های ساختاری نمی‌گرفتند)؛ + شل shop سکشن ساختگی `pb-16 lg:pb-24` چاپ می‌کرد که در میرور وجود ندارد (archive-open داخل فرگمنت هیرو shop است) — هر دو فیکس شد؛ تست ۶.۷ همچنان ۲۱/۲۱.
- تست ۶.۸: ۲۹/۲۹ (هیرو بایت‌دقیق با ۳ جایگزینی، FAQ متا/خالی، topbar/empty مشترک، شل‌های ساختاری + archive-open تک، breadcrumb داینامیک). فیکس تست ۵.۳: فیکسچر بخش ۴ باید count=4 خودش را ست کند (ارث از بخش ۱ = ۳ کارت).
- **رگرسیون کامل: ۱۰۹۸/۰** (۸۱ فایل test-task + font) — placeholderهای ۶.۹–۶.۱۱ و فاز ۷/۸ بدون «نتیجه» طبیعی است.

### ۶.۷ (تسک هفتم) — آرشیو فروشگاه shop (2026-11-04)
- خروجی‌ها: `archive-product.php` (شل داینامیک: هیرو میرور + گرید + empty) + `template-parts/archive/product-filters.php` (aside برش ۱۳۹۴۵۳→۱۵۴۶۷۳ با ۸ توکن شامل درخت دسته/آپشن‌ها) + `template-parts/archive/pagination.php` (nav برش با ۸ توکن) + `inc/Woo/ShopFilters.php` (pre_get_posts@20: s/purchase/price/ip/voltage/sort + per-page کلومپ ۱..۱۰۰).
- آپشن IP/ولتاژ selectها از termهای pa_ip-rating (description پرانتز) و pa_input-voltage.
- pagination: صفحه فعلی button فعال، بقیه <a href="?paged=n>؛ تک‌صفحه‌ای → return؛ next/prev با data-href داینامیک (میرور JS استاتیک — جایگزینی محیطی تست).
- تست ۶.۷: ۲۱/۲۱ (aside و pagination بایت‌دقیق + args فیلترها + کلومپ per-page با ES_FakeQuery).
- استاب جدید: is_post_type_archive بر __wp_is_post_type_archive/__wp_post_type_archive؛ ES_FakeQuery با get/set.


### ۶.۶ (تسک ششم) — مرتبط‌ها و دیدگاه‌ها + ادغام فونت پنل (2026-11-04)
- خروجی‌ها: `related-projects.php` (SEC-A 195281→ کارت-پروژه روشن از متای project-ids CSV)، `related-products.php` (SEC-C هم‌دسته product_cat به‌جز خود، count=3، فیلتر es_product_related_args)، `reviews.php` (پنل کامل تب reviews) + `inc/Woo/Reviews.php` (ذخیره rating/es_review_org در comment_meta + ایمیل placeholder مهمان).
- specs-tabs: پنل reviews وقتی در tabs باشد از reviews.php رندر می‌شود (ob_start) — فعال‌سازی تب همچنان با فیلتر es_product_specs_args[has_reviews] (پیش‌فرض false قفل بایت‌دقیق ۶.۳).
- chip کارت SEC-C = اولین دسته‌ای که دسته مشترک نیست (بج نوع اختصاصی میرور).
- فرم دیدگاه دستی با action=wp-comments-post.php + hidden comment_post_ID؛ فیلدهای میرور data-review-* حفظ شد.
- **آفست‌های پایتونی (کاراکتری) را هرگز در PHP (بایتی) استفاده نکن** — برش مرجع در تست‌ها همیشه با strpos الگویی.
- استاب‌های جدید: get_comments/get_comment_meta/add_comment_meta/get_comment_date (رشته غیرعددی مستقیم)/wp_parse_url؛ get_posts گسترش: include/post__in/post__not_in/posts_per_page/tax_query (بر __es_stub_post_term_map).
- ادغام فونت وزیرمتن پنل: @font-face سه زیرمجموعه از styles.css به assets/admin/admin.css + وراثت button/input/.button + preload در admin_head (AdminAssets::preload_fonts) — تست 21/21 (temp/phase-02…/tests/test-font-vazirmatn.php).


### ۶.۵ (تسک پنجم) — ناحیه خرید محصول (2026-11-04)
- خروجی‌ها: `template-parts/single-product/purchase.php` (برش بایت‌دقیق ۷۰۲۵ بایتی wall-washer از wrapper تا قبل از `</section>`) + `inc/Woo/PurchaseFlow.php` (فیلتر woocommerce_is_purchasable + متن دکمه‌ها).
- purchase.php: رگیون nowdoc با ۱۱ توکن ({ES_STATUS/RATING_HTML/SKU/OLD_HTML/PRICE_HTML/MIN_HTML/SUMMARY_HTML/BTN_HTML/CART_URL/PHONE_URL/PHONE_LINE}) + nowdoc دکمه $ES_BTN (۸ توکن data-product-*) — nowdocها باید **قبل از منطق** بیایند (const fatal در include دوم).
- دوگانه: `Purchasable::is_online` && قیمت≠۰ → دکمه pdp-add-to-cart + قیمت فارسی؛ وگرنه قیمت «استعلامی» + تبدیل button→a با `data-pdp-inquiry` و es_cta_url(). حذف کارت ۵.۱۱: min/step اتریبیوت‌ها فقط حفظ شدند.
- min/step: متای es_min_qty/es_step_qty (پیش‌فرض ۱ — ارقام لاتین در data-*، فارسی در چیپ نمایشی).
- تماس: OptionsOutput::contact_args()['phone'] (پیش‌فرض میرور) + es_option('footer.purchase_phone_display') — کلید phone_url وجود ندارد؛ tel: با preg_replace ساخته می‌شود.
- single-product.php: تزریق purchase در شل ۶.۲ (هیرو→گرید گالری ۷/خرید ۵→specs-tabs→FAQ)؛ رگیون خودش wrapper col-span-5 sticky دارد.
- تست ۶.۵: ۱۵/۱۵ (بایت‌دقیق با ۴ جایگزینی محیطی: cart/tel/img/price-attr) · رگرسیون: همه فایل‌های دارای «نتیجه» سبز (۸۲ فایل اسکن شد).

- 6.4 features/usages/faq/notice: چهار پارتیال single-product (gen_p64.py) — features.php: پنل desc بایت‌دقیق (معرفی+english_titl شرطی+محتوای post_content خام+ریپیتر product-feature با آیکون چک+feature2 دو بخشی [vaule_of_variable] الگوی خواهر+notice آمبر before-sale-c+usages)؛ usages.php: چیپ‌های usage_product + لینک ترم‌های usage_countr (get_term/get_term_link؛ dedup با نام ترم؛ مخفی اگر هر دو خالی)؛ faq.php: آکاردئون data-faq-* بایت‌دقیق (باز=آیتم اول)؛ notice.php: مخفی اگر خالی. specs-tabs: تب docs + پنل کاتالوگ (متا product-catalog-file → es_media_url؛ کارت PDF با target=_blank) اضافه شد. فیلترهای es_product_features_args/es_product_faq_rows. استاب‌های جدید: is_wp_error/get_post_field (__es_stub_posts)/get_term (__es_stub_terms). فیکسچر تست از خود میرور با regex استخراج می‌شود (ZWNJ تضمینی). — 18/18.
- 6.3 specs-tabs: template-parts/single-product/specs-tabs.php (gen_p63.py، برش product/wall-washer.html) — هدر تب‌ها (desc/specs/reviews فقط دارای محتوا؛ فعال = اولین) + پنل specs: جدول از _product_attributes (is_taxonomy→terms.name وگرنه value؛ خالی‌ها حذف، لیبل def.label) + کارت‌های شناسه قانونی [product_id/gs1_id/national_code/hs_id/isic_id/cpc_id با کلاس امرالد فقط GS1] فقط مقدار-دار؛ فیلتر es_product_specs_args (rows/legal/has_desc/has_reviews/rev_count؛ شمارنده دیدگاه get_comments_number فارسی)؛ بستن سکشن همیشه. استاب جدید هارنس: get_comments_number (__es_stub_comment_counts). Woo\Setup گارد is_admin گرفت (قرارداد فاز ۱). شیم v6 بازنویسی شد — recipe کامل در QA log ۶.۳ (loadNodeRuntime+processId، PHP از universal، run با scriptPath نه code، exit صریح، اجرای مستقیم /host). — 17/17 (بایت‌دقیق پنل specs).
- 6.2 single-product hero+gallery: template-parts/single-product/{hero,gallery}.php + single-product.php (شل: main bg-slate-50/50 + گرید ۱۲ستونه، ستون خرید sticky برای 6.5) — برش بایت‌دقیق product/wall-washer.html با gen_p62.py؛ hero: breadcrumb سفارشی (خانه/شاپ es_page_url/دسته term link/چیپ عنوان) + H1 + zireonvan + english_titl شرطی (dir=ltr mono)؛ gallery: data-gallery-* عین میرور (main-img/chip دسته/prev/next/counter فارسی ۱ / N/expand/thumbs با ring/لایت‌باکس + دات‌ها aria لاتین)؛ تصاویر: _thumbnail_id + ProductGallery::get_ids → es_media_url → placeholder light-tree.jpg — 21/21. استاب‌های جدید هارنس: get_the_ID (__es_stub_current_id) + __es_stub_titles در get_the_title. woocommerce.php (شل میرور: get_header → <main> → woocommerce_content → get_footer)؛ inc/Woo/Setup.php (حذف breadcrumb/sidebar/related + woocommerce_show_page_title=false؛ فیلتر woocommerce_enqueue_styles فقط blockui+select2؛ loop_shop_columns از archives.products_columns با کلمپ ۲..۴) — 18/18.

## رویدادهای فاز ۵ (2026-11-04)
- 5.1 قرارداد Registry/SectionManager/فرونت‌پیج + ۱۵ wrapper — 24/24.
- 5.2 hero: gen_hero5.py + Icons + es_media_url/es_link_url — 12/12.
- 5.3 projects: get_posts projects؛ دستی → post__in+orderby=post__in؛ تأخیر rtrim — 10/10.
- 5.4 about: ۱۱ فیلد + circle-check/award — 10/10.
- 5.5 history: ۶ پنل ۱۴اسلاتی + data-history-total (لاتین) + {ES_TOTAL_FA} فارسی — 13/13.
- 5.6 categories: ۶ کارت ۹فیلدی + CTA + هلپرهای termmeta (thumbnail/faq) + آیکون wifi — 12/12.
- 5.7 catalog+services: catalog (۱۳ فیلد؛ file=PDF opt-in وگرنه shop) — services (رپیتر ۵ ردیفی [title,desc,icon,image]؛ کلاس‌های active فقط ردیف ۰؛ شمارنده «N / TOTAL» فارسی؛ preview ردیف ۰؛ building2 کلاس دوبل lucide-building-2) — 13/13. F_CLOSE فقط «>» (ref به </section ختم می‌شود و PREVIEW خودش بستن‌ها را دارد).
- نکته 5.7: svgهای لوسیسک میرور در سکشن خدمات stroke-width=1.7 (نه ۲)؛ js میرور data-service-* را می‌راند → بدون JS سفارشی.
- 5.8 aftersale+customers: aftersale تک‌بلوکی ۱۲فیلدی (نقشه SVG تزئینی ~۳۰KB عیناً حفظ؛ tel:tel:03191091011 typo میرور به‌عنوان default فیلد) — customers رپیتر [title,sub,icon] ×۱۲ پیش‌فرض (میرور لوگو/لینک ندارد؛ کارت icon+شماره+متن)؛ خالی → مخفی؛ 11/11.
- 5.9 blog-preview: WP_Query (posts_per_page فیلد count=4، ignore_sticky)؛ کارت‌ها: permalink/thumbnail/category-chip (fallback «مجله علمی»)/date با es_fa_num/زمان مطالعه (متا es_read_time → برآورد ceil(واژه‌ها/225) با preg_split فارسی)؛ آرشیو = page_for_posts → /blog/؛ خالی → مخفی؛ فیلتر es_blog_preview_query_args/es_blog_preview_args؛ 8/8. استاب‌های هارنس جدید: WP_Query/get_the_title/get_the_excerpt/get_the_date/get_the_category/wp_trim_words (نال‌پذیری صریح برای PHP8.4!).
- 5.10 contact+map: contact تک‌بلوک ۱۹فیلدی (تلفن/ایمیل/ساعات: فیلد → گزینه مرکزی OptionsOutput::contact_args → خالی)؛ map: بستر SVG تزئینی میرور ↔ iframe ادمین با wp_kses (iframe لیست مجاز اتریبیوت)؛ نشانی/کدپستی از گزینه مرکزی در صورت خالی بودن فیلد؛ مخفی اگر desc+phone+email همه خالی (map: title+address)؛ استاب wp_kses به هارنس اضافه شد (اتریبیوت‌فیلتر واقعی)؛ 10/10. نکته: es_option کش استاتیک دارد → بعد از تغییر __wp_options باید es_option_reset_cache() صدا زده شود؛ گزینه‌ها زیر کلید erfansanat_theme_options نستد.
- 5.11 کارت‌ها+quality: سه کارت استاندارد template-parts/cards/{card-product,card-project,card-post}.php (برش از shop/projects/blog.html) — کارت محصول: پوش data-product-* (دسته، purchasable=true_sale!=='false'، قیمت _price (خالی→۰)، ip/voltage از تاکسونومی pa_ip/pa_voltage، views=number2-viewc، rating=_wc_average_rating)؛ badge آنلاین/استعلام؛ تصویر: شاخص→گالری (galerry_produ/_product_image_gallery)→placeholder میرور؛ کیفیت raw اتریبیوت‌ها لاتین + نمایش فارسی. کارت پروژه: project_type/city ترم‌ها + finish-pro-time + es_rating. کارت مقاله: featured (پارامتر)، زمان مطالعه متا→برآورد. سکشن quality (جاافتاده از درخت تسک‌ها — رفیجستری داشت): دو رپیتر features/steps + باند فرآیند؛ آیکون‌های ۹گانه جدید (badge-check/cloud-rain/radio-tower/search/pencil-ruler/wrench/hard-hat/lightbulb) stroke=1.75؛ تأخیرها شامل «0.15000000000000002» میرور؛ 37/37.
- رگرسیون پس از 5.7: فاز ۱ (۸/۸: 154 شرط) فاز ۲ (۹/۹: ۱۶۰ شرط) فاز ۴ (۲۱۲/۲۱۲) فاز ۵ (۹۴/۹۴) — همه سبز.
- 5.12 about-intro + projects-gallery (آخرین دو سکشن ۱۵گانه): معرفی — ۲۳فیلد (badge/mono/since/3×title+desc/2×گالری[تصویر/alt/چیپ/عنوان]/CTA) با default تصاویر میرور؛ گالری — تب‌های [همه/تونل/urban] با ردیپر tabs(key/label/types)، گروه‌بندی get_posts(30,DESC) با تطبیق name ترم project_type، هر تب per_tab=5، سال از متا es_project_year → preg ارقام فارسی از finish-pro-time، خط متا «type · سال Y»، آرشیو es_page_url(projects)، مخفی اگر تب خالی یا بدون گروه؛ فیلترهای es_projects_gallery_args/es_projects_gallery_query_args؛ CSS نیازمندی‌ها در styles.css موجود (line-clamp-2 + [hidden])؛ JS تب‌ها با میرور home2 — بدون پورت. تست جامع: 9/9 (۱۵ سکشن + ترتیب + toggle/reorder + args WP_Query و get_posts) — رگرسیون کامل ۱۲ فایل/۱۶۹ شرط سبز.

## رویدادهای فاز ۲ (2026-11-03)
- Task 2.2 (FieldRenderer+Sanitizer) به ابتدای فاز جابه‌جا شد (وابستگی 2.1) — D-014.
- دارایی‌های ادمین: assets/admin/{admin.css (RTL مدرن), admin.js (تب/AJAX/media/color/repeater با درگ)} — صفر وابستگی خارجی.
- اسکیمای ۵ تب با پیش‌فرض‌های بایت‌به‌بایت میرور استخراج‌شده (تلفن/آدرس/سوشال/کپی‌رایت/لینک‌های ۸گانه فوتر).
- SectionManager: متا `_es_sections` [{id,enabled,order,fields}]؛ سفیدلیست ID؛ حفظ کلیدهای legacy خارج اسکیم (merge رو به جلو)؛ es_render_sections با ادغام پیش‌فرض اسکیم (حالت پیش‌فرض = ظاهر میرور).
- stubهای جدید: add_menu_page/submenu، wp_die/check_ajax_referer (استثنا ES_WpDieException)، wp_send_json_* (استثنا ES_JsonExitException)، post meta، add_meta_box، wp_is_post_autosave/revision، wp_get_attachment_url، wp_kses_post واقعی (strip_tags با لیست مجاز).
- تصحیح رفتار core در تست‌ها: strip_tags محتوای داخل تگ را نگه می‌دارد (sanitize text/textarea).
- رانرهای تست همه فازها: باگ {passed}/{total} → $passed/$total اصلاح شد.

## رویدادهای فاز ۱ (2026-11-03)
- سان‌باکس بین پیام‌ها ریست شد → ترمیم: ZIP میرور داخل ریپو ذخیره شد (`uploads/erfansanat-static-mirror.zip`، gitignore فقط نسخه استخراج‌شده)؛ PHP wasm بازنصب شد.
- تسک 1.1 تا 1.8 اجرا و هرکدام جداگانه کامیت شد؛ تست‌ها: 18/18, 16/16, 18/18, 11/11, 24/24, 21/21, 25/25, 21/21 — فاز: ۸/۸ تسک پاس.
- فایل‌های قالب ساخته‌شده: style.css, functions.php, index.php, inc/{Theme,Autoloader,Helpers,ThemeSetup}.php, inc/Frontend/{DeBloat,AssetManager}.php + assets/ کامل (۱۳MB).
- زیرساخت تست: temp/harness/{wp-stubs.php (۶۲ تابع), README.md} + شیم /home/user/tools/php (بازنویسی کامل).

## تصمیمات جدید فاز ۱
- **D-010:** تسک 1.8 (wp-stubs) به ابتدای فاز جابه‌جا شد چون تست‌های 1.2+ به آن نیاز دارند (در QA log مستند است).
- **D-011:** شیم PHP: ریپو با `createNodeFsMountHandler` به wasm مونت می‌شود (خواندن میرور بدون سینک سنگین)؛ `PHP.run` روی exitCode≠0 استثنا می‌دهد (پاسخ در e.response)؛ wrapper مسیر واقعی را با ES_SCRIPT_PATH تزریق می‌کند (dirname سطح ۴).
- **D-012:** توابع stub باید در گارد function_exists باشند — اعلان بدون شرط در زمان کامپایل bind می‌شود و redeclare fatal می‌دهد حتی با گارد runtime.
- **D-013:** `es_option_reset_cache()` اضافه شد (برای ذخیره ایجکس فاز ۲)؛ `es_stubs_reset` آن را صدا می‌زند.

## پاسخ‌های دریافتی از کارفرما
- Q1: live_slugs · Q2: cpt_services · Q3: compose_from_components · Q4: wc_full  (2026-11-03)
