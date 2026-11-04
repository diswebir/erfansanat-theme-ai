# Task 1.7 — AssetManager — Enqueue سراسری/شرطی با نسخه‌گذاری filemtime و Preload فونت

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-07.php`

## 🎯 هدف
استایل و اسکریپت قالب با نسخه خودکار (کش‌باستینگ) و فونت‌ها با preload صحیح لود شوند؛ زیرساخت شرطی‌سازی per-template آماده فازهای بعد باشد

## 🔗 وابستگی‌ها
- Task 1.6
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- temp/STATIC_TEMPLATE_MAP.md §۱

## 📤 فایل‌های خروجی
- `inc/Frontend/AssetManager.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. متد enqueue(): ثبت styles.css با نسخه filemtime + main.js با defer (اسکریپت‌های وردپرس جلوی defer را نگیرند — فیلتر script_loader_tag)
2. تابع es_asset_version($relpath) → filemtime با fallback به ERFANSANAT_VERSION
3. Preload سه فونت woff2 با as=font و crossorigin در wp_head (قبل از استایل)
4. wp_localize_script یا داده JSON در head: window.ES = { ajaxUrl, nonce, shopUrl, homeUrl } — nonce الان placeholder، فاز ۸ پر می‌شود
5. متد شرطی conditionals() بر اساس قالب جاری (is_product و...) برای لود اسکریپت‌های اضافی آینده — با map مستند از STATIC_TEMPLATE_MAP
6. غیرفعال‌سازی dequeue هر CSS خارجی CDN (هیچ‌کدام نباید باشد — چک لاگ)

## ⛔ محدودیت‌ها و خط‌قرمزها
- فقط دارایی‌های local
- فونت preload فقط woff2 اصلی (هیچ duplicate)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] تگ link استایل شامل ver=filemtime واقعی فایل باشد
- [ ] سه تگ preload فونت با crossorigin در head چاپ شود
- [ ] main.js با اتریبیوت defer لود شود
- [ ] متغیر ES.ajaxUrl در head/localize موجود باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-07.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
