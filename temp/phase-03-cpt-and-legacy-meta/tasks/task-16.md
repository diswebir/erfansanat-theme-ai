# Task 3.16 — Hreflang (`hrefmeta_fa/en/ar`, `same_key_hrefl`)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.8` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-16.php` |

## ۱. هدف

متاباکس چندزبانه روی `post`, `page`, `product`, `projects` و همه تکسونومی‌های اصلی با اعتبارسنجی URL و ستون `same_key_hrefl` قابل مرتب‌سازی.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/Hreflang.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. سه فیلد URL با `esc_url_raw` + امکان خالی‌بودن.
2. `same_key_hrefl` متن کوتاه + ستون مرتب‌سازی.
3. هوک خروجی `<link rel="alternate" hreflang>` (مصرف در فاز ۴).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — URL نامعتبر رد شود، رشته خالی مجاز باشد
- [x] **P2** — ستون `same_key_hrefl` مرتب‌سازی شود
- [x] **P3** — مقادیر روی ترم‌ها هم ذخیره شوند

## ۵. تست‌های خودکار

- `اعتبار URL`
- `مرتب‌سازی ستون`
- `ترم‌متا`

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
