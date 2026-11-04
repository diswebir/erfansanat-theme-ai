# Task 7.23 — گیت فاز ۷: رندر همه قالب‌ها + جستجوی ۴۰۴

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 7.1`، `Task 7.2`، `Task 7.3`، `Task 7.4`، `Task 7.5`، `Task 7.6`، `Task 7.7`، `Task 7.8`، `Task 7.9`، `Task 7.10`، `Task 7.11`، `Task 7.12`، `Task 7.13`، `Task 7.14`، `Task 7.15`، `Task 7.16`، `Task 7.17`، `Task 7.18`، `Task 7.19`، `Task 7.20`، `Task 7.21`، `Task 7.22` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-23.php` |

## ۱. هدف

اجرای کل تست‌ها، رندر هر ۲۲ قالب با داده seed، و بررسی HTTP 200 برای همه URLهای میرور (۱۲۸ مسیر).

## ۲. فایل‌های هدف

- `temp/phase-07-projects-blog-and-pages/tests/run-phase-07-tests.sh`
- `tools/harness/url-audit.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. مقایسه فهرست URLهای میرور با URLهای قالب.
2. گزارش هر مسیر با کد وضعیت.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۱۰۰٪ تست‌ها PASS
- [ ] **P2** — همه مسیرهای حیاتی ۲۰۰ (بدون ۴۰۴ ناخواسته)
- [ ] **P3** — صفر Warning

## ۵. تست‌های خودکار

- `اجرای فاز`
- `ممیزی URL`
- `بدون Warning`

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
