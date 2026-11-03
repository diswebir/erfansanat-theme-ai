# Task 2.1 — پنل «داشبورد اختصاصی عرفان صنعت» و لایه آپشن

**فاز:** 2 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

صفحه ادمین مدرن و تب‌بندی‌شده با ذخیره AJAX، Nonce و Capability (`manage_options`). ذخیره در آپشن واحد `erfansanat_theme_options` و دسترسی از طریق `es_option($key,$default)` با کش استاتیک.

## فایل‌های هدف

- `inc/Admin/AdminMenu.php`
- `inc/Admin/ThemeOptions.php`
- `inc/Admin/Fields/FieldRenderer.php`
- `inc/Helpers/Options.php`
- `assets/admin/admin.css`
- `assets/admin/admin.js`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. ثبت منوی سطح‌بالا با آیکون اختصاصی + زیرمنوها
2. Schema تب‌ها (هدر، فوتر، تماس، شبکه‌های اجتماعی، برند/رنگ، اسکریپت‌ها، آرشیوها، منوها)
3. `FieldRenderer`: text, textarea, url, email, color, media, toggle, repeater, select
4. AJAX save با `check_ajax_referer` + `current_user_can`
5. `es_option()` با کش استاتیک و fallback دقیقاً برابر محتوای استاتیک
6. صفحه‌بندی «برند و رنگ» فقط متغیرهای CSS مجاز را تغییر دهد (بدون دست‌زدن به ساختار)

## شروط پاس شدن (Pass Conditions)

- [ ] درخواست بدون Nonce یا بدون Capability رد شود (403)
- [ ] ذخیره در یک ردیف آپشن (`erfansanat_theme_options`) انجام شود
- [ ] مقدار پیش‌فرض هیچ تفاوتی با خروجی استاتیک ایجاد نکند
- [ ] تمام فیلدها با `sanitize_*` مناسب پاکسازی و با `esc_*` خروجی داده شوند
- [ ] `es_option()` در ۱۰۰ فراخوانی فقط ۱ بار `get_option` بزند (کش)

## تست‌های خودکار

- تست منفی Nonce/Capability
- تست رفت‌وبرگشت ذخیره فیلدها
- شمارش فراخوانی get_option
- تطابق پیش‌فرض با استاتیک

> اسکریپت: `temp/phase-02-custom-admin-dashboard/tests/test-task-1.php`
