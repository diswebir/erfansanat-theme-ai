# Task 7.5 — آرشیو پروژه‌ها (`archive-projects.php`)

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 7.1` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

آرشیو با hero، گرید پروژه‌ها، دراوور فیلتر، جستجوی متنی و مرتب‌سازی — همه با همان هوک‌های `data-project-*` و شمارنده واقعی.

## ۲. فایل‌های هدف

- `archive-projects.php`
- `template-parts/archive/project-grid.php`
- `template-parts/archive/project-hero.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کارت پروژه با `data-project-type/location/views/rating/search-text` از داده واقعی.
2. `data-project-count` از `$wp_query->found_posts`.
3. صفحه‌بندی با `paginate_links`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `/projects/` رندر صحیح با ۱۲+ کارت
- [ ] **P2** — شمارنده با `found_posts` برابر
- [ ] **P3** — جستجو/فیلتر سمت کلاینت با JS موجود کار کند
- [ ] **P4** — صفحه ۲ بدون خطا

## ۵. تست‌های خودکار

- `رندر`
- `شمارنده`
- `فیلتر JS`
- `صفحه‌بندی`

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
