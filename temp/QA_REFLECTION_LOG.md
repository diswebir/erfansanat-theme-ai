# QA_REFLECTION_LOG — لاگ تفکر، تست و خوداصلاحی

> هر تسک: ۱) خواندن حافظه، ۲) تحلیل، ۳) نوشتن تست، ۴) پیاده‌سازی، ۵) اجرا و خوداصلاحی، ۶) به‌روزرسانی حافظه.
> این فایل «دفترچه دیده‌بان» است: هر باگ کشف‌شده، هر تغییر مسیر و هر دلیل فنی که بعداً لازم می‌شود.

---

## STAGE 0 — Discovery (2026-11-03)

### S0-1. بررسی محیط (Harness probing)
| تلاش | نتیجه | خوداصلاحی |
|---|---|---|
| `apt-get install php` | ❌ `deb.debian.org` مسدود (فقط github/npm/pypi مجاز) | — |
| static-php-cli release | ❌ `release-assets.githubusercontent.com` مسدود (۳۵ bytes/sec قطع) | — |
| `shivammathur/php-builder` | ❌ همان مسیر release-assets | — |
| **`@php-wasm/node` از registry.npmjs.org** | ✅ PHP 8.3.36 با mysqli, sqlite3, PDO, mbstring, dom, gd, intl, zip | **مسیر نهایی** |
| MySQL/MariaDB | ❌ apt مسدود | ✅ SQLite drop-in (`sqlite-database-integration` v2.1.13) |
| WP-CLI | ❌ release-assets | ✅ اسکریپت‌های PHP مستقیم روی `wp-load.php` |
| WordPress | ✅ `codeload.github.com` (۶.۷.۱) | ✅ نصب شد + `wp_install()` |

**درس:** قبل از فرض «ابزار هست»، باید وجودش را تست کرد. سه تلاش بی‌ثمر قبل از یافتن مسیر npm مصرف شد؛ ثبت می‌شود تا در فازهای بعد تکرار نشود.

### S0-2. اسکن استاتیک — یافته‌های غیرمنتظره
1. **هدر/فوتر مشترک:** ۱۲۸ فایل، فقط یک قالب هدر و یک قالب فوتر. تفاوت‌ها صرفاً `../` و کلاس حالت فعال (`bg-emerald-50 text-emerald-800`). → یک `header.php`/`footer.php` کافی است (D3).
2. **۳۶ گروه تکراری:** `product/x.html` = `products/x.html`، `shop.html` = `products.html`، `product_cat/y.html` = `products/category-*.html` (هش بدنه یکتاست بعد از نرمال‌سازی). → ۱۲۸ فایل = ۹۲ طرح واقعی (D4).
3. **بقایای React:** `custom="0.03"` روی کارت‌ها و `data-scroll-root` — اتریبیوت `custom` هیچ معنای HTML/JS ندارد؛ در PHP باید حذف شود (Task 4.1/5.1).
4. **`styles.css` یک فایل مینیفای‌شده Tailwind** (۱۹۰KB, ۲ خط) با ۳ `@font-face` که مسیر `../fonts/*.woff2` دارد → انتقال باید نسبت `assets/css/` → `assets/fonts/` را حفظ کند (Task 1.3).
5. **`main.js` با ۲۲۵ `querySelector`** و ۱۶ تابع `init*`؛ قرارداد PHP↔JS همان `data-*` است. هر تغییری در `data-*` باعث خاموشی تعاملات می‌شود (R1/D5).
6. **فیلترها کاملاً سمت کلاینت‌اند:** `data-product-category`, `data-product-price`, `data-product-ip` … روی خود کارت. پس فیلتر ایجکس فاز ۸ باید همان اتریبیوت‌ها را در HTML تولید کند (نه JSON جدا).

### S0-3. مسائل حل‌شده در هارنس تست
| مشکل | راه‌حل |
|---|---|
| `PHPLoader.processId must be set before init` | `loadNodeRuntime('8.3', { emscriptenOptions: { processId: 42 } })` |
| `Cannot mix bracketed/unbracketed namespace` در `assert.php` | بازنویسی فایل به فرم bracketed کامل |
| خروجی تست به‌صورت `Uint8Array` | دیکودر UTF-8 در `run-tests.mjs` |
| `require_once __DIR__` در حالت `code:` | استفاده از `php.run({ scriptPath })` |
| جلسه PHP-wasm کند (~۲۵s بوت) | اجرای دسته‌ای تست‌های یک فاز در یک پروسه |

