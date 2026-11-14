# PROJECT_MEMORY — حافظه زنده پروژه (Active State)

> آخرین به‌روزرسانی: 2026-11-07 · قبل از هر تسک بخوان، بعد از هر تسک آپدیت کن.

## وضعیت فعلی: **PHASE 8 IN PROGRESS** — فاز ۷ کامل (28/28)؛ فاز ۸: P08-T001..T024 پاس — **فاز ۸ کامل (24/24)**؛ e2e نهایی 2808 چک / 0 رد / exit 0.
```
PHASES 1–6 COMPLETE; P06 runner ALL PASS twice (real-Woo staging verification deferred)
PHASE 7: T001=14, T002=22, T003=12, T004=11, T005=10, T006=19, T007=10, T008=10
ADMIN-PRO: 25 PHP + 7 JSDOM checks; phase-02 regression 287 passed / 0 failed
P07-T009=23 (16 PHP + 7 JSDOM)
P07-T010=12; P07-T011=26; P07-T012=14; P07-T013=19; P07-T014=20; P07-T015=17; P07-T016=13; P07-T017=14; P07-T018=14; P07-T019=12; P07-T020=10; P07-T021=10; P07-T022=13; P07-T023=13(+9 DOM); P07-T024=13; P07-T025=14(+8 DOM); P07-T026=15; P07-T027=15; P07-T028=17 (+8 DOM)
PHASE 7 COMPLETE: run-phase-tests.sh → ALL PASS (28 PHP suites + 9 DOM suites).
P08-T001=20 (Ajax contract: registry/nonce/whitelist/JSON envelope + ES_AJAX config)
P08-T002=14 (AjaxSearch::search_products — title/meta english_titl,zireonvan,_sku/product_cat؛ سقف ۱۲؛ visibility exclusion؛ normalize ی/ک)
P08-T003=13 (content_search projects+post؛ type=project/post؛ ترم city/project_type با variants ZWNJ؛ term_variants برای product_cat هم)
P08-T004=12 (+10 DOM) (search.js: debounce 250ms/abort/seq/keyboard/empty؛ enqueue es-search dep es-main؛ گارد __esSearchInit)
P08-T005=15 (SmartFilters::build_product_query/query_products — whitelist product_cat+18 pa_*، price NUMERIC BETWEEN، true_sale، orderby whitelist، per_page≤48؛ بدون SQL خام)
P08-T006=16 (+9 DOM) (product-filters part+JS؛ endpoint es_product_filters در SmartFilters::boot/init؛ شل آرشیو پارت را include می‌کند؛ URL state es_cats/es_q/es_price/es_mode/es_sort/es_page؛ window.ES_PRODUCT_FILTERS + __esFiltersExtraBody هوک T007/T008؛ ajax-bridge.php برای DOM واقعی)
P08-T007=12 (+4 DOM در سوئیت T006) (sanitize_price/sanitize_sale_mode عمومی؛ JS اسلایدر data-product-price-range و رادیو data-product-purchase-mode را می‌خواند؛ at-max یعنی بی‌فیلتر؛ es_price/es_mode در URL)
P08-T008=11 (+5 DOM → مجموع سوئیت 19) (گروه‌های pa_* در product-filters.php با data-product-attr-group/attr-filter؛ سواچ pa_color از فیلتر es_product_color_swatch_map؛ JS state.attrs → body pa_* و URL es_attr_{tax}؛ کلیدها با خط تیره: pa_ip-rating، pa_input-voltage)
P08-T009=14 (+5 DOM → مجموع سوئیت 24) (sort select مرجع→ORDERBY؛ renderPagination سرور-محور داخل data-pagination-root با کلاس‌های مرجع؛ clamp صفحه خارج از بازه با probe page-1 برای SQLite quirk؛ ریست کامل شامل سورت/صفحه)
P08-T010=18 (+9 DOM) (build_project_query/query_projects/project_term_counts facet-aware + endpoint es_project_filters؛ project-filters.php گروه type/city با data-project-type-filter/-city-filter و data-project-term-count؛ project-filters.js با es_ptypes/es_pcities/es_pq/es_psort/es_ppage؛ شل پروژه پارت را در سایدبار+drawer include می‌کند — وابستگی ثبت‌شده)
P08-T011=12 (کش نسخه‌دار cache_get/cache_set/bust_cache با option es_filters_cache_version + هوک‌های save_post/deleted_post/set_object_terms/terms؛ _prime_post_caches قبل از رندر کارت‌ها؛ search_products/search_content هم کش؛ بنچمارک ۱۰۰/۱۰۰ p95<200ms)
P08-T012=22 (Schema چهار فرم contact/quote/recruitment/feedback + validate/sanitize؛ Entries::store/status/mark_read/query/delete با CPT خصوصی es_form_entry و meta _es_entry_data/_form/_status/_meta؛ boot از functions.php — وابستگی ثبت‌شده؛ در QA هیچ کاربر ادمین نیست → ادمین ساختنی در تست)
P08-T013=18 (forms/contact.php با name attrs/honeypot es_cf_hp/nonce/error box + inline JS stopImmediatePropagation بر main.js؛ ContactForm::submit + endpoint es_contact_submit؛ section-form به پارت ارجاع — وابستگی ثبت‌شده؛ دو چک P07-T021 به قرارداد جدید به‌روز شد)
P08-T014=22 (QuoteForm::submit با resolve_content product/projects فقط publish؛ CTA استعلامی purchase.php → button data-quote-open + مودال forms/quote.php در حالت استعلامی — وابستگی ثبت‌شده؛ endpoint es_quote_submit)
P08-T015=27 (RecruitmentForm::submit با whitelist واحد rnd/project/accounting/it + experience و رزومه اختیاری pdf/doc/docx/rtf/txt تا 2MB در uploads/es-resumes با نام تصادفی؛ endpoint es_recruitment_submit با $_FILES؛ صفحه استخدام به پارت ارجاع — وابستگی؛ سه چک P07-T025 به قرارداد جدید به‌روز شد)
P08-T016=20 (FeedbackForm با نرمال‌سازی ارقام فارسی و بازه ۱..۵؛ وضعیت بررسی _es_feedback_status pending/approve؛ فرم مستقل forms/feedback.php با endpoint es_feedback_submit)
P08-T017=17 (FormEntries زیرمنوی پیشخوان: لیست فیلترشدنی نوع/وضعیت/بازه تاریخ + رندر escape‌شده + اکشن‌های read/unread/approve/delete با nonce و manage_options؛ SC-02 subscriber → WP_Error)
P08-T018=28 (SpamGuard مشترک: honeypot_ok + nonce_ok + rate-limit per-IP+scope با transient و reset_all؛ Mailer با گیرنده option es_form_recipient + filter es_form_recipient → admin_email؛ ایمیل بعد از store در هر ۴ فرم — خطای ایمیل ورودی را نگه می‌دارد؛ Ajax::dispatch nonce را از SpamGuard می‌گیرد)
P08-T019=20 (Core/Seo.php: canonical فقط در شکاف‌های هسته — front/posts-mode و آرشیو ترم — singular را core می‌پوشاند؛ پلاگین SEO فعال → تم ساکت + remove_action rel_canonical؛ JSON-LD سه‌گانه Organization/Article/Product با es_seo_product_data filter؛ audit_hreflang کمکی؛ hreflang مالکش Core\Hreflang باقی می‌ماند)
P08-T024=15 (run-all-phases-e2e.sh با گزارش e2e-report.md + e2e-state.txt؛ package-theme.sh → temp/dist/erfansanat.zip فقط پوشه erfansanat/؛ RELEASE_CHECKLIST.md با جدول واقعی ۸ فاز؛ E2E فرمی ۴ فرم + اکشن‌های ادمین + zip با ZipArchive؛ نصب تمیز با بسته اثبات شد)
P08-T023=17 (DEACTIVATION_RUNBOOK.md: ترتیب ۶ مرحله‌ای بک‌آپ→قالب→بررسی→Elementor→Jet*→بررسی+ممیزی؛ سه نقطه rollback با sha256؛ فقط Deactivate نه uninstall؛ چک‌لیست ۲۰۰؛ سناریوی rollback روی کپی scratch تمرین شد — active_plugins بدون Jet*/Elementor و متاهای legacy دست‌نخورده)
P08-T022=20 (staging-migration-dry-run.sh + MIGRATION_REPORT_TEMPLATE.md: کپی scratch با symlink + DB واقعی، بک‌آپ + sha256، شمارش پست/ترم/۳۷ کلید legacy قبل-بعد، drift=0، گزارش markdown؛ تست جریان را in-process بازتولید می‌کند چون php-wasm subprocess ندارد — اسکریپت با bash هارنس اثبات شد)
P08-T021=13 (ممیزی ریسپانسیو/a11y: بریک‌پوینت‌های CSS 640/768/1024؛ بدون عرض ثابت ≥۱۰۰۰px؛ همه img دارای alt؛ label هر فیلد فرم؛ مودال/دراور با aria-label/role dialog؛ آکاردئون button+aria-expanded؛ Escape در main.js/certificates/مودال استعلام؛ onclick فقط روی button/input/a؛ اسنپ‌شات سکشن‌های خانه پایدار + viewport meta + max-w-[1300px])
P08-T020=12 (ممیزی کارایی/escaping: اسکن echo خام بدون phpcs:ignore=0؛ رندر بدون warning صفحه اصلی/تک‌مقاله/آرشیو؛ بودجه asset styles<=8/scripts<=6 و کوئری front<=250/single<=150؛ assetهای صف‌شده همه داخلی؛ emoji حذف. **باگ واقعی topbar.php:36 اصلاح شد — # داخل regex باید \# باشد (وابستگی ثبت‌شده)**)
NEXT: فاز ۸ کامل شد (24/24). تمام تسک‌های برنچ انجام شده؛ گام بعدی فقط درخواست کاربر (مثلاً PR یا تسک‌های جدید).
```
- **درخواست جاری کاربر (2026-11-07):** «کامل روی فاز 7 شروع به کار کن و همه تسک‌هاشو کامل کن» — اجرای پیوسته T014..T028. اجرای اتمیک فاز ۷ ادامه دارد؛ هر کامیت به برنچ ثابت Arena پوش می‌شود.
- **بازیابی محیط:** تاریخچه محلی به base ریست شده بود؛ با fetch + reset --mixed به `41316d2` بازیابی شد (فایل‌ها سالم، working tree تمیز). هارنس بازسازی و قالب/rewrite فعال شد.
- **ارتقای ادمین تحویل شد:** `97d24cc` پیشخوان آماری، رفع باگ ذخیره link، فونت فارسی و هشدار تغییرات ذخیره‌نشده؛ `41316d2` لاگ QA.
- مطالب پایین‌تر تاریخچه فازهای قبلی‌اند؛ وضعیت جاری همین بلوک و MASTER_PLAN است.

