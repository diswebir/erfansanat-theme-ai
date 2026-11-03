# Task 8.1 — زیرساخت AJAX امن (Router + Nonce + Rate Limit + Response استاندارد)

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-01.php`

## 🎯 هدف
پایه مشترک همه سرویس‌های ایجکس با امنیت کامل و قالب پاسخ ثابت

## 🔗 وابستگی‌ها
- Task 1.7

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase8 (امنیت)

## 📤 فایل‌های خروجی
- `inc/Ajax/Router.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. Router: ثبت اکشن‌های wp_ajax + nopriv با مپ به هندلرها؛ nonce سراسری (ES.ajaxNonce از 1.7) با چک در هر هندلر
2. Rate limit: transient شمارنده بر (user_id|ip) با سقف تنظیم‌پذیر (پیش‌فرض ۶۰/دقیقه) و پاسخ 429 JSON
3. قالب پاسخ: {success, html, counts, found, filters} + هدرهای content-type صحیح
4. امنیت ورودی: همه پارامترها sanitize شده (text/number/tax/meta)؛ هیچ SQL خام
5. لاگ خطا با WP_DEBUG فقط

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ هندلری بدون nonce اجرا نشود
- کاربر مهمان هم پشتیبانی (nopriv)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] درخواست بدون nonce رد شود (تست)
- [ ] rate limit بعد از سقف رد کند (تست با transient stub)
- [ ] ساختار پاسخ JSON استاندارد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-01.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
