# Task 7.9 — خدمات: single-services.php + فهرست خدمات + card-service

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-09.php`

## 🎯 هدف
قالب‌های CPT services مطابق سینگل‌ها و services.html میرور

## 🔗 وابستگی‌ها
- Task 3.3
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- services/square-lighting.html (سینگل خدمت)
- services.html (فهرست)

## 📤 فایل‌های خروجی
- `single-services.php`
- `page-templates/page-services.php (فهرست)`
- `template-parts/cards/card-service.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. card-service: کارت خدمت میرور (تصویر/عنوان/خلاصه/لینک) — با کلاس‌های کارت‌های services.html
2. single-services.php: هیرو (breadcrumb/H1/خلاصه)، تصویر، بدنه، CTA استعلام/تماس (دکمه میرور)، خدمات مرتبط (هم‌دسته اگر تکسونومی بود؛ فعلاً latest)، بخش محصولات مرتبط اختیاری (فیلد متا ساده)
3. page-services (قالب برگه فهرست): هیرو + گرید card-service از کوئری CPT + صفحه‌بندی؛ برگه فروشنده در ادمین assign می‌شود
4. hreflang/بازدید روی سینگل services فعال (از فاز 3.14 خودکار)

## ⛔ محدودیت‌ها و خط‌قرمزها
- URL /services/{slug}
- فهرست از قالب برگه نه archive CPT

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] سینگل و فهرست رندر fixture
- [ ] کارت مطابق میرور
- [ ] CTA دکمه‌ها صحیح

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-09.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
