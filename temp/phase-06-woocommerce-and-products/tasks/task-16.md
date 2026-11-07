# Task 6.16 — حساب کاربری (`woocommerce/myaccount/*`) + ورود پیامکی

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 6.1` |
| وضعیت | `[PASSED — 3 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-16.php` |

## ۱. هدف

Override صفحه حساب کاربری، ورود/ثبت‌نام، سفارش‌ها، آدرس‌ها و خروج با UI استاتیک و سازگاری با افزونه `voorodak` (ورود پیامکی).

## ۲. فایل‌های هدف

- `woocommerce/myaccount/my-account.php`
- `woocommerce/myaccount/dashboard.php`
- `woocommerce/myaccount/orders.php`
- `woocommerce/global/form-login.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. تب‌های حساب با `wc_get_account_menu_items()`.
2. حفظ فیلدهای `voorodak` (بدون حذف فرم‌های افزونه).
3. بدون ورود، فرم ورود استایل‌شده.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ورود/خروج کار کند
- [ ] **P2** — صفحه سفارش‌ها رندر شود
- [ ] **P3** — فرم پیامکی افزونه حذف/شکسته نشود

## ۵. تست‌های خودکار

- `ورود/خروج`
- `سفارش‌ها`
- `سازگاری voorodak`

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
