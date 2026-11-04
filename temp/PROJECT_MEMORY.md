# PROJECT_MEMORY — حافظه زنده پروژه (Active State)

> آخرین به‌روزرسانی: 2026-11-04 · قبل از هر تسک بخوان، بعد از هر تسک آپدیت کن.

## وضعیت فعلی: **PHASE 4 PASSED** — همه ۲۰ تسک فاز ۴ پاس شدند.
```
PHASE 4 PASSED — P04-T001..T020 PASSED (310 checks) — WAITING FOR USER APPROVAL FOR PHASE 5
```
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

## گام بعدی (پس از تایید کاربر)
**PHASE 5** — طبق `temp/MASTER_PLAN.md` (۲۱ تسک اتمیک: صفحه اصلی و سکشن‌ها). فاز ۴ کامل پاس شد (۳۱۰ چک)؛ در انتظار تایید کاربر برای شروع فاز ۵.
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