- **درخواست جدید کاربر (2026-11-04):** «ادامه بده و موارد باقی‌مانده را کامل کن» + «کد پنل پیشخوان قالب را با فونت وزیر متن به‌صورت کامل ادغام کن». → پس از T007، یک تسک الحاقی **P02-T019** برای یکپارچه‌سازی وزیرمتن در داشبورد/متاباکس‌های ادمین (بدون نشت به فرانت‌اند، حفظ dashicons) اجرا می‌شود؛ سپس ادامه فاز ۶.
- **فاز ۶ تاکنون:** T001 shim سازگاری QA (۳۲)؛ T002 shell تک‌محصول (۲۷)؛ T003 گالری+لایت‌باکس (۱۸)؛ T004 بلوک عنوان summary.php (۱۷)؛ T005 شناسه‌های قانونی legal-ids.php (۱۹)؛ T006 کارت خرید purchase.php با منطق true_sale (۲۳)؛ T007 سواچ/تعداد (۴۳ = ۳۱ PHP + ۱۲ JSDOM روی main.js واقعی). **مجموع تجمعی: ۱۳۹۲ + ۱۷۹ = ۱۵۷۱ چک سبز.**
- **معماری تک‌محصول:** `content-single-product.php` هوک‌های استاندارد Woo را با الگوی hook-or-fallback اجرا می‌کند. گالری → `woocommerce_before_single_product_summary` (T003)؛ بلوک عنوان → `get_template_part('template-parts/product/summary')` در hero (T004)؛ کارت خرید → `woocommerce_single_product_summary` (T006) با هوک‌های داخلی `es_product_summary_mid` (مشخصات/سواچ) و `es_product_summary_after` (اطلاعیه/کاتالوگ)؛ شناسه‌های قانونی → `es_product_specs_panel` (T005). محصول متغیر واقعی Woo: `purchase.php` فرم `woocommerce/single-product/variable.php` را جایگزین stepper/swatch ایستا می‌کند (وقتی `WC_VERSION` تعریف شده). fallback بدون Woo: سواچ `data-opt-*` + stepper `data-pdp-qty-*` + guard حداکثر `product-quantity.js` (فقط وقتی Woo نیست). `Core\ProductQuantity::rules/validate` قواعد min/max/step را نرمال و اعتبارسنجی می‌کند (متاهای `_min_qty/_max_qty/_step_qty`).
- **وابستگی‌های ثبت‌شده فاز ۶:** functions.php (وایرینگ هوک‌ها + enqueue guard تعداد)؛ shim (متدهای متغیر P06-T007)؛ AssetManager دست‌نخورده (enqueue از functions.php). `main.js` هرگز ویرایش نشد؛ تست DOM با JSDOM وابستگی QA شد (`temp/tools` jsdom + setup-harness).
- **فاز ۵ کامل شد (صفحه اصلی و سکشن‌های ماژولار):** controller `front-page.php` (رندر ۱۳ سکشن از `Sections\Renderer` داخل `<main>`، CTA/فوتر فقط از `get_footer()`)؛ ۳ کارت استاندارد پروژه/محصول/مقاله (بازاستفاده در سکشن‌ها و آرشیوها)؛ ۱۳ سکشن صفحه اصلی با DOM عین مرجع و هوک‌های کامل main.js (هیرو، انواع پروژه، پروژه‌های منتخب تب‌دار، درباره+آمار شمارنده، بومی‌سازی، تاریخچه اسکرولی، محصولات منتخب، CTA کاتالوگ، خدمات تب‌دار با ۱۷ هوک، استانداردها، پشتیبانی+نقشه ایران، مشتریان، پیش‌نمایش وبلاگ)؛ همه option/repeater-driven با پیش‌فرض فیلترپذیر و حالت خالی امن؛ کوئری‌های publish-only با بودجه ≤۶۰ (واقعی ۱۱ خالی/۱۸ با داده)؛ رگرسیون جامع ترتیب/خاموش‌سازی/حالت خالی.
- نتیجه runner فاز ۵: `PHASE RESULT: ALL PASS` (۲۱ تست، **۴۱۰ چک پاس، ۰ fail/pending**). رگرسیون P02-T018 Renderer بعد از هر part=۱۰/۱۰؛ رگرسیون کامل فازهای ۱–۴ پاس (۱۱۱+۲۶۰+۳۰۱+۳۱۰). **مجموع تجمعی: ۱۳۹۲ چک.**
- شمارش چک هر تسک فاز ۵: T001=14، T002=24، T003=31، T004=18، T005=20، T006=16، T007=23، T008=17، T009=15، T010=20، T011=14، T012=15، T013=18، T014=15، T015=23، T016=24، T017=20، T018=21، T019=19، T020=22، T021=21.
- **فاز ۴ کامل شد (هدر، مگامنو، منوی موبایل و فوتر):** ۴ مکان منو + fallback واقعی (`Core\Menus`)؛ پوسته header/footer با DOM عین مرجع؛ topbar/brand-row از options؛ `DesktopWalker` (مگامنوهای محصولات/پروژه‌ها/شرکتی از taxonomy + آپشن‌ها)؛ `MobileWalker` (کشو + آکاردئون + تب‌های داخلی مگا)؛ `navigation.js` کمکی (Escape/focus-trap/restore + scroll-spy لندینگ — main.js دست‌نخورده)؛ مودال جستجو (نتایج واقعی DB + data-search-item)؛ مودال سبد (payload واقعی WC + فیلتر `es_cart_modal_data` + حالت خالی واقعی + quick-add)؛ footer-info (برند/دسترسی سریع/دسته‌ها/تماس از options)؛ footer پایانی (کپی‌رایت/شبکه‌های امن/scroll-top)؛ CTA مشترک `id=contact` (۹ کلید `cta_*`، خاموش‌شدنی)؛ `Core\Hreflang` (link استاندارد از hrefmeta_*، تعویق به SEOPress/hreflangpch)؛ `TolidatWalker` (لیست تخت فوتر + fallback هم‌کلاس)؛ `LandingWalker` (لنگر سکشن‌ها + is-active + رندر opt-in)؛ ممیزی نهایی parity/a11y با fixture کامل.
- نتیجه runner فاز ۴: `PHASE RESULT: ALL PASS` (۲۰ تست، **۳۱۰ چک پاس، ۰ fail/pending**). رگرسیون کامل فازهای ۱–۳ هم پاس (۱۱۱+۲۶۰+۳۰۱). **مجموع تجمعی: ۹۸۲ چک.**
- شمارش چک هر تسک: T001=14، T002=15، T003=9، T004=15، T005=13، T006=10، T007=14، T008=11، T009=16، T010=12، T011=11، T012=16، T013=18، T014=11، T015=14، T016=22، T017=23، T018=15، T019=18، T020=33.
- **فاز ۳ کامل شد (CPTها، تکسونومی‌ها و متاباکس‌های Legacy):** CPT بومی `projects` و `services`؛ تکسونومی‌های `project_type`/`city` (فارسی‌پذیر)؛ حفظ rewrite ووکامرس (`product`=/product/، `product_cat` با with_front=true و سلسله‌مراتب، `product-tag`، `product_brand`) بدون ثبت مجدد؛ نقشه ۱۸ ویژگی `pa_*`؛ flush نسخه‌دار فقط در فعال‌سازی/ارتقا؛ ریدایرکت ۳۰۱ aliasهای محصول/دسته/پروژه/شهر/نوع (نگاشت فیلترپذیر برای اسلاگ فارسی لایو)؛ ستون‌ها/فیلترهای ادمین پروژه؛ Codec متاهای Legacy (PHP/JSON item-0/list، حفظ فرمت، wp_slash-safe)؛ متاباکس‌های محصول (شناسه‌های صنعتی، عنوان/فروش، کاتالوگ/گالری با fallback، روابط، repeater مشخصات/کاربرد/FAQ)؛ Quick/Bulk Edit؛ متاباکس‌های پروژه (گالری ۵ کلیدی، تاریخ/محصولات)؛ متای دسته (thumbnail/FAQ)؛ Hreflang (پست/ترم)؛ شمارنده بازدید اتمیک.
- نتیجه runner فاز ۳: `PHASE RESULT: ALL PASS` (۲۴ تست، **۳۰۱ چک پاس، ۰ fail/pending**).
- شمارش چک هر تسک: T001=15، T002=12، T003=15، T004=14، T005=13، T006=10، T007=10، T008=14، T009=12، T010=12، T011=12، T012=16، T013=10، T014=10، T015=16، T016=10، T017=13، T018=11، T019=13، T020=16، T021=10، T022=12، T023=11، T024=14.
- **فاز ۲ کامل شد (داشبورد اختصاصی + Section Builder):** Schema/Store آپشن‌ها (`erfansanat_theme_options`)، `es_option()` با کش، منوی ادمین + AJAX امن (nonce/cap)، UI تب‌بندی RTL/ARIA، FieldRenderer (پایه/media/link/icon/repeater)، Sanitizer per-type، تب‌های Header/MegaMenu/Footer/Brand/Archive، و Section Builder کامل: `Sections\Schema` (۱۳ سکشن خانه + about/contact/arya)، `SectionManager` (متای نسخه‌دار `_es_sections_v1`، ترتیب/فعال/شناسه یکتا، ویرایش فیلد schema-only)، `Admin\SectionMetabox` (drag&drop + AJAX امن)، `Sections\Renderer` (رندر ترتیبی enabled-only + کش + fallback امن).
- نتیجه runner فاز ۲: `PHASE RESULT: ALL PASS` (۱۸ تست، **۲۶۰ چک پاس، ۰ fail/pending**).
- شمارش چک هر تسک: T001=16، T002=10، T003=9، T004=24، T005=30، T006=19، T007=18، T008=13، T009=24، T010=11، T011=7، T012=10، T013=10، T014=9، T015=14، T016=12، T017=14، T018=10.
- **فاز ۱ کامل شد:** bootstrap/constants، Autoloader، Theme supports، fallback index، DeBloat، انتقال CSS/فونت/JS/تصاویر/ویدیو، AssetManager نسخه‌گذاری/enqueue شرطی، بسته‌بندی zip.
- نتیجه runner فاز ۱: `PHASE RESULT: PASS` (۱۲ تست، ۰ fail/pending). HTTP asset check: 27/27 OK. zip فقط `erfansanat/`.
- Stage 0 ✅ (اسکن + هارنس + ۴ فایل حافظه) — کاربر به همه ۷ سوال پاسخ داد.
- Stage 1 ✅ نسخه اول: ۸ فاز / ۲۲ تسک.
- **Stage 1 بازطراحی اتمیک ✅ طبق درخواست کاربر:** ۸ فاز / **۱۷۵ تسک اتمیک** با وابستگی، فایل هدف، منبع، کلید داده، شروط پذیرش AC و سناریوی تست SC.
- شمارش فازها: P1=12، P2=18، P3=24، P4=20، P5=21، P6=28، P7=28، P8=24.
- نگاشت تسک‌های قبلی: `temp/LEGACY_TASK_MAP.md` · فهرست: `temp/ATOMIC_TASK_INDEX.md` · برنامه: `temp/MASTER_PLAN.md`.
- کاربر شروع Phase 1 را تایید کرد. **P01-T001 پاس شد**: `erfansanat/style.css` + حداقل `erfansanat/index.php` ساخته شد؛ تست 12/12 پاس. CSS استاتیک هنوز کپی نشده.
- اصلاح حین اجرا: WP پوسته کلاسیک بدون `index.php` خطای `theme_no_index` می‌دهد؛ بنابراین P01-T001 شامل حداقل index.php شد. fallback کامل در P01-T005 می‌ماند.

