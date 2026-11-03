# Task 8.2 — UI جستجوی زنده در مودال هدر (Debounce + حالت خالی)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 8.1`، `Task 4.9` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

اتصال ورودی جستجو به اندپوینت با Debounce ۲۵۰ms، حالت لودینگ، حالت خالی و کلید Escape — بدون تغییر `main.js`.

## ۲. فایل‌های هدف

- `assets/js/search.js`
- `template-parts/header/search-modal.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Debounce + AbortController برای لغو درخواست قبلی.
2. حالت‌های `loading`/`empty`/`error` با کلاس‌های موجود.
3. کلیدهای Arrow/Enter برای انتخاب نتیجه (دسترسی‌پذیری).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — تایپ سریع فقط یک درخواست نهایی بفرستد
- [ ] **P2** — Escape مودال را ببندد
- [ ] **P3** — بدون نتیجه، حالت خالی نمایش دهد
- [ ] **P4** — کارکرد کامل با کیبورد

## ۵. تست‌های خودکار

- `Debounce`
- `Escape`
- `حالت خالی`
- `کیبورد`

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
