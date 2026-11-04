# Task 4.10 — شمارنده سبد خرید هدر (WooCommerce Fragment)

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 4.2` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-10.php` |

## ۱. هدف

اتصال `data-header-cart-badge` و `data-cart-modal-count` به سبد واقعی ووکامرس با Fragment استاندارد و به‌روزرسانی بدون رفرش.

## ۲. فایل‌های هدف

- `inc/Woo/HeaderCart.php`
- `assets/js/header-cart.js`
- `template-parts/header/cart-modal.php`
- `header.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `woocommerce_add_to_cart_fragments` شمارنده badge و برچسب شمارنده مودال را جداگانه روی selectorهای پایدار جایگزین می‌کند.
2. پشتیبانی از `wc-cart-fragments` (بدون duplicate enqueue) و adapter برای همگام‌سازی متن هر دو شمارنده.
3. حفظ مودال سبد و هوک‌های `main.js` در partial مستقل؛ در نبود ووکامرس، شمارنده صفر و markup سالم بدون خطا.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — افزودن محصول → عدد badge افزایش یابد
- [x] **P2** — حذف محصول → کاهش یابد
- [x] **P3** — بدون ووکامرس، هدر سالم رندر شود
- [x] **P4** — badge و شمارنده مودال با دو Fragment مستقل و مقدار live به‌روز شوند

## ۵. تست‌های خودکار

- `افزایش/کاهش badge و شمارنده مودال`
- `Fragments مجزا و عدم enqueue تکراری`
- `ایمنی بدون ووکامرس`

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
