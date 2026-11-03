# Task 5.2 — اتصال داینامیک سکشن‌ها + کارت‌های استاندارد

> فاز: صفحه اصلی + کامپوننت‌های ماژولار · وضعیت: `[PENDING]`
> پروتکل: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + Commit

## شرح تسک
اتصال سکشن‌ها به WP_Query بهینه و فیلدهای داشبورد؛ هیرو D5؛ کارت‌های استاندارد در template-parts/cards/.

## فایل‌های هدف
- `erfansanat/template-parts/cards/card-product.php`
- `erfansanat/template-parts/cards/card-project.php`
- `erfansanat/template-parts/cards/card-post.php`
- `erfansanat/template-parts/home/section-hero.php`

## شروط پاس شدن (Pass Conditions)
- [ ] صفر محتوای هاردکد
- [ ] هیرو: ویدیوی اصلی جدا + اسلایدهای انتخابی از پروژه‌ها (D5)
- [ ] کارت‌ها data-product-*/data-project-* را داینامیک پر کنند
- [ ] WP_Query بهینه (no found_rows در صورت عدم نیاز)

## تست
- اسکریپت: `tests/test-task-02.php` (اجرا: `php temp/phase-05-homepage-and-sections/tests/test-task-02.php`)
- تست جامع فاز: `tests/run-phase-tests.sh`

## یادداشت‌ها
- حفظ ۱۰۰٪ DOM/CSS/data-attr؛ بدون Placeholder؛ املای دقیق کلیدهای متا.
- تصمیمات مرتبط کاربر را از `temp/PROJECT_MEMORY.md` بخوان.
