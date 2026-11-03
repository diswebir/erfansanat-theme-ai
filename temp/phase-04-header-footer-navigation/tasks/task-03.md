# Task 4.3 — `footer.php` + بازگشت به بالا + اطلاعات تماس

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 4.2` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

فوتر مشترک (۱۲۸ صفحه یکسان) با ستون‌های لینک سریع/دسته‌ها/اطلاعات تماس/کپی‌رایت و دکمه `data-scroll-top` با داده‌های داشبورد.

## ۲. فایل‌های هدف

- `footer.php`
- `template-parts/footer/columns.php`
- `template-parts/footer/contact.php`
- `template-parts/footer/copyright.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کپی مارک‌آپ فوتر با حفظ کلاس‌ها.
2. دسته‌ها از `product_cat` (۵ مورد پرکاربرد یا از تنظیمات).
3. تلفن/ایمیل/آدرس/ساعات کاری از `es_option`.
4. `wp_footer()` پیش از بستن `</body>`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — DOM ≥ ۹۹٪ منطبق با استاتیک
- [ ] **P2** — لینک‌های فوتر با `home_url`/`get_term_link` ساخته شوند
- [ ] **P3** — `wp_footer()` حضور داشته باشد
- [ ] **P4** — در نبود داده تماس، خطا/بلوک خالی رندر نشود

## ۵. تست‌های خودکار

- `diff فوتر`
- `لینک‌های صحیح`
- `وجود wp_footer`
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
