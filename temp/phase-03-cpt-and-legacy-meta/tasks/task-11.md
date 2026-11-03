# Task 3.11 — تب روابط محصول (`project-ids`, `usage_countr`)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 3.8` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-11.php` |

## ۱. هدف

انتخابگر روابط: پروژه‌های مرتبط (`project-ids` = آرایه ID از `projects`) و کاربردها (`usage_countr` = آرایه `term_id` از `project_type`)، با جستجوی ایجکس در ادمین و ذخیره سازگار با JetEngine.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/Tabs/ProductRelationsTab.php`
- `assets/admin/relations.js`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. جستجو با `wp_ajax_es_search_posts` (پست‌تایپ محدود، nonce).
2. چیدمان tag-like با حذف آیتم.
3. ذخیره آرایه IDها به فرمت اصلی (serialize/JSON).
4. اعتبارسنجی: فقط post_type مجاز و term موجود پذیرفته شود.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ذخیره/خواندن هر دو کلید کار کند
- [ ] **P2** — ID نامعتبر (پست‌تایپ اشتباه) رد شود
- [ ] **P3** — جستجو فقط همان post_type را برگرداند
- [ ] **P4** — فرمت ذخیره با داده قبلی یکسان بماند

## ۵. تست‌های خودکار

- `CRUD روابط`
- `اعتبار نوع`
- `محدودیت جستجو`
- `فرمت`

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
