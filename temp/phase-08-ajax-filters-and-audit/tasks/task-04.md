# Task 8.4 — فیلتر ایجکس پروژه‌ها (نوع/شهر + جستجو + مرتب‌سازی)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 5.5`، `Task 5.6`، `Task 7.6` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-04.php` |

## ۱. هدف

اندپوینت `es_filter_projects` و اتصال به آرشیو پروژه‌ها با شمارنده هر ترم و fallback فرم GET.

## ۲. فایل‌های هدف

- `inc/Ajax/ProjectFilterEndpoint.php`
- `assets/js/project-filters.js`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. whitelist: `type[]`, `city[]`, `s`, `orderby`, `paged`.
2. شمارنده ترم‌ها از `get_terms` با `count` (کش transient).
3. فرم GET هم‌ارز.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ترکیب نوع+شهر نتیجه درست بدهد
- [ ] **P2** — شمارنده با نتیجه هم‌خوان باشد
- [ ] **P3** — بدون JS قابل استفاده باشد

## ۵. تست‌های خودکار

- `ترکیب`
- `شمارنده`
- `بدون JS`

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
