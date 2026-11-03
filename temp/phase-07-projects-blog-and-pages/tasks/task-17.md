# Task 7.17 — برگه «تماس با ما» (`template-contact.php`) + فرم

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.11` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-17.php` |

## ۱. هدف

هیرو تماس، فرم مشاوره (`data-contact-form`) با Nonce/Honeypot (منطق در ۸.۵)، اطلاعات تماس، نقشه.

## ۲. فایل‌های هدف

- `page-templates/template-contact.php`
- `inc/Sections/Schema/contact.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. مارک‌آپ فرم استاتیک با `name`های مشخص و nonce.
2. اطلاعات تماس از `es_option`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — فرم بدون JS هم قابل ارسال باشد (`action` معتبر)
- [ ] **P2** — Nonce در فرم موجود باشد
- [ ] **P3** — اطلاعات تماس از داشبورد

## ۵. تست‌های خودکار

- `فرم بدون JS`
- `nonce`
- `داده‌های تماس`

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
