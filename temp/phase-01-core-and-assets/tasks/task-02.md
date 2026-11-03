# Task 01.02 — ماژول DeBloat برای حذف کامل بloat وردپرس

**فاز:** phase-01-core-and-assets · **وضعیت:** `[PENDING]`

## شرح تسک
- ساخت `inc/Frontend/DeBloat.php`
- حذف `wp-block-library`, `global-styles`, `classic-theme-styles` CSS
- حذف اسکریپت/استایل ایموجی و فیلترهای smilies
- پاکسازی `wp_head`: rsd_link, wlwmanifest, wp_generator, shortlink, REST link, oEmbed, feed links extra, emoji svg…
- غیرفعال‌سازی gutenberg inline styles/script در body و قالب‌های بلوکی بدون تخریب ادیتور ادمین (فقط فرانت)

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] در سورس HTML خروجی فرانت هیچ ردی از `wp-block-library`, `global-styles`, `emoji` نباشد
- [ ] ادیتور و پیشخوان سالم بمانند (فیلترها فقط روی `wp_enqueue_scripts` فرانت)
- [ ] php -l پاس شود

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
