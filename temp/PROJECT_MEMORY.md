# PROJECT_MEMORY — حافظه زنده (Active State)

**آخرین به‌روزرسانی:** 2026-11-03 — پایان STAGE 0 (Discovery) — وضعیت: `STAGE 0 COMPLETED — WAITING FOR USER CLARIFICATION (Gate 0)`

## ۱. وضعیت لحظه‌ای
| مورد | مقدار |
|---|---|
| فاز جاری | STAGE 0 — تجهیز هارنس، اسکن استاتیک، ساخت حافظه |
| آخرین تسک پاس‌شده | — (هنوز کدنویسی قالب آغاز نشده) |
| دروازه تأیید | Gate 0: در انتظار پاسخ کاربر به ۱۲ سؤال ابهام‌زدایی |
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

## ۴. پاسخ‌های کاربر (بدون پاسخ تا این لحظه)
| # | سؤال | پاسخ |
|---|---|---|
| Q1 | ویدیو هیرو (۶.۸MB) → کتابخانه رسانه یا فایل تم؟ | — |
| Q2 | ۲۰ محصول/۱۵ پروژه استاتیک در میرور، ولی دیتابیس زنده چند صد مورد دارد؛ منبع محتوا؟ | — |
| Q3 | صفحه‌های `arya` / `smartelement` (لندینگ) در میرور نیستند | — |
| Q4 | ۳ هیرو واریانت صفحه اصلی: کدام‌ها باید ساخته شوند؟ | — |
| Q5 | «خدمات» پست‌تایپ نیست؛ با برگه/CPT جدید یا لندینگ ساخته شود؟ | — |
| Q6 | سبد خرید/پرداخت: جایگزینی کامل UI ووکامرس یا استایل‌دهی روی فرم‌های استاندارد؟ | — |
| Q7 | فرم‌ها: ایمیل مقصد + ذخیره در پیشخوان؟ | — |
| Q8 | داشبورد: پسوند نام آپشن و پیشوند متا (`es_`)? | — |
| Q9 | منوها از «ظاهر → فهرست‌ها» یا مدیریت کامل از داشبورد اختصاصی؟ | — |
| Q10 | پیام‌های اعتبارسنجی/اعداد: فارسی‌سازی سمت سرور (WP-Parsidate)؟ | — |
| Q11 | فونت/آیکون: SVGهای inline حفظ شوند یا اسپرایت؟ | — |
| Q12 | تست روی وردپرس ۶.۷ محلی یا نیاز به اسکریپت استقرار روی لایو؟ | — |

## ۵. گام بعدی (پس از دریافت پاسخ)
1. به‌روزرسانی `PROJECT_MEMORY.md` + `STATIC_TEMPLATE_MAP.md` با پاسخ‌ها.
2. ساخت `temp/MASTER_PLAN.md` و `temp/QA_REFLECTION_LOG.md` + پوشه‌های ۸ فاز با `tasks/` و `tests/` (STAGE 1).
3. توقف در Gate 1 و شروع Task 1.1 پس از تأیید.