## پاسخ‌های کاربر به سوالات Stage 0 (ثبت قطعی — 2026-11-03)
1. **صفحات غایب** (`arya`, `my-account`, `auth`, `search`, `404`): ✅ ساخته شوند با همان design system.
2. **خدمات:** ✅ `services.html` = برگه `smartelement` (11984). ۵ تک‌خدمت → **CPT بومی `services`** (پیش‌فرضِ گزینه اول؛ اسلاگ `services`، آرشیو مستقل ندارد — آرشیو همان برگه smartelement).
3. **Aliasها:** ساختار اصلی حفظ می‌شود. ⚠️ نکته حیاتی کاربر: در سایت لایو اسلاگ ترم‌ها **فارسی** است (مثال: `https://erfansanat.com/city/اصفهان/`). پس قالب‌ها باید **slug-agnostic** باشند؛ اسلاگ‌های انگلیسی میرور فقط مرجع DOM هستند. مسیرهای قدیمی → **ریدایرکت 301** به ساختار اصلی (نگاشت اسلاگ هنگام استقرار از دیتابیس لایو).
4. **برگه `project` (8366):** ✅ همان آرشیو پروژه‌ها → **ریدایرکت 301 به `/projects/`**.
5. **هیرو صفحه اصلی:** ✅ در تنظیمات قالب قابل تنظیم؛ **ویدیوی اصلی جداگانه** (Media) و **بقیه اسلایدها از بین پروژه‌ها انتخاب‌شدنی** (Repeater آیدی پروژه).
6. **وبلاگ:** ✅ TOC + بخش دیدگاه **با همان سبک طراحی** اضافه شود (مجوز طراحی جدید فقط برای این دو کامپوننت).
7. **تست ووکامرس:** ✅ اوکی — تلاش از گیت‌هاب؛ در صورت عدم امکان، shim/stub + نهایی‌سازی روی استیجینگ.

