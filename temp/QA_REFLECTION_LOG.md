# QA_REFLECTION_LOG — لاگ تفکر، تست و خوداصلاحی

> هر تسک: تحلیل → تست → باگ‌ها → خوداصلاحی → نتیجه. جدیدترین بالا.

---

## 2026-11-03 — P01-T001 (هدر قالب، style.css و حداقل index.php)
- **READ MEMORY:** برنامه اتمیک ۱۷۵ تسکی و دروازه شروع Phase 1 خوانده شد؛ کاربر تایید کرد.
- **THINK & PLAN:** هدف فقط متادیتای پوسته بود؛ اما تست WP نشان داد پوسته کلاسیک بدون `index.php` خطای `theme_no_index` می‌دهد و در فهرست پوسته‌ها نمی‌آید.
- **WRITE TEST:** تست واقعی نوشته شد: کشف پوسته، نبود خطای اعتبارسنجی، هدرهای معتبر، فقط-هدر بودن style.css، فعال‌سازی و بازگردانی پوسته قبلی.
- **IMPLEMENT:** `erfansanat/style.css` با هدر معتبر و `erfansanat/index.php` حداقلی ساخته شد. CSS استاتیک کپی نشد.
- **RUN TEST:** `php -l` روی index.php و تست بدون خطا؛ تست تسک = **12 passed, 0 failed, 0 skipped/pending**. `wp theme list` قالب `erfansanat` را نشان می‌دهد؛ پوسته فعال QA به حالت قبل برگشت.
- **SELF-CORRECTION:** دامنه P01-T001 به‌صورت شفاف اصلاح شد تا حداقل index.php را شامل شود؛ fallback کامل همچنان P01-T005 است. برنامه/تسک/حافظه به‌روزرسانی شدند.
- **UPDATE MEMORY:** وضعیت = P01-T001 PASSED، گام بعد P01-T002.

---

## 2026-11-03 — Stage 1 بازطراحی اتمیک (درخواست کاربر)
- **درخواست کاربر:** تسک‌های هر فاز کم بودند؛ باید اتمیک‌تر، ریزتر، با جزئیات و جامع‌تر شوند.
- **تحلیل:** ۲۲ تسک قبلی چندین مسئولیت مستقل را ادغام کرده بودند (مثلاً 6.1 کل تک‌محصول، 7.3 همه برگه‌ها). این با قانون Atomic Task-by-Task و تست‌پذیری مستقل سازگار نبود.
- **اقدام:** برنامه به **۱۷۵ تسک** شکسته شد: P1=12، P2=18، P3=24، P4=20، P5=21، P6=28، P7=28، P8=24. هر تسک شناسه پایدار `Pxx-Tyyy`، وابستگی قبلی، فایل هدف محدود، منبع استاتیک، کلیدهای داده، شروط پذیرش `AC` و سناریوی تست `SC` دارد.
- **نگاشت:** ۲۲ تسک قبلی حذف/بایگانی معنایی شدند و نگاشت کامل در `LEGACY_TASK_MAP.md` ثبت شد؛ هیچ قابلیت قبلی گم نشد. قابلیت‌های پوشش‌داده‌نشده قبلی (migration dry-run، rollback، rate-limit فرم، mini-cart AJAX، proforma، parity/a11y، بسته انتشار) به تسک مستقل اضافه شدند.
- **اصلاح تست:** `es_pending` و `es_summary(strict)` اضافه شد؛ سناریوی رفتاری خودکارنشده دیگر نمی‌تواند silent pass بدهد. runner فاز در صورت FAIL/PENDING exit 1 می‌دهد. فعال‌سازی قالب در runner بدون `|| true` انجام می‌شود.
- **اعتبارسنجی:** جنریتور وابستگی‌ها را assert می‌کند (وابستگی رو به جلو/گم‌شده ممنوع). `php -l` روی ۱۷۶ فایل PHP = بدون خطا. تعداد task/test = ۱۷۵/۱۷۵. فایل‌های دوب رقمی قبلی = ۰. اجرای نمونه P03-T015 = FAIL target + PENDING scenarios (قرمزِ مورد انتظار).
- **نتیجه:** برنامه اتمیک آماده؛ همچنان هیچ کد قالبی نوشته نشده و در انتظار تایید کاربر برای P01-T001 هستیم.

