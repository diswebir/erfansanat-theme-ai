# Task 5.19 — لایه کوئری بهینه صفحه اصلی (`HomeQueries`)

| فیلد | مقدار |
|---|---|
| فاز | 5 — صفحه اصلی (۱۵ سکشن) و کامپوننت‌های ماژولار |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 5.18` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-19.php` |

> پیشرفت: helperهای bounded/cached projects, posts, products و product_cat، versioned keys و invalidation implement شده‌اند; mock suite passes. Full homepage query count/cache-hit integration gate هنوز تکمیل نیست.

## ۱. هدف

تمام کوئری‌های صفحه اصلی در یک کلاس با کش transient، `no_found_rows`, `update_post_meta_cache=false` برای کارت‌ها، و invalidation هوشمند روی `save_post`/`edited_term`.

## ۲. فایل‌های هدف

- `inc/Query/HomeQueries.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کش با کلید نسخه‌دار (`es_home_v{ES_THEME_VERSION}`).
2. Invalidation روی `save_post`, `deleted_post`, `edited_term`, `set_object_terms`.
3. حداکثر ۶ کوئری مستقل برای کل صفحه.
4. پشتیبانی از حالت بدون کش (query string یا ثابت).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ≤ ۳۰ کوئری کل صفحه اصلی (با شمارش `$wpdb->num_queries`)
- [ ] **P2** — بار دوم رندر، کوئری‌های محتوایی از ۶ کمتر شود (کش)
- [ ] **P3** — افزودن یک پروژه جدید، کش را باطل کند
- [ ] **P4** — با `update_post_meta_cache=true` غلط، داده کارت‌ها صحیح بماند

## ۵. تست‌های خودکار

- `شمارش کوئری`
- `اثر کش`
- `invalidation`
- `صحت داده`

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
