# Task 1.2 — Autoloader PSR-4 دستی و Namespace `ErfanSanat\Theme`

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.1` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

پیاده‌سازی Autoloader بدون Composer که کلاس‌های `ErfanSanat\Theme\Foo\Bar` را به `inc/Foo/Bar.php` نگاشت می‌کند، با کشِ نام‌کلاس→مسیر و پشتیبانی از interface/trait.

## ۲. فایل‌های هدف

- `inc/Autoloader.php`
- `inc/bootstrap.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `spl_autoload_register` با prefix دقیق `ErfanSanat\Theme\` و `strlen` guard (نه `strpos === 0` خام).
2. نگاشت `\` → `/` و `.php`؛ پشتیبانی از حروف بزرگ/کوچک فایل (تبدیل به form استاندارد).
3. کش استاتیک `array $paths` تا `file_exists` تکراری انجام نشود.
4. `inc/bootstrap.php` تنها نقطه ورود: تعریف ثابت‌ها → ثبت Autoloader → راه‌اندازی Kernel.
5. هیچ `require` دستی برای کلاس‌ها؛ فقط `bootstrap.php` در `functions.php` require شود.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — کلاس تازه‌ساخته `ErfanSanat\Theme\Demo\Probe` بدون `require` بارگذاری شود
- [x] **P2** — کلاس ناموجود هیچ خطا/Notice تولید نکند (فقط false)
- [x] **P3** — کلاس خارج از namespace قالب (مثل `WP_Query`) دست‌نخورده بماند
- [x] **P4** — `inc/bootstrap.php` تنها فایل require‌شده در `functions.php` باشد
- [x] **P5** — تعداد `file_exists` در دو بار بارگذاری یک کلاس = ۱ (کش کار کند)

## ۵. تست‌های خودکار

- `بارگذاری خودکار`
- `ایمنی کلاس ناموجود`
- `عدم تداخل با هسته`
- `شمارش file_exists`

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
