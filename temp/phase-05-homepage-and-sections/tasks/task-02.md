# Task 05.02 — اتصال داینامیک سکشن‌ها به کوئری‌ها

**فاز:** phase-05-homepage-and-sections · **وضعیت:** `[PENDING]`

## شرح تسک
- سکشن دسته‌بندی‌ها → WP_Term_Query از `product_cat` (با thumbnail ترم)
- محصولات ویژه/جدید → WP_Query روی `product` (منبع انتخابی در ادمین)
- پروژه‌های اخیر → WP_Query روی `projects` + متاهای گالری
- مقالات اخیر → WP_Query روی `post`
- آمار/مشتریان/کاتالوگ → فیلدهای داشبورد (Repeater)
- کارت‌های استاندارد: `template-parts/cards/card-product.php`, `card-project.php`, `card-post.php` (+ card-service.php برای فاز ۷) — منطبق بر کارت میرور

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] صفر محتوای هاردکد داینامیک‌پذیر در سکشن‌ها (تست جستجوی رشته‌های نمونه میرور)
- [ ] کوئری‌ها بهینه (no_found_rows در جایی که صفحه‌بندی نیست، transients در صورت نیاز)
- [ ] کارت‌ها در حالت خالی، خود را مخفی می‌کنند

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
