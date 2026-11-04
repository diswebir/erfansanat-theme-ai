# Task 8.1 — اندپوینت جستجوی زنده (`SearchEndpoint`)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 4.9` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

`admin-ajax.php?action=es_search` با Nonce، محدودیت نرخ، whitelist نوع محتوا، خروجی JSON/HTML کارت‌ها و جستجوی امن (بدون SQL دستی).

## ۲. فایل‌های هدف

- `inc/Ajax/SearchEndpoint.php`
- `assets/js/search.js`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `check_ajax_referer` + محدودیت نرخ با transient (IP hash).
2. `WP_Query` با `s`, `post_type` از whitelist، `posts_per_page` محدود (۱۰).
3. پاسخ شامل HTML آماده رندر (کارت) + JSON متا.
4. escape کامل خروجی و عدم افشای اطلاعات (بدون لیست پیش‌نویس‌ها).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — پاسخ < ۲۰۰ms در تست محلی
- [ ] **P2** — payloadهای `<script>`, `%27 OR 1=1`, `">` بی‌اثر باشند
- [ ] **P3** — پیش‌نویس/خصوصی برنگردد
- [ ] **P4** — بدون nonce → 403

## ۵. تست‌های خودکار

- `زمان پاسخ`
- `تست SQLi/XSS`
- `عدم افشای پیش‌نویس`
- `nonce`

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
