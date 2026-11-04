# Task 7.1 — `single-projects.php`: هیرو + گالری + شناسنامه

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 3.1`، `Task 3.2`، `Task 3.3`، `Task 5.18` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

صفحه تک‌پروژه: breadcrumb، عنوان، شهر/نوع، تاریخ تکمیل (`finish-pro-time`)، گالری ۵ تصویره (`second-image`..`six-image`) و شناسنامه اجرایی.

## ۲. فایل‌های هدف

- `single-projects.php`
- `template-parts/project/hero.php`
- `template-parts/project/gallery.php`
- `template-parts/project/facts.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Breadcrumb: خانه › پروژه‌ها › نوع › عنوان.
2. گالری فقط با تصاویر موجود (۱ تا ۵) بدون خانه خالی.
3. نمایش `city`/`project_type` با `get_the_terms`.
4. تاریخ تکمیل با تبدیل شمسی صحیح (WP-Parsidate یا helper).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — پروژه با ۵ تصویر و پروژه بدون گالری هر دو رندر شوند
- [ ] **P2** — تاریخ نامعتبر باعث خطا نشود (fallback مخفی)
- [ ] **P3** — شهر/نوع با لینک آرشیو صحیح
- [ ] **P4** — h1 یکتا

## ۵. تست‌های خودکار

- `دو حالت گالری`
- `تاریخ`
- `لینک تاکسونومی`
- `h1`

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
