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
