# Task 7.19 — برگه «سوالات متداول» (`template-faq.php`)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.11` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-19.php` |

## ۱. هدف

FAQ صفحه‌ای با گروه‌بندی موضوعی، آکاردئون (`data-faq-*`) و Schema.org `FAQPage` برای سئو.

## ۲. فایل‌های هدف

- `page-templates/template-faq.php`
- `inc/Sections/Schema/faq.php`
- `inc/SEO/FaqSchema.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Repeater گروه‌ها (`گروه`, `سؤال/پاسخ`).
2. تولید JSON-LD `FAQPage` از همان داده (بدون تکرار میانی).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — آکاردئون با JS موجود کار کند
- [ ] **P2** — JSON-LD معتبر و برابر محتوای صفحه
- [ ] **P3** — بدون سؤال، سکشن حذف شود

## ۵. تست‌های خودکار

- `آکاردئون`
- `JSON-LD`
- `حذف خالی`

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
