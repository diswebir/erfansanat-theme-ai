# Task 2.6 — فیلد Repeater با Drag&Drop (بدون jQuery UI)

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.5` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-06.php` |

## ۱. هدف

Repeater عمومی برای داشبورد با افزودن/حذف/جابه‌جایی سطرها در JavaScript خالص، ذخیره به فرمت **سازگار با JetEngine** (`{"item-0":{...}}`) و کلیدهای پایدار.

## ۲. فایل‌های هدف

- `inc/Admin/Fields/types/repeater.php`
- `assets/admin/repeater.js`
- `inc/Admin/Fields/RepeaterCodec.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. ساختار آرایه‌ای `item-0`, `item-1` … با بازشماره‌گذاری پس از حذف.
2. Drag&Drop با Pointer Events و بدون کتابخانه.
3. ذخیره هر دو فرمت (array/JSON) با حفظ فرمت قبلی؛ decode تحمل‌کننده.
4. هر سطر nonce/کلید مستقل ندارد اما sanitize در سطح فیلد انجام شود.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — افزودن/حذف/جابه‌جایی، ترتیب ذخیره‌شده را تغییر دهد
- [x] **P2** — فرمت ذخیره با نمونه‌های JetEngine (`{"item-0":{...}}`) منطبق باشد
- [x] **P3** — داده serialize و JSON هر دو decode شوند (round-trip)
- [x] **P4** — سطر خالی ذخیره نشود

## ۵. تست‌های خودکار

- `ترتیب`
- `انطباق فرمت`
- `round-trip دو فرمت`
- `حذف سطر خالی`

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
