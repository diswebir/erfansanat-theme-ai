# Task 8.8 — ممیزی پرفورمنس و DeBloat نهایی

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-08.php`

## 🎯 هدف
تأیید بارگذاری حداقلی: هیچ CSS/JS بloat، فونت preload، lazy تصاویر، سازگاری کش LiteSpeed

## 🔗 وابستگی‌ها
- Task همه فازهای فرانت

## 📎 مراجع (منابع برش/استخراج)
- temp/PROJECT_GOAL.md §۴ (معیارها)

## 📤 فایل‌های خروجی
- `تقریر ممیزی temp/phase-08…/reports/performance-audit.md`
- `بسط‌های ریز AssetManager/DeBloat در صورت نیاز`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. سوییپ خروجی head صفحات نمونه: هیچ wp-block-library/global-styles/emoji/CDN خارجی
2. تصاویر کارت‌ها و سکشن‌ها: loading=lazy (به‌جز هیرو) + decoding=async + ابعاد explicit
3. main.js در defer + بدون blocking؛ اسکریپت‌های فاز ۸ نازک و فقط در صفحات مربوطه
4. بررسی کش LiteSpeed: AJAX بدون کش (هدر) + TTL مناسب transientها
5. سنجش زمان رندر صفحات کلیدی روی fixture (زیر هدف) و ثبت در گزارش

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ قابلیتی قربانی پرفورمنس نشود — فقط خروجی بهینه

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] سوییپ صفر بloat در صفحات نمونه
- [ ] lazy فقط خارج از هیرو
- [ ] هدر no-cache روی AJAX
- [ ] گزارش ممیزی با نتایج ثبت شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
