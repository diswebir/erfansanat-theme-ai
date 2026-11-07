# Task 6.18 — گیت فاز ۶: تست سرتاسری فروشگاه

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `S` — کوچک (~نیم روز) |
| وابستگی | `Task 6.2`، `Task 6.3`، `Task 6.4`، `Task 6.5`، `Task 6.6`، `Task 6.7`، `Task 6.8`، `Task 6.9`، `Task 6.10`، `Task 6.11`، `Task 6.12`، `Task 6.13`، `Task 6.14`، `Task 6.15`، `Task 6.16`، `Task 6.17` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-18.php` |

## ۱. هدف

تست محصول ساده/متغیر/استعلامی، آرشیو و دسته، چرخه سبد→پرداخت (mock gateway) و بررسی صفر خطای PHP/JS.

## ۲. فایل‌های هدف

- `temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh`
- `tools/harness/seed-products.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Seed سه محصول با انواع مختلف و ویژگی‌های `pa_*`.
2. اجرای چرخه خرید کامل با درگاه mock.
3. پاک‌سازی داده.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ۱۰۰٪ تست‌ها PASS
- [ ] **P2** — چرخه خرید کامل بدون خطا
- [ ] **P3** — صفر Warning

## ۵. تست‌های خودکار

- `اجرای فاز`
- `چرخه خرید`
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
