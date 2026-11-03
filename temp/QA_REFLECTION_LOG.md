# QA REFLECTION LOG — زنجیره تفکر، تست‌ها و خوداصلاحی

> بعد از هر چرخه RUN TEST، نتیجه، باگ‌های کشف‌شده و اصلاحات اینجا ثبت می‌شود.

---

## 2026-11-03 — STAGE 0 (تجهیز هارنس و اسکن)

### زنجیره تفکر و اقدامات
1. **هدف:** آماده‌سازی محیط PHP/Node برای توسعه و تست قالب وردپرس + اسکن کامل میرور.
2. **مشکل ۱ — PHP نصب نبود:** `apt-get` ناموفق (شبکه فقط GitHub/npm/pypi را مجاز می‌داند).
3. **تلاش ۱:** باینری آماده `pmmp/PHP-Binaries` (PHP 8.4) از GitHub Releases → **ناموفق**: ریدایرکت به `release-assets.githubusercontent.com` مسدود است (SSL_ERROR_SYSCALL).
4. **تلاش ۲ (موفق):** پکیج `@php-wasm/node` + `@php-wasm/universal` از npm (WordPress Playground) — PHP 8.4.27 کامپایل‌شده به WASM با افزونه‌های کامل (dom, mbstring, sqlite3, gd, curl, zip, mysqli, xml…).
5. **باگ ۱ — `PHP` export نبود:** API نسخه ۳ کلاس `PHP` را در `@php-wasm/universal` دارد نه `@php-wasm/node`. → اصلاح import.
6. **باگ ۲ — `PHPLoader.processId must be set before init`:** لودر Node نسخه ۳ `processId` را فقط در محیط VITEST خودکار می‌سازد. → اصلاح: پاس‌دادن `emscriptenOptions: { processId: … }` به `loadNodeRuntime`.
7. **باگ ۳ — مسیر شیم:** `/home/user/tools/php` ابتدا دایرکتوری بود (از تلاش اول) → حذف دایرکتوری و ساخت شیم bash جدید. تست شد: `php -f` و نسخه 8.4.27 ✓.
8. **کشف ۱:** فایل آپلودی کاربر `*.zip.png` در واقع ZIP بود (بايت امضای PK) → استخراج مستقیم.
9. **کشف ۲:** میرور ۱۲۸ HTML دارد نه ۳۷ (برخلاف README میرور) — دو اسکیم URL موازی تولید شده است → شناسایی واریانت‌های تکراری و ارائه به کارفرما (پاسخ: canonical = اسلاگ‌های زنده).
10. **کشف ۳:** `category/magazine.html` و `category/news-and-events.html` باگ `title=undefined` دارند (H1 سالم) → ثبت در نقشه.
11. **کشف ۴:** CSS واحد minified (۲ خط) و همه url()ها نسبی → ساختار `assets/` باید عیناً حفظ شود.
12. **کشف ۵:** main.js بدون fetch/XHR؛ ۲۶۳ data-hook سمت کلاینت → استراتژی فاز ۸ (AJAX Native + حفظ hookها).

### نتیجه
✅ STAGE 0 PASS — محیط آماده، اسکن کامل، ۴ فایل حافظه ساخته و کامیت شد (`94d4986`). سوالات Q1-Q4 پرسیده و پاسخ دریافت شد.

---

## 2026-11-03 — STAGE 1 (درخت فازها)

### زنجیره تفکر و اقدامات
1. پاسخ‌های کارفرما اعمال شد: Q1=live_slugs، Q2=cpt_services، Q3=compose_from_components، Q4=wc_full.
2. تصمیم Q2 (CPT services) وارد MASTER_PLAN شد: رجیستر در Task 3.1 (با `has_archive=false` برای جلوگیری از تداخل rewrite با برگه فهرست)، قالب‌ها در Task 7.3.
3. تصمیم Q3 وارد Task 7.3 و Task 6.3 شد (ساخت از کامپوننت‌های میرور + استایل فرم‌های استاندارد).
4. درخت کامل ۸ فاز × تسک‌ها × شروط پاس × اسکلت تست‌ها + `run-phase-XX-tests.sh` ساخته شد.
5. نکته اجرایی تست‌ها (D-001): PHP wasm بوت سنگین دارد (~۱۵-۳۰s) → همه تست‌های یک تسک در یک فایل PHP واحد اجرا می‌شوند (`/home/user/tools/php -f`).

### نتیجه
✅ STAGE 1 PASS — در انتظار تایید کارفرما برای شروع Phase 1.