---

## 2026-11-03 — Stage 1 (ساخت درخت فازها)
- **تحلیل:** پاسخ‌های کاربر اعمال شد (D1..D8). نکته کلیدی D3: اسلاگ ترم‌های لایو فارسی است → قالب‌ها slug-agnostic؛ تست‌ها باید با ترم فارسی (`اصفهان`) هم کار کنند.
- **تصمیم معماری D8:** سورس قالب در `erfansanat/`؛ mu-plugin محیط تست ریشه ریپو را theme directory می‌کند (تست شد: `wp eval` نشان داد ثبت شده).
- **باگ/محدودیت کشف‌شده:** دانلود zip بیلدشده WooCommerce از release گیت‌هاب → ریدایرکت به `objects.githubusercontent.com` (مسدود) → TLS EOF. **خوداصلاحی:** فاز ۶ با shim سازگاری ووکامرس تست می‌شود (تایید کاربر D7)؛ نهایی‌سازی روی استیجینگ.
- **اقدام:** ساخت ۸ پوشه فاز × (tasks + tests) + bootstrap تست + run-phase-tests.sh. اسکلت تست‌ها الان **قرمز (TDD)** هستند چون هنوز قالبی ساخته نشده — این مورد انتظار است.
- **باگ ۱ (خوداصلاحی):** جنریتور مسیر ریشه را اشتباه محاسبه می‌کرد و فایل‌ها را در `temp/temp/` می‌ساخت → اصلاح `ROOT` (سه سطح بالا) و حذف پوشه اشتباه؛ بازتولید در مسیر صحیح.
- **باگ ۲ (خوداصلاحی — مهم):** چک‌های وابسته به WP روی قالب پیش‌فرض (twentytwentyfive) اجرا می‌شدند و `post-thumbnails`/`html5` را **به‌اشتباه PASS** می‌کردند → حالا فقط وقتی قالب خودمان فعال باشد اجرا می‌شوند (`es_activate_theme()`) وگرنه SKIP. runner هم قالب را در پروسه جدا pre-activate می‌کند تا `after_setup_theme` درست اجرا شود.
- **تست اجراشده:** `php -l` روی هر ۲۳ فایل PHP = بدون خطا. اجرای task 1.1 = `0 passed, 6 failed, 5 skipped` (قرمزِ مورد انتظار). exit code runner = 1 (صحیح).
- **نتیجه:** درخت آماده؛ در انتظار تایید کاربر برای Phase 1.

---

## 2026-11-03 — Stage 0 (Bootstrapping)
- **کشف:** فایل پیوست `erfansanat-static-mirror.zip.png` در واقع ZIP بود (magic bytes `PK`) → استخراج به `static-mirror/` (۱۲۸ HTML).
- **محدودیت شبکه:** apt/wordpress.org مسدود → PHP از `@php-wasm/node` (نصب موفق: 8.4.27). `proc_open` در WASM نیست → هشدار بی‌ضرر wp-cli `--info`.
- **باگ میرور:** `<title>undefined</title>` در `category/magazine.html` و `category/news-and-events.html` → در وردپرس از title-tag/SEOPress استفاده می‌شود (الگو نیست).
- **کشف ساختاری:** ۴۰ فایل Alias (فقط canonical متفاوت) → پیشنهاد 301 (تایید شد D3). هدر/فوتر تماماً data-attribute (بدون id).
- **نتیجه:** هارنس سالم (`wp core install` + `wp eval` موفق)؛ ۴ فایل حافظه ساخته شد.

---

