# Task 4.2 — header.php — shell سند، hreflang و فراخوانی‌های استاندارد وردپرس

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-02.php`

## 🎯 هدف
هست استاندارد قالب: زبان/charset/body_class/wp_head/wp_body_open + تگ‌های hreflang داینامیک از متا

## 🔗 وابستگی‌ها
- Task 4.1
- Task 3.14
- Task 1.7

## 📎 مراجع (منابع برش/استخراج)
- head میرور (متاهای hreflang ساختار)

## 📤 فایل‌های خروجی
- `header.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. header.php با doctype و html language_attributes و bloginfo charset
2. تگ‌های hreflang: خواندن hrefmeta_fa/en/ar صفحه جاری (یا ترم جاری) + x-default؛ رندر فقط اگر مقدار موجود و افزونه hreflangpch تکراری نسازد (فیلتر es_disable_builtin_hreflang برای هماهنگی)
3. body_class() + wp_body_open() بلافاصله بعد از body
4. فراخوانی wp_head() در جای صحیح؛ آیکون‌های SVG sprite میرور (اگر جداست) داخل body ابتدای هدر
5. بخش data-navbar-header شروع — ادامه در 4.3

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ استایل/اسکریپت دستی — فقط wp_head
- hreflang فقط وقتی متا پر است

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] سه فراخوانی language_attributes/body_class/wp_body_open و wp_head موجود
- [ ] تگ link hreflang فقط برای متاهای پر تولید شود (تست شرطی)
- [ ] فیلتر غیرفعال‌سازی داخلی موجود باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
