# Task 8.18 — گیت فاز ۸ + E2E نهایی (`run-all-phases-e2e.sh`)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 8.1`، `Task 8.2`، `Task 8.3`، `Task 8.4`، `Task 8.5`، `Task 8.6`، `Task 8.7`، `Task 8.8`، `Task 8.9`، `Task 8.10`، `Task 8.11`، `Task 8.12`، `Task 8.13`، `Task 8.14`، `Task 8.15`، `Task 8.16`، `Task 8.17` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-18.php` |

## ۱. هدف

اجرای مجدد همه تست‌های فاز ۱ تا ۸ در یک‌جا، ممیزی URL، و صدور «گزارش آمادگی استقرار» نهایی.

## ۲. فایل‌های هدف

- `temp/phase-08-ajax-filters-and-audit/tests/run-all-phases-e2e.sh`
- `temp/DEPLOYMENT_READINESS.md`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. اجرای همه ۸ فاز در یک پروسه.
2. تولید گزارش آمادگی (چک‌لیست فازها، عملکرد، امنیت، سئو).
3. بسته‌بندی قالب (`zip`) بدون فایل‌های harness.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۱۰۰٪ تست‌های ۸ فاز PASS
- [ ] **P2** — صفر Warning در `WP_DEBUG=true`
- [ ] **P3** — صفر ۴۰۴ ناخواسته در ۱۲۸ مسیر
- [ ] **P4** — بسته zip قالب فقط شامل فایل‌های لازم باشد

## ۵. تست‌های خودکار

- `همه فازها`
- `بدون Warning`
- `ممیزی URL`
- `بسته‌بندی`

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
