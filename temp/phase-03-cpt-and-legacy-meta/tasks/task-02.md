# Task 3.2 — ثبت تکسونومی `project_type`

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.1` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

ثبت Native با slug `project_type`، `with_front=false`، `hierarchical=true`، REST و برچسب‌های فارسی.

## ۲. فایل‌های هدف

- `inc/Content/Taxonomies.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `register_taxonomy` با آرگومان‌های بلوپرینت.
2. `show_admin_column => true` و `show_in_rest => true`.
3. برچسب‌های فارسی.
4. urldecode/اصلاح کاراکترهای فارسی در نام ترم پشتیبانی شود (WP-Parsidate).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `taxonomy_exists('project_type')` true
- [ ] **P2** — `get_term_link` = `/project_type/{slug}/`
- [ ] **P3** — rewrite با `hierarchical => false` در بخش rewrite باشد
- [ ] **P4** — آرشیو تاکسونومی بدون 404 رندر شود

## ۵. تست‌های خودکار

- `ثبت تکسونومی`
- `لینک ترم`
- `آرشیو بدون 404`

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
