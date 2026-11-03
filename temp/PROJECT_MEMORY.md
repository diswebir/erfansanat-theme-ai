# PROJECT MEMORY — حافظه زنده (Active State)

> ⚠️ قبل از شروع هر تسک این فایل خوانده شود و بلافاصله پس از اتمام تسک آپدیت شود.

## وضعیت فعلی
**STAGE 0 COMPLETED — WAITING FOR USER CLARIFICATION** (توقف روی دروازه تایید کارفرما)

## آخرین رویداد
- **2026-11-03 — Stage 0 انجام شد:**
  1. محیط تجهیز شد: Node 22 ✅، PHP 8.4.27 (WASM از `@php-wasm/node`، شیم `/home/user/tools/php`) ✅، curl/git/unzip ✅. نصب apt مسدود بود (شبکه فقط GitHub/npm/pypi) — باینری release-assets هم مسدود؛ راه‌حل: PHP wasm. برای تست‌های سرتاسری آینده `@wp-playground/cli` (WordPress + SQLite روی npm) در دسترس است (در فاز ۱ فعال می‌شود).
  2. میرور استاتیک از `erfansanat-static-mirror.zip.png` (فایل ZIP با پسوند اشتباه) استخراج شد به `uploads/static-mirror/` — ۱۲۸ HTML، ۳۹MB.
  3. اسکن کامل انجام و ۴ فایل حافظه ساخته شد (`PROJECT_GOAL`, `DATA_BLUEPRINT`, `STATIC_TEMPLATE_MAP`, `PROJECT_MEMORY`).
  4. `.gitignore` برای `uploads/` (مرجع سنگین) و `node_modules` ساخته شد. کامیت Stage 0 روی `arena/594c659e-erfansanat-theme-ai` ثبت شد.

## تصمیمات فنی ثبت‌شده
- **D-001:** PHP runtime = `@php-wasm/node` 8.4 (شیم bash: `/home/user/tools/php -l/-f/-r`). هر بوت ~۱۵-۳۰ ثانیه طول می‌کشد؛ تست‌های سنگین باید در یک فرآیند واحد (اسکریپت واحد) اجرا شوند.
- **D-002:** میرور استاتیک فقط مرجع است و کامیت نمی‌شود؛ دارایی‌ها در فاز ۱ کپی می‌شوند.
- **D-003:** واریانت‌های تکراری میرور (`products/*`, `products.html`, `projects/city-*`, `projects/type-*`) به‌احتمال قوی خروجی دو اسکیم URL ژنراتور میرورند؛ canonical = اسلاگ‌های سایت زنده (`product/`, `shop`, `product_cat/`, `city/`, `project_type/`). **در انتظار تایید کارفرما (Q1).**
- **D-004:** `category/magazine.html` و `category/news-and-events.html` باگ title=undefined دارند؛ H1 معتبر است. عنوان‌های واقعی از دیتابیس زنده رندر می‌شوند.
- **D-005:** معماری `services/` (CPT یا برگه) **در انتظار پاسخ کارفرما (Q2)** — پیش‌فرض: برگه + SectionManager.
- **D-006:** لندینگ‌های `arya`/`smartelement` و صفحات `my-account`/`auth` در میرور نیستند؛ پیش‌فرض: ساخت از کامپوننت‌های موجود. **در انتظار تایید (Q3).**

## نکات به‌یادماندنی برای تسک‌های بعدی
- هدر/فوتر در هر ۱۲۸ صفحه یکسان است → برش تمیز در فاز ۴.
- CSS واحد minified است (۲ خط)؛ هنگام DeBloat دست نزنیم، فقط Enqueue.
- مسیر فونت‌ها در CSS نسبی است (`../fonts/`) → ساختار پوشه `assets/` باید مثل میرور حفظ شود.
- main.js هیچ fetch ندارد؛ همه فیلترها سمت کلاینت‌اند → در فاز ۸ به AJAX متصل می‌شوند و data-hookها حفظ می‌شوند.
- سبد خرید میرور localStorage است → با ووکامرس جایگزین می‌شود (Q4 برای نحوه پیش‌فاکتور).
- قیمت‌ها «تومان» (IRT) ✓؛ RTL ✓؛ Vazirmatn ✓.
- JSON-LDهای (Organization/Breadcrumb) میرور باید داینامیک شوند.

## پاسخ‌های دریافتی از کارفرما
- (هنوز پاسخی ثبت نشده — سوالات Stage 0: Q1 واریانت‌های URL، Q2 معماری services، Q3 صفحات بدون مرجع، Q4 سبد/پیش‌فاکتور)
