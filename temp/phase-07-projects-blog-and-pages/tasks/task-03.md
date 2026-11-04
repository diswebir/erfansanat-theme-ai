# Task 7.3 — محصولات استفاده‌شده در پروژه (`mortabet_mahsole_2`)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 7.1`، `Task 5.18` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

سکشن `#used-products` با کارت محصولات مرتبط شده به پروژه، به ترتیب ذخیره‌شده و با حذف محصولات ناموجود.

## ۲. فایل‌های هدف

- `template-parts/project/used-products.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `WP_Query` روی `post__in` با `orderby => post__in`.
2. کارت با `card-product` و `data-product-*` کامل.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ترتیب ذخیره‌شده حفظ شود
- [ ] **P2** — بدون محصول، سکشن حذف شود
- [ ] **P3** — ID حذف‌شده نادیده گرفته شود

## ۵. تست‌های خودکار

- `ترتیب`
- `حذف خالی`
- `ID حذف‌شده`

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
