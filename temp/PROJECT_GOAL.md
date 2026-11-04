# PROJECT_GOAL — اهداف کلان، قوانین خط قرمز و معیارهای پذیرش

> این فایل **ثابت و مرجع** است. تغییر آن فقط با تأیید صریح کارفرما مجاز است.

## ۱. مأموریت
تبدیل **مو‌به‌مو** قالب استاتیک آماده (`reference/static-mirror` — ۱۲۸ فایل HTML، یک `styles.css`، یک `main.js`) به یک **قالب وردپرس اختصاصی، فوق‌سریع و بدون صفحه‌ساز** برای سایت زنده `https://erfansanat.com` (شرکت دانش‌بنیان عرفان صنعت اصفهان — نورپردازی شهری و المان‌های نوری).

## ۲. قوانین خط قرمز (Red Lines)
| # | قانون | معیار سنجش |
|---|---|---|
| R1 | **طراحی مجدد فرانت‌اند ممنوع** | ۱۰۰٪ کلاس‌های Tailwind، ساختار DOM، `id`ها و `data-*`های فایل استاتیک باید در خروجی PHP حفظ شوند. هیچ کلاس یا مارک‌آپ جدیدی برای «زیباسازی» اضافه نمی‌شود. |
| R2 | **حذف کامل Elementor / JetEngine / JetSearch / JetSmartFilters / JetTabs** | هیچ فراخوانی `elementor_*`، `jet_*`، `\Jet_Engine`، شورت‌کد `[jet_*]` یا متای اختصاصی افزونه‌ها در کد قالب نباشد؛ قالب باید مستقل کار کند. |
| R3 | **حفظ ۱۰۰٪ دیتای دیتابیس زنده** | تمام کلیدهای متا با املای دقیق تاریخی (`foure-image`, `galerry_produ`, `usage_countr`, `english_titl`, `vaule_of_variable`, `mortabet_mahsole_2`, `number2-viewc`, `before-sale-c`) خوانده/نوشته شوند. |
| R4 | **حفظ ۱۰۰٪ URL و سئو** | اسلاگ‌ها: `projects`, `project_type`, `city`, `product_cat` (نه `product-category`), `product-tag` با `with_front` دقیق طبق `DATA_BLUEPRINT.md`. هیچ ریدایرکت ۳۰۱ ناخواسته‌ای ایجاد نشود. |
| R5 | **داشبورد ادمین اختصاصی** | تمام متون/تصاویر/ترتیب/فعال‌بودن سکشن‌ها، هدر، فوتر، مگامنو و فرم‌ها از پیشخوان خود قالب (بدون هیچ افزونه‌ای) قابل ویرایش باشد. |
| R6 | **صفر Placeholder** | `TODO`، `rest of html`، `...` و حذف مارک‌آپ برای کوتاه‌سازی = خطای بحرانی. |
| R7 | **بدون وابستگی npm/Composer در خروجی** | قالب نهایی فقط PHP + CSS + JS باشد؛ هیچ build step لازم نباشد. |
| R8 | **RTL و فارسی** | `dir="rtl"`, `lang="fa"`, فونت Vazirmatn محلی، اعداد فارسی در خروجی UI (طبق `toFa` در `main.js`). |

## ۳. معیارهای عملکرد (Performance Budget)
| شاخص | هدف |
|---|---|
| تعداد درخواست CSS/JS در فرانت | ≤ ۲ (یک CSS + یک JS افزودنی) + صفر فایل بلاک وردپرس |
| حجم CSS گشاد‌نشده | ≤ ۲۲۰KB (بدون تغییر در `styles.css` استاتیک) |
| LCP موبایل (4G مصنوعی) | < ۲.۵ ثانیه |
| CLS | < ۰.۱ |
| TTFB | < ۴۰۰ms |
| کوئری‌های صفحه اصلی | ≤ ۳۰ کوئری، بدون `WP_Query` تکراری، استفاده از `no_found_rows`، transient cache |
| بارگذاری دارایی صفحه | فقط CSS/JS همان صفحه (Enqueue شرطی) |

## ۴. نیازمندی‌های کارفرما (از `agent.md`)
- ۸ فاز، ۲۴ تسک، با تست خودکار برای هر تسک (`temp/phase-XX/tests/test-task-YY.php`)
- ۶ فایل حافظه + پوشه `tasks/` و `tests/` برای هر فاز
- توقف در دروازه‌های تأیید انسانی (Gate 0 پس از اسکن، Gate 1 پس از ساخت نقشه راه)
- PHP 8.4+، WordPress 7.1+، WooCommerce 10.9+ (محیط هدف) — هارنس تست محلی روی PHP 8.3 (wasm) + WordPress 6.7 اجرا می‌شود
- افزونه‌های باقی‌مانده که قالب باید با آن‌ها سازگار باشد: WooCommerce, Variation Swatches, WPC Product Quantity, SEOPress (+PRO و متاهای قدیمی `rank_math_*`), LiteSpeed Cache, WP-Parsidate, voorodak, fl-panel, hreflangpch