### S0-4. تصمیم‌های Gate 0
پاسخ‌های کاربر ثبت و در `MASTER_PLAN.md` به‌صورت G1..G6 مستند شد. مهم‌ترین اثر معماری: **G5** → منو/مگامنو کاملاً در Appearance → Menus (هوک `wp_nav_menu_item_custom_fields`)، و داشبورد اختصاصی فقط تنظیمات نمایشی را می‌گیرد. این تصمیم Task 4.2 را از «ساخت پنل منو در داشبورد» به «Walker + فیلدهای آیتم منو» تغییر داد.

---

## STAGE 1 — Scaffolding
- ۲۴ فایل تسک + ۲۴ اسکلت تست + ۸ `run-phase-XX-tests.sh` + `temp/tests/bootstrap.php` با سه وضعیت `PASS/FAIL/PENDING`.
- تست‌ها در وضعیت فعلی همگی `PENDING` هستند و `RESULT: NOT OK` می‌دهند — این **رفتار درست** است: یعنی هیچ تسکی بدون پیاده‌سازی واقعی «سبز» نمی‌شود.
- اجرای نمونه: `node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-01.php` → ۴×PENDING، بدون خطای PHP.

## STAGE 1-REV — بازطراحی به تسک‌های اتمیک (2026-11-03)
**درخواست:** «تسک‌ها کم هستن برای هر فاز → اتمیک و ریزتر و جامع‌تر».

### چه تغییری کرد
- منبع حقیقت از یک اسکریپت بزرگ به ۸ ماژول داده‌ای (`tools/plan/phaseNN.py`) منتقل شد؛ تولید فایل‌ها با `tools/gen_phase_scaffold.py` انجام می‌شود.
- ۲۴ تسک → **۱۳۷ تسک** با اندازه‌گذاری S/M/L، وابستگی صریح، فایل‌های هدف، مراحل گام‌به‌گام و شروط `P1..Pn`.
- صفحه اصلی به **۱۶ تسک سکشن مستقل** شکسته شد (نه یک تسک «۱۵ سکشن»).
- ۶ برگه ثابت (درباره/تماس/FAQ/مجوزها/گارانتی/استخدام) هر کدام یک تسک جدا.
- متاباکس محصول به ۶ تسک (شناسه‌ها، گالری، روابط، Repeaterها، Quick Edit، امنیت) تفکیک شد.

### باگ‌هایی که در خود ابزار تولید پیدا و رفع شد
| # | مشکل | رفع |
|---|---|---|
| B1 | شناسه‌های تسک فاز ۵ تکراری (`5.1` دو بار) و پرش (`5.16`→`5.17`) → نام فایل‌ها روی هم می‌افتاد | جانمایی مجدد: تسک front-page = `5.1`، سکشن‌ها = `5.2..5.17`، کارت‌ها `5.18`، کوئری `5.19`، واریانت‌ها `5.20`، گیت `5.21` + بررسی خودکار پیوستگی |
| B2 | تولید تست با `count(t['files'])` در متن PHP → `Undefined constant "t"` | تبدیل به f-string واقعی در سمت پایتون |
| B3 | تولید `check('label','pending_assertion')` → خطای TypeError | الگو به `check('label', function(){ return null; })` تغییر کرد |
| B4 | «،.» چسبیده در برخی توضیحات | پاکسازی خودکار همه ماژول‌ها |
| B5 | وابستگی به `5.17` در فازهای ۶/۷ پس از جانمایی جدید نامعتبر شد | جستجو و به‌روزرسانی همه ارجاع‌ها (`5.17`→`5.18`) + اعتبارسنجی خودکار وابستگی‌ها |

### اعتبارسنجی انجام‌شده
- پیوستگی شناسه‌ها در هر ۸ فاز: ✅ (`1..N` بدون پرش/تکرار)
- اعتبار همه وابستگی‌ها: ✅ (بدون ارجاع ناموجود)
- اجرای سه تست نمونه روی PHP-wasm: بدون Fatal، خروجی `PENDING` و `RESULT: NOT OK` (رفتار درست پیش از پیاده‌سازی)

---

## PHASE 1 GATE — بسته شدن فاز ۱ (زیرساخت هسته و دارایی‌ها)

تاریخ: 2026-11-03 · شاخه: `arena/3730add5-erfansanat-theme-ai`

