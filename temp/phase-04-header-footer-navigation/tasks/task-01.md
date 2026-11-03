# Task 4.1 — `header.php` و `footer.php` از روی میرور

**فاز:** 4 — هدر، مگامنو، منوی موبایل و فوتر  
**وضعیت:** `[PENDING]`  
**وابستگی:** —

## هدف

برش هدر/فوتر مشترک (۱۲۸ فایل یکسان) به همراه Hreflang در `<head>`, `wp_head`/`wp_body_open`/`wp_footer`, و اتصال به Theme Options.

## فایل‌های هدف

- `header.php`
- `footer.php`
- `template-parts/header/topbar.php`
- `template-parts/header/nav.php`
- `template-parts/header/mobile-menu.php`
- `inc/Frontend/Head.php`

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

1. استخراج هدر از `index.html` و پارامتری کردن لینک‌ها با `home_url()`
2. جایگزینی لینک‌های ثابت با `get_permalink`/`get_term_link`
3. Hreflang از متای `hrefmeta_*` + پیش‌فرض سایت
4. حذف `custom="0.03"` باقی‌مانده از React (تبدیل به `data-*` در صورت نیاز JS)
5. فوتر با همان ساختار و داده‌های تماس از داشبورد

## شروط پاس شدن (Pass Conditions)

- [ ] DOM خروجی ≥ ۹۹٪ منطبق بر استاتیک (تست مقایسه ساختاری)
- [ ] وجود `wp_head`, `body_class`, `wp_body_open`, `wp_footer`
- [ ] همه لینک‌های داخلی با `home_url` ساخته شوند (بدون `.html`)
- [ ] بدون `custom=` در خروجی
- [ ] هیچ کلاس Tailwind حذف/اضافه نشود

## تست‌های خودکار

- diff ساختاری DOM
- تست وجود هوک‌ها
- تست مطلق‌بودن لینک‌ها
- تست نبود custom=

> اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-1.php`
