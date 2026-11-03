# Task 3.17 — شمارنده بازدید `number2-viewc` (با fallback `number-viewc`)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.8` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-17.php` |

## ۱. هدف

افزایش بازدید روی `post`, `projects`, `product` با کشف ربات‌ها، محدودیت یک‌بار در نشست، نوشتن فقط روی `number2-viewc` و خواندن با fallback به `number-viewc`، بدون تاثیر محسوس بر TTFB.

## ۲. فایل‌های هدف

- `inc/Meta/Views.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. هوک `wp` روی singular محدود.
2. تشخیص ربات با User-Agent + `DONOTCACHEPAGE` سازگار LiteSpeed.
3. افزایش با `update_post_meta` (نه query دستی) در `shutdown` (خارج از مسیر پاسخ).
4. `es_get_views($id)` با fallback.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — در یک نشست دو بار بازدید، تنها یک‌بار شمارش شود
- [ ] **P2** — ربات شناسایی‌شده شمارش نشود
- [ ] **P3** — کلید `number2-viewc` نوشته شود (نه `number-viewc`)
- [ ] **P4** — خواندن در نبود `number2-viewc` از `number-viewc` مقدار بدهد

## ۵. تست‌های خودکار

- `یک‌بار در نشست`
- `فیلتر ربات`
- `کلید نوشتن`
- `fallback خواندن`

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
