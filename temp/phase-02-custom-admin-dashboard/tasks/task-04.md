# Task 2.4 — ذخیره AJAX امن (Nonce + Capability + rate)

| فیلد | مقدار |
|---|---|
| فاز | 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 2.3` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-04.php` |

## ۱. هدف

اندپوینت `wp_ajax_es_save_options` با `check_ajax_referer`, `current_user_can`, بازسازی schema فقط برای فیلدهای مجاز، بازگشت JSON استاندارد و ثبت لاگ تغییرات.

## ۲. فایل‌های هدف

- `inc/Admin/AjaxSave.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `check_ajax_referer('es_options', 'nonce')` پیش از هر کاری.
2. `current_user_can('manage_options')` + پاسخ `wp_send_json_error(403)`.
3. Whitelist فیلدها از Schema (هر فیلد غیرمجاز نادیده گرفته شود).
4. ادغام با آپشن موجود و ذخیره یک‌باره (`update_option` تک‌فراخوان).
5. پاسخ `{success:true, data:{saved:[...]}}` + `wp_send_json`.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — درخواست بدون nonce → 403 و بدون تغییر آپشن
- [ ] **P2** — کاربر بدون `manage_options` → 403
- [ ] **P3** — فیلد غیرمجاز ذخیره نشود
- [ ] **P4** — حداکثر یک `update_option` در هر درخواست
- [ ] **P5** — پاسخ JSON معتبر باشد (`json_decode` بدون خطا)

## ۵. تست‌های خودکار

- `غیرفعال‌سازی nonce`
- `تست capability`
- `فیلد غیرمجاز`
- `شمارش update_option`
- `اعتبار JSON`

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