| سنجه | مقدار اندازه‌گیری‌شده | بودجه | وضعیت |
|---|---|---|---|
| فایل‌های تست فاز | 12/12 | 12 | ✅ |
| Assertion های PASS | 170 | همه | ✅ |
| Assertion های FAIL | 0 | 0 | ✅ |
| Assertion های PENDING | 0 | 0 | ✅ |
| درخواست CSS قالب در `/` | 1 | ≤ 2 | ✅ |
| درخواست JS قالب در `/` | 1 | ≤ 2 | ✅ |
| Warning/Deprecated در رندر | 0 | 0 | ✅ |
| زمان رندر قالب (`theme_ms`) | 49–79 ms | < 500 | ✅ |
| زمان کل بوت + رندر | 735 ms | ثبت شده | ℹ️ |
| تعداد کوئری SQL | 14 | < 60 | ✅ |
| حجم HTML صفحه `/` | 2033 B | — | ℹ️ |
| پوشش چند صفحه (بدون warning) | `/`, `/blog/`, `/shop/`, `/sample-page/` | — | ✅ |
| زمان کل گیت کامل | ~۳ ثانیه | — | ✅ |

### چه چیزی واقعاً ساخته شد

- `theme/` با ساختار PSR-4: `ErfanSanat\Theme\{Theme, Contracts, Setup, Assets, Performance}`.
- `functions.php` ۱۲ خطی → `inc/bootstrap.php` → Constants → Autoloader → `Theme::instance()->boot()`.
- ماژول‌های ثبت‌شده: `theme-support`, `menus`, `debloat`, `assets`, `preload`.
- `AssetManager` با نسخه‌گذاری `filemtime` و گارد `is_readable` (هیچ ۴۰۴ در تگ‌ها).
- `DeBloat` بدون حذف هیچ هوک لازم: block-library، global-styles، emoji، wp-embed، generator، rsd، wlw، shortlink، REST/oEmbed link.
- `Preload`: یک فونت + یک LCP (`hero.webp`, fetchpriority=high) و فقط در فرانت.
- دارایی‌های استاتیک بایت‌به‌بایت (md5) منتقل شدند: CSS 188K، JS 112K، ۳ فونت، ۲۱ تصویر، ویدیو ۶.۸M.
- ۹ تصویر WebP با GD تولید شد (میانگین ۱۹٪ کوچک‌تر) + `screenshot.png` ۱۲۰۰×۹۰۰.
- هارنس: `tools/harness/fetch.mjs` + `fetch.php` (رندر تک‌URL)، `perf.mjs`، `gate.php`، `run-phase.mjs`.

### باگ‌ها و درس‌ها (فاز ۱)

