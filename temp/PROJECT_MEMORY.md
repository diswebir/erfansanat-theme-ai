# PROJECT MEMORY — حافظه زنده (Active State)

> ⚠️ قبل از شروع هر تسک این فایل خوانده شود و بلافاصله پس از اتمام تسک آپدیت شود.

## وضعیت فعلی
**PHASE 6 IN PROGRESS (7/11 TASKS DONE: 6.1–6.7 · PHASE 5 COMPLETE 12/12)**

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

## رویدادهای فاز ۶ (2026-11-04)

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