## تصمیمات فنی (تاکنون)
- **D8 — محل سورس قالب:** پوشه `erfansanat/` در ریشه ریپو (نه خود ریشه). دلیل: ریشه شامل `static-mirror/` و `temp/` است که نباید در zip قالب بروند. یک mu-plugin در محیط تست، ریشه ریپو را به‌عنوان theme directory ثبت می‌کند (`register_theme_directory`) تا قالب بدون کپی کشف شود. بیلد/زیپ نهایی فقط `erfansanat/` را بسته‌بندی می‌کند.
- محیط تست WP 7.1.4 + PHP 8.4.27 (WASM) + SQLite در `temp/wp/` (gitignore). بازسازی: `bash temp/tools/setup-harness.sh`.
- `static-mirror/` در Git (مرجع طراحی + تست DOM). سنگین‌ها (`temp/wp`, `node_modules`, `bin`) gitignore.
- **ووکامرس:** دانلود zip بیلدشده از release گیت‌هاب ممکن نشد (asset به هاست مسدود `objects.githubusercontent.com` ریدایرکت می‌شود؛ TLS EOF). → فاز ۶ با **shim سازگاری** (ثبت `product`/`product_cat`/`product_tag` + stub توابع WC موردنیاز قالب) تست می‌شود؛ نهایی‌سازی روی استیجینگ واقعی.
- تست‌ها: plain-PHP با `temp/tests/bootstrap.php` (توابع `es_check/es_file/es_contains/es_load_wp/es_activate_theme/es_summary`). اجرا: `php temp/phase-XX/tests/test-task-YY.php`.

