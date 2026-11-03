# Task 5.1 — front-page.php + پیکربندی سکشن‌های صفحه اصلی در SectionManager

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-01.php`

## 🎯 هدف
قالب صفحه اصلی با رندر پویای SectionManager و ثبت schema کامل ۱۴ سکشن (ترتیب پیش‌فرض عین میرور)

## 🔗 وابستگی‌ها
- Task 2.9

## 📎 مراجع (منابع برش/استخراج)
- index.html (ترتیب سکشن‌ها)
- برگه home (ID 8146)

## 📤 فایل‌های خروجی
- `front-page.php`
- `inc/Sections/HomeSections.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. front-page.php: get_header → es_render_sections(ID برگه home) → get_footer (دقت: front page برگه است؛ از option page_on_front خوانده شود)
2. HomeSections: register کردن سکشن‌ها با id/label/schema در ترتیب میرور: hero-home1، projects-gallery، projects-grid؟ (بررسی دقیق ۱۴ سکشن از index.html)، about، history، categories، catalog، services، aftersale، customers، blog-preview، contact، map
3. schema هر سکشن: فیلدهای متنی/تصویری/repeater که در تسک‌های بعدی پر می‌شوند
4. فیلدهای متاباکس برگه home به‌طور خودکار همه سکشن‌ها را نشان دهد

## ⛔ محدودیت‌ها و خط‌قرمزها
- ترتیب پیش‌فرض = ترتیب سکشن‌های index.html
- حالت متای خالی = رندر کامل میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] همه سکشن‌ها با id یکتا ثبت شده باشند (تست شمارش)
- [ ] es_render_sections همه را به ترتیب لود کند (تست stub)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-01.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
