# MASTER_PLAN — نقشه راه کامل پروژه (۸ فاز / ۲۴ تسک)

> وضعیت‌ها: `[PENDING]` · `[IN_PROGRESS]` · `[TESTING]` · `[PASSED]`
> قاعده: هیچ فازی بدون دستیابی ۱۰۰٪ شروط پاس تسک‌هایش وارد فاز بعد نمی‌شود.

## تصمیم‌های دروازه Gate 0 (تأیید کاربر — 2026-11-03)
| # | سؤال | تصمیم |
|---|---|---|
| G1 | واریانت‌های هیروی صفحه اصلی | **فقط واریانت فعال فعلی (`home1`) رندر می‌شود**؛ مارک‌آپ `home2`/`home3` به‌صورت فایل آماده در قالب نگه داشته می‌شود و از داشبورد قابل فعال‌سازی است (بدون حذف). |
| G2 | لندینگ‌های `arya` و `smartelement` | **فعلاً مسکوت**؛ در فاز ۷ با Section Builder عمومی و پس از دریافت HTML/اسکرین‌شات لایو ساخته می‌شوند. |
| G3 | سبد خرید / پرداخت / حساب | **Override قالب‌های ووکامرس با همان UI استاتیک** + حفظ کامل هوک‌های استاندارد (سازگاری درگاه و افزونه‌ها). |
| G4 | راهبرد محتوا | **کاملاً داینامیک از دیتابیس**؛ صفر محتوای هاردکد. میرور فقط «نقشه طراحی» است. |
| G5 | مدیریت منوها | **همه‌چیز در Appearance → Menus**: ساختار، آیتم‌ها و فیلدهای مگامنو (نوع پنل، ستون، آیکون، برچسب، تب) با هوک `wp_nav_menu_item_custom_fields`. **فقط تنظیمات نمایشی** (روشن/خاموش مگامنو، hover/click، چسبندگی) در داشبورد اختصاصی. |
| G6 | تحویل این دور | Stage 1 (همین سند + ۲۴ تسک + اسکلت تست‌ها)، سپس توقف در Gate 1. |

## پیش‌نیازهای سراسری (Pre-flight)
- [ ] دریافت و نصب WooCommerce برای هارنس تست (در جریان — دانلود از `downloads.wordpress.org` مسدود است؛ مسیر جایگزین در حال بررسی)
- [ ] ساخت `tools/harness/render.php` (رندر کامل قالب بدون وب‌سرور) در Task 1.1
- [ ] تعریف ثابت `ES_THEME_VERSION` برای flush rewrite نسخه‌دار

---

## Phase 1 — هسته قالب و Assets · `temp/phase-01-core-and-assets`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 1.1 | اسکلت پایه قالب + معماری OOP (بدون Composer) | `[PENDING]` | `tests/test-task-01.php` |
| 1.2 | DeBloat (بلاک‌ها، ایموجی، oEmbed، head) | `[PENDING]` | `tests/test-task-02.php` |
| 1.3 | انتقال Assets + `AssetManager` شرطی | `[PENDING]` | `tests/test-task-03.php` |
| — | تست جامع فاز | — | `tests/run-phase-01-tests.sh` |

## Phase 2 — داشبورد اختصاصی و Section Builder · `temp/phase-02-custom-admin-dashboard`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 2.1 | منوی ادمین + `es_option()` + ذخیره AJAX امن | `[PENDING]` | `tests/test-task-01.php` |
| 2.2 | تب‌های سراسری (هدر/فوتر/تماس/برند/اسکریپت/آرشیو) | `[PENDING]` | `tests/test-task-02.php` |
| 2.3 | موتور سکشن‌ها (ترتیب، فعال/غیرفعال، Repeater) | `[PENDING]` | `tests/test-task-03.php` |

## Phase 3 — CPTها، تکسونومی‌ها و متاهای JetEngine · `temp/phase-03-cpt-and-legacy-meta`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 3.1 | `projects` + `project_type` + `city` + rewriteهای سئو | `[PENDING]` | `tests/test-task-01.php` |
| 3.2 | متاباکس محصولات + `RepeaterCodec` (serialize/JSON) | `[PENDING]` | `tests/test-task-02.php` |
| 3.3 | متاباکس پروژه‌ها، FAQ تکسونومی، Hreflang، `number2-viewc` | `[PENDING]` | `tests/test-task-03.php` |

## Phase 4 — هدر، مگامنو، منوی موبایل، فوتر · `temp/phase-04-header-footer-navigation`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 4.1 | `header.php` / `footer.php` از میرور + Hreflang | `[PENDING]` | `tests/test-task-01.php` |
| 4.2 | Walker مگامنو + فیلدها در Appearance → Menus | `[PENDING]` | `tests/test-task-02.php` |
| 4.3 | جستجوی گلوبال هدر + شمارنده سبد | `[PENDING]` | `tests/test-task-03.php` |

## Phase 5 — صفحه اصلی و کارت‌ها · `temp/phase-05-homepage-and-sections`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 5.1 | برش ۱۵ سکشن + انتخاب واریانت هیرو | `[PENDING]` | `tests/test-task-01.php` |
| 5.2 | اتصال به `WP_Query` + کارت‌های استاندارد | `[PENDING]` | `tests/test-task-02.php` |
| 5.3 | تعاملات (`main.js` بدون تغییر) | `[PENDING]` | `tests/test-task-03.php` |

## Phase 6 — ووکامرس · `temp/phase-06-woocommerce-and-products`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 6.1 | `single-product` + همه متاها + دو حالت فروش | `[PENDING]` | `tests/test-task-01.php` |
| 6.2 | آرشیو/دسته/برچسب محصول + FAQ دسته | `[PENDING]` | `tests/test-task-02.php` |
| 6.3 | سبد/پرداخت/حساب با UI استاتیک | `[PENDING]` | `tests/test-task-03.php` |

## Phase 7 — پروژه‌ها، وبلاگ، برگه‌ها · `temp/phase-07-projects-blog-and-pages`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 7.1 | تک‌پروژه + آرشیو نوع/شهر | `[PENDING]` | `tests/test-task-01.php` |
| 7.2 | وبلاگ، تک‌نوشته، جستجو، ۴۰۴ | `[PENDING]` | `tests/test-task-02.php` |
| 7.3 | قالب‌های برگه اختصاصی | `[PENDING]` | `tests/test-task-03.php` |

## Phase 8 — ایجکس، فرم‌ها، ممیزی نهایی · `temp/phase-08-ajax-filters-and-audit`
| تسک | عنوان | وضعیت | تست |
|---|---|---|---|
| 8.1 | جستجوی زنده + فیلترهای هوشمند (جایگزین Jet*) | `[PENDING]` | `tests/test-task-01.php` |
| 8.2 | فرم‌ساز اختصاصی + ذخیره/ایمیل | `[PENDING]` | `tests/test-task-02.php` |
| 8.3 | ممیزی سئو/امنیت/عملکرد + E2E | `[PENDING]` | `tests/test-task-03.php` |

---

## Gateها
| دروازه | شرط عبور | وضعیت |
|---|---|---|
| Gate 0 | تأیید اسکن و پاسخ سؤالات ابهام | ✅ PASSED (2026-11-03) |
| Gate 1 | تأیید این نقشه راه و فایل‌های تسک | ⏳ WAITING |
| Gate 2 | پایان هر فاز: گزارش تست + تأیید | — |
