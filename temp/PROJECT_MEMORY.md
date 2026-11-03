# PROJECT_MEMORY — حافظه زنده (Active State)

**آخرین به‌روزرسانی:** 2026-11-03 — Gate 1 تأیید شده؛ Tasks 2.1–2.12 پاس شده‌اند؛ ادامه Phase 2 از Task 2.13.

## ۱. وضعیت لحظه‌ای
| مورد | مقدار |
|---|---|
| فاز جاری | Phase 2 — داشبورد اختصاصی / ۱۵ تسک اتمیک |
| آخرین تسک پاس‌شده | 2.12 — Section Builder admin UI (4 PASS / 0 FAIL؛ مجموع Phase 2: 92 PASS، 0 FAIL، 9 PENDING در 15 فایل) |
| دروازه تأیید | Gate 1: ✅ کاربر تأیید کرد؛ Gate 2 پس از اتمام Phase 2 |
| شاخه گیت | `arena/3730add5-erfansanat-theme-ai` (پایه: `8eddaff`) |
| فایل مرجع ورودی | `reference/static-mirror/` (۱۲۸ فایل HTML، ۳۹MB شامل ۶.۸MB ویدیو) |

## ۲. تصمیمات فنی ثبت‌شده (Decisions)
| # | تصمیم | دلیل |
|---|---|---|
| D1 | قالب کامل OOP با Namespace `ErfanSanat\Theme`، Autoloader دستی (بدون Composer) در `inc/` | قانون R7 و Task 1.1 |
| D2 | یک `styles.css` استاتیک بدون تغییر منتقل می‌شود؛ هیچ PostCSS/Tailwind build در قالب نیست | R1 و R7؛ CSS کامپایل‌شده آماده است |
| D3 | هدر و فوتر **یک** نسخه دارند (۱۲۸ فایل مشترک) → یک `header.php` / `footer.php` با پارامتر `activeMenu` | اسکن: هش هدر/فوتر یکسان است (تفاوت فقط `../`) |
| D4 | ۳۶ گروه فایل استاتیک کاملاً تکراری‌اند (مثل `product/x.html` = `products/x.html` و `shop.html` = `products.html`) → یک منبع واحد | `temp/_scan/dedupe.json` |
| D5 | `data-*`ها و کلاس‌ها «قرارداد» بین PHP و `main.js` هستند؛ هیچ‌کدام تغییر نمی‌کند | R1؛ `main.js` با `querySelector` روی همین هوک‌ها کار می‌کند |
| D6 | هارنس تست: PHP 8.3 (wasm) + WordPress 6.7.1 + SQLite drop-in (`/home/user/.cache/harness/wpn`)؛ هاست‌های apt/Docker مسدود هستند | تنها مسیر اجرای واقعی PHP در این سندباکس |
| D7 | ذخیره داده: تمام متاها در `postmeta` با همان کلیدها؛ داشبورد اختصاصی در آپشن واحد `erfansanat_theme_options` + کش استاتیک | Task 2.1 / R3 |
| D8 | سکشن‌های صفحه اصلی: هر سکشن یک فایل `template-parts/home/section-*.php` با متادیتای ترتیب/فعال‌بودن در `_es_sections` | Task 2.3 و 5.1 |

