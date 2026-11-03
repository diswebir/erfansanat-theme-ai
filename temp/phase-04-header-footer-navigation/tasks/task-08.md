# Task 4.8 — منوی موبایل off-canvas (`aside[data-mobile-sidebar]`)

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 4.2` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-08.php` |

## ۱. هدف

مارک‌آپ کامل منوی موبایل با کلاس‌های استاتیک، سطوح بازشو، دکمه بستن و back-drop، متصل به فهرست `mobile-menu` و کارکرد با `main.js` بدون تغییر.

## ۲. فایل‌های هدف

- `template-parts/header/mobile-menu.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کپی مارک‌آپ `aside` از استاتیک.
2. `wp_nav_menu` با `theme_location=mobile-menu` و walker ساده چندسطحی.
3. دکمه‌های باز/بسته با همان `data-*` استاتیک.
4. حالت پیش‌فرض: بسته (`aria-hidden="true"` مطابق استاتیک).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — باز/بسته شدن با JS موجود کار کند
- [ ] **P2** — ساختار چندسطحی منو حفظ شود
- [ ] **P3** — بدون منو، لینک‌های پیش‌فرض استاتیک نمایش داده شود

## ۵. تست‌های خودکار

- `کارکرد JS`
- `سطوح منو`
- `fallback`

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
