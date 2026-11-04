# Task 1.2: پیاده‌سازی ماژول DeBloat برای حذف کامل استایل‌ها و اسکریپت‌های بلوک‌های گوتنبرگ و زوائد wp_head

## فاز مربوطه
- **Phase 1:** زیرساخت هسته قالب، Autoloader، DeBloat و انتقال بی‌نقص Assets استاتیک (`temp/phase-01-core-and-assets`)
- **وضعیت:** `[PASSED]`

## شرح مهندسی تسک
حذف کامل استایل‌ها و اسکریپت‌های ویرایشگر بلوک گوتنبرگ و استایل‌های سراسری (`wp-block-library`, `wp-block-library-theme`, `wc-blocks-style`, `global-styles`, `classic-theme-styles`) در فرانت‌اند، غیرفعال‌سازی کامل اسکریپت و استایل ایموجی‌های وردپرس، حذف تگ‌های متای غیرضروری `wp_head` (`wp_generator`, `wlwmanifest_link`, `rsd_link`, `wp_shortlink_wp_head`, `rest_output_link_wp_head`, `wp_oembed_add_discovery_links`) و غیرفعال‌سازی ویرایشگر گوتنبرگ برای حفظ یکپارچگی قالب کلاسیک.

## فایل‌های هدف (Target Files)
- `inc/Core/DeBloat.php`
- `inc/Core/Plugin.php`

## شروط پاس شدن (Pass Conditions)
- [x] صفر بودن تگ‌های `wp-block-library`، `global-styles-inline-css` و `classic-theme-styles` در خروجی HTML رندرشده `wp_head()` و `wp_footer()`.
- [x] صفر بودن اسکریپت `wp-emoji-release.min.js` و استایل‌های ایموجی در خروجی HTML.
- [x] حذف کامل تگ `<meta name="generator">` و لینک‌های `wlwmanifest` / `EditURI` از خروجی `<head>`.
- [x] عدم بروز هیچ‌گونه PHP Notice / Warning / Deprecated در زمان اجرای `wp_head()` و `wp_footer()`.

## چرخه اجرایی الزامی (6-Step CoT + TDD Loop)
1. `[READ MEMORY]` مطالعه `temp/PROJECT_MEMORY.md`، `temp/STATIC_TEMPLATE_MAP.md` و این فایل تسک.
2. `[THINK & PLAN]` تحلیل ساختار DOM استاتیک و طراحی دقیق معماری PHP/JS.
3. `[WRITE TEST]` تکمیل اسکریپت تست خودکار در `temp/phase-01-core-and-assets/tests/test-task-02.php`.
4. `[IMPLEMENT]` پیاده‌سازی کامل کد بدون هیچ‌گونه Placeholder یا حذف کلاس‌های CSS.
5. `[RUN TEST & CHECK CONDITIONS]` اجرای تست و ثبت نتیجه در `temp/QA_REFLECTION_LOG.md`.
6. `[UPDATE MEMORY]` به‌روزرسانی `temp/PROJECT_MEMORY.md` و `temp/MASTER_PLAN.md` + ثبت Git Commit.