## ۳. یافته‌های کلیدی اسکن (به‌خاطر بسپار)
1. HTML خروجی یک اپ React (Tailwind) است که **pre-render** شده؛ `custom="0.03"` روی کارت‌ها باقی‌مانده (نکته: در PHP باید حذف یا به `data-*` تبدیل شود تا `custom` بی‌معنا نماند).
2. **هدر** یکسان: ۶۹.۳–۶۹.۶KB، شامل نوار اعلان بالا، لوگو، `nav[aria-label="منوی اصلی"]`، مگامنوی محصولات (تب‌دار)، مگامنوی پروژه‌ها (تب نوع/شهر)، جستجوی گلوبال، سبد خرید، منوی موبایل (`aside[data-mobile-sidebar]`).
3. **فوتر** یکسان: ۷.۴۷–۷.۵۲KB، بدون آکاردئون، لینک‌های سریع/دسته‌ها/تماس + دکمه `data-scroll-top`.
4. صفحه اصلی: **۱۵ سکشن** با ۳ هیرو واریانت موازی (`home1` اسلایدر/ویدیو، `home2` تب‌های پورت‌فولیو + تایم‌لاین تاریخچه، `home3` — فقط بخشی از هوک‌ها موجود است).
5. آرشیو محصولات: ۲۰ کارت با هوک‌های `data-product-item/category/price/ip/voltage/views/rating/purchasable` → این‌ها ورودی فیلتر سمت کلاینت‌اند و باید از متای واقعی پر شوند.
6. محصول تکی: هیرو بونر → گالری (thumb/lightbox) → `#product-details` با **تب‌ها** (`data-prod-tab-*`) شامل معرفی/جدول مشخصات/شناسه‌های قانونی/نظرات → پروژه‌های مرتبط → FAQ → محصولات مشابه.
7. پروژه تکی: هیرو → گالری → `#project-details` (تب‌ها) → `#used-products` → سایر پروژه‌ها.
8. تک‌نوشته: هیرو → بدنه + دکمه‌های اشتراک‌گذاری → مطالب مرتبط.
9. فیلترهای آرشیو (`data-product-*`, `data-project-*`, `data-blog-*`) **کاملاً سمت کلاینت** روی DOM اجرا می‌شوند؛ برای جایگزینی JetSmartFilters باید همان ساختار DOM از کوئری PHP تولید شود + صفحه‌بندی سرور برای سئو.
10. `main.js` ماژولار است: `initNavbar, initMegaMenuAndSearch, initHeroVideo, initHomeHeroVariants, initCounters, initHomeServices, initGalleries, initProjectsArchive, initProductsArchive, initProductDetailAndFaqs, initPurchaseJourney, initBlogArchive, initRecruitmentTabs, initShareButtons, initContactForm, initAll`.

## ۴. پاسخ‌های کاربر (Gate 0 — تأییدشده 2026-11-03)
| # | سؤال | پاسخ کاربر |
|---|---|---|
| G1 | واریانت‌های هیرو | فقط **home1** رندر شود؛ home2/home3 به‌عنوان فایل آماده و قابل فعال‌سازی از داشبورد بمانند |
| G2 | `arya` / `smartelement` | فعلاً **مسکوت**؛ بعداً با Section Builder و HTML لایو |
| G3 | سبد/پرداخت/حساب | **Override قالب‌های ووکامرس با همان UI استاتیک** + هوک‌های استاندارد |
| G4 | راهبرد محتوا | **کاملاً داینامیک از DB**، صفر هاردکد |
| G5 | منوها | **ساختار + آیتم‌ها + قابلیت‌های مگامنو همه در Appearance → Menus**؛ فقط تنظیمات نمایشی (خاموش/روشن، hover/click، چسبندگی) در داشبورد اختصاصی |
| G6 | تحویل | Stage 1 سپس توقف در Gate 1 |

## ۵. گام بعدی
- ادامه Phase 2 از Task 2.13: فرانت‌اند Renderer برای ترتیب/وضعیت سکشن‌ها و fallback قالب.
- `Store` داده هر صفحه را در `_es_sections` روی post meta نگه می‌دارد؛ برای صفحه اصلی از شناسه برگه front page و برای صفحات ثابت از شناسه برگه متناظر استفاده شود.
- پس از هر تسک: تست اختصاصی، ثبت نتیجه در QA، به‌روزرسانی این حافظه و کامیت روی شاخه فعال.

## ۶. وضعیت هارنس تست
- PHP 8.3 (wasm) ✅ · WordPress 6.7.1 + SQLite ✅ (`/home/user/.cache/harness/wpn`)
- اجرای تست: `node tools/harness/run-tests.mjs <test.php>` (نیازمند `npm install` یک‌باره — `package.json` در ریشه)
- WooCommerce برای هارنس هنوز نصب نیست ⚠️ (فاز ۶ به آن نیاز دارد)

