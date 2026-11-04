# Task 1.7 — انتقال بایت‌به‌بایت Assets استاتیک (CSS/JS/فونت/لوگو)

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 1.6` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-07.php` |

## ۱. هدف

کپی **بدون هیچ تغییر** فایل‌های `styles.css`, `main.js`, سه فونت Vazirmatn و لوگوی SVG به ساختار `assets/`. صحت انتقال با md5 تایید می‌شود؛ هیچ اصلاح/minify جدید مجاز نیست.

## ۲. فایل‌های هدف

- `assets/css/styles.css`
- `assets/js/main.js`
- `assets/fonts/vazirmatn-arabic.woff2`
- `assets/fonts/vazirmatn-latin.woff2`
- `assets/fonts/vazirmatn-latin-ext.woff2`
- `assets/img/ese-logo.svg`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کپی و ثبت md5 مبدأ/مقصد در `temp/_scan/assets.md5` (این فایل در گیت نگه داشته می‌شود).
2. حفظ دقیق مسیر نسبی فونت‌ها: `assets/css/../fonts/*.woff2` باید معتبر بماند.
3. `main.js` با `defer` (بدون تغییر محتوا) — Enqueue در تسک ۱.۹.
4. بدون تغییر نام فایل‌ها (مسیر `url()` داخل CSS به نام فونت وابسته است).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — md5 همه ۶ فایل مبدأ = مقصد
- [x] **P2** — مسیر `../fonts/vazirmatn-arabic.woff2` از `assets/css/` قابل حل باشد
- [x] **P3** — CSS و JS بدون minify مجدد و بدون تغییر محتوا منتقل شده باشند
- [x] **P4** — هیچ فایل فونتی در مسیر اشتباه نباشد

## ۵. تست‌های خودکار

- `مقایسه md5`
- `حل مسیر فونت`
- `عدم تغییر محتوا`

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
