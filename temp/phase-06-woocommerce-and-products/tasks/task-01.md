# Task 6.1 — راه‌اندازی پوشه `woocommerce/` و `declare_compatibility`

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.9` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

فعال‌سازی پشتیبانی رسمی WooCommerce (HPOS و blocks) و ساخت ساختار پوشه قالب‌های ووکامرس + نسخه‌گذاری قالب‌ها.

## ۲. فایل‌های هدف

- `inc/Woo/WooSupport.php`
- `woocommerce/.keep`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true)` در `before_woocommerce_init`.
2. `declare_compatibility('cart_checkout_blocks', ...)` مطابق تصمیم G3 (قالب‌های کلاسیک).
3. حذف `wc-blocks` از فرانت در DeBloat (تسک ۱.۶) و اطمینان از سالم بودن چک‌اوت کلاسیک.
4. تعریف `ES_WC_TEMPLATE_VERSION` برای همگامی با نسخه قالب‌های ووکامرس.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — در WooCommerce > Status هیچ «outdated template» دیده نشود
- [ ] **P2** — بدون WooCommerce، قالب بدون Fatal کار کند
- [ ] **P3** — اعلام سازگاری HPOS ثبت شده باشد

## ۵. تست‌های خودکار

- `اعلام سازگاری`
- `ایمنی بدون ووکامرس`
- `بدون قالب قدیمی`

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