## گام بعدی
**PHASE 6 ادامه دارد** — کاربر «ادامه بده و موارد باقی‌مانده را کامل کن» داد. T008..T028 به‌ترتیب اجرا می‌شوند؛ به‌علاوه تسک الحاقی P02-T019 (وزیرمتن در پنل پیشخوان). فاز ۵ کامل پاس شد (**۴۱۰ چک**).
- **معماری فاز ۵ برای فازهای بعد:** ۱۳ سکشن صفحه اصلی از `Sections\Renderer::render($id,'home')` در `front-page.php` (داخل `<main>` ساده). هر سکشن `template-parts/sections/home/section-{type}.php`؛ blog-preview → `section-blog.php`. ترتیب/فعال‌سازی از متای `_es_sections_v1` (`SectionManager`) با fallback `Schema::defaults('home')`؛ ذخیره `erfansanat_sections_updated` → `Renderer::invalidate`. CTA `id=contact` + فوتر فقط از `get_footer()` (هرگز در front-page تکرار نمی‌شود).
- **کارت‌های استاندارد (بازاستفاده در آرشیوها):** `card-project` (data-project-item + فیلترهای rating/client)، `card-product` (data-product-item + SKU/_min_qty/_wc_average_rating + نشان online/inquiry؛ **data-product-category نام دسته است نه slug**)، `card-post` (sticky ★، زمان مطالعه /200wpm، تاریخ فارسی، wp_trim_words 35). فراخوانی: `get_template_part('template-parts/cards/card-X', null, ['post_id'=>get_the_ID()])`.
- **الگوی repeater خالی:** پیش‌فرض‌های فیلترپذیر در template (عمدی — سکشن ناپدید نمی‌شود). آیتم خالی (بدون عنوان/توضیح) رندر نمی‌شود. کلیدهای اضافهٔ فقط-پیش‌فرض (badge استانداردها، url مشتریان، description مشتریان) چون در Schema نیستند، روی آیتم ذخیره‌شده حذف می‌شوند.
- **هوک‌های main.js که سکشن‌ها تولید می‌کنند:** هیرو `initHomeHeroVariants`؛ تاریخچه `data-history-scroll-track/stage/step-label/progress-bar` + `data-home2-history-btn/panel/prev/next`؛ پروژه‌های منتخب `data-home2-port-panel` + کلاس‌های تب/نشان؛ خدمات **۱۷ هوک** `data-service-*` (ردیف‌ها + پنل پیش‌نمایش، `initHomeServices`). main.js هرگز ویرایش نشد.
- **فیلترهای جدید فاز ۵:** `es_products_section_category/eyebrow`، `es_services_icons/section_count/fallback_image/eyebrow`، `es_standards_icon_svgs/default_items/process_steps/eyebrow/subheading`، `es_support_stats/eyebrow/coverage/default_content`، `es_clients_default_logos/eyebrow/subheading`، `es_blog_preview_eyebrow/subheading/archive_label/archive_url`، `es_catalog_eyebrow/note/bg_image/cover_image/version`، `es_history_*`، `es_about_*`، `es_localization_*`.
- **آیکون‌ها:** `FieldRenderer::allowed_icons()` فقط ۱۴ slug (phone/mobile/email/location/clock/instagram/telegram/linkedin/whatsapp/aparat/check/star/shield/truck)؛ Sanitizer مقدار ناشناخته را رد می‌کند. نگاشت slug→SVG در template (فیلترپذیر) با fallback امن.
- **بودجه کوئری خانه:** رندر کامل ۱۱ کوئری (خالی) / ۱۸ (با داده) — بدون N+1؛ گارد رگرسیون ≤۶۰ (T021).
- **محدودیت هارنس (تکرار):** `get_header()`/`get_footer()` یک‌بار در هر درخواست؛ تست تمام‌صفحه = یک رندر واحد. `wp rewrite flush` در WASM شکست می‌خورد → `wp eval 'flush_rewrite_rules(true);'`.
- معماری فاز ۴ برای فازهای بعد: هدر/فوتر کامل و parity هستند؛ `get_header()`/`get_footer()` پوسته را می‌سازند و `main` با `pt-28` باز می‌شود. سکشن‌های صفحه اصلی (فاز ۵) باید داخل `main` و از طریق `Sections\Renderer` رندر شوند؛ CTA مشترک `id=contact` خودکار قبل از footer می‌آید (با `cta_enabled` خاموش‌شدنی).
- `Core\Menus::render($loc,$args)` echo-aware با fallback؛ `page_url($slug)` برای لینک برگه‌ها (فیلتر `es_menu_page_url`). واکرها در `Core/NavWalkers.php` (یک فایل، چند کلاس — require دستی در functions.php): DesktopWalker/MobileWalker/TolidatWalker/LandingWalker.
- `LandingWalker::render()` فقط وقتی منوی landing اختصاص‌یافته و پر باشد خروجی می‌دهد (برای page-templates لندینگ در فاز ۷). `data-landing-link` + scroll-spy در navigation.js.
- `Core\Hreflang` روی `wp_head` اولویت ۵؛ با فعال‌بودن SEOPress/hreflangpch خودکار خاموش می‌شود (فیلتر `es_hreflang_enabled`).
- آپشن‌های جدید فاز ۴: ۹ کلید `cta_*` در تب footer + `FooterTab::cta()`. فیلترها: `es_footer_quick_links`، `es_cart_modal_data`، `es_mega_products_tabs`.
- **محدودیت هارنس:** `get_header()`+`get_footer()` در هر درخواست فقط یک‌بار قابل فراخوانی است (wp_footer دوم بافر را خالی می‌کند). تست‌های رندر چندحالته باید از `get_template_part` مستقیم یا رندر واحد + چک لایه داده استفاده کنند.
- انحراف ثبت‌شده: دکمه `data-cart-modal-restore` مرجع (نمونه‌ساز) با لینک واقعی فروشگاه جایگزین شد (P04-T013). اتریبیوت‌های `custom` (باقی‌مانده Framer) و کلاس `grid-Floor` در مرجع بی‌اثرند؛ `custom` حذف شد، `grid-Floor` برای parity نگه داشته شد.
- نکته معماری فاز ۳ برای فازهای بعد: همه کلیدهای متای legacy با املای دقیق در متاباکس‌ها مدیریت می‌شوند. `Core\LegacyMeta` codec مشترک (get/set/get_for_term/set_for_term) فرمت PHP/JSON item-0/list را حفظ می‌کند و **wp_slash** قبل از update_metadata می‌زند (هسته wp_unslash می‌کند). هرگز کلید با املای اصلاح‌شده نساز.
- `Core\Rewrites` فقط فیلتر args می‌زند (ثبت مجدد product/product_cat ممنوع). flush نسخه‌دار (`es_rewrite_version`) فقط در after_switch_theme/admin_init.
- `Core\LegacyRedirects::resolve()` نگاشت alias→canonical (فیلترهای `es_legacy_redirects_product_categories`/`_cities`/`_project_types` برای اسلاگ فارسی لایو).
- `Core\ViewCounter` افزایش اتمیک SQL (`meta_value + 1`)؛ `number-viewc` فقط fallback خواندن.
- template-partهای سکشن‌ها هنوز slice نشده‌اند (فاز ۵/۷)؛ `Sections\Renderer` fallback امن می‌دهد.

## نکات یادآوری برای تسک‌های آینده
- املای تاریخی متاها مقدس است: `foure-image`, `galerry_produ`, `english_titl`, `vaule_of_variable`, `usage_countr`, `mortabet_mahsole_2`, `number2-viewc`.
- `product_cat` اسلاگ واقعی (نه `product-category`)؛ `with_front=true` فقط همین تکسونومی.
- اسلاگ ترم‌های لایو فارسی است → هیچ کوئری/لینکی نباید به اسلاگ انگلیسی میرور هاردکد شود.
- هدر/فوتر تماماً `data-*` + کلاس Tailwind (بدون id) → Walkerها همان DOM را تولید کنند.
- کارت‌های آرشیو `data-product-*`/`data-project-*` را داینامیک پر کنند.
- هیرو: ویدیوی اصلی جدا + اسلایدهای انتخابی از پروژه‌ها (D5).
- وبلاگ: TOC + دیدگاه با همان سبک (D6). برگه `project` → 301 به `/projects/` (D4).
- هر تسک = ۶ گام + کامیت روی برنچ `arena/7378effd-erfansanat-theme-ai`.
