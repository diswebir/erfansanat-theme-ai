# Task 2.1 — لایه آپشن: `es_option()` / `es_update_option()` با کش و fallback

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.3` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-01.php` |

## ۱. هدف

قلب دسترسی به تنظیمات: خواندن/نوشتن در آپشن واحد `erfansanat_theme_options` با کش استاتیک درون‌درخواستی، پشتیبانی از dot-notation و مقدار پیش‌فرض برابر محتوای استاتیک.

## ۲. فایل‌های هدف

- `inc/Helpers/Options.php`
- `inc/Helpers/Defaults.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `es_option( string $key, $default = null )` با `get_option` یک‌بار (static cache) و پشتیبانی `header.phone`.
2. `es_update_option( $key, $value )` با merge در آرایه واحد (بدون نوشتن آپشن جدید).
3. `Defaults.php`: تمام مقادیر پیش‌فرض استخراج‌شده از میرور (تلفن‌ها، ایمیل، آدرس، شبکه‌ها، متن کپی‌رایت، نوار اعلان).
4. `es_option_reset()` و هوک `es_options_saved` برای invalidation.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — ۱۰۰ فراخوانی `es_option` در یک درخواست فقط ۱ بار `get_option` بزند
- [x] **P2** — dot-notation مقدار درست برگرداند (`social.instagram`)
- [x] **P3** — در نبود آپشن، مقدار پیش‌فرض برابر متن استاتیک باشد
- [x] **P4** — `es_update_option` آپشن دوم نسازد (شمارش ردیف‌های `option` ثابت بماند)

## ۵. تست‌های خودکار

- `شمارش get_option`
- `dot-notation`
- `تطابق پیش‌فرض با استاتیک`
- `عدم ایجاد آپشن جدید`

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
