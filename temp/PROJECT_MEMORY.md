# PROJECT_MEMORY — حافظه زنده پروژه (Active State)

> آخرین به‌روزرسانی: 2026-11-03 · قبل از هر تسک بخوان، بعد از هر تسک آپدیت کن.

## وضعیت فعلی
```
STAGE 0 COMPLETED — WAITING FOR USER CLARIFICATION
```
- فاز جاری: **Stage 0 (Discovery & Bootstrapping)** — تمام شده؛ در انتظار پاسخ کاربر به سوالات ابهام‌زدایی.
- آخرین تسک پاس‌شده: اسکن کامل استاتیک + تجهیز هارنس + ساخت ۴ فایل حافظه.
- **هیچ فایل قالب وردپرسی ساخته نشده** (مطابق قانون Stage 0). ورود به Stage 1 فقط پس از تایید کاربر.

## کارهای انجام‌شده در Stage 0
1. فایل پیوست کاربر (`erfansanat-static-mirror.zip.png`) در واقع **ZIP** بود → استخراج به `static-mirror/` (۱۲۸ HTML، ۳۹MB).
2. هارنس تجهیز شد:
   - **PHP 8.4.27** (WASM از `@php-wasm/node@3.1.62` روی Node 22) — `php -l` و اجرای اسکریپپت تست OK. اکستنشن‌های موجود: sqlite/pdo_sqlite, mbstring, dom, xml, simplexml, curl, gd, zip, mysqli, openssl.
   - **WP-CLI 2.12.0** (`temp/tools/bin/wp-cli.phar`).
   - **WordPress 7.1.4** نصب‌شده روی SQLite در `temp/wp/` (فقط محیط تست؛ gitignore شده). `wp core install` موفق؛ `wp eval` سالم.
   - ابزارهای موجود سیستم: node 22, npm, git, curl, python3, gcc/make.
   - ⚠️ محدودیت شبکه سندباکس: فقط github.com / api.github.com / codeload.github.com / registry.npmjs.org / pypi.org در دسترس‌اند (apt و wordpress.org بسته‌اند).
   - ⚠️ php-wasm: `proc_open` ندارد → برخی دستورات subprocess-محور wp-cli کار نمی‌کنند (هشدار بی‌ضرر در `--info`).
   - بازسازی هارنس: `bash temp/tools/setup-harness.sh`
3. اسکن کامل ۱۲۸ صفحه HTML + ساختار هدر/فوتر/سکشن‌ها + ماژول‌های main.js → `temp/STATIC_TEMPLATE_MAP.md`.
4. فایل‌های حافظه ساخته شد: `PROJECT_GOAL.md`, `DATA_BLUEPRINT.md`, `STATIC_TEMPLATE_MAP.md`, `PROJECT_MEMORY.md`.

## تصمیمات فنی مهم (تاکنون)
- محیط تست WP با SQLite (drop-in `wp-sqlite-db`) چون MySQL در سندباکس نیست.
- `static-mirror/` در Git کامیت می‌شود (منبع طراحی + مرجع تست‌های مقایسه DOM). `temp/wp/`, `temp/tools/node_modules/`, `temp/tools/bin/` در `.gitignore` (سنگین، بازتولیدپذیر).
- Aliasهای میرور (`products/*`≡`product/*` و...) فقط در متاهای canonical تفاوت دارند → پیشنهاد ریدایرکت 301 (در انتظار تایید).
- هارنس PHP از طریق `temp/tools/php` و `temp/tools/wp` (wrapper) — نیاز به `export PATH="$PWD/temp/tools:$PATH"`.

## سوالات در انتظار پاسخ کاربر (Stage 0 Gate)
1. صفحات غایب در میرور: `arya`, `my-account`, `auth`, `search`, `404` — ساخته شوند با همان design system؟
2. `services.html` و ۵ تک‌خدمت: برگه `smartelement` + CPT `services` یا برگه‌های فرزند؟
3. Aliasهای قدیمی (`products/*`, `projects/city-*`, `projects/type-*`, `products.html`): ریدایرکت 301 به ساختار اصلی؟
4. برگه `project` (ID 8366 «پروژه‌های برگزیده») چه قالبی بگیرد؟
5. هیرو صفحه اصلی: دستی از Section Manager یا کوئری خودکار؟ (+ ویدیوی 7MB: assets قالب یا Media؟)
6. وبلاگ: TOC + دیدگاه‌ها اضافه شود (طبق MASTER_PLAN) یا وفادار به استاتیک (بدون آنها)؟
7. WooCommerce برای تست‌های فاز ۶: تلاش برای نصب از GitHub در سندباکس؛ در صورت عدم امکان، تست‌ها با stub انجام و روی سایت واقعی نهایی شود — مورد تایید؟

## گام بعدی (پس از تایید کاربر)
**STAGE 1:** ساخت `MASTER_PLAN.md` + `QA_REFLECTION_LOG.md` + درخت کامل ۸ فاز با `tasks/task-XX.md` (شروط پاس) و اسکلت `tests/test-task-XX.php` در `temp/phase-XX-name/`. سپس توقف برای تایید (Stage 2 gate طبق منشور).

## نکات یادآوری برای تسک‌های آینده
- املای تاریخی متاها مقدس است: `foure-image`, `galerry_produ`, `english_titl`, `vaule_of_variable`, `usage_countr`, `mortabet_mahsole_2`, `number2-viewc`.
- `product_cat` اسلاگ واقعی است (نه `product-category`)؛ `with_front=true` فقط برای همین تکسونومی.
- هدر/فوتر تماماً با `data-*` و کلاس Tailwind کار می‌کند (id ندارد) → Walkerها باید همان DOM را تولید کنند.
- کارت‌های آرشیو باید `data-product-*`/`data-project-*` را داینامیک پر کنند تا JS فیلتر/جستجو کار کند.
- تگ `<title>` دو صفحه category در میرور `undefined` است (باگ میرور، نه الگو).
- هر تسک = ۶ گام + کامیت Git روی برنچ `arena/7378effd-erfansanat-theme-ai`.
