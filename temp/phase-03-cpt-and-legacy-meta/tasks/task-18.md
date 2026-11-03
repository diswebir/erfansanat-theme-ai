# Task 3.18 — گیت فاز ۳: تست یکپارچگی داده

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 3.1`، `Task 3.2`، `Task 3.3`، `Task 3.4`، `Task 3.5`، `Task 3.6`، `Task 3.7`، `Task 3.8`، `Task 3.9`، `Task 3.10`، `Task 3.11`، `Task 3.12`، `Task 3.13`، `Task 3.14`، `Task 3.15`، `Task 3.16`، `Task 3.17` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-18.php` |

## ۱. هدف

اجرای کل تست‌های فاز، seed داده نمونه (پروژه + محصول با هر دو فرمت متا)، و اثبات بی‌نقص ماندن داده پس از چرخه save/load.

## ۲. فایل‌های هدف

- `temp/phase-03-cpt-and-legacy-meta/tests/run-phase-03-tests.sh`
- `tools/harness/seed-fixtures.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Seed: ۲ پروژه، ۲ محصول (یکی simple purchasable، یکی استعلامی)، ۲ ترم دسته.
2. اجرای تست‌ها روی داده seed.
3. پاک‌سازی (tear-down) پس از تست.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۱۰۰٪ تست‌ها PASS
- [ ] **P2** — داده seed پس از round-trip بیت‌به‌بیت معنادار برابر باشد
- [ ] **P3** — هیچ ردیف یتیم در `postmeta` نماند

## ۵. تست‌های خودکار

- `اجرای کل فاز`
- `round-trip داده`
- `بدون ردیف یتیم`

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
