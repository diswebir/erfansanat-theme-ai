# MASTER_PLAN — نقشه راه کامل پروژه

> وضعیت‌ها: `[PENDING]` · `[IN_PROGRESS]` · `[TESTING]` · `[PASSED]`
> آخرین به‌روزرسانی: 2026-11-03 (Stage 1 ساخته شد)

## مراحل آماده‌سازی
| مرحله | وضعیت |
|---|---|
| Stage 0 — تجهیز هارنس + اسکن استاتیک + پرسش‌نامه | `[PASSED]` |
| Stage 1 — ساخت درخت فازها/تسک‌ها/تست‌ها + MASTER_PLAN + QA_LOG | `[PASSED]` |
| **دروازه تایید کاربر برای شروع Phase 1** | ⏳ در انتظار |

---

## Phase 1 — هسته قالب، Autoloader و Assets (`temp/phase-01-core-and-assets`)
| تسک | شرح | وضعیت |
|---|---|---|
| 1.1 | اسکلت قالب (`style.css`, `functions.php`, `index.php`) + OOP در `inc/` با Namespace `ErfanSanat\Theme` | `[PENDING]` |
| 1.2 | ماژول `DeBloat.php` (حذف CSS/JS گوتنبرگ، ایموجی، پاکسازی `wp_head`) | `[PENDING]` |
| 1.3 | انتقال Assets به `assets/` + `AssetManager.php` (enqueue شرطی per-template + `filemtime`) | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` | `[PENDING]` |

## Phase 2 — داشبورد اختصاصی ادمین + Section Builder (`temp/phase-02-custom-admin-dashboard`)
| تسک | شرح | وضعیت |
|---|---|---|
| 2.1 | پنل «داشبورد عرفان صنعت» (`inc/Admin/ThemeOptions.php`) — RTL، تب‌بندی، AJAX Save، بدون افزونه | `[PENDING]` |
| 2.2 | تب‌های سراسری (هدر/مگامنو/لوگو/اعلان/فوتر/تماس/شبکه‌های/رنگ‌ها/اسکریپت‌ها/آرشیو) با پیش‌فرض استاتیک | `[PENDING]` |
| 2.3 | موتور `SectionManager.php` (روشن/خاموش، Drag&Drop، Repeater/Media/Link/Icon، رندر پویا) | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` | `[PENDING]` |

## Phase 3 — CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده (`temp/phase-03-cpt-and-legacy-meta`)
| تسک | شرح | وضعیت |
|---|---|---|
| 3.1 | ثبت `projects` + `project_type` + `city` + CPT `services` (D2) + rewrite دقیق `product_cat` + فیلترهای ادمین + ریدایرکت `project`→`/projects/` (D4) | `[PENDING]` |
| 3.2 | موتور متاباکس محصولات (`inc/Admin/ProductMetaboxes.php`) — همه کلیدها + Repeaterها، سازگار با فرمت JetEngine | `[PENDING]` |
| 3.3 | متاباکس پروژه‌ها (`second-image`..`six-image`, `finish-pro-time`, `mortabet_mahsole_2`) + FAQ دسته + Hreflang + شمارنده بازدید | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` | `[PENDING]` |

## Phase 4 — هدر، مگامنو، منوی موبایل، فوتر (`temp/phase-04-header-footer-navigation`)
| تسک | شرح | وضعیت |
|---|---|---|
| 4.1 | برش هدر دسکتاپ/موبایل/فوتر → `header.php`, `footer.php` + تنظیمات داشبورد + Hreflang در `<head>` | `[PENDING]` |
| 4.2 | Custom Nav Walkers برای `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing` | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` | `[PENDING]` |

## Phase 5 — صفحه اصلی + کامپوننت‌های ماژولار (`temp/phase-05-homepage-and-sections`)
| تسک | شرح | وضعیت |
|---|---|---|
| 5.1 | برش ۱۵ سکشن → `template-parts/home/section-*.php` + `front-page.php` با حلقه SectionManager | `[PENDING]` |
| 5.2 | اتصال داینامیک سکشن‌ها (هیرو D5، دسته‌ها، محصولات، پروژه‌ها، مقالات، آمار/مشتریان) + کارت‌های استاندارد | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` | `[PENDING]` |

## Phase 6 — ووکامرس، محصولات تکی و آرشیو فروشگاه (`temp/phase-06-woocommerce-and-products`)
| تسک | شرح | وضعیت |
|---|---|---|
| 6.1 | `single-product.php` از استاتیک + همه متاها + `true_sale` (آنلاین/استعلامی) + گالری + ۱۸ ویژگی + FAQ + مرتبط‌ها | `[PENDING]` |
| 6.2 | آرشیوها (`archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php`) + FAQ دسته + صفحه‌بندی | `[PENDING]` |
| 6.3 | `cart`, `checkout`, `my-account` + `auth` (D1) با پوسته استاتیک + سازگاری درگاه/افزونه‌ها | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` (با shim ووکامرس — D7) | `[PENDING]` |

## Phase 7 — پروژه‌ها، وبلاگ، برگه‌ها و لندینگ‌ها (`temp/phase-07-projects-blog-and-pages`)
| تسک | شرح | وضعیت |
|---|---|---|
| 7.1 | `single-projects.php` + آرشیوها (`archive-projects.php`, `taxonomy-project_type.php`, `taxonomy-city.php`) + گالری/تاریخ/محصولات استفاده‌شده | `[PENDING]` |
| 7.2 | وبلاگ (`home.php`/`archive.php`/`category.php`), `single.php` (TOC + دیدگاه D6 + اشتراک + بازدید + مرتبط), `search.php`, `404.php` | `[PENDING]` |
| 7.3 | `page-templates/` (درباره/تماس/مجوزها/FAQ/گارانتی/استخدام/smartelement/آریا) + ویرایش کامل از داشبورد | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` | `[PENDING]` |

## Phase 8 — جستجو/فیلتر ایجکس، فرم‌ها و ممیزی نهایی (`temp/phase-08-ajax-filters-and-audit`)
| تسک | شرح | وضعیت |
|---|---|---|
| 8.1 | Live AJAX Search + Smart Filters (محصولات: متن/دسته/قیمت/`true_sale`/`pa_*` + مرتب‌سازی؛ پروژه‌ها: نوع/شهر + شمارنده) — <200ms، امن | `[PENDING]` |
| 8.2 | سیستم فرم‌ها (تماس/استعلام/استخدام/بازخورد) + ذخیره در ادمین + Honeypot/Nonce + ایمیل | `[PENDING]` |
| 8.3 | ممیزی سئو/پرفورمنس + `run-all-phases-e2e.sh` (SEOPress/Hreflang/JSON-LD/Escaping/ریسپانسیو + بازاجرای همه تست‌ها) | `[PENDING]` |
| تست جامع | `tests/run-phase-tests.sh` + `run-all-phases-e2e.sh` | `[PENDING]` |

---

## پروتکل هر تسک (الزامی)
`[READ MEMORY] → [THINK & PLAN] → [WRITE TEST] → [IMPLEMENT] → [RUN TEST & SELF-CORRECT] → [UPDATE MEMORY + Git Commit]`
- بعد از هر تسک/فاز: گزارش نتیجه تست + **توقف برای تایید کاربر**.
- ممنوعیت کد ناقص/Placeholder؛ حفظ ۱۰۰٪ DOM/CSS/data-attr.
