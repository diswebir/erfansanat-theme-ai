# Task 3.8 — `MetaRepository` + `RepeaterCodec` (سازگاری دوطرفه JetEngine)

| فیلد | مقدار |
|---|---|
| فاز | 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 1.3` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-08.php` |

## ۱. هدف

لایه خواندن/نوشتن متا: پشتیبانی هم‌زمان از آرایه سریالایز و JSON، نرمال‌سازی `{"item-0":{...}}`، حفظ فرمت قبلی هنگام ذخیره، و API ساده `es_meta($id,$key,$fallback)`.

## ۲. فایل‌های هدف

- `inc/Meta/MetaRepository.php`
- `inc/Meta/RepeaterCodec.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `decode()`: اگر رشته JSON → آرایه؛ اگر سریالایز → `maybe_unserialize`؛ اگر CSV → آرایه.
2. `encode()` با پارامتر `preferred_format` (از داده قبلی تشخیص داده شود).
3. پشتیبانی از ترم‌متا با همان API (`es_term_meta`).
4. کش درون‌درخواستی برای جلوگیری از خواندن تکراری (به‌ویژه در حلقه).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [x] **P1** — round-trip هر دو فرمت بدون تغییر ساختار
- [x] **P2** — آرایه تک‌سطری `{"item-0":{...}}` به همان شکل ذخیره شود
- [x] **P3** — CSV گالری → آرایه صحیح و برگشت پایدار
- [x] **P4** — کش متا: ۱۰ خواندن = ۱ کوئری

## ۵. تست‌های خودکار

- `round-trip`
- `فرمت تک‌سطری`
- `CSV`
- `کش متا`

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
