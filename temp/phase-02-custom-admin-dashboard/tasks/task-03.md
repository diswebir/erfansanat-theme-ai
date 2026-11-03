# Task 2.3 — موتور فیلدها: Renderer، Sanitizer، Schema

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.2` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-03.php` |

## ۱. هدف

سازوکار اعلانی (declarative) برای تعریف تب/فیلد و رندر خودکار: `text, textarea, url, email, tel, number, color, media, toggle, select, checkbox-group, repeater`. هر فیلد sanitizer خودش را اعلام می‌کند؛ هیچ فیلدی بدون sanitize ذخیره نمی‌شود.

## ۲. فایل‌های هدف

- `inc/Admin/Fields/Schema.php`
- `inc/Admin/Fields/FieldRenderer.php`
- `inc/Admin/Fields/Sanitizer.php`
- `inc/Admin/Fields/types/*.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. Schema اعلانی: `es_field(['id'=>'header.phone','type'=>'tel','label'=>'…','default'=>…])`.
2. Renderer: تولید HTML با `esc_attr`/`esc_html` + `name="es_options[header][phone]"`.
3. Sanitizer: `sanitize_text_field`, `esc_url_raw`, `sanitize_email`, `wp_kses_post` (textarea HTML), `absint`, `sanitize_hex_color`.
4. Toggle: ذخیره بولین واقعی؛ Select: whitelist گزینه‌ها.
5. خروجی همه فیلدها با `wp_nonce_field` یکتا برای همان صفحه.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — همه انواع فیلد بدون خطا رندر شوند
- [x] **P2** — payload مخرب (`<script>`, `javascript:`, `">`) در هر نوع پاکسازی شود
- [x] **P3** — مقدار نامعتبر select به پیش‌فرض بازگردد
- [x] **P4** — toggle مقدار `1`/`` (نه رشته‌های نامعتبر) ذخیره کند

## ۵. تست‌های خودکار

- `رندر همه انواع`
- `تست XSS در هر فیلد`
- `whitelist select`
- `نوع toggle`

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
