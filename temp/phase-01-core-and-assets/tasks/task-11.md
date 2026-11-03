# Task 1.11 — ثبت جایگاه‌های منو و `load_theme_textdomain`

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 1.3` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-11.php` |

## ۱. هدف

ثبت چهار جایگاه منو (`main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing`) با نام‌های فارسی، و بارگذاری دامنه ترجمه — بدون ساخت Walker (تسک ۴.۴).

## ۲. فایل‌های هدف

- `inc/Setup/Menus.php`
- `languages/erfansanat.pot (اختیاری)`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `register_nav_menus` در `after_setup_theme`.
2. نام‌های نمایشی فارسی مطابق DATA_BLUEPRINT.
3. `load_theme_textdomain` با مسیر `ES_THEME_DIR . '/languages'`.
4. بدون `wp_nav_menu` در این تسک (فراخوانی در فاز ۴).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — `get_registered_nav_menus()` دقیقاً همان ۴ کلید را برگرداند
- [x] **P2** — نام‌های نمایشی فارسی صحیح باشند
- [x] **P3** — بدون منو، هیچ خطایی رخ ندهد

## ۵. تست‌های خودکار

- `ثبت جایگاه‌ها`
- `نام‌های فارسی`
- `ایمنی در نبود منو`

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
