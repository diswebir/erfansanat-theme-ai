# Task 7.10 — لندینگ‌ها: arya و smartelement + منوی landing

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-10.php`

## 🎯 هدف
دو لندینگ اسکرولی از کامپوننت‌های میرور (تصمیم Q3) با منوی landing ثابت

## 🔗 وابستگی‌ها
- Task 4.7
- Task 2.9

## 📎 مراجع (منابع برش/استخراج)
- کامپوننت‌های هیرو/فیچر/گالری/CTA میرور (index/about) — تصمیم D-006

## 📤 فایل‌های خروجی
- `page-templates/page-landing.php`
- `inc/Sections/LandingSections.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. page-landing.php: قالب عمومی لندینگ: منوی landing (LandingWalker) ثابت سمت راست + es_render_sections با سکشن‌های لندینگ (هیرو تمام‌صفحه، معرفی، ویژگی‌ها، گالری، مشخصات فنی، FAQ، فرم درخواست، فوتر لندینگ)
2. LandingSections: schema سکشن‌های فوق با فیلدهای کامل؛ پیش‌فرض‌ها بازسازی ظاهر میرورگونه (از کامپوننت‌های موجود)
3. پیکربندی اولیه دو برگه arya و smartelement: متن‌های اولیه درگه (ویرایش‌پذیر کامل از ادمین)
4. فرم درخواست لندینگ متصل به 8.6
5. اسکروال انکر با JS میرور (hookهای scene/scroll حفظ تا حد کامپوننت)

## ⛔ محدودیت‌ها و خط‌قرمزها
- لندینگ نباید منوی سراسری را نشان دهد (طبق الگوی لندینگ)
- پیش‌فرض‌ها placeholder محتوایی نه، متن واقعی اولیه

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر لندینگ با سکشن‌های فعال
- [ ] منوی landing اسکرول کار کند
- [ ] فرم متصل به سیستم فرم

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-10.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
