# Task 8.11 — تنظیمات فرم‌ها (ایمیل مقصد، موضوع، autoresponder، reCAPTCHA اختیاری)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 8.10` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-11.php` |

## ۱. هدف

تب تنظیمات فرم‌ها در داشبورد: ایمیل دریافت‌کننده، موضوع، متن autoresponder، و کلیدهای reCAPTCHA v3 (اختیاری/خاموش به‌صورت پیش‌فرض).

## ۲. فایل‌های هدف

- `inc/Admin/Tabs/FormsTab.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. فیلدهای email/متن با sanitize.
2. وقتی reCAPTCHA خاموش است، هیچ اسکریپت خارجی لود نشود.
3. استفاده از `wp_mail` با `From` معتبر دامنه.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — ایمیل نامعتبر رد شود
- [ ] **P2** — در حالت خاموش، صفر اسکریپت third-party
- [ ] **P3** — موضوع/متن autoresponder در ایمیل (mock) اعمال شود

## ۵. تست‌های خودکار

- `اعتبار ایمیل`
- `حالت خاموش`
- `autoresponder`

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
