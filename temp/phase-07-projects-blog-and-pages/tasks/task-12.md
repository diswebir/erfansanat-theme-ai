# Task 7.12 — تک‌نوشته (`single.php`): محتوا + TOC + اشتراک‌گذاری

| فیلد | مقدار |
|---|---|
| فاز | 7 — صفحه پروژه‌ها، وبلاگ، خدمات و برگه‌های عمومی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 7.10`، `Task 3.17` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-12.php` |

## ۱. هدف

تک‌نوشته با hero، جدول محتوا از `h2/h3`، دکمه‌های اشتراک‌گذاری (`data-share-btn`)، تاریخ شمسی، زمان مطالعه و افزایش بازدید.

## ۲. فایل‌های هدف

- `single.php`
- `template-parts/blog/single-hero.php`
- `template-parts/blog/toc.php`
- `template-parts/blog/share.php`
- `inc/Blog/TableOfContents.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. TOC از محتوای فیلترشده (`apply_filters('the_content')`) با `DOMDocument` و افزودن `id` به سرتیترها.
2. دکمه‌های اشتراک با `data-share-label` (کپی لینک/توییتر/واتساپ/تلگرام) بدون کتابخانه.
3. زمان مطالعه با شمارش کلمات فارسی.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — TOC لینک‌های صحیح به سرتیترها بدهد
- [ ] **P2** — اشتراک‌گذاری بدون خطا
- [ ] **P3** — افزایش بازدید ثبت شود
- [ ] **P4** — محتوای خالی خطا ندهد

## ۵. تست‌های خودکار

- `TOC`
- `اشتراک`
- `بازدید`
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
