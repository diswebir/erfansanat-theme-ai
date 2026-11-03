# Task 1.10 — Preload/Preconnect فونت و LCP (شرطی)

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.9` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-10.php` |

## ۱. هدف

تزریق `<link rel="preload">` فونت عربی Vazirmatn در همه صفحات و تصویر LCP صفحه اصلی فقط در `is_front_page()`، به‌همراه `preconnect` مناسب — بدون پیش‌بارگذاری بی‌مصرف در سایر صفحات.

## ۲. فایل‌های هدف

- `inc/Assets/Preload.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `preload` فونت `vazirmatn-arabic.woff2` با `as="font" type="font/woff2" crossorigin` (تنها فونت حیاتی).
2. `preload` تصویر هیرو (WebP با fallback) فقط `is_front_page()`.
3. `preconnect` به دامنه‌های لازم (بدون دامنه خارجی چون همه محلی است → حذف).
4. جلوگیری از preload تکراری با `wp_resource_hints` و array_unique.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — در `/` هم preload فونت و هم preload هیرو موجود باشد
- [ ] **P2** — در `/contact/` preload هیرو موجود نباشد
- [ ] **P3** — هیچ preload تکراری رندر نشود
- [ ] **P4** — خروجی معتبر HTML باشد (تست پارس با DOMDocument)

## ۵. تست‌های خودکار

- `preload شرطی`
- `عدم تکرار`
- `اعتبار HTML`

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
