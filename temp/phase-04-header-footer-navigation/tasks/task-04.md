# Task 4.4 — Walker مگامنو محصولات (تب‌ها + ستون‌ها)

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 4.2`، `Task 1.11` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-04.php` |

## ۱. هدف

Walker اختصاصی که مارک‌آپ `data-mega-menu-products` / `data-mega-prod-tab` / `data-mega-prod-panel` را از ساختار فهرست وردپرس تولید کند: هر آیتم سطح ۱ = تب، آیتم‌های فرزند = ستون‌ها و لینک‌ها.

## ۲. فایل‌های هدف

- `inc/Nav/MegaMenuWalker.php`
- `template-parts/header/mega-products.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Walker با `start_lvl`/`start_el`/`end_el` مطابق مارک‌آپ.
2. تب اول به‌صورت پیش‌فرض `aria-expanded="false"` و پنل `block`/`hidden` مطابق استاتیک.
3. ستون همه دسته‌ها از ترم‌ها (بدون کوئری اضافه؛ کش transient).
4. پشتیبانی از `<svg>` آیکون تعریف‌شده در فیلد آیتم منو.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — خروجی Walker با نمونه استاتیک منطبق (تعداد تب‌ها/پنل‌ها/لینک‌ها)
- [ ] **P2** — کارکرد باز/بسته شدن با `main.js` بدون تغییر JS
- [ ] **P3** — بدون منو، fallback استاتیک رندر شود
- [ ] **P4** — کوئری اضافه در هر صفحه ایجاد نشود (≤ ۲ کوئری Term)

## ۵. تست‌های خودکار

- `انطباق مارک‌آپ`
- `کارکرد JS`
- `fallback`
- `شمارش کوئری`

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
