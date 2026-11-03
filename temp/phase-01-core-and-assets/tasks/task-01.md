# Task 01.01 — ساخت اسکلت پایه قالب و معماری OOP

**فاز:** phase-01-core-and-assets · **وضعیت:** `[PENDING]`

## شرح تسک
- ساخت `style.css` (هدر استاندارد قالب: Theme Name: ErfanSanat, Text Domain: erfansanat, قابلیت WooCommerce)
- ساخت `functions.php` به‌عنوان bootstrap + `index.php` fallback
- ساخت Autoloader PSR-4 در `inc/` با Namespace `ErfanSanat\Theme`
- ثبت `add_theme_support` برای `title-tag`, `post-thumbnails`, `woocommerce` (+ gallery lightzoom خاموش), `html5`, `custom-logo`, `automatic-feed-links`
- ثبت ۴ منو: `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing`

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] فعال‌سازی فرضی قالب بدون Fatal Error (پاس شدن php -l تمام فایل‌ها)
- [ ] وجود هر ۴ theme support در کد
- [ ] اتولودر کلاس نمونه `inc/` را بدون include دستی resolve می‌کند
- [ ] ۴ menu location با اسلاگ دقیق ثبت شده‌اند

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