| # | مشکل | ریشه | اصلاح |
|---|---|---|---|
| B6 | `img.php` همیشه exit 64 | argv/env در php-wasm به اسکریپت نمی‌رسد | job-file درون‌خطی (`img-jobs.json`) + `img-batch.php` |
| B7 | `render.mjs` دو بار ۲۵۰s تایم‌اوت | بوت PHP-wasm ~۱۰۰s + رندر | یک URL در هر پروسه؛ تایم‌اوت ابزار ≥ ۲۶۰s |
| B8 | `theme_code()` فایل‌ها را پیدا نمی‌کرد | ریشه روی مخزن بود نه `theme/` | ریشه اصلاح شد به `ES_REPO_ROOT/theme` |
| B9 | تست‌ها `get_file_data` را صدا می‌زدند | WP لود نشده بود | `EsTest\parse_headers()` مستقل از WP |
| B10 | `GLOB_BRACE` تعریف‌نشده در wasm | build محدود PHP | حلقه `scandir()` |
| B11 | مسیر reference اشتباه (`assets/img` vs `images`) | گمان به‌جای بررسی | `find` روی mirror و اصلاح نقشه |
| B12 | runner تست، FAIL را تشخیص نمی‌داد | exit code از PHP CLI نمی‌آمد | تشخیص «RESULT: NOT OK» در stderr/stdout |
| B13 | سقف ۵۰۰ms با بوت WP شکسته می‌شد | سنجه اشتباه (کل درخواست) | mu-plugin زمان‌سنج + سنجه `theme_ms` |
| B14 | `wp-embed-responsive` در body_class | DeBloat فقط اسکریپت را حذف می‌کرد | فیلتر `body_class` |
| B15 | بازتولید scaffold تست‌های دست‌نویس را پاک می‌کرد | generator فقط فایل تولیدی می‌نوشت | پوشه `tools/plan/filled/phase-NN/` منبع رسمی تست‌ها شد |
| B16 | اسکریپت gate فاز با backtick شکسته بود | عنوان تسک داخل «`» در bash | حذف backtick در label و اجرای تک‌پروسه‌ای |
| B17 | `phase-gate.mjs` در مرحله perf هنگ می‌کرد | `execFileSync` خروجی php-wasm را بافر می‌کرد | `spawnSync` با `stdio: 'inherit'` |
| B18 | `perf.mjs` بعد از import هیچ خروجی نمی‌داد | `process.exit(0)` سطح-بالا در ماژول importشده اجرا می‌شد | exit فقط در CLI خودِ perf.mjs |


## Phase 2 — Task 2.1 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| حافظه و نقشه خوانده شد | Gate 1 پیش‌تر سبز بود؛ تأیید کاربر برای Phase 2 دریافت شد |
| تست پیش از پیاده‌سازی | تست 9 شرطی برای cache، dot-path، fallback، single-option و hook نوشته شد |
| پیاده‌سازی | `Defaults.php` + `Options.php` و بارگذاری helper از bootstrap |
| تست نهایی | `node tools/harness/run-tests.mjs temp/phase-02-custom-admin-dashboard/tests/test-task-01.php` → **9 PASS / 0 FAIL / 0 PENDING** |
| خوداصلاحی | اجرای نخست مسیر bootstrap در تست canonical اشتباه بود؛ مسیر سازگار با canonical و scaffold تعریف شد. همچنین PHP namespace ترکیبی را رد کرد؛ هر دو namespace به فرم bracketed همسان شدند. |
| وضعیت | Task 2.1 `[PASSED]`; ادامه با Task 2.2 |


## Phase 2 — Task 2.2 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | 9 شرط را ابتدا اجرا کردیم؛ هر 9 مورد به‌درستی FAIL شدند چون منو/asset هنوز موجود نبود |
| پیاده‌سازی | `AdminMenu`، ثبت در Kernel، CSS/JS scoped، صفحه داشبورد و routeهای زیرمنو |
| تست اختصاصی | **9 PASS / 0 FAIL / 0 PENDING**؛ دسترسی، اسلاگ و capability، زیرمنوها، enqueue شرطی و خروجی RTL |
| Suite فاز تا اینجا | 18 PASS در تسک‌های 2.1–2.2؛ سایر تست‌های هنوز پیاده‌نشده PENDING هستند |
| JS syntax | `node --check theme/assets/admin/admin.js` → موفق |
| اصلاح در مسیر | اندیس اسلاگ/کال‌بک منوی وردپرس در تست با امضای واقعی API (`page_slug` و callback) هم‌تراز شد؛ آزمون رد دسترسی هم پیش از بررسی enqueue تنظیم شد |
| وضعیت | Task 2.2 `[PASSED]`; بعدی: Task 2.3 |


## Phase 2 — Task 2.3 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست کامل 13 شرطی پیش از کد اجرا شد و به‌درستی FAIL شد |
| پیاده‌سازی | `Schema`, `Sanitizer`, `FieldRenderer` + type renderers پایه/مدیا/Repeater |
| تست اختصاصی | **15 PASS / 0 FAIL / 0 PENDING** (رندر 12 نوع، escaping، nonce، XSS، whitelist، boolean، ID رسانه و nested repeater) |
| Suite فاز | 33 PASS در تسک‌های 2.1–2.3؛ 40 PENDING در 12 تسک باقیمانده؛ 0 FAIL در کار انجام‌شده |
| خوداصلاحی | fixtureهای تست اصلاح شد تا nonce, escape و تفاوت `sanitize_email` (حذف markup به‌جای حذف متن مجازِ اسم تگ) را درست منعکس کند |
| وضعیت | Task 2.3 `[PASSED]`; بعدی: Task 2.4 |


## Phase 2 — Task 2.4 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 8 شرطی قبل از کد نوشته و اجرا شد؛ همه به‌درستی FAIL شدند |
| پیاده‌سازی | endpoint authenticated-only، nonce/capability، whitelist+sanitize، پاسخ JSON، rate limit و `Options::update_many` تک‌نوشت |
| تست اختصاصی | **9 PASS / 0 FAIL / 0 PENDING**: nonce، capability، فیلد ناشناس، پاک‌سازی، یک write، JSON، rate limit و ثبت hook |
| تست کل Phase تا اینجا | 42 PASS / 0 FAIL / 35 PENDING در 15 فایل؛ 11 فایل pending مربوط به کارهای آینده است |
| باگ تست/اصلاح | نام متغیرهای تست (`$files`/`$file`) با متغیر حلقه‌ی `run-phase.php` تصادم داشت و گزارش را به‌هم می‌ریخت؛ به `$dependency_files`/`$dep_file` تغییر کرد |
| وضعیت | Task 2.4 `[PASSED]`; بعدی: Task 2.5 |


## Phase 2 — Task 2.5 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | 5 assertion ابتدا fail شد (template, JS, validation, CSV, clear) |
| پیاده‌سازی | `wp.media` picker تک/چندتایی، server attachment validation، media template و enqueue مشروط |
| تست PHP | **5 PASS / 0 FAIL / 0 PENDING**؛ در کنارش Tasks 2.2 و 2.3 هم سبز شدند |
| تست رفتاری JS | `node tools/harness/test-media-picker.mjs` → انتخاب ID، پیش‌نمایش، حذف، CSV چندتایی، جلوگیری از `javascript:` PASS |
| Syntax | `node --check theme/assets/admin/media.js` → موفق |
| خوداصلاحی | Media preview ابتدا صرفاً CSV را parse می‌کرد و attachment ناموجود را هم نگه می‌داشت؛ اکنون renderer و sanitizer هر دو post_type و در صورت نیاز MIME را اعتبارسنجی می‌کنند |
| وضعیت | Task 2.5 `[PASSED]`; بعدی: Task 2.6 |

## Phase 2 — Task 2.6 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | 6 assertion در شروع به‌درستی FAIL شدند؛ بعد از پیاده‌سازی به 7 شرط گسترش یافت |
| پیاده‌سازی | Codec آرایه/JSON/serialized + UI ردیف‌های JetEngine + JS بومی reorder/add/remove/keyboard |
| تست اختصاصی PHP | **7 PASS / 0 FAIL / 0 PENDING** (round-trip سه فرمت، کلید item-N، template و حذف ردیف خالی) |
| تست رفتاری JS | `node tools/harness/test-repeater.mjs` → جابه‌جایی صفحه‌کلید، حذف، افزودن و نام‌های ذخیره‌شده PASS |
| کل suite فاز تا اینجا | 54 PASS / 0 FAIL / 27 PENDING از 15 فایل؛ 9 فایلِ باقیمانده pending هستند |
| خوداصلاحی | تست اولیه `$rows` سراسری run-phase.php را shadow می‌کرد و گزارش تجمیعی را خراب می‌ساخت؛ متغیر به `$es_repeater_rows` تغییر کرد؛ تست فاز پس از scaffold از 54 PASS تأیید شد |
| وضعیت | Task 2.6 `[PASSED]`; بعدی: Task 2.7 |


## Phase 2 — Task 2.7 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 8 شرطی پیش از وجود tab classها اجرا شد و **0 PASS / 8 FAIL / 0 PENDING** ثبت کرد (red baseline مورد انتظار) |
| پیاده‌سازی | Header/Footer schema+tab، Defaults، رندر صفحه تنظیمات، تب‌های دسترس‌پذیر، ذخیره AJAX با nonce موجود، شرط رندر اعلان، و allowlist آیکون/URL |
| تست اختصاصی نهایی | **11 PASS / 0 FAIL / 0 PENDING**؛ fallbackها، عدم تکرار منوی سفارشی، خروجی tabs/nonce، URL اجتماعی نامعتبر و حذف کامل اعلان خاموش بررسی شدند |
| Suite Phase 2 | **65 PASS / 0 FAIL / 24 PENDING** در 15 فایل؛ pendingها فقط از Task 2.8–2.15 هستند |
| Syntax / diff | `node --check theme/assets/admin/admin.js` و `git diff --check` موفق |
| خوداصلاحی | runner چند تسک در یک PHP process اجرا می‌کند؛ تست مستقل‌سازی options/capability را صریح کرد تا نتیجه به state قبلی Task 2.1/2.2 وابسته نباشد. در تست فاز ابتدا `wp_localize_script` stub نشده بود؛ وابستگی اضافه حذف شد و admin از `ajaxurl` استاندارد وردپرس استفاده می‌کند |
| وضعیت | Task 2.7 `[PASSED]`; بعدی: Task 2.8 |


## Phase 2 — Task 2.8 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 5 شرطی در نبود tab/inline CSS ابتدا **0 PASS / 5 FAIL / 0 PENDING** داد |
| پیاده‌سازی | `BrandTab`, `InlineCss`, اتصال route صفحه برند و hook `wp_head`; CSS فقط در صورت override معتبر |
| تست نهایی | **5 PASS / 0 FAIL / 0 PENDING**؛ default بدون style، color form/nonce، CSS scoped، رد ورودی نامعتبر و حفظ مقدار قبلی |
| Suite Phase 2 | **70 PASS / 0 FAIL / 21 PENDING**؛ pendingها Tasks 2.9–2.15 |
| محافظت | محدودسازی خروجی به `sanitize_hex_color`; override روی wrapper موجود `#es-page.es-site`, بدون تغییر stylesheet یا `!important` |
| وضعیت | Task 2.8 `[PASSED]`; بعدی: Task 2.9 |


