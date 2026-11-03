# Task 7.7 — قالب برگه‌های about و contact (با SectionManager)

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-07.php`

## 🎯 هدف
دو قالب برگه با سکشن‌های کاملاً قابل ویرایش از ادمین — پیش‌فرض عین میرور

## 🔗 وابستگی‌ها
- Task 2.9
- Task 8.5 (اسکلت فرم)

## 📎 مراجع (منابع برش/استخراج)
- about.html (هیرو/معرفی/آمار/تاریخچه/ویدیو home3-about-video/ارزش‌ها)
- contact.html (اطلاعات/فرم/نقشه)

## 📤 فایل‌های خروجی
- `page-templates/page-about.php`
- `page-templates/page-contact.php`
- `inc/Sections/AboutSections.php`
- `inc/Sections/ContactSections.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. page-about.php: es_render_sections با Registry سکشن‌های about (هیرو، معرفی، آمار، تایم‌لاین، ویدیو معرفی، ارزش‌ها) — schema فیلد کامل هر سکشن
2. ویدیو about با hookهای home3-* میرور (play/toggle) حفظ
3. page-contact.php: سکشن‌های (هیرو، اطلاعات تماس، فرم تماس، نقشه) — فرم متصل به سیستم فرم فاز ۸ (action ajax با nonce؛ تا فاز ۸ حالت نمایش)
4. استایل فرم contact مطابق میرور (contact-form/reset/success hookها)
5. هر دو قالب در select قالب برگه ظاهر شوند (Template Name)

## ⛔ محدودیت‌ها و خط‌قرمزها
- پیش‌فرض سکشن‌ها = متن/تصویر میرور
- فرم هنوز ارسال نمی‌کند تا 8.5 (پیام آماده‌سازی)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر پیش‌فرض ساختاری مطابق میرور
- [ ] toggle/reorder ادمین اثر کند
- [ ] فرم رندر با hookها

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-07.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
