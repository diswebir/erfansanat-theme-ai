# Task 3.4 — تصحیح Rewriteهای ووکامرس (`product_cat`, `product-tag`, سینگل)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 1.3` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-04.php` |

## ۱. هدف

اعمال دقیق قواعد سئو: `product_cat` با `with_front=true` و `hierarchical=true`، `product_tag` با slug `product-tag` و `with_front=false`، سینگل محصول `/product/{slug}/`. بدون هیچ تغییر ۳۰۱ ناخواسته.

## ۲. فایل‌های هدف

- `inc/Content/Rewrites.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. فیلتر `woocommerce_product_category_rewrite` یا `register_taxonomy_args` برای `product_cat`.
2. `product_tag` → slug `product-tag`, with_front=false.
3. ثبت `product_brand` (hierarchical) اگر وجود نداشت.
4. Flush rewrite **فقط** با تغییر `ES_REWRITE_VERSION` (نه در هر درخواست).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `get_term_link` دسته محصول = `/product_cat/{slug}/`
- [ ] **P2** — `get_term_link` برچسب = `/product-tag/{slug}/`
- [ ] **P3** — `get_permalink` محصول = `/product/{slug}/`
- [ ] **P4** — هیچ فراخوانی `flush_rewrite_rules` در مسیر عادی درخواست نباشد

## ۵. تست‌های خودکار

- `لینک‌های سه‌گانه`
- `نبود flush در مسیر عادی`

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
