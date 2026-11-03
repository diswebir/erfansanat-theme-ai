# Task 8.9 — فرم بازخورد مشتریان (`client-feedback`)

| فیلد | مقدار |
|---|---|
| فاز | 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی |
| اندازه | `M` — متوسط (~۱ روز) |
| وابستگی | `Task 8.6` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-09.php` |

## ۱. هدف

فرم بازخورد با امتیاز ستاره‌ای، نوع بازخورد و ذخیره در همان جدول با `type=feedback` (بدون انتشار عمومی).

## ۲. فایل‌های هدف

- `inc/Forms/FeedbackHandler.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. امتیاز ۱..۵ با whitelist.
2. ذخیره با `type=feedback` و `status=unread`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — امتیاز خارج از بازه رد شود
- [ ] **P2** — نوع ذخیره صحیح
- [ ] **P3** — نمایش در لیست ادمین (۸.۱۰)

## ۵. تست‌های خودکار

- `بازه امتیاز`
- `نوع ذخیره`

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
