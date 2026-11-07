# Task 6.2 — هیرو محصول (`template-parts/product/hero.php`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.1`، `Task 5.18` |
| وضعیت | `[PASSED — 4 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

برش سکشن اول صفحه محصول: مسیر راهنما (breadcrumb)، عنوان، زیرعنوان (`zireonvan`)، عنوان انگلیسی (`english_titl`)، نشان وضعیت فروش، و تصویر/گالری کوچک.

## ۲. فایل‌های هدف

- `woocommerce/single-product.php`
- `template-parts/product/hero.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `single-product.php` = `get_header()` + `wc_get_template` ساختار استاندارد + قطعات اختصاصی.
2. Breadcrumb مطابق استاتیک (صفحه اصلی › محصولات › دسته › محصول).
3. نمایش `zireonvan` فقط اگر پر باشد؛ `english_titl` با `dir="ltr"`.
4. حذف سکشن‌های خالی (بدون رندر باکس خالی).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — دو محصول (با/بدون زیرعنوان) بدون خطا رندر شوند
- [ ] **P2** — Breadcrumb با `Rank Math`/SEOPress تداخل نداشته باشد
- [ ] **P3** — `h1` تنها یک‌بار در صفحه
- [ ] **P4** — همه متن‌ها escape شده باشند

## ۵. تست‌های خودکار

- `دو حالت`
- `یکتایی h1`
- `escaping`
- `breadcrumb`

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
