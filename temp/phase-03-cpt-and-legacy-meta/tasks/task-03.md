# Task 3.3 — ثبت تکسونومی `city`

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.1` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

ثبت Native `city` (محل پروژه) دقیقاً مطابق بلوپرینت با slug `city` و `with_front=false`.

## ۲. فایل‌های هدف

- `inc/Content/Taxonomies.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. همان رویه ۳.۲ با اسلاگ `city`.
2. نمایش آرشیو شهر با همان قالب `taxonomy-city.php` (فاز ۷).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — `taxonomy_exists('city')` true
- [x] **P2** — `get_term_link` = `/city/{slug}/`
- [x] **P3** — حذف/ویرایش ترم باعث خطا در آرشیو نشود

## ۵. تست‌های خودکار

- `ثبت`
- `لینک ترم`
- `ایمنی حذف ترم`

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
