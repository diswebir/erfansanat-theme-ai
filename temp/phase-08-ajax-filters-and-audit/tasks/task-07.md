# Task 8.7 — ممیزی سئو: JSON-LD داینامیک، Hreflang و سازگاری SEOPress

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-07.php`

## 🎯 هدف
اسکیماهای ساختاریافته داینامیک + hreflang بدون تکرار + پشتیبانی متاهای قدیمی rank_math برای SEOPress

## 🔗 وابستگی‌ها
- Task 3.14
- Task 6.2

## 📎 مراجع (منابع برش/استخراج)
- JSON-LD میرور (Organization/BreadcrumbList)
- agent.md §Phase8 Task8.3

## 📤 فایل‌های خروجی
- `inc/SEO/Schema.php`
- `inc/SEO/Hreflang.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. Schema::organization() از آپشن‌های تماس (نام/لوگو/تلفن/آدرس/همان مقادیر JSON-LD میرور) در homepage
2. Product schema در سینگل: نام/تصویر/برند/GTIN از gs1_id/قیمت/availability (true_sale → InStock/PreOrder-ish)/sku
3. Article/Project schema در سینگل‌ها + BreadcrumbList همه صفحات مطابق میرور
4. Hreflang: خروجی تگ‌ها از متاهای hrefmeta_* (فاز 3.14) + هماهنگی با hreflangpch (غیرفعال‌سازی یکی با فیلتر) + x-default
5. سازگاری SEOPress: چک عدم تکرار og/meta؛ پشتیبانی خواندن کلیدهای rank_math_* قدیمی در صورت نبود مقدار SEOPress (فیلتر راهنما)
6. تست اعتبار JSON با json_decode همه خروجی‌ها

## ⛔ محدودیت‌ها و خط‌قرمزها
- JSON-LD فقط مقادیر واقعی — بدون ساختگی
- بدون تکرار تگ hreflang/og

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] خروجی JSON-LD هر صفحه معتبر (تست decode)
- [ ] hreflang فقط متاهای پر
- [ ] بدون دوبل سازمان/بریدکرمب
- [ ] fallback rank_math کار کند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-07.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
