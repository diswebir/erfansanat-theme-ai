# Task 3.6 — ستون‌های ادمین + فیلترهای دراپ‌داون پروژه‌ها

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.1`، `Task 3.2`، `Task 3.3` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-06.php` |

## ۱. هدف

ستون‌های `city`, `project_type`, `finish-pro-time`, `number2-viewc`, `same_key_hrefl` در لیست پروژه‌ها، به‌همراه فیلتر دراپ‌داون شهر/نوع در بالای لیست و مرتب‌سازی ستون‌ها.

## ۲. فایل‌های هدف

- `inc/Admin/ListTables.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `manage_projects_posts_columns` / `manage_projects_posts_custom_column`.
2. `restrict_manage_posts` برای دو دراپ‌داون.
3. `manage_edit-projects_sortable_columns` + `pre_get_posts` برای مرتب‌سازی.
4. نمایش `—` برای مقدار خالی (نه «undefined»).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — ستون‌های اضافه در لیست پروژه‌ها دیده شوند
- [x] **P2** — فیلتر شهر/نوع کوئری را فیلتر کند
- [x] **P3** — مرتب‌سازی روی تاریخ تکمیل/بازدید کار کند
- [x] **P4** — صفحه‌بندی با فیلتر فعال حفظ شود

## ۵. تست‌های خودکار

- `ستون‌ها`
- `فیلتر`
- `مرتب‌سازی`
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
