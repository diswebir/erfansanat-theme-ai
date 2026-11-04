# Task 8.3 — فیلتر هوشمند محصولات (جایگزین JetSmartFilters)

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-03.php`

## 🎯 هدف
فیلتر ایجکس گرید آرشیو محصولات: متن/دسته/قیمت/وضعیت خرید/IP/ولتاژ/مرتب‌سازی/تعداد — بدون رفرش

## 🔗 وابستگی‌ها
- Task 8.1
- Task 6.7

## 📎 مراجع (منابع برش/استخراج)
- نوار فیلتر shop.html (product-*)

## 📤 فایل‌های خروجی
- `inc/Ajax/ProductFilters.php`
- `بسط live-search.js یا assets/js/product-filters.js`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هندلر es_filter_products: پارامترها s، cat (slug)، price_min/max، mode (true_sale)، ip، voltage، sort (date/price-asc/price-desc/popularity/views)، paged، per_page — تبدیل به args WP_Query امن (tax_query/meta_query/date/orderby)
2. شمارنده‌های هر فیلتر (تعداد نتایج در هر گزینه) با کوئری سبک aggregate و کش transient
3. رندر partial: گرید card-product + شمارنده + صفحه‌بندی آپدیت‌شده
4. JS: بایند به نوار فیلتر موجود (hookها حفظ) — آپدیت URL با history.replaceState (قابل اشتراک/رفرش)
5. دکمه حذف فیلترها (بازنشانی) + حالت خالی میرور
6. سازگاری LiteSpeed: ارسال هدر no-cache برای پاسخ‌های AJAX

## ⛔ محدودیت‌ها و خط‌قرمزها
- فیلترها دقیقاً همان شش‌گانه بلوپرینت
- کوئری‌ها بهینه (no_found_rows جز صفحه‌بندی)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] هر پارامتر فیلتر args صحیح تولید کند (تست جدولی)
- [ ] partial شامل کارت‌ها و شمارنده
- [ ] URL آپدیت و بازگردانی state
- [ ] امنیت پارامترها (تست payload)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
