# Task 4.1 — برش هدر/فوتر + اتصال به تنظیمات + Hreflang

> فاز: هدر، مگامنو، منوی موبایل، فوتر · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
برش هدر دسکتاپ/موبایل و فوتر از استاتیک به header.php/footer.php با حفظ ۱۰۰٪ DOM و هوک‌های استاندارد.

## فایل‌های هدف
- `erfansanat/header.php`
- `erfansanat/footer.php`
- `erfansanat/template-parts/header/`
- `erfansanat/template-parts/global/`

## شروط پاس شدن (Pass Conditions)
- [ ] حفظ ۱۰۰٪ کلاس‌ها/DOM/data-attr هدر و فوتر استاتیک
- [ ] wp_head(), body_class(), wp_body_open(), wp_footer() فراخوانی شوند
- [ ] اتصال به تنظیمات داشبورد (لوگو/اعلان/تماس/شبکه‌ها)
- [ ] تگ‌های Hreflang در <head>

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-04-header-footer-navigation/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
