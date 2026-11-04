# Task 4.5 — Walker مگامنو پروژه‌ها (تب نوع/شهر)

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 4.2`، `Task 1.11` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

مارک‌آپ `data-mega-menu-projects` با دو تب «نوع پروژه» و «شهر» و ستون‌های ترم‌ها، شامل همه شهرها (`city`) و انواع (`project_type`) با لینک صحیح.

## ۲. فایل‌های هدف

- `inc/Nav/ProjectsMegaWalker.php`
- `template-parts/header/mega-projects.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. لیست ترم‌ها از `get_terms` با `hide_empty => false` (نمایش شهرهای بدون پروژه هم مجاز است).
2. ساختار دو-تبی.
3. لینک هر ترم با `get_term_link` (بدون hardcode).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — همه ترم‌های `city` و `project_type` در پنل ظاهر شوند
- [ ] **P2** — لینک‌ها با `get_term_link` برابر باشند
- [ ] **P3** — در نبود ترم، تب مربوطه رندر نشود (نه لینک شکسته)

## ۵. تست‌های خودکار

- `پوشش ترم‌ها`
- `لینک صحیح`
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
