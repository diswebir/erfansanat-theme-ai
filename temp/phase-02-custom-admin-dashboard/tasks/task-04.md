# Task 2.4 — تب «فوتر، اطلاعات تماس و شبکه‌های اجتماعی»

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-04.php`

## 🎯 هدف
مدیریت کامل محتوای فوتر سه‌ستونه و اطلاعات تماس با پیش‌فرض‌های دقیق JSON-LD میرور

## 🔗 وابستگی‌ها
- Task 2.2

## 📎 مراجع (منابع برش/استخراج)
- footer میرور (سه ستون: دسترسی سریع/دسته‌بندی محصولات/اطلاعات تماس)
- JSON-LD Organization میرور: تلفن 031-91091011، info@erfansanat.com، آدرس اصفهان - خمینی‌شهر

## 📤 فایل‌های خروجی
- `بسط ThemeOptions.php (schema تب footer)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. فیلدهای تماس: contact_phone (پیش‌فرض 031-91091011)، contact_phone2، contact_email (info@erfansanat.com)، contact_address (عین متن میرور)، working_hours، contact_fax اختیاری
2. شبکه‌های اجتماعی: instagram/telegram/whatsapp/linkedin/aparat (url، خالی = مخفی)
3. فوتر: footer_about_text، footer_copyright، footer_note، فیلد لینک‌های ستون دسترسی سریع به‌صورت repeater (label+url) به‌عنوان fallback وقتی منوی وردپرسی تعریف نشده
4. مقدار پیش‌فرض هر فیلد = دقیقاً متن/مقدار میرور (extract دستی از footer.html و JSON-LD)

## ⛔ محدودیت‌ها و خط‌قرمزها
- تلفن/ایمیل/آدرس پیش‌فرض با JSON-LD میرور بایت‌به‌بایت یکی باشد

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] پیش‌فرض تلفن = 031-91091011 و ایمیل = info@erfansanat.com
- [ ] schema شامل repeater لینک‌های دسترسی سریع باشد
- [ ] ۵ شبکه اجتماعی قابل مدیریت باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
