# Task 8.5 — سیستم فرم‌ها — Backend (ثبت امن + مدیریت ادمین + ایمیل)

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-05.php`

## 🎯 هدف
موتور ثبت پیام‌های فرم‌ها: CPT داخلی، Honeypot/Nonce/Rate limit، لیست ادمین و اطلاع ایمیل

## 🔗 وابستگی‌ها
- Task 8.1

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase8 Task8.2

## 📤 فایل‌های خروجی
- `inc/Forms/Manager.php`
- `inc/Forms/AdminList.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. CPT غیرعمومی es_form_entry (public=false, exclude_from_search, show_ui سفارشی) با متا: form_type، payload (آرایه فیلدها)، ip، user_agent
2. Manager::submit($form_type, $fields): چک nonce اختصاصی فرم + honeypot فیلد مخفی + rate limit + sanitize هر فیلد (text/email/tel/url/file-link) + insert_post
3. AdminList: منوی «پیام‌های فرم‌ها» با لیست (نوع، نام، تلفن، تاریخ، وضعیت خوانده/جديد)، صفحه جزئیات، اکشن خوانده‌شده/حذف، فیلتر نوع فرم
4. ایمیل: wp_mail به آپشن ایمیل مدیر (fallback admin_email) با قالب ساده + گزینه خاموش/روشن در داشبورد
5. محافظت در برابر CSRF و حمله تکرار (پردازش فقط یک‌بار per nonce)

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ فیلدی بدون sanitize ذخیره نشود
- لیست ادمین escaping کامل

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] ثبت سالم با fixture فیلدها
- [ ] بدون nonce/با honeypot پر → رد
- [ ] لیست ادمین و جزئیات درست
- [ ] ایمیل enqueue شده (تست با wp_mail stub)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-05.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