## Phase 2 — Task 2.9 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 6 شرطی را ابتدا اجرا کردیم؛ همه **0 PASS / 6 FAIL / 0 PENDING** بودند |
| پیاده‌سازی | `ArchiveTab`، allowlist تنظیمات و `ArchiveConfig` برای query اصلی و کنترل‌های فیلتر قابل‌رندر؛ route ادمین آرشیو اضافه شد |
| تست اختصاصی | **5 PASS / 0 FAIL / 0 PENDING**: posts-per-page/order، fallback ورودی نامعتبر، حذف فیلتر خاموش از render list و فرم امن |
| Suite Phase 2 | **75 PASS / 0 FAIL / 18 PENDING**؛ 6 تسک آینده باقی است |
| اصلاح هم‌پوشانی | Task 2.2 submenu regression assertion به‌روزرسانی شد تا صفحه آرشیو جدید را نیز پوشش دهد (7 submenu با manage_options) |
| وضعیت | Task 2.9 `[PASSED]`; بعدی: Task 2.10 |


## Phase 2 — Task 2.10 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 5 شرطی پیش از ایجاد moduleها اجرا شد: **0 PASS / 5 FAIL / 0 PENDING** |
| پیاده‌سازی | field type `code`، KSES policy مشترک، ScriptsTab در صفحه ابزارها و `CustomCode` برای `wp_head`/`wp_footer` |
| تست اختصاصی | **7 PASS / 0 FAIL / 0 PENDING**؛ خروجی خالی/ادمین، slot و priority=99، KSES allowlist، capability و فرم nonce بررسی شد |
| سازگاری موتور فیلد | Task 2.3 تست supported-types را برای نوع جدید `code` به‌روز کرد؛ suite همان **15 PASS / 0 FAIL / 0 PENDING** ماند |
| امنیت | AJAX endpoint همچنان nonce+manage_options را الزام می‌کند؛ sanitizer محتوای code نیز capability را مستقل بررسی و خروجی در front-end مجدداً KSES می‌شود |
| Suite Phase 2 | **82 PASS / 0 FAIL / 15 PENDING**؛ pendingها فقط Tasks 2.11–2.15 |
| وضعیت | Task 2.10 `[PASSED]`; بعدی: Task 2.11 |

