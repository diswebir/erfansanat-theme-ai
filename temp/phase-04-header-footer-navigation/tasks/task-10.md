# Task 4.10 — شمارنده سبد خرید هدر (WooCommerce Fragment)

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 4.2` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-10.php` |

## ۱. هدف

اتصال `data-header-cart-badge` و `data-cart-modal-count` به سبد واقعی ووکامرس با Fragment استاندارد و به‌روزرسانی بدون رفرش.

## ۲. فایل‌های هدف

- `inc/Woo/HeaderCart.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `woocommerce_add_to_cart_fragments` برای شمارنده/آیکون.
2. پشتیبانی از `wc-cart-fragments` (بدون duplicate enqueue).
3. در نبود ووکامرس: نمایش ۰ یا حذف badge (نه خطا).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — افزودن محصول → عدد badge افزایش یابد
- [ ] **P2** — حذف محصول → کاهش یابد
- [ ] **P3** — بدون ووکامرس، هدر سالم رندر شود

## ۵. تست‌های خودکار

- `افزایش/کاهش`
- `سازگاری Fragment`
- `ایمنی بدون ووکامرو`

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
