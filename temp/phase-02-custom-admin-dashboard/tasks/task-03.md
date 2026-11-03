# Task 2.3 — تب «هدر و لوگو» + نوار اعلان — فیلدها و Fallback دقیق میرور

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-03.php`

## 🎯 هدف
مدیریت لوگو، هدر چسبان، دکمه‌های هدر و نوار اعلان با پیش‌فرض‌هایی که عین میرور است

## 🔗 وابستگی‌ها
- Task 2.2

## 📎 مراجع (منابع برش/استخراج)
- uploads/static-mirror/index.html بلوک header (لوگو/دکمه‌ها)
- ese-logo.svg

## 📤 فایل‌های خروجی
- `بسط ThemeOptions.php (schema تب header)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. schema تب header: logo (media)، logo_alt_text، sticky_header (switch، پیش‌فرض on مطابق data-navbar-header)، search_toggle (switch on)، cart_toggle (switch on)
2. نوار اعلان: announcement_enabled (پیش‌فرض off — میرور نوار ندارد)، announcement_text، announcement_link، announcement_bg_color
3. اتصال پیش‌فرض‌ها از طریق فیلتر es_default_options (مقدار logo پیش‌فرض = مسیر assets/images/ese-logo.svg)
4. مستند هر فیلد با help_text فارسی

## ⛔ محدودیت‌ها و خط‌قرمزها
- پیش‌فرض‌ها باید دقیقاً ظاهر/رفتار میرور را بازتولید کنند

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] schema تب header شامل همه فیلدهای فوق باشد
- [ ] پیش‌فرض sticky_header و search/cart toggle = on باشد
- [ ] پیش‌فرض logo به ese-logo.svg اشاره کند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-03.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
