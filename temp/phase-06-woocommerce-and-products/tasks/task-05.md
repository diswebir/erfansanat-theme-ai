# Task 6.5 — جدول مشخصات فنی (۱۸ ویژگی `pa_*`)

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.2` |
| وضعیت | `[PASSED — 4 PHP-WASM source assertions; live WooCommerce/browser validation unverified]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

جدول کامل مشخصات با ترتیب ثابت ۱۸ ویژگی، نمایش `pa_color` به‌صورت سواچ رنگ، پنهان‌سازی ویژگی‌های خالی، و پشتیبانی از ویژگی‌های سفارشی اضافه.

## ۲. فایل‌های هدف

- `template-parts/product/specs.php`
- `inc/Woo/AttributeData.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ترتیب دقیق: after-sales-support, beam-angle, body-material, brand, bulb-type, cable-material, certifications, color, diameter, diffuser-color, input-voltage, intensity, ip-rating, led-chip-type, lifespan, size, warranty, wattage.
2. خواندن از `$product->get_attributes()` (نه متای خام).
3. نمایش `pa_color` با `term meta` رنگ (در صورت وجود) و fallback متنی.
4. اگر هیچ ویژگی پر نبود، کل سکشن حذف شود.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — هر ۱۸ ویژگی با داده ساختگی در جدول ظاهر شوند
- [ ] **P2** — ویژگی خالی نمایش داده نشود
- [ ] **P3** — برچسب فارسی هر ویژگی از taxonomy label بیاید (نه hardcode)
- [ ] **P4** — بدون ویژگی، سکشن حذف شود

## ۵. تست‌های خودکار

- `۱۸ ویژگی`
- `پنهان‌سازی خالی`
- `برچسب از تکسونومی`
- `حذف سکشن`

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
