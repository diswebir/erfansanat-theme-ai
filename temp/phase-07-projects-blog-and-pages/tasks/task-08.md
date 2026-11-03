# Task 7.8 — آرشیو/فهرست خدمات (`services.html` → برگه «خدمات»)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.11`، `Task 5.18` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-08.php` |

## ۱. هدف

صفحه فهرست خدمات (۴ سکشن) با ردیف‌های خدمات و لینک به صفحات جزئی؛ داده از Repeater داشبورد (چون CPT خدمات وجود ندارد).

## ۲. فایل‌های هدف

- `page-templates/template-services.php`
- `template-parts/services/hero.php`
- `template-parts/services/list.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Schema سکشن برای این برگه در Registry.
2. ۵ خدمت به‌عنوان Repeater با slug/link/icon/image.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۵ خدمت با لینک صحیح نمایش داده شوند
- [ ] **P2** — متن‌ها از داشبورد قابل ویرایش باشند
- [ ] **P3** — بدون Repeater، سکشن حذف شود

## ۵. تست‌های خودکار

- `پنج خدمت`
- `ویرایش‌پذیری`
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
