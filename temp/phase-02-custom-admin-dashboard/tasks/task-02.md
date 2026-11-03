# Task 2.2 — منوی ادمین سطح‌بالا + پوسته RTL داشبورد

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.1` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

ثبت منوی «عرفان صنعت» با آیکون اختصاصی، زیرمنوها و صفحه اصلی پنل با پوسته مدرن RTL (CSS/JS اختصاصی فقط در همان صفحات) — بدون هیچ کتابخانه خارجی.

## ۲. فایل‌های هدف

- `inc/Admin/AdminMenu.php`
- `assets/admin/admin.css`
- `assets/admin/admin.js`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `add_menu_page` با `manage_options` + `dashicons-lightbulb` یا آیکون SVG.
2. زیرمنوها: داشبورد، سراسری (هدر/فوتر)، برند و رنگ، سکشن‌های صفحه، فرم‌ها، ابزارها.
3. شرطی‌کردن enqueue: فقط در `hook_suffix` صفحات خودمان (`admin_enqueue_scripts` + prefix check).
4. پوسته RTL (اتکا به `is_rtl()` + کلاس‌های scoped `.es-admin`).
5. بدون بارگذاری در سایر صفحات ادمین.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — منو فقط برای `manage_options` دیده شود
- [x] **P2** — CSS/JS فقط در صفحات قالب لود شوند (تست hook)
- [x] **P3** — در سایر صفحات ادمین هیچ فایل `es-admin` نباشد
- [x] **P4** — صفحه بدون خطای PHP رندر شود

## ۵. تست‌های خودکار

- `دسترسی‌پذیری`
- `شرطی‌بودن assets`
- `بدون خطای رندر`

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
