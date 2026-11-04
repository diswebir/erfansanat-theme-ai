# Task 7.10 — آرشیو وبلاگ (`home.php`) با فیلتر دسته و جستجو

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 5.18` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-10.php` |

## ۱. هدف

صفحه `blog` با hero، فیلتر دسته (`data-blog-cat-btn`)، جستجو (`data-blog-search`)، گرید کارت‌ها، شمارنده (`data-blog-count`) و صفحه‌بندی.

## ۲. فایل‌های هدف

- `home.php`
- `template-parts/blog/hero.php`
- `template-parts/blog/grid.php`
- `template-parts/blog/filters.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کارت با `data-blog-item`, `data-blog-category`, `data-blog-search-text`.
2. دسته‌ها از `get_categories()` با شمارش.
3. صفحه‌بندی سئو-دوست.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `/blog/` رندر صحیح با ۷+ مقاله
- [ ] **P2** — فیلتر و جستجوی کلاینت با JS موجود کار کند
- [ ] **P3** — شمارنده با تعداد واقعی

## ۵. تست‌های خودکار

- `رندر`
- `فیلتر JS`
- `شمارنده`

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