## Phase 2 — Task 2.11 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | پنج check شامل وجود module، پیش‌فرض، round-trip، ثبت سکشن تازه و نادیده‌گرفتن سکشن حذف‌شده؛ baseline: **0 PASS / 5 FAIL / 0 PENDING** |
| پیاده‌سازی | `Registry` با ۱۵ سکشن صفحه اصلی و شش schema برگه ثابت؛ `Store` برای `_es_sections` با normalize کردن order/enabled/data و پشتیبانی schema drift |
| تست اختصاصی | **5 PASS / 0 FAIL / 0 PENDING**؛ ترتیب پیش‌فرض، نبود meta، round-trip و تغییر Registry بررسی شدند |
| Suite Phase 2 | **87 PASS / 0 FAIL / 12 PENDING**؛ pendingها فقط Tasks 2.12–2.15 هستند |
| مرجع/محدوده | تعداد ۱۵ سکشن و ترتیب از `reference/static-mirror/index.html` و `temp/STATIC_TEMPLATE_MAP.md` تأیید شد؛ بدون تغییر markup یا static assets |
| وضعیت | Task 2.11 `[PASSED]`; بعدی: Task 2.12 |

## Phase 2 — Task 2.12 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 4 شرطی پیش از ساخت UI/assetها **0 PASS / 4 FAIL / 0 PENDING** بود |
| پیاده‌سازی | `SectionsPage`، endpoint امن `es_save_sections`، builder drag/drop + keyboard، پنل editor و فیلدهای schema-backed؛ asset enqueue محدود به صفحه Sections |
| تست PHP اختصاصی | **4 PASS / 0 FAIL / 0 PENDING**: پوشش ۱۵ کارت، nonce، active/available، ترتیب ذخیره‌شده، field render و keyboard controls |
| تست رفتاری JS | `node tools/harness/test-sections-builder.mjs` → add/remove/move/toggle/disclosure/state serialization موفق؛ بدون خطای کنسول؛ `node --check` موفق |
| Suite Phase 2 | **92 PASS / 0 FAIL / 9 PENDING**؛ pendingها فقط Tasks 2.13–2.15 هستند |
| اصلاح مدل UI | برای حفظ تمایز «حذف از چیدمان» و toggle خاموش، `Store` حالا کلید محدود `available` را هم normalize می‌کند؛ `order` همچنان ترتیب همه Registry sections را برای Renderer نگه می‌دارد |
| وضعیت | Task 2.12 `[PASSED]`; بعدی: Task 2.13 |