## ۷. بازطراحی تسک‌ها به اتمیک (درخواست کاربر — 2026-11-03)
> «تسک‌ها کم هستن برای هر فاز… به تسک‌های اتمیک و ریزتر و با جزئیات تر و جامع تر تبدیل کنی»

| فاز | تسک قبلی | تسک اتمیک جدید | تفکیک کلیدی |
|---|---|---|---|
| ۱ | ۳ | **۱۲** | `style.css` / Autoloader / Kernel / functions / هارنس رندر / DeBloat / CSS-JS-فونت / تصاویر+ویدیو / AssetManager / Preload / منوها / گیت |
| ۲ | ۳ | **۱۵** | لایه آپشن / منو ادمین / موتور فیلد / AJAX امن / Media / Repeater / تب هدر-فوتر / برند / آرشیو / اسکریپت / Section Store / UI سکشن / Renderer / Import-Export / گیت |
| ۳ | ۳ | **۱۸** | `projects` / `project_type` / `city` / rewrite ووکامرس / flush+ممیزی / ستون پروژه / ستون سایر / MetaRepository / متاباکس شناسه / گالری / روابط / Repeaterها / Quick Edit / متاباکس پروژه / ترم‌متا / Hreflang / بازدید / گیت |
| ۴ | ۳ | **۱۲** | head+Hreflang / header / footer / Walker محصولات / Walker پروژه‌ها / فیلدهای منو در Appearance / تنظیمات نمایشی / منوی موبایل / مودال جستجو / سبد هدر / سازگاری main.js / گیت |
| ۵ | ۳ | **۲۱** | front-page + **یک تسک برای هر ۱۶ سکشن** + کارت‌ها + کوئری‌ها + واریانت‌های home2/3 + گیت |
| ۶ | ۳ | **۱۸** | Woo support / هیرو / گالری / جعبه خرید / جدول ۱۸ ویژگی / Repeaterها / شناسه‌ها / کاتالوگ / پروژه‌های مرتبط / تب‌ها+نظرات / مشابه / آرشیو / برچسب-برند / سبد / پرداخت / حساب / صفحات بی‌قالب / گیت |
| ۷ | ۳ | **۲۳** | تک‌پروژه (هیرو/گالری) / تب‌ها / محصولات پروژه / پروژه‌های مرتبط / آرشیو / نوع / شهر / خدمات / جزئیات خدمت / وبلاگ / دسته‌ها / تک‌نوشته+TOC / نظرات+مرتبط / جستجو / ۴۰۴ / ۶ برگه ثابت (هر کدام یک تسک) / page.php+نگاشت / گیت |
| ۸ | ۳ | **۱۸** | جستجوی ایجکس / UI جستجو / فیلتر محصول / فیلتر پروژه / جدول فرم‌ها / فرم تماس / استعلام / استخدام+آپلود / بازخورد / لیست پیام‌ها / تنظیمات فرم / Hreflang / JSON-LD / امنیت / عملکرد / ریسپانسیو / سازگاری افزونه / E2E |

**جمع:** ۲۴ → **۱۳۷ تسک** (S=۱۶ · M=۶۸ · L=۵۳). هر تسک اکنون: وابستگی صریح، فایل‌های هدف، مراحل گام‌به‌گام، شروط پاس شماره‌گذاری‌شده (`P1..Pn`)، تست‌های خودکار، و بخش «ضدالگوها».
**منبع تولید:** `tools/plan/phase01..08.py` → `tools/gen_phase_scaffold.py` (قابل بازتولید و ویرایش ساختاریافته).

---

## §8 — فاز ۱ اجرا شد (زیرساخت هسته و دارایی‌ها) — 2026-11-03

