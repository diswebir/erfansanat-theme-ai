# Task 3.1 — ثبت پست‌تایپ `projects` (Native)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.3` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

ثبت دقیق `projects` مطابق بلوپرینت: `public`, `has_archive`, slug `projects`, `with_front=false`, non-hierarchical، supports کامل، `show_in_rest`, و آیکون منو. برچسب‌های فارسی برای همه `labels`.

## ۲. فایل‌های هدف

- `inc/Content/PostTypes.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `register_post_type` با آرگومان‌های دقیق بلوپرینت (بدون تغییر).
2. Labels فارسی: نام، تکی، افزودن، ویرایش، آرشیو، «یافت نشد».
3. `map_meta_cap => true` و `capability_type => 'post'`.
4. `rewrite` فقط با slug و with_front (بدون `hierarchical`).
5. ثبت در `init` اولویت پیش‌فرض (۱۰).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — `post_type_exists('projects')` و `is_post_type_public` true
- [ ] **P2** — `get_post_type_archive_link('projects')` = `/projects/`
- [ ] **P3** — `get_post_type_object('projects')->rewrite` دقیقاً slug/with_front مورد انتظار
- [ ] **P4** — supports شامل title, editor, thumbnail, comments, custom-fields, revisions باشد

## ۵. تست‌های خودکار

- `ثبت تایپ`
- `لینک آرشیو`
- `rewrite`
- `supports`

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
