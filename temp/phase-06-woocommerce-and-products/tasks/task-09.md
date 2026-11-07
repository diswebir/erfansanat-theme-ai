# Task 6.9 — پروژه‌های مرتبط محصول (`project-ids`) + کاربردها (`usage_countr`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.11`، `Task 5.18` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-09.php` |

## ۱. هدف

نمایش کارت‌های پروژه‌های مرتبط از `project-ids` و برچسب‌های کاربرد از ترم‌های `usage_countr` با لینک به `project_type`.

## ۲. فایل‌های هدف

- `template-parts/product/related-projects.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `WP_Query` روی `post__in` با حفظ ترتیب ذخیره‌شده.
2. کارت پروژه از `card-project.php`.
3. حذف IDهای ناموجود.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — پروژه‌ها با ترتیب ذخیره‌شده نمایش داده شوند
- [ ] **P2** — ID ناموجود خطا ندهد
- [ ] **P3** — بدون رابطه، سکشن حذف شود

## ۵. تست‌های خودکار

- `ترتیب`
- `ID ناموجود`
- `حذف خالی`

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
