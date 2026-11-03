# PROJECT_MEMORY — حافظه زنده (Active State)

**آخرین به‌روزرسانی:** 2026-11-03 — STAGE 1 کامل شد — وضعیت: `STAGE 1 COMPLETED — WAITING FOR GATE 1 APPROVAL`

## ۱. وضعیت لحظه‌ای
| مورد | مقدار |
|---|---|
| فاز جاری | STAGE 1 — اسکلت ۸ فاز / ۲۴ تسک آماده؛ منتظر تأیید Gate 1 |
| آخرین تسک پاس‌شده | — (همه ۲۴ تسک `PENDING`؛ عمداً تست‌ها سبز نمی‌شوند) |
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
