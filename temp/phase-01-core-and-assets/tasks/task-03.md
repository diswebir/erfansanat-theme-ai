# Task 1.3 — Theme Supports، ثبت ۴ موقعیت منو و هوک‌های فعال‌سازی

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-03.php`

## 🎯 هدف
قابلیت‌های هسته قالب ثبت شود و ۴ منوی بلوپرینت (main-mega-menu, mobile-menu, tolidat-sherkat, landing) با اسلاگ دقیق ثبت شوند

## 🔗 وابستگی‌ها
- Task 1.1

## 📎 مراجع (منابع برش/استخراج)
- agent.md §موقعیت‌های منو
- temp/DATA_BLUEPRINT.md §۱۰

## 📤 فایل‌های خروجی
- `inc/ThemeSetup.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. add_theme_support برای: title-tag، post-thumbnails، html5 (style-script/search-form/comment-form/comment-list/gallery/caption)، custom-logo (ارتفاع/عرض منعطف)، automatic-feed-links
2. add_theme_support('woocommerce') به‌همراه غیرفعال‌سازی گالری پیش‌فرض ووکامرس (product-gallery-zoom/lightbox/slider) چون گالری میرور جایگزین آن است
3. register_nav_menus با ۴ کلید دقیق: main-mega-menu، mobile-menu، tolidat-sherkat، landing (لیبل فارسی)
4. هوک after_switch_theme → flush_rewrite_rules (برای فاز ۳) + تعیین پیش‌فرض‌های امن
5. فیلتر jpeg_quality/upload_size نامتناسب نیست — صرف‌نظر؛ فقط image_size های موردنیاز میرور (card، thumb) با add_image_size ثبت شود (card-600، card-400)

## ⛔ محدودیت‌ها و خط‌قرمزها
- اسلاگ ۴ منو حتی یک کاراکتر هم نباید تغییر کند
- افزودن سایز تصویر جدید با crop مطابق کارت‌های میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] هر ۵ theme support در کد موجود باشد (title-tag, post-thumbnails, html5, custom-logo, woocommerce)
- [ ] ۴ menu location با کلیدهای دقیق ثبت شده باشند
- [ ] هوک after_switch_theme به flush_rewrite_rules متصل باشد
- [ ] add_image_size برای کارت‌ها ثبت شده باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
