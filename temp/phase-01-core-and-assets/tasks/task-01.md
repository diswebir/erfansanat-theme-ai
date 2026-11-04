# Task 1.1 — اسکلت پایه قالب و بوت‌استرپ (style.css / functions.php / index.php)

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-01.php`

## 🎯 هدف
فایل‌های ریشه قالب به‌گونه‌ای ساخته شوند که وردپرس قالب را بدون خطا بشناسد و bootstrap مرکزی وجود داشته باشد

## 🔗 وابستگی‌ها
- (تسک شروع‌کننده — بدون وابستگی)

## 📎 مراجع (منابع برش/استخراج)
- هیچ — فایل جدید

## 📤 فایل‌های خروجی
- `style.css (هدر استاندارد قالب)`
- `functions.php (bootstrap)`
- `index.php (fallback template)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. ساخت style.css فقط با هدر استاندارد وردپرس (Theme Name: ErfanSanat | Theme URI: https://erfansanat.com | Description | Author | Version: 1.0.0 | Requires at least: 7.1 | Requires PHP: 8.4 | Text Domain: erfansanat) — هیچ CSS واقعی داخلش نریزید؛ CSS میرور در assets/ لود می‌شود
2. ساخت index.php به‌عنوان آخرین حلقه دفاعی قالب: get_header() + پیام ساده + get_footer() با مارک‌آپ مینیمال و کلاس‌های میرور
3. ساخت functions.php: گارد ABSPATH، تعریف ثابت‌ها ERFANSANAT_VERSION / ERFANSANAT_DIR / ERFANSANAT_URI، require ماژول‌های inc/ (به‌ترتیب: Autoloader → Helpers → بقیه)
4. اجرای init قالب از طریق کلاس Theme (متد boot) به‌جای کد procedural پراکنده
5. php -l روی هر سه فایل

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ خروجی قبل از get_header مجاز نیست
- متن دامنه در همه رشته‌ها erfansanat

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] php -l هر سه فایل پاس شود
- [ ] هدر style.css شامل Theme Name و Text Domain: erfansanat باشد
- [ ] گارد ABSPATH در functions.php و index.php وجود داشته باشد
- [ ] ثابت‌های ERFANSANAT_VERSION / ERFANSANAT_DIR / ERFANSANAT_URI تعریف شده باشند
- [ ] index.php شامل get_header() و get_footer() باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-01.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
