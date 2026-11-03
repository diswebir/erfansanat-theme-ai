# Task 5.1 — برش ۱۵ سکشن + front-page.php با SectionManager

> فاز: صفحه اصلی + کامپوننت‌های ماژولار · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
برش تک‌تک سکشن‌های صفحه اصلی به template-parts/home/ و رندر پویا با ترتیب/فعال‌سازی از داشبورد.

## فایل‌های هدف
- `erfansanat/front-page.php`
- `erfansanat/template-parts/home/section-*.php`

## شروط پاس شدن (Pass Conditions)
- [ ] ۱۵ سکشن مطابق جدول §۴ نگاشت به فایل مجزا
- [ ] رندر پیش‌فرض دقیقاً مشابه استاتیک
- [ ] تغییر ترتیب/حذف سکشن از داشبورد اعمال شود

## تست
- اسکریپت: `tests/test-task-01.php` (اجرا: `php temp/phase-05-homepage-and-sections/tests/test-task-01.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
