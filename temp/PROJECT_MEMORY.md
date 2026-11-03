# PROJECT MEMORY — حافظه زنده (Active State)

> ⚠️ قبل از شروع هر تسک این فایل خوانده شود و بلافاصله پس از اتمام تسک آپدیت شود.

## وضعیت فعلی
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
