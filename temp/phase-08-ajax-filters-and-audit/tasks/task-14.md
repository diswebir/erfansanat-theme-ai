# Task 8.14 — ممیزی امنیتی: escaping، capability، nonce، sanitize، آپلود

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 2.4`، `Task 8.10` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-14.php` |

## ۱. هدف

اسکن خودکار کد قالب برای الگوهای پرخطر (`echo $`, `$_GET` مستقیم، `eval`, `unserialize` روی ورودی، `file_get_contents` URL)، و رفع همه موارد.

## ۲. فایل‌های هدف

- `temp/phase-08-ajax-filters-and-audit/tests/security-scan.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. اسکنر PHP با regex + AST ساده روی `inc/`, `template-parts/`, `woocommerce/`.
2. گزارش خط/فایل و شدت.
3. رفع و اجرای مجدد تا صفر مورد.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — صفر مورد `echo $_` بدون escape
- [ ] **P2** — صفر `unserialize` روی داده ورودی
- [ ] **P3** — همه فرم‌ها nonce دارند
- [ ] **P4** — همه اندپوینت‌های AJAX capability/nonce دارند

## ۵. تست‌های خودکار

- `صفر خروجی خام`
- `صفر unserialize`
- `nonce همه فرم‌ها`
- `capability AJAX`

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