**وضعیت: `PHASE 1 DONE — GATE 1 GREEN (170/170) — WAITING FOR USER APPROVAL TO START PHASE 2`**

### آنچه ساخته شد (کد واقعی، بدون placeholder)

```
theme/
├── style.css                 هدر قالب، صفر قاعده CSS
├── functions.php             ۱۲ خط: ABSPATH + require inc/bootstrap.php
├── index.php                 fallback loop + pagination + empty state
├── header.php                پوسته سند: language_attributes, wp_head, body_class, wp_body_open
├── footer.php                پوسته سند: wp_footer + </html>
├── screenshot.png            ۱۲۰۰×۹۰۰
├── inc/
│   ├── Constants.php         ES_THEME_VERSION/SLUG/DIR/URI/ASSETS/REWRITE_VERSION
│   ├── Autoloader.php        PSR-4 دستی، prefix ErfanSanat\Theme\ → inc/, cache + class_alias
│   ├── bootstrap.php         Constants → Autoloader → Theme::instance()->boot()
│   ├── Theme.php             singleton kernel + register_module + action es_theme_booted
│   ├── Contracts/ModuleInterface.php
│   ├── Setup/ThemeSupport.php    textdomain, title-tag, thumbnails, html5, logo, Woo (شرطی)
│   ├── Setup/Menus.php       4 جایگاه: main-mega-menu, mobile-menu, tolidat-sherkat, landing
│   ├── Performance/DeBloat.php  حذف block-library/global-styles/emoji/embed/meta های زائد
│   └── Assets/{Manifest,AssetManager,Preload}.php
├── assets/                   css 188K · js 112K · ۳ فونت · ۲۱ تصویر + ۹ webp · ویدیو ۶.۸M
└── languages/
```

### جایگاه هدر/فوتر
`header.php` و `footer.php` در فاز ۱ فقط **پوسته معتبر سند** هستند (برای صفر شدن Warning هسته).
مارک‌آپ پیکسل‌به‌پیکسل در **Phase 4 / Task 4.2** جایگزین می‌شود؛ تست 1.4 صریحاً تضمین می‌کند این دو فایل «کپی صفحه استاتیک» ندارند.

### نتیجه گیت (اندازه‌گیری‌شده)

| سنجه | مقدار | بودجه |
|---|---|---|
| فایل تست سبز | 12/12 | 12 |
| assertion PASS | 170 | همه |
| FAIL / PENDING | 0 / 0 | 0 |
| CSS قالب در هر صفحه | 1 | ≤ 2 |
| JS قالب در هر صفحه | 1 | ≤ 2 |
| Warning/Deprecated | 0 | 0 |
| theme_ms | 49–79ms | < 500 |
| کوئری SQL (`/`) | 14–17 | < 60 |

### دستورهای تکرارپذیر
```bash
node tools/harness/run-phase.mjs temp/phase-01-core-and-assets/tests   # 164 assertion
node tools/harness/perf.mjs "/"                                        # بودجه
node tools/harness/run-php.mjs tools/harness/gate.php                  # GATE BUDGET: OK
node tools/harness/phase-gate.mjs 01 "/"                               # گیت کامل
```

### نکات برای فازهای بعد
- هر قالب جدید **باید** `get_header()`/`get_footer()` بزند؛ وگرنه همان دو deprecated notice برمی‌گردد.
- `AssetManager` فایل ناموجود را صف نمی‌کند (گارد `is_readable`) → اگر استایل جدید اضافه شد، اول فایل را روی دیسک بگذارید، بعد Manifest را ویرایش کنید.
- سنجه زمان در gate = `theme_ms`، نه کل درخواست (بوت PHP-wasm ~۱۰۰s است).


## §9 — Phase 2 شروع شد (2026-11-03)

**تأیید کاربر:** «اوکیه ادامه بده» پس از گزارش Gate 1؛ مجوز صریح برای شروع فاز داشبورد.

