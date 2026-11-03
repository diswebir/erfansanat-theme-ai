# Task 8.9 — ممیزی امنیت و i18n (Escaping/Sanitize/Text Domain)

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-09.php`

## 🎯 هدف
سوییپ نهایی امنیت خروجی/ورودی و یکدستی text domain و ترجمه‌پذیری

## 🔗 وابستگی‌ها
- Task همه فازها

## 📎 مراجع (منابع برش/استخراج)
- استانداردهای WP

## 📤 فایل‌های خروجی
- `تقریر temp/phase-08…/reports/security-i18n-audit.md`
- `اصلاحات نقطه‌ای`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. grep سیستماتیک: echo/get_ بدون esc_html/esc_attr/esc_url → اصلاح؛ ورودی POST/GET/REQUEST بدون sanitize → اصلاح
2. کوئری‌های سفارشی: صفر SQL خام؛ همه از WP_Query/WP_Term_Query/$wpdb->prepare (در صورت لزوم)
3. nonce/capability همه نقاط write (متاباکس/فرم/AJAX) دوباره چک
4. همه رشته‌های UI با __()/esc_html__('…','erfansanat')؛ اسکن رشته‌های خام فارسی بدون دامنه
5. WP_DEBUG=true اجرای کامل صفحات نمونه → صفر notice/warning (ثبت در گزارش)

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ regression عملکردی از اصلاحات

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] سوییپ اسکریپتی صفر مورد بحرانی
- [ ] گزارش با فهرست اصلاحات
- [ ] debug log خالی در رندر نمونه

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-09.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
