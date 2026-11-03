# Task 1.5 — هارنس رندر کامل قالب (`tools/harness/render.php`)

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 1.4` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

ابزار تست که بدون وب‌سرور، یک URL را از طریق هسته وردپرس رندر می‌کند و HTML نهایی + کوئری‌های اجراشده + Warningها را برمی‌گرداند. **پیش‌نیاز همه تست‌های ساختاری فازهای بعد.**

## ۲. فایل‌های هدف

- `tools/harness/render.php`
- `tools/harness/wp-cli-shim.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. شبیه‌سازی درخواست با `$_SERVER` و `WP::main()` یا مسیر `template-loader.php`.
2. بافر خروجی + شمارش `$wpdb->num_queries` + `error_get_last()` و لاگ `wp-content/debug.log`.
3. پارامتر ورودی: مسیر نسبی (`/`, `/shop/`, `/product/{slug}/`) و خروجی JSON (`html`,`queries`,`warnings`).
4. پشتیبانی از ترتیب اجرای چند مسیر در یک پروسه (برای سرعت PHP-wasm).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — رندر `/` بدون Fatal و با `</html>` پایان یابد
- [ ] **P2** — تعداد کوئری و لیست Warningها در خروجی JSON موجود باشد
- [ ] **P3** — اجرای ۳ مسیر در یک پروسه ممکن باشد
- [ ] **P4** — در حالت `WP_DEBUG=true` هیچ Warning مخفی نماند

## ۵. تست‌های خودکار

- `رندر صفحه اصلی`
- `گزارش کوئری`
- `چند مسیر در یک اجرا`
- `گزارش Warning`

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
