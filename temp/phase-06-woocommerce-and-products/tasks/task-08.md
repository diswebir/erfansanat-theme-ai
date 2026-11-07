# Task 6.8 — دانلود کاتالوگ محصول (`product-catalog-file`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.10` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-08.php` |

## ۱. هدف

دکمه دانلود کاتالوگ با URL رسانه و ویژگی‌های امنیتی (`rel="noopener"`, `download`) و نمایش حجم فایل.

## ۲. فایل‌های هدف

- `template-parts/product/catalog.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. خواندن URL با `esc_url`.
2. نمایش حجم فایل با `size_format` اگر فایل محلی باشد.
3. در نبود فایل، هیچ خروجی.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — با فایل: دکمه معتبر
- [ ] **P2** — بدون فایل: صفر خروجی
- [ ] **P3** — لینک با `esc_url` و `rel` امن

## ۵. تست‌های خودکار

- `حالت با/بدون فایل`
- `امنیت لینک`

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
