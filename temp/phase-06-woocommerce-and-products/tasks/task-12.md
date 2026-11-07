# Task 6.12 — آرشیو فروشگاه و دسته‌ها (`archive-product.php`, `taxonomy-product_cat.php`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.1`، `Task 5.18` |
| وضعیت | `[PASSED — 4 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-12.php` |

## ۱. هدف

آرشیو با همان گرید، دراوور فیلتر (`data-archive-drawer`)، توضیحات دسته، FAQ دسته (term meta)، شمارش نتایج و صفحه‌بندی سئو-دوست.

## ۲. فایل‌های هدف

- `woocommerce/archive-product.php`
- `woocommerce/taxonomy-product_cat.php`
- `template-parts/archive/hero.php`
- `template-parts/archive/grid.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کارت‌ها با `card-product` و `data-product-*` کامل.
2. FAQ دسته از term meta `faq` (تسک ۳.۱۵).
3. صفحه‌بندی با `woocommerce_pagination` و `rel` صحیح.
4. فیلتر سمت کلاینت موجود (`initProductsArchive`) بدون تغییر JS.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `/shop/` و `/product_cat/{slug}/` رندر صحیح
- [ ] **P2** — صفحه ۲ بدون خطا و با `rel="next"`
- [ ] **P3** — دسته با FAQ، آکاردئون را نشان دهد
- [ ] **P4** — دسته بدون محصول، حالت خالی داشته باشد

## ۵. تست‌های خودکار

- `رندر دو آرشیو`
- `صفحه‌بندی`
- `FAQ دسته`
- `حالت خالی`

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
