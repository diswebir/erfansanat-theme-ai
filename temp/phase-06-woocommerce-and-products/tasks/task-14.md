# Task 6.14 — سبد خرید (`woocommerce/cart/*`) با UI استاتیک

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.1` |
| وضعیت | `[PASSED — 4 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-14.php` |

## ۱. هدف

Override قالب سبد با مارک‌آپ استاتیک، حفظ کامل هوک‌های ووکامرس و به‌روزرسانی تعداد/جمع بدون شکستن اسکریپت‌های ووکامرس.

## ۲. فایل‌های هدف

- `woocommerce/cart/cart.php`
- `woocommerce/cart/cart-empty.php`
- `woocommerce/cart/cart-totals.php`
- `woocommerce/cart/cross-sells.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. حفظ `woocommerce_before_cart_table` … `woocommerce_after_cart` (همه هوک‌ها).
2. حذف JS سبد استاتیک (`data-cart-page`) در این صفحه و اتکا به `wc-cart-fragments`.
3. کارت خالی با همان مارک‌آپ.
4. RTL و فرمت `IRT`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — افزودن/حذف/تغییر تعداد محصول کار کند
- [ ] **P2** — جمع کل با کوپن به‌روز شود
- [ ] **P3** — سبد خالی مارک‌آپ اختصاصی داشته باشد
- [ ] **P4** — هیچ خطای JS در کنسول

## ۵. تست‌های خودکار

- `عملیات سبد`
- `کوپن`
- `سبد خالی`
- `خطای JS`

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
