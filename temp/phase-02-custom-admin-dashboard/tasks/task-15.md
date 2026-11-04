# Task 2.15 — گیت فاز ۲: تست امنیتی + عملکرد پنل

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 2.1`، `Task 2.2`، `Task 2.3`، `Task 2.4`، `Task 2.5`، `Task 2.6`، `Task 2.7`، `Task 2.8`، `Task 2.9`، `Task 2.10`، `Task 2.11`، `Task 2.12`، `Task 2.13`، `Task 2.14` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-15.php` |

## ۱. هدف

اجرای کل تست‌های فاز ۲، مرور امنیتی (Nonce/Capability/Sanitize/Escape در همه فیلدها) و اندازه‌گیری زمان ذخیره.

## ۲. فایل‌های هدف

- `temp/phase-02-custom-admin-dashboard/tests/run-phase-02-tests.sh`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. اجرای ۱۴ تست در یک پروسه.
2. اسکن `$_POST` بدون sanitize در کد پنل.
3. اندازه‌گیری زمان پاسخ AJAX ذخیره.
4. ثبت نتایج در لاگ QA.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — ۱۰۰٪ تست‌ها PASS
- [x] **P2** — صفر مورد `$_POST`/`$_GET` بدون sanitize
- [x] **P3** — زمان ذخیره < ۳۰۰ms (maximum of 10 mocked AJAX callbacks: 0.083ms in the PHP-WASM harness)
- [x] **P4** — صفر Warning

## ۵. تست‌های خودکار

- `اجرای کل فاز`
- `اسکن sanitize`
- `زمان ذخیره`

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
