# Task 5.21 — گیت فاز ۵: تست رندر و کوئری صفحه اصلی

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 5.2`، `Task 5.3`، `Task 5.4`، `Task 5.5`، `Task 5.6`، `Task 5.7`، `Task 5.8`، `Task 5.9`، `Task 5.10`، `Task 5.11`، `Task 5.12`، `Task 5.13`، `Task 5.14`، `Task 5.15`، `Task 5.16`، `Task 5.17`، `Task 5.18`، `Task 5.19`، `Task 5.20` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-21.php` |

## ۱. هدف

اجرای کل تست‌ها + مقایسه سکشن‌به‌سکشن با استاتیک + اجرای `main.js` روی خروجی (jsdom) برای اطمینان از عدم خطای JS.

## ۲. فایل‌های هدف

- `temp/phase-05-homepage-and-sections/tests/run-phase-05-tests.sh`
- `tools/harness/jscheck.mjs`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. رندر `/` و استخراج هر سکشن برای diff.
2. اجرای `main.js` در jsdom و جمع‌آوری خطاها.
3. شمارش کوئری و ثبت در لاگ.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۱۵ سکشن با انطباق ≥ ۹۹٪
- [ ] **P2** — صفر خطای JS در jsdom
- [ ] **P3** — ≤ ۳۰ کوئری
- [ ] **P4** — ۱۰۰٪ تست‌های تسک PASS

## ۵. تست‌های خودکار

- `انطباق ۱۵ سکشن`
- `خطای JS`
- `کوئری`

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
