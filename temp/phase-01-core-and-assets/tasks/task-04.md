# Task 1.4 — `functions.php` سبک + `index.php` + `screenshot.png`

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 1.3` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-04.php` |

## ۱. هدف

`functions.php` باید زیر ۴۰ خط و فقط شامل گارد، require بوت‌استرپ و فراخوانی Kernel باشد. `index.php` حداقلی و امن (fallback سلسله‌مراتب) و یک screenshot مطابق هویت بصری.

## ۲. فایل‌های هدف

- `functions.php`
- `index.php`
- `screenshot.png`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `functions.php`: گارد ABSPATH → تعریف `ES_THEME_DIR`/`ES_THEME_URI` در صورت نبود → `require inc/bootstrap.php` → `Theme::instance()->boot()`.
2. `index.php`: `get_header()`, پیام «محتوایی یافت نشد» با همان کلاس‌های استاتیک در صورت وجود، `get_footer()`.
3. `screenshot.png` ۱۲۰۰×۹۰۰ از هیرو استاتیک (بدون تغییر طراحی).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `functions.php` ≤ ۴۰ خط و بدون منطق تجاری
- [ ] **P2** — `index.php` بدون خطا رندر شود و `get_header`/`get_footer` را صدا بزند
- [ ] **P3** — وجود `screenshot.png` با ابعاد ۱۲۰۰×۹۰۰
- [ ] **P4** — هیچ `echo`/`var_dump` در `functions.php` نباشد

## ۵. تست‌های خودکار

- `شمارش خطوط`
- `فراخوانی هدر/فوتر`
- `ابعاد screenshot`

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
