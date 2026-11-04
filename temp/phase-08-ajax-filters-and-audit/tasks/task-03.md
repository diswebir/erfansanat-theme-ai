# Task 8.3 — فیلتر ایجکس محصولات (دسته، قیمت، IP، ولتاژ، وضعیت فروش، مرتب‌سازی)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 6.12` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

اندپوینت `es_filter_products` + اتصال به دراوور فیلتر: به‌روزرسانی گرید بدون رفرش، حفظ `data-product-*` و افزودن fallback فرم GET برای سئو.

## ۲. فایل‌های هدف

- `inc/Ajax/FilterEndpoint.php`
- `assets/js/filters.js`
- `template-parts/archive/filters.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Whitelist پارامترها: `cat[]`, `min_price`, `max_price`, `ip`, `voltage`, `sale`, `orderby`, `paged`.
2. `tax_query`/`meta_query` با sanitize؛ `no_found_rows` برای فیلتر سریع.
3. Rooz-Reshte: خروجی HTML کارت + شمارنده + صفحه‌بندی.
4. فرم GET هم‌ارز (بدون JS) با همان پارامترها.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ترکیب ۳ فیلتر هم‌زمان نتیجه صحیح بدهد
- [ ] **P2** — پارامتر خارج از whitelist نادیده گرفته شود
- [ ] **P3** — بدون JS، فرم GET نتیجه دهد
- [ ] **P4** — زمان پاسخ < ۲۰۰ms

## ۵. تست‌های خودکار

- `ترکیب فیلتر`
- `whitelist`
- `fallback بدون JS`
- `زمان`

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