### Task 2.1 — PASS
- `theme/inc/Helpers/Defaults.php`: درخت fallback برداشت‌شده از mirror (تلفن کارخانه/مدیر فروش، ایمیل، آدرس، ساعت کاری، کپی‌رایت و شبکه‌ها).
- `theme/inc/Helpers/Options.php`: `es_option()`, `es_update_option()`, `es_option_reset()`؛ dot-notation، merge fallback، یک option canonical و cache در سطح درخواست.
- Bootstrap، فایل helper را بار می‌کند و hook `es_options_saved` را برای invalidation ثبت می‌کند.
- تست خودکار: **9 PASS / 0 FAIL / 0 PENDING**؛ 100 خواندن فقط یک بار `get_option` را صدا زد؛ update فقط همان `erfansanat_theme_options` را نوشت.
- تست canonical: `tools/plan/filled/phase-02/test-task-01.php`; scaffold در `temp/phase-02-custom-admin-dashboard/tests/`.
- وضعیت بعدی: Task 2.4 — ذخیره AJAX امن (Nonce + Capability + whitelist schema).


### Task 2.2 — PASS
- `theme/inc/Admin/AdminMenu.php`: صفحه سطح‌بالا «عرفان صنعت» با `manage_options` و پنج زیرصفحه (سراسری، برند، سکشن‌ها، فرم‌ها، ابزارها).
- Theme kernel ماژول `admin-menu` را ثبت می‌کند.
- `theme/assets/admin/admin.css` پوسته RTL واکنش‌گرا و scoped؛ `admin.js` فعال‌سازی `aria-current`؛ هر دو فقط روی hookهای صفحه قالب enqueue می‌شوند.
- صفحه داشبورد و صفحه‌های دسته‌بندی، خروجی Escape شده و بررسی Capability دارند؛ Appearance → Menus و ساختار منوها همچنان منبع بومی باقی می‌ماند.
- تست: **9 PASS / 0 FAIL / 0 PENDING**؛ suite فعلی برای دو تسک انجام‌شده **18 PASS**؛ 13 تسک باقی‌مانده هنوز `PENDING` و عمداً فاز را سبز نمی‌کنند. `node --check` برای JS نیز موفق است.
- وضعیت بعدی: Task 2.4.


### Task 2.3 — PASS
- `Schema` تعریف‌های فیلد را با مسیر dot-safe، type allow-list، options/fields لازم ثبت می‌کند؛ `register_many()` در خطا atomic rollback دارد.
- `FieldRenderer` HTML قابل دسترس و Escape شده برای ۱۲ نوع فیلد می‌سازد، نام‌های تو در تو `es_options[...]` را نگه می‌دارد و nonce یکتای هر صفحه را تولید می‌کند.
- `Sanitizer` نوع‌محور: text/textarea/url/email/tel/number/color/toggle/select/checkbox-group/media/repeater؛ repeater فقط فیلدهای schema را می‌پذیرد و `item-N` را حفظ می‌کند.
- type rendererهای Basic/Media/Repeater در `inc/Admin/Fields/Types/` قرار گرفتند؛ CSS پایه فیلدها در پوسته admin اضافه شد.
- تست: **15 PASS / 0 FAIL / 0 PENDING**؛ کل تست‌های پیاده‌شده (2.1–2.3): **33 PASS**؛ 12 تست آینده عمداً PENDING هستند.
- وضعیت بعدی: Task 2.4 — endpoint امن ذخیره گزینه‌ها.


### Task 2.4 — PASS
- `AjaxSave` ثبت‌کننده‌ی authenticated-only action `wp_ajax_es_save_options` است؛ ابتدا nonce با `check_ajax_referer(..., false)`، سپس `manage_options` و rate limit (30 درخواست/دقیقه/کاربر) بررسی می‌شوند.
- payload از `es_options` می‌خواند (نامی مطابق `FieldRenderer`)، از Schema فقط مسیرهای ثبت‌شده را استخراج و sanitize می‌کند؛ کلید ناشناس حذف می‌شود.
- `Options::update_many()` چند مقدار را در یک `update_option('erfansanat_theme_options', ...)` ذخیره می‌کند؛ پاسخ `wp_send_json_success/error` و hook `es_options_changed` ارائه می‌شود.
- تست: **9 PASS / 0 FAIL / 0 PENDING**؛ suite 15 فایلی فعلی: **42 PASS / 0 FAIL / 35 PENDING** (تسک‌های هنوز پیاده‌نشده).
- وضعیت بعدی: Task 2.5 — Media Picker مبتنی بر `wp.media` و اعتبارسنجی attachment.


