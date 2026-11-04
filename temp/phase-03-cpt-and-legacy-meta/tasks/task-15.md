# Task 3.15 — ترم‌متای `product_cat`: تصویر و FAQ

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 3.4` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-15.php` |

## ۱. هدف

فیلد تصویر (`thumbnail`) و FAQ تکراری (`faq` با `name_question`/`answer_question`) روی فرم ویرایش `product_cat` با media picker و repeater، و ستون تصویر در لیست.

## ۲. فایل‌های هدف

- `inc/Admin/Metaboxes/TermMeta.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. هوک `product_cat_edit_form_fields` / `product_cat_add_form_fields`.
2. ذخیره در `edited_product_cat`/`created_product_cat`.
3. ستون تصویر و FAQ-count در لیست دسته‌ها.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — thumbnail و faq ذخیره/بازیابی شوند
- [x] **P2** — دسته بدون تصویر، placeholder نشان دهد (نه خطا)
- [x] **P3** — FAQ با زیرفیلدهای دقیق ذخیره شود

## ۵. تست‌های خودکار

- `CRUD ترم‌متا`
- `حالت خالی`
- `زیرفیلدها`

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
