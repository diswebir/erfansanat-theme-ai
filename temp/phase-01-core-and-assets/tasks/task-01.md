# Task 1.1 — هدر قالب در `style.css` و متادیتای نسخه

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | — |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

ساخت `style.css` صرفاً به‌عنوان شناسنامه قالب (هدر استاندارد وردپرس) — **بدون هیچ استایل واقعی** چون تمام CSS از `assets/css/styles.css` می‌آید. تعریف ثابت `ES_THEME_VERSION` برای استفاده در نسخه‌گذاری دارایی‌ها و flush نسخه‌دار rewrite.

## ۲. فایل‌های هدف

- `style.css`
- `inc/Constants.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. هدر استاندارد: Theme Name `عرفان صنعت اصفهان (ErfanSanat)`, Theme URI, Author, Version `1.0.0`, Requires at least `6.5`, Requires PHP `8.0`, License `GPL-2.0-or-later`, Text Domain `erfansanat`, Domain Path `/languages`, Tags.
2. `inc/Constants.php`: `ES_THEME_VERSION` (خوانده‌شده از هدر فایل با `wp_get_theme()` یک‌بار و کش‌شده)، `ES_THEME_DIR`, `ES_THEME_URI`, `ES_THEME_SLUG`.
3. هیچ قاعده CSS در `style.css` نوشته نشود (فقط کامنت هدر + بلوک توضیح).
4. گارد `defined( 'ABSPATH' ) || exit;` در ابتدای هر فایل PHP.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `style.css` فقط شامل کامنت هدر و توضیح باشد (بدون `{` استایل واقعی بیرون از کامنت)
- [ ] **P2** — `wp_get_theme()->get('Version')` مقدار `1.0.0` بدهد
- [ ] **P3** — `wp_get_theme()->get('TextDomain')` برابر `erfansanat` باشد
- [ ] **P4** — `ES_THEME_VERSION` با نسخه هدر برابر باشد (تست همگامی)
- [ ] **P5** — ثابت‌ها فقط یک‌بار تعریف شوند (`defined()` guard)

## ۵. تست‌های خودکار

- `پارس هدر قالب`
- `همگامی نسخه`
- `نبود قاعده CSS`
- `گارد ABSPATH`

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