## Phase 2 — Task 2.13 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | چهار check در نبود Renderer: **0 PASS / 4 FAIL / 0 PENDING** |
| پیاده‌سازی | `Sections\\Renderer` با ترتیب Store، skip حالت خاموش، `locate_template` پیش از `get_template_part`، args سازگار WP 5.5+ و hookهای before/after |
| Fallback | مسیر template در Registry محدود شد؛ template گمشده بدون warning رد می‌شود؛ exception هر template خروجی جزئی را دور می‌اندازد و رندر سکشن‌های بعدی را متوقف نمی‌کند |
| تست اختصاصی | **4 PASS / 0 FAIL / 0 PENDING**: صفر خروجی خاموش، fallback فایل/exception، ادامه رندر، ترتیب ذخیره و داده args |
| Suite Phase 2 | **96 PASS / 0 FAIL / 6 PENDING**؛ pendingها فقط Tasks 2.14–2.15 |
| وضعیت | Task 2.13 `[PASSED]`; بعدی: Task 2.14 |

## Phase 2 — Task 2.14 (2026-11-03)

| اقدام | نتیجه |
|---|---|
| تست پیش از پیاده‌سازی | تست 4 شرطی قبل از ماژول: **0 PASS / 4 FAIL / 0 PENDING** |
| پیاده‌سازی | `ImportExport` در صفحه Tools؛ JSON export/attach، import از فایل کوچک یا textarea، فیلتر Schema، پشتیبان خودکار و reset انتخابی |
| تست اختصاصی | **4 PASS / 0 FAIL / 0 PENDING**؛ round-trip تنظیمات و meta سکشن، snapshot backup، malformed JSON، type/keys ناشناخته و حفظ گروه‌های خارج هدف |
| امنیت | admin-post endpointها nonce + `manage_options`; upload تا 1MB با پسوند JSON و `is_uploaded_file`; reset علاوه بر nonce نیاز به checkbox تأیید دارد |
| Suite Phase 2 | **100 PASS / 0 FAIL / 3 PENDING**؛ فقط Task 2.15 pending است |
| وضعیت | Task 2.14 `[PASSED]`; بعدی: Task 2.15 (final gate) |

## Phase 2 — Task 2.15 final gate (2026-11-04)

| شرط | نتیجه |
|---|---|
| P1 — کل suite | **104 PASS / 0 FAIL / 0 PENDING**, 15 test files |
| P2 — اسکن `$_POST`/`$_GET` | 6 direct reads inspected, **0 unsafe** |
| P3 — save timing | Maximum of 10 `AjaxSave::handle()` callbacks in PHP-WASM mock harness: **0.083ms** (<300ms); DB/network not included |
| P4 — warnings | **0 PHP warnings** captured in the security/performance smoke path; no warnings in full-suite output |
| وضعیت | Task 2.15 `[PASSED]`; Gate 2 complete; stop pending explicit user approval before Phase 3 |

## Phase 3 — Tasks 3.1–3.3 (2026-11-04)

| تسک | نتیجه |
|---|---|
| 3.1 — Native `projects` CPT | **4 PASS / 0 FAIL**؛ public/archive/rewrite/supports/REST و ادغام Theme |
| 3.2 — `project_type` | **4 PASS / 0 FAIL**؛ اتصال به projects، labels، REST/admin column، route و hierarchical rewrite contract |
| 3.3 — `city` | **3 PASS / 0 FAIL**؛ اتصال، لینک پایدار، hierarchical و کنترل term حذف/ناموجود |
| سیاست‌ها | هیچ تغییری در `main.js`, `styles.css` یا markup استاتیک؛ هیچ کلید متا دستکاری نشده |
| وضعیت | Phase 3 با تأیید صریح کاربر آغاز شد؛ ادامه از Task 3.4 |

## Phase 3 — Tasks 3.4–3.5 (2026-11-04)

| تسک | نتیجه |
|---|---|
| 3.4 — WooCommerce rewrite قراردادها | **4 PASS / 0 FAIL**؛ `/product_cat/`, `/product-tag/`, `/product/`; `product_brand` fallback؛ no normal-request flush |
| 3.5 — نسخه‌دارکردن flush و URL audit | **4 PASS / 0 FAIL**؛ version mismatch → یک soft flush؛ اجرای دوباره → بدون flush دوم؛ شش route family؛ 404 privacy log |
| 404 privacy | query string/IP/cookie ذخیره نمی‌شود؛ مسیرها normalize/truncate و تعداد ردیف‌ها محدود است |
| وضعیت | مجموع Tasks 3.1–3.5: **19 PASS / 0 FAIL**؛ بعدی 3.6 |

