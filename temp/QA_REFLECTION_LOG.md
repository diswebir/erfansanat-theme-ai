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
