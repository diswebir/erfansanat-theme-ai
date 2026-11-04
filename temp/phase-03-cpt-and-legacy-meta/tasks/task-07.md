# Task 3.7 — ستون‌های ادمین نوشته‌ها/محصولات/دسته‌ها

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.4` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-07.php` |

## ۱. هدف

ستون `same_key_hrefl` برای `post`/`page`/`product`/`projects`، ستون بازدید `number2-viewc`، ستون شناسه‌های قانونی محصول (`gs1_id`, `national_code`)، و ستون تصویر دسته `product_cat`.

## ۲. فایل‌های هدف

- `inc/Admin/ListTables.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ستون‌ها با `esc_html` و لینک مرتب‌سازی.
2. ستون `primary-image` دسته با `thumbnail` term meta.
3. ستون بازدید با fallback `number-viewc`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — همه ستون‌ها در لیست‌های مربوطه ظاهر شوند
- [x] **P2** — مقادیر خالی به‌درستی نمایش داده شوند
- [x] **P3** — مرتب‌سازی ستون بازدید کار کند

## ۵. تست‌های خودکار

- `ظهور ستون`
- `مقدار خالی`
- `مرتب‌سازی بازدید`

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
