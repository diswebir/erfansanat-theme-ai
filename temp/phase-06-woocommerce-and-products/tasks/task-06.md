# Task 6.6 — Repeaterهای محصول در فرانت (مشخصات، ویژگی‌های کلیدی، کاربردها، FAQ)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 3.12` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-06.php` |

## ۱. هدف

رندر `product-feature`, `product-feature2_copy` (عنوان/مقدار), `usage_product`, `faq` با همان مارک‌آپ استاتیک و آکاردئون `data-faq-*`.

## ۲. فایل‌های هدف

- `template-parts/product/features.php`
- `template-parts/product/usage.php`
- `template-parts/product/faq.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. خواندن با `RepeaterCodec` و حذف سطرهای خالی.
2. ویژگی‌های کلیدی دو ستونی (عنوان → `add-new-feature`, مقدار → `vaule_of_variable`).
3. FAQ با `data-faq-toggle/icon/body/list` برای کارکرد JS.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — هر چهار Repeater رندر شوند
- [ ] **P2** — آکاردئون FAQ با JS موجود باز/بسته شود
- [ ] **P3** — Repeater خالی → سکشن حذف
- [ ] **P4** — HTML هر سطر با `wp_kses_post` مجاز باشد

## ۵. تست‌های خودکار

- `رندر چهار Repeater`
- `آکاردئون`
- `حذف خالی`
- `kses`

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
