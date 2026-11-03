# Task 1.9 — `AssetManager` با Enqueue شرطی و نسخه‌گذاری filemtime

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 1.8` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-09.php` |

## ۱. هدف

کلاس مدیریت دارایی‌ها: نگاشت «شرط صفحه → دارایی» (Manifest)، `register`/`enqueue` با نسخه `filemtime`، `defer` برای JS، و جلوگیری از لود CSS/JS بی‌مصرف در صفحاتی که آن را ندارند.

## ۲. فایل‌های هدف

- `inc/Assets/AssetManager.php`
- `inc/Assets/Manifest.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `Manifest`: آرایه `handle => [src, type, deps, condition]`؛ condition می‌تواند `is_front_page`, `is_product`, `is_tax(product_cat)` … باشد.
2. نسخه‌گذاری: `ES_THEME_VERSION . '.' . filemtime($path)` با کش در transient کوتاه نمی‌خواهد (filemtime ارزان است).
3. `styles.css` با handle `es-main` در همه صفحات لود شود (تنها CSS قالب).
4. `main.js` با `defer` و `in_footer = false` (مطابق استاتیک: `script src ... defer` در head) — بررسی سازگاری با وردپرس: `wp_enqueue_script(..., true)` + `script_loader_tag` برای defer.
5. در `admin` هیچ‌کدام از این دارایی‌ها لود نشود.
6. `preconnect`/`preload` در تسک ۱.۱۰.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — در `/` فایل `styles.css` یک‌بار و `main.js` با `defer` در سورس باشد
- [ ] **P2** — هیچ handle تکراری ثبت/لود نشود (`wp_script_is` شمارش)
- [ ] **P3** — query string نسخه شامل `filemtime` باشد و با تغییر mtime تغییر کند
- [ ] **P4** — در `is_admin()` هیچ‌کدام لود نشود
- [ ] **P5** — صفحه‌ای که به JS صفحه‌ای نیاز ندارد، فایل اضافه لود نکند

## ۵. تست‌های خودکار

- `شمارش enqueue`
- `نسخه‌گذاری filemtime`
- `وجود defer`
- `ایمنی ادمین`

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
