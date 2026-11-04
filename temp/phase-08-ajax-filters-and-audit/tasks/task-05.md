# Task 8.5 — جدول فرم‌ها + `FormRepository` + migration نسخه‌دار

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.4` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-05.php` |

## ۱. هدف

جدول `{prefix}es_forms` با اسکیمای نسخه‌دار (`id, type, name, email, phone, subject, message, meta_json, meta_files, ip_hash, ua, status, created_at`) و migration با `dbDelta` فقط در تغییر نسخه.

## ۲. فایل‌های هدف

- `inc/Forms/FormRepository.php`
- `inc/Forms/Migrator.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `dbDelta` در `es_forms_db_version` مقایسه‌شده.
2. ذخیره با `$wpdb->insert` و `$wpdb->prepare` (بدون رشته چسباندن).
3. ایندکس روی `type`, `status`, `created_at`.
4. پشتیبانی از آرشیو فایل در `uploads/es-forms/` با محافظت `index.php`/`.htaccess`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — جدول در فعال‌سازی ساخته شود (یک‌بار)
- [ ] **P2** — نوع/طول ستون‌ها درست باشد (متن طولانی `LONGTEXT`)
- [ ] **P3** — آپلود فایل خارج از `uploads` مستقیم قابل دسترس نباشد (`.htaccess`/index)
- [ ] **P4** — migration دوباره اجرا نشود بدون تغییر نسخه

## ۵. تست‌های خودکار

- `ساخت جدول`
- `اسکیما`
- `محافظت آپلود`
- `migration یک‌بار`

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
