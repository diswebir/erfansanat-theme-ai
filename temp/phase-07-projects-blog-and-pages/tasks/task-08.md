# Task 7.8 — قالب برگه‌های licenses، faq، refund_returns و recruitment

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-08.php`

## 🎯 هدف
چهار قالب برگه اختصاصی با Repeaterهای قابل ویرایش و فرم استخدام

## 🔗 وابستگی‌ها
- Task 2.9

## 📎 مراجع (منابع برش/استخراج)
- licenses-and-honors.html
- faq.html
- refund_returns.html
- recruitment.html (recruit-tabs/btn/panel)

## 📤 فایل‌های خروجی
- `page-templates/page-licenses.php`
- `page-templates/page-faq.php`
- `page-templates/page-refund.php`
- `page-templates/page-recruitment.php`
- `inc/Sections/*.php مربوطه`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. licenses: سکشن‌های هیرو + گرید گواهینامه‌ها (Repeater: تصویر/عنوان/توضیح) + افتخارات (Repeater متن)
2. faq: هیرو + آکاردئون‌های دسته‌بندی‌شده (Repeater گروه: عنوان گروه + Repeater سوال/جواب) با hookهای faq-* میرور
3. refund: هیرو + بخش‌های متنی/لیست (Repeater بلوک متن با عنوان) مطابق میرور
4. recruitment: هیرو + تب‌های واحدها (IT/حسابداری/پروژه/R&D از Repeater: عنوان/توضیحات/نیازمندی‌ها) + فرم ارسال درخواست همکاری (اتصال 8.6) با hookهای recruit-*
5. همه آکاردئون/تب‌ها با JS میرور کار کنند

## ⛔ محدودیت‌ها و خط‌قرمزها
- پیش‌فرض محتوا = متن دقیق میرور
- تب‌ها/آکاردئون بدون اسکریپت جدید

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر چهار قالب مطابق میرور
- [ ] Repeaterها از ادمین قابل ویرایش
- [ ] فرم recruitment رندر با hookها

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
