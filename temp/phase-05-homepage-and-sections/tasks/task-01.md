# Task 5.1 — `front-page.php` + حلقه رندر ۱۵ سکشن

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.11`، `Task 2.13` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

ساخت `front-page.php` که فقط از `Renderer` سکشن‌ها استفاده می‌کند (بدون هیچ مارک‌آپ سکشن داخل خودش)، به‌همراه انتخاب واریانت هیرو از آپشن (طبق G1: پیش‌فرض `home1`).

## ۲. فایل‌های هدف

- `front-page.php`
- `inc/Sections/Renderer.php`
- `inc/Sections/Schema/home.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `front-page.php` = `get_header()` + `Renderer::render('home')` + `get_footer()`.
2. آپشن `home_hero_variant` با مقادیر `home1`/`home2`/`home3` (پیش‌فرض `home1`).
3. فهرست ۱۵ کلید سکشن در Registry به ترتیب دقیق استاتیک.
4. پشتیبانی از `page` واقعی صفحه اصلی (fallback به محتوای برگه در صورت خالی بودن سکشن‌ها).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — `front-page.php` هیچ مارک‌آپ سکشن نداشته باشد (تست grep)
- [x] **P2** — ۱۵ سکشن در ترتیب صحیح رندر شوند
- [x] **P3** — تغییر `home_hero_variant` به `home2` به template واریانت مجزا dispatch شود؛ template در Task 5.20 تکمیل می‌شود
- [x] **P4** — بدون سکشن فعال، صفحه خطا ندهد

## ۵. تست‌های خودکار

- `بدون مارک‌آپ مستقیم`
- `ترتیب ۱۵ سکشن`
- `سوییچ واریانت`
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
