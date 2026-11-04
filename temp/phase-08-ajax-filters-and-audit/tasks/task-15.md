# Task 8.15 — ممیزی عملکرد: Core Web Vitals و بودجه بارگذاری

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 1.6`، `Task 5.18`، `Task 6.12`، `Task 7.5` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-15.php` |

## ۱. هدف

اندازه‌گیری LCP/CLS/TTFB محلی، بررسی حجم CSS/JS، تعداد کوئری‌ها در ۶ صفحه کلیدی، و ثبت گزارش در `QA_REFLECTION_LOG.md`.

## ۲. فایل‌های هدف

- `tools/harness/perf-report.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ضبط زمان رندر و تعداد کوئری برای `/`, `/shop/`, `/product/{slug}/`, `/projects/`, `/blog/`, `/contact/`.
2. محاسبه حجم منتقل‌شده CSS/JS (gzip).
3. گزارش جدولی + آستانه‌ها از `PROJECT_GOAL.md`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — CSS گشاد‌نشده ≤ ۲۲۰KB
- [ ] **P2** — ≤ ۲ درخواست CSS/JS اختصاصی قالب
- [ ] **P3** — ≤ ۳۰ کوئری صفحه اصلی، ≤ ۴۵ آرشیو
- [ ] **P4** — TTFB محلی < ۴۰۰ms

## ۵. تست‌های خودکار

- `حجم CSS`
- `تعداد درخواست`
- `کوئری`
- `TTFB`

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
