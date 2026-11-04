# Task 1.5 — DeBloat — ایموجی، wp_head cleanup و حذف متاهای زائد

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-05.php`

## 🎯 هدف
پاکسازی wp_head از ایموجی، RSD، WLW، generator، shortlink، REST link، oEmbed و feedهای اضافه برای حداکثر سرعت

## 🔗 وابستگی‌ها
- Task 1.4

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase1 Task1.2

## 📤 فایل‌های خروجی
- `بسط inc/Frontend/DeBloat.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. حذف print_emoji_detection_script و print_emoji_styles و فیلتر the_content_feed/rss wp_staticize_emoji
2. remove_action: rsd_link، wlwmanifest_link، wp_generator، wp_shortlink_wp_head، rest_output_link_wp_head، wp_oembed_add_discovery_links، wp_oembed_add_host_js
3. حذف adjacِent_posts_rel_link_wp_head و index_rel_link و parent_post_rel_link
4. فیلتر the_generator به رشته خالی
5. غیرفعال‌سازی XML-RPC (add_filter xmlrpc_enabled __return_false) مگر افزونه‌ای نیاز داشت — با فیلتر قابل تغییر es_debloat_xmlrpc
6. حذف dashicons در فرانت برای بازدیدکنندگان واردنشده (opt-in با فیلتر)

## ⛔ محدودیت‌ها و خط‌قرمزها
- حذف REST link فقط از head — REST API برای AJAX خودمان لازم است (disable نه)
- قابل بازگشت با فیلتر es_debloat_* برای سازگاری افزونه‌ها

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] لیست کامل remove_actionها در کد موجود باشد (emoji, rsd, wlw, generator, shortlink, rest, oembed, adjacent)
- [ ] هیچ remove_action بدون گارد is_admin نباشد (به‌جز emoji که سراسری امن است)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-05.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