## 2026-11-03 — Phase 1 تکمیل شد (P01-T001..T012)
- **READ MEMORY:** PROJECT_MEMORY و تسک‌های فاز ۱ خوانده شد. کاربر مجوز داد همه تسک‌های فاز ۱ بدون توقف بین تسک‌ها کامل شوند.
- **THINK & PLAN:** ترتیب وابستگی رعایت شد؛ چون `functions.php` به Autoloader/Theme/DeBloat/AssetManager نیاز دارد، ماژول‌ها قبل از تست bootstrap ساخته شدند. Assetها عیناً از mirror کپی شدند (hash یکسان).
- **WRITE TEST + IMPLEMENT:** برای هر تسک تست اختصاصی واقعی نوشته شد (بدون PENDING). فایل‌های هدف: `functions.php`, `inc/Constants.php`, `inc/Autoloader.php`, `inc/Core/Theme.php`, `inc/Core/DeBloat.php`, `inc/Core/AssetManager.php`, `index.php`, `assets/` و `temp/tools/package-theme.sh`.
- **RUN TEST / SELF-CORRECT:**
  - محیط کار در ابتدای ادامه به snapshot قدیمی برگشته بود؛ با `git fetch`/`reset --hard origin/arena/...` به `adcc499` بازیابی شد و هارنس بازسازی شد.
  - `run-phase-tests.sh` مسیر ROOT را اشتباه محاسبه می‌کرد (`../..` به‌جای `../../..`) → اصلاح شد و جنریتور هم اصلاح شد تا بازتولید سالم بماند.
  - PHP-WASM امکان `shell_exec`/HTTP ندارد → تست HTTP 200 با اسکریپت Python (`check_assets_http.py`) انجام و گزارش JSON به تست‌های PHP داده می‌شود.
  - `set_current_screen('dashboard')` در WP-CLI باعث توقف می‌شد → تست admin با `define('WP_ADMIN', true)` شبیه‌سازی شد.
  - `functools.partial` قابل subclass نبود → handler سرور HTTP اصلاح شد.
- **نتیجه تست‌ها:** T001=12، T002=12، T003=6، T004=9، T005=7، T006=6، T007=11، T008=11، T009=7، T010=8، T011=13، T012=9؛ مجموعاً **111 چک پاس، ۰ fail، ۰ pending**. `bash temp/phase-01-core-and-assets/tests/run-phase-tests.sh` → `PHASE RESULT: PASS`.
- **Asset/Package:** `check-assets-http.sh` → 27/27 HTTP 200 و تطابق اندازه. `package-theme.sh` → `temp/dist/erfansanat.zip` فقط شامل `erfansanat/` (بدون static-mirror/temp/node_modules). تست T012 زیپ را extract و پوسته بسته‌بندی‌شده را فعال/برگرداند.
- **UPDATE MEMORY + Git:** وضعیت فاز ۱ PASSED؛ گام بعدی Phase 2 / P02-T001 در انتظار تایید کاربر. کامیت‌ها روی برنچ `arena/7378effd-erfansanat-theme-ai`.

### خوداصلاحی تکمیلی فاز ۱ (پایداری تست‌ها)
- **باگ ۳ (مهم):** تست T012 پوسته بسته‌بندی‌شده را در `temp/dist/package-test` استخراج و فعال می‌کرد؛ `switch_theme` باعث می‌شد `stylesheet_root`/`template_root` روی مسیر موقت بمانند و در اجرای بعدی `wp_get_theme('erfansanat')` خطای `theme_not_found` بدهد. **اصلاح:** استخراج در مسیر یکتا، تغییر نام به `erfansanat-packaged`، بازگرداندن صریح `stylesheet_root`/`template_root`، پاک‌سازی کامل مسیر موقت. تست‌های T001/T004 نیز rootها را صریح برمی‌گردانند.
- **نتیجه:** runner فاز ۱ دو بار متوالی `PHASE RESULT: PASS`؛ هیچ leftover package-test؛ ریشه پوسته فعال سالم.