## Phase 3 — Tasks 3.6–3.7 (2026-11-04)

| تسک | نتیجه |
|---|---|
| 3.6 — پروژه admin list | **4 PASS / 0 FAIL**؛ columns, filters, AND-tax_query, sortable meta fields, preserved pagination |
| 3.7 — سایر لیست‌ها | **4 PASS / 0 FAIL**؛ post/page/product/projects columns, IDs, term thumbnail, empty values, legacy view fallback |
| Integration | `ListTables` از Theme kernel ثبت می‌شود؛ escape برای مقادیر متنی و core-generated attachment HTML |
| نتیجه تجمعی | Tasks 3.1–3.7: **27 PASS / 0 FAIL**؛ ادامه Task 3.8 |

## Phase 3 — Task 3.8 (2026-11-04)

| شرط | نتیجه |
|---|---|
| Round-trip JSON + serialized | **PASS**; format preserved after update |
| Single JetEngine `item-0` row | **PASS**; exact key/shape retained |
| Gallery CSV | **PASS**; unique positive integer IDs and stable CSV encoding |
| Cache + term/post helpers | **PASS**; 10 reads = 1 lookup, `es_meta`/`es_term_meta` work |
| Integration | MetaRepository registered in Theme; bootstrap loads global helper functions |
| تجمعی | Tasks 3.1–3.8: **31 PASS / 0 FAIL** |

## Phase 3 — Task 3.9 (2026-11-04)

| شرط | نتیجه |
|---|---|
| CRUD 10 کلید محصول | **PASS**؛ field render + save |
| املای legacy | **PASS**؛ `english_titl` حفظ و `english_title` ساخته نمی‌شود |
| true_sale | **PASS**؛ فقط `true`/`false` پذیرفته می‌شود |
| nonce/capability | **PASS**؛ نبود nonce هرگونه write را متوقف می‌کند |
| مجموع Phase 3 متمرکز | Tasks 3.1–3.9: **35 PASS / 0 FAIL** |


## Phase 3 — Tasks 3.10–3.18 / Gate 3 (2026-11-04)

| تسک | نتیجه |
|---|---|
| 3.10 — فایل کاتالوگ و گالری | **4 PASS**؛ گالری `galerry_produ` ↔ `_product_image_gallery`، fallback قدیمی، URL PDF پیوست معتبر، خروجی خالی |
| 3.11 — روابط محصول | **4 PASS**؛ `project-ids` و `usage_countr`، رد پست/ترم نامعتبر، جست‌وجوی محدود به `projects`، حفظ JSON/serialized |
| 3.12 — چهار Repeater | **4 PASS**؛ CRUD، حفظ `vaule_of_variable` و `item-0`، حذف ردیف خالی |
| 3.13 — Quick Edit | **3 PASS**؛ nonce/capability، پیش‌پرکردن داده، فقط فیلدهای whitelist |
| 3.14 — متاباکس پروژه | **3 PASS**؛ پنج کلید تصویری شامل `foure-image`، اعتبار تاریخ جلالی، لینک محصولات معتبر |
| 3.15 — ترم‌متای دسته محصول | **3 PASS**؛ `thumbnail`, placeholder خالی، FAQ با فیلدهای دقیق و پاکسازی HTML |
| 3.16 — Hreflang | **3 PASS**؛ URL فقط HTTP(S)، کلید قابل مرتب‌سازی، ذخیره term meta و alternate links |
| 3.17 — شمارنده بازدید | **4 PASS**؛ یک شمارش به‌ازای session-cookie، شناسایی bot، نوشتن `number2-viewc` و خواندن fallback |
| 3.18 — Gate 3 | **3 PASS**؛ تمام تست‌های قبل سبز، round-trip JSON/serialized، نبود parent orphan در داده fixture |
| تجمعی Phase 3 | **18 test files · 66 PASS / 0 FAIL / 0 PENDING** |
| قیدهای استاتیک | `theme/assets/js/main.js` و `theme/assets/css/styles.css` تغییر نکردند؛ فایل‌های مرجع استاتیک/markup دست‌نخورده |
| Regression Phase 2 | **104 PASS / 0 FAIL / 0 PENDING** |
| محدودیت harness | Phase 1 suite در این نشست به‌دلیل نبود `/home/user/.cache/harness/wpn` و snapshotهای render، گزارش سبز/قابل بازتولید نداد؛ lint ساختاری PHP پاس شد |
| وضعیت | Gate 3 کامل؛ Phase 4 شروع نمی‌شود تا تأیید جداگانه کاربر |