### Task 2.5 — PASS
- Media control واقعی در `inc/Admin/Fields/types/media.php` + adapter سازگار با autoloader؛ hidden field برای ID یا CSV، پیش‌نمایش، انتخاب/حذف و `aria-live`.
- `assets/admin/media.js` با wp.media، تک‌انتخاب و چندانتخاب، ساخت DOM از nodeها (نه HTML ناامن)، eventهای input/change، و allowlist آدرس‌های http/https برای preview/link.
- `wp_enqueue_media()` و `media.js` فقط در صفحه‌های داشبورد قالب enqueue می‌شوند.
- Sanitizer رسانه اکنون attachment واقعی را بررسی می‌کند، در حالت image نوع تصویر را الزام می‌کند و CSV چندتایی را یکتا/پاک می‌سازد؛ URL اختیاری برای فیلد تک‌رسانه پشتیبانی می‌شود.
- تست PHP: **5 PASS / 0 FAIL / 0 PENDING**؛ harness رفتاری JS با DOM mock: تک/چند انتخاب، پیش‌نمایش، پاک‌کردن، reject کردن `javascript:` همگی PASS؛ `node --check` موفق.
- تست قدیمی Task 2.3 به‌روزرسانی شد تا media validation فعلی را با fixture attachment واقعی بسنجد.
- وضعیت بعدی: Task 2.6 — Repeater Codec و drag-and-drop بی‌وابستگی.

### Task 2.6 — PASS
- `RepeaterCodec` safely decodes JetEngine `item-N` arrays, JSON and PHP serialized arrays (`allowed_classes=false`); `preserve_format()` encodes updated values in the previous representation.
- Repeater markup lives at `inc/Admin/Fields/types/repeater.php`: keyed rows, reorder handle, up/down buttons, delete, and a safely escaped `<template>` whose placeholders become `item-N` form names.
- `assets/admin/repeater.js` is dependency-free: add/remove, native drag/drop, keyboard arrows and up/down buttons all reindex both IDs and `name` attributes. The script enqueues only on theme admin screens.
- `Sanitizer` now uses `RepeaterCodec` when available and continues to discard empty rows / sanitize schema-defined children.
- Tests: **7 PASS / 0 FAIL / 0 PENDING**. The Node DOM mock verified reorder, deletion, add and stable posted names. Complete current phase runner: **54 PASS / 0 FAIL / 27 PENDING** (9 remaining tasks not implemented yet).
- Next: Task 2.11 — Section Manager schema/storage; no Phase 3 work until Gate 2 review and approval.


