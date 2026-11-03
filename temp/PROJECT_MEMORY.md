# PROJECT_MEMORY — حافظه زنده (Active State)

**آخرین به‌روزرسانی:** 2026-11-03 — بازطراحی نقشه راه به تسک‌های اتمیک (137 تسک) — وضعیت: `STAGE 1 REVISED — WAITING FOR GATE 1 APPROVAL`

## ۱. وضعیت لحظه‌ای
| مورد | مقدار |
|---|---|
| فاز جاری | STAGE 1 — اسکلت ۸ فاز / **۱۳۷ تسک اتمیک**؛ منتظر تأیید Gate 1 |
| آخرین تسک پاس‌شده | — (همه ۱۳۷ تسک `PENDING`؛ عمداً تست‌ها سبز نمی‌شوند) |
| دروازه تأیید | Gate 1: در انتظار تأیید `temp/MASTER_PLAN.md` و ۲۴ فایل تسک |
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

## ۵. گام بعدی (پس از تأیید Gate 1)
1. **Task 1.1** — اسکلت قالب (OOP، Autoloader، Kernel، theme supports) + ساخت `tools/harness/render.php` برای رندر کامل قالب در هارنس.
2. تکمیل تست واقعی `test-task-01.php` (جایگزینی `pending()` با `check()`)، اجرا تا ۱۰۰٪ PASS، سپس کامیت.
3. ادامه ۱.۲ و ۱.۳ و اجرای `run-phase-01-tests.sh`.
4. گزارش فاز ۱ → Gate 2.

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
