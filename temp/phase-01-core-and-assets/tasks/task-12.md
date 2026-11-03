# Task 1.12 — گیت فاز ۱: اجرای کامل تست‌ها + بودجه عملکرد

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 1.6`، `Task 1.9`، `Task 1.10` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-12.php` |

## ۱. هدف

اجرای `run-phase-01-tests.sh` و سنجش بودجه عملکرد (تعداد درخواست CSS/JS، حجم، زمان رندر) و ثبت نتایج در `QA_REFLECTION_LOG.md` — دروازه ورود به فاز ۲.

## ۲. فایل‌های هدف

- `temp/phase-01-core-and-assets/tests/run-phase-01-tests.sh`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. اجرای همه ۱۱ تست تسک در یک پروسه PHP-wasm.
2. شمارش `stylesheets` + `scripts` در سورس `/` و `/contact/`.
3. اندازه‌گیری زمان رندر با `microtime(true)` در `render.php`.
4. ثبت جدول نتایج در لاگ QA.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۱۰۰٪ تست‌های تسک ۱.۱ تا ۱.۱۱ PASS
- [ ] **P2** — ≤ ۲ درخواست CSS/JS اختصاصی قالب در هر صفحه
- [ ] **P3** — زمان رندر محلی < ۵۰۰ms (بدون کش)
- [ ] **P4** — صفر Warning در `WP_DEBUG=true`

## ۵. تست‌های خودکار

- `اجرای کل فاز`
- `شمارش دارایی‌ها`
- `زمان رندر`
- `لاگ بدون Warning`

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
