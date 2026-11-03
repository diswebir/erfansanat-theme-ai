# Task 7.6 — آرشیو نوع پروژه (`taxonomy-project_type.php`)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.2` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-06.php` |

## ۱. هدف

آرشیو نوع پروژه با hero اختصاصی (نام/توضیح/تصویر ترم) و همان گرید؛ شامل شمارنده پروژه‌های هر نوع.

## ۲. فایل‌های هدف

- `taxonomy-project_type.php`
- `template-parts/archive/term-hero.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. تیتر و توضیح از `term_description()` و نام ترم.
2. تصویر هیرو از term meta (اختیاری، رندر شرطی).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — هر ۸ نوع پروژه رندر شوند
- [ ] **P2** — ترم خالی، حالت «پروژه‌ای یافت نشد» داشته باشد
- [ ] **P3** — `term_description` با `wp_kses_post`

## ۵. تست‌های خودکار

- `رندر انواع`
- `حالت خالی`
- `kses`

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
