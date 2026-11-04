# Task 1.3 — Kernel قالب و ثبت `add_theme_support`ها

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.2` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

کلاس `Theme` به‌عنوان Kernel: ثبت هوک‌ها، بارگذاری ماژول‌ها از یک رجیستری، و `after_setup_theme` با تمام theme supportهای لازم (بدون هیچ خروجی HTML).

## ۲. فایل‌های هدف

- `inc/Theme.php`
- `inc/Contracts/ModuleInterface.php`
- `inc/Setup/ThemeSupport.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. الگوی Singleton کنترل‌شده (`Theme::instance()`) + متد `boot()`.
2. `ModuleInterface` با `register(): void` — هر ماژول خودش هوک‌هایش را ثبت می‌کند (Kernel هوک‌های ماژول‌ها را نمی‌شناسد).
3. `after_setup_theme`: `title-tag`, `post-thumbnails`, `html5` (`search-form`,`comment-form`,`comment-list`,`gallery`,`caption`,`style`,`script`), `custom-logo`, `automatic-feed-links`, `responsive-embeds`.
4. `woocommerce` support + `wc-product-gallery-zoom`, `wc-product-gallery-lightbox`, `wc-product-gallery-slider` (فقط اگر WooCommerce فعال باشد).
5. `load_theme_textdomain('erfansanat', ES_THEME_DIR . '/languages')`.
6. `add_theme_support('align-wide')` **ثبت نشود** (تعارض با مارک‌آپ استاتیک).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — `after_setup_theme` بدون هیچ خروجی/دامپ اجرا شود
- [x] **P2** — `current_theme_supports('title-tag'|'post-thumbnails'|'html5'|'custom-logo')` همه true
- [x] **P3** — در نبود WooCommerce هیچ Fatal/Notice رخ ندهد و `woocommerce` support ثبت نشود
- [x] **P4** — `align-wide` ثبت نشده باشد
- [x] **P5** — ماژول نمونه با `register()` شناسایی و هوک‌هایش ثبت شود

## ۵. تست‌های خودکار

- `theme supports`
- `رفتار بدون ووکامرس`
- `عدم ثبت align-wide`
- `ثبت ماژول`

## ۶. چرخه اجرایی (۶ گام اجباری)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | `temp/PROJECT_MEMORY.md` + `temp/STATIC_TEMPLATE_MAP.md` + این فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ/داده مربوطه و طرح اتصال |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` (تبدیل `pending()` به `check()`) |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM/هوک |
| `[RUN TEST]` | اجرا تا ۱۰۰٪ PASS + ثبت در `QA_REFLECTION_LOG.md` |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md` + کامیت |

## ۷. ضدالگوها (Anti-patterns) — انجام نده

- طراحی مجدد مارک‌آپ یا افزودن کلاس Tailwind جدید (قانون R1)
- تغییر فایل `assets/js/main.js` یا `assets/css/styles.css` (md5 باید ثابت بماند)
- استفاده از `TODO`/`...`/حذف بخشی از HTML برای کوتاه‌سازی (قانون R6)
- خواندن/نوشتن متا با املای «تصحیح‌شده» (مثل `english_title` به‌جای `english_titl`)
