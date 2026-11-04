# Task 6.4 — جعبه خرید: قیمت، وضعیت `true_sale`، افزودن به سبد

| فیلد | مقدار |
|---|---|
| فاز | 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.2` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-04.php` |

## ۱. هدف

جعبه خرید با دو حالت: `true_sale="true"` → فرم استاندارد ووکامرس (ساده/متغیر با Swatches)؛ `true_sale="false"` → دکمه «استعلام قیمت» + لینک تماس/واتساپ. به‌همراه اطلاعیه `before-sale-c`.

## ۲. فایل‌های هدف

- `template-parts/product/purchase-box.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `woocommerce_template_single_add_to_cart()` برای حالت قابل خرید (بدون بازنویسی فرم).
2. حالت استعلام: دکمه/پاپ‌آپ فرم استعلام (تسک ۸.۴).
3. نمایش `before-sale-c` با `wp_kses_post` و کلاس استاتیک.
4. پشتیبانی از محصول متغیر + افزونه Variation Swatches و WPC Quantity.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — محصول simple با `true_sale=true` فرم افزودن به سبد معتبر داشته باشد
- [ ] **P2** — محصول `true_sale=false` هیچ فرم سبد رندر نکند و دکمه استعلام داشته باشد
- [ ] **P3** — محصول متغیر با Swatches بدون خطا کار کند
- [ ] **P4** — `before-sale-c` فقط اگر پر باشد رندر شود

## ۵. تست‌های خودکار

- `دو حالت`
- `متغیر + Swatches`
- `اطلاعیه شرطی`
- `بدون فرم در حالت استعلام`

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
