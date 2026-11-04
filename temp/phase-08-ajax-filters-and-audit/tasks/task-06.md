# Task 8.6 — فرم‌های فرانت: تماس، استعلام قیمت، استخدام، بازخورد

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-06.php`

## 🎯 هدف
اتصال چهار فرم میرور به Backend با پیام‌های موفقیت/خطای میرور

## 🔗 وابستگی‌ها
- Task 8.5
- Task 6.5
- Task 7.7
- Task 7.8

## 📎 مراجع (منابع برش/استخراج)
- contact.html فرم (contact-form/reset/success)
- checkout.html استایل فرم
- recruitment.html فرم
- سینگل محصول دکمه استعلام (6.5)

## 📤 فایل‌های خروجی
- `assets/js/forms.js`
- `template-parts/forms/*.php (۴ فرم)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. فرم تماس (سکشن تماس/برگه تماس): نام، تلفن، ایمیل، موضوع، متن — ارسال AJAX با nonce → پیام contact-success میرور
2. فرم استعلام قیمت: پاپ‌آپ مودال میرورگونه در سینگل محصول استعلامی (نام/تلفن + نام محصول خودکار + لینک محصول) → form_type=quote
3. فرم استخدام: مشخصات + واحد شغلی (select از Repeater تب‌های 7.8) + لینک/آپلود رزومه (wp_handle_upload با پسوندهای pdf/doc مجاز و سقف حجم) → form_type=recruit
4. فرم بازخورد مشتریان: امتیاز/متن → form_type=feedback
5. forms.js: submit ایجکس همه فرم‌ها + اعتبارسنجی ساده سمت کلاینت + پیام‌های خطا میرور
6. Honeypot مخفی در هر ۴ فرم

## ⛔ محدودیت‌ها و خط‌قرمزها
- آپلود فقط نوع مجاز + سقف ۵MB
- پیام‌های success/خطا با hookهای میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] هر ۴ فرم ثبت موفق (fixture backend)
- [ ] آپلود غیرمجاز رد شود
- [ ] پیام success میرور نمایش داده شود
- [ ] honeypot و nonce در markup

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