### Task 2.7 — PASS
- `HeaderTab` و `FooterTab` در `theme/inc/Admin/Tabs/` فیلدهای سراسری را ثبت می‌کنند: اعلان، شعار، لوگو، تلفن‌ها، ایمیل، شبکه‌های اجتماعی با آیکون‌های allowlist شده، آدرس/ساعات/کپی‌رایت و کنترل نمایش اجزای فوتر.
- تنظیمات پیوندهای سریع فقط وضعیت نمایش را در Options ذخیره می‌کنند؛ خود آیتم‌ها و ساختار فهرست در Appearance → Menus باقی می‌ماند.
- تنظیمات سراسری اکنون UI تب‌های Header/Footer قابل‌دسترس، nonce و فرم یکپارچه AJAX دارد؛ `admin.js` کلیک/کلیدهای جهت/Home/End، submit به `es_save_options` و پیام وضعیت را پشتیبانی می‌کند.
- URLها به HTTP/HTTPS محدودند؛ URL اجتماعی باید مطلق باشد و مقدار نامعتبر پیش از ذخیره در whitelist خالی می‌شود. `HeaderTab::render_announcement()` هنگام خاموش بودن اعلان، کل خروجی را `''` برمی‌گرداند.
- تست اختصاصی: **11 PASS / 0 FAIL / 0 PENDING**؛ تست syntax جاوااسکریپت موفق. کل suite فاز: **65 PASS / 0 FAIL / 24 PENDING** در 15 فایل؛ 8 تسک آینده عمداً PENDING هستند.
- ادامه بعدی پس از تکمیل Task 2.8: Task 2.9؛ پس از اتمام Phase 2 در Gate 2 توقف و برای Phase 3 منتظر تأیید صریح کاربر بمان.


### Task 2.8 — PASS
- `BrandTab` گزینه رنگ را در صفحه اختصاصی برند/رنگ ثبت می‌کند؛ `AdminMenu::render_section()` به این فرم متصل است و فرم nonce/AJAX دارد.
- رنگ زمردی ثابت `#047857` از Defaults به‌عنوان پالت پایه است؛ بدون override هیچ `<style>` اضافه‌ای منتشر نمی‌شود.
- `InlineCss` فقط رنگ hex معتبر و متفاوت از پالت پایه را روی wrapper موجود `#es-page.es-site` در متغیر `--es-brand` اعمال می‌کند؛ `styles.css` و کلاس‌های DOM دست‌نخورده‌اند و هیچ `!important` تولید نمی‌شود.
- payload رنگ نامعتبر با `reject_invalid_save` از whitelist حذف می‌شود تا مقدار ذخیره‌شده قبلی باقی بماند.
- تست اختصاصی: **5 PASS / 0 FAIL / 0 PENDING**؛ کل Suite Phase 2: **70 PASS / 0 FAIL / 21 PENDING**؛ pendingها فقط Tasks 2.9–2.15 هستند.
- ادامه پس از تکمیل Task 2.9: Task 2.10؛ پس از Task 2.15 در Gate 2 توقف و برای Phase 3 منتظر تأیید کاربر بمان.


### Task 2.9 — PASS
- `ArchiveTab` صفحه تنظیمات آرشیو را با ستون‌های گرید 1–4، تعداد مجاز 6/9/12/24، ترتیب whitelist شده، toggle فیلترهای دسته/IP/ولتاژ/قیمت/امتیاز/بازدید و نمایش FAQ ثبت می‌کند.
- `ArchiveConfig` فقط روی کوئری اصلی آرشیوی `pre_get_posts` تعداد/ترتیب را اعمال می‌کند؛ `visible_filters()` و `render_filter()` امکان حذف کامل کنترل خاموش از DOM را برای templateها می‌دهند، بدون تغییر کلاس‌های استاتیک یا ساخت query جدید.
- تنظیمات آرشیو در AdminMenu اضافه شد؛ تست قدیمی ثبت منوها برای زیرصفحه جدید هم‌راستا شد.
- تست اختصاصی: **5 PASS / 0 FAIL / 0 PENDING**؛ full Phase 2 suite: **75 PASS / 0 FAIL / 18 PENDING**؛ موارد PENDING فقط Tasks 2.10–2.15 هستند.
- ادامه پس از تکمیل Task 2.10: Task 2.11؛ Gate 2 پس از پایان Task 2.15 و پیش از هر کار Phase 3.


