# Task 6.15 — تسویه حساب (`woocommerce/checkout/*`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.1` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-15.php` |

## ۱. هدف

Override فرم پرداخت با UI استاتیک، حفظ کامل فیلدها و هوک‌ها، سازگاری با درگاه بانکی و `checkout.js` ووکامرس.

## ۲. فایل‌های هدف

- `woocommerce/checkout/form-checkout.php`
- `woocommerce/checkout/review-order.php`
- `woocommerce/checkout/thankyou.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. حفظ `woocommerce_checkout_before_customer_details` … `_after_order_review`.
2. حذف کامل فرم استاتیک (`data-checkout-form`) و اتکا به فرم واقعی.
3. صفحه تشکر با مارک‌آپ استاتیک.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ثبت سفارش تستی کامل شود
- [ ] **P2** — درگاه (mock) خطا ندهد
- [ ] **P3** — صفحه تشکر با جزئیات سفارش رندر شود

## ۵. تست‌های خودکار

- `ثبت سفارش`
- `سازگاری درگاه`
- `صفحه تشکر`

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