### Task 2.10 — PASS
- `ScriptsTab` در صفحه ابزارها دو code-field امن برای `wp_head` و `wp_footer` می‌دهد؛ محتوا با KSES محدود به تگ‌های `<script>` و صفات امن می‌ماند.
- `CustomCode` در اولویت 99 به `wp_head`/`wp_footer` وصل است، در صفحه ادمین یا در صورت خالی بودن مقدار هیچ خروجی ندارد و قبل از انتشار مجدداً sanitize می‌کند.
- type فیلد `code` به Schema/BasicField افزوده شد؛ sanitization ذخیره علاوه بر nonce/capability endpoint، خودش هم `manage_options` را الزام می‌کند. صفحه Tools به فرم nonce/AJAX متصل شد.
- Task 2.3 renderer suite برای فیلد code به‌روز شد؛ تست اختصاصی Task 2.10: **7 PASS / 0 FAIL / 0 PENDING**.
- full Phase 2 suite پس از تکمیل: **82 PASS / 0 FAIL / 15 PENDING**؛ pendingها فقط Tasks 2.11–2.15 هستند.
- پس از این task، Task 2.11 پیاده‌سازی شد؛ Phase 3 فقط بعد از Gate 2 و تأیید کاربر.

### Task 2.11 — PASS
- `Registry` اکنون schema صفحه اصلی را دقیقاً با ۱۵ سکشن از ترتیب مرجع ثبت می‌کند و schemaهای مجزا برای برگه‌های درباره، تماس، FAQ، مجوزها، گارانتی و استخدام دارد.
- هر سکشن دارای `key`, `label`, `template`, تعریف فیلدها و `default_data` است؛ فیلتر `es_sections_registry` امکان تکمیل/تغییر registry را برای توسعه‌دهنده‌ها فراهم می‌کند.
- `Store` داده `order`, `enabled`, `data` را در post meta با کلید دقیق `_es_sections` ذخیره و می‌خواند؛ نبود متا به ترتیب پیش‌فرض و همه‌فعال برمی‌گردد.
- Normalization کلیدهای خارج‌شده از schema را حذف می‌کند، سکشن‌های تازه را به انتهای ترتیب اضافه می‌کند، ترتیب تکراری را حذف می‌کند و فقط فیلدهای schemaشده را نگه می‌دارد.
- تست مستقل: **5 PASS / 0 FAIL / 0 PENDING**؛ full Phase 2 suite: **87 PASS / 0 FAIL / 12 PENDING** (فقط Tasks 2.12–2.15 هنوز pending هستند).
- ادامه: Task 2.12 — Sections Builder در ادمین؛ Gate 2 پس از Task 2.15 و Phase 3 فقط با تأیید صریح کاربر.

### Task 2.12 — PASS
- `SectionsPage` اکنون یک builder RTL برای ۱۵ سکشن خانه دارد: ترتیب قابل drag-and-drop، دکمه‌های بالا/پایین، toggle فعال‌بودن، و پنل‌های ویرایش با `aria-expanded` و schema-backed fields.
- دکمه‌های حذف/افزودن، کارت را بین چیدمان و قفسه آماده‌افزودن جابه‌جا می‌کنند؛ حالت قفسه به‌صورت `available` در `_es_sections` نگه داشته می‌شود تا بعد از بارگذاری مجدد هم باقی بماند.
- فیلدهای hero شامل متن/URL دکمه و گالری شامل Repeater نمونه است؛ داده‌ها با همان `Store` و sanitizerهای schema ذخیره می‌شوند.
- AJAX `es_save_sections` فقط با nonce معتبر، `manage_options`، شناسه front page و مجوز `edit_post` مجاز است؛ CSS/JS فقط در صفحه سکشن‌ها بارگذاری می‌شوند.
- تست PHP اختصاصی: **4 PASS / 0 FAIL / 0 PENDING**؛ Node DOM mock عملیات افزودن/حذف/ترتیب کیبورد/toggle/editor/serialize را PASS کرد؛ `node --check` موفق.
- کل suite Phase 2: **92 PASS / 0 FAIL / 9 PENDING** (فقط Tasks 2.13–2.15 pending هستند).
- ادامه: Task 2.13 — Renderer سکشن‌ها در فرانت‌اند؛ Gate 2 و Phase 3 فقط پس از Task 2.15 و تأیید صریح کاربر.
